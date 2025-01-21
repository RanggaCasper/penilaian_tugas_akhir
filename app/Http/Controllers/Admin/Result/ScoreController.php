<?php

namespace App\Http\Controllers\Admin\Result;

use App\Models\User;
use App\Models\Period;
use App\Exports\ScoreExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use App\Models\Exam\Assessment;
use Barryvdh\DomPDF\Facade\Pdf;
use Yajra\DataTables\DataTables;
use App\Services\AssessmentService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ScoreController extends Controller
{
    public function index(){
        return view('admin.result.score');
    }

    public function get(Request $request)
    {
        if ($request->ajax()) {
            try {
                $data = Period::with('generation')->select(['id', 'name', 'generation_id', 'type'])
                        ->where('program_study_id', Auth::user()->program_study_id);
                return DataTables::of($data)  
                    ->addColumn('no', function ($row) {  
                        static $counter = 0;  
                        return ++$counter;  
                    }) 
                    ->addColumn('generation', function ($row) {  
                        return $row->generation->name;
                    })  
                    ->addColumn('type', function ($row) {  
                        return $row->type == 'proposal' ? 'Proposal' : 'Tugas Akhir';                
                    }) 
                    ->addColumn('action', function ($row) {  
                        $pdfUrl = route('admin.result.score.download', ['id' => $row->id, 'type' => $row->type, 'download' => 'pdf']);
                        $excelUrl = route('admin.result.score.download', ['id' => $row->id, 'type' => $row->type, 'download' => 'excel']);
                        
                        return '
                            <a href="'.$pdfUrl.'" class="btn btn-primary btn-sm">PDF</a>  
                            <a href="'.$excelUrl.'" class="btn btn-success btn-sm">Excel</a>
                        ';  
                    })                    
                    ->rawColumns(['action'])
                    ->make(true);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Terjadi kesalahan saat mengambil data',
                ], 500);
            }
        }

        abort(403);
    }

    public function download(AssessmentService $assessmentService, Excel $excel, $type, $id, $download)
    {
        try {
            $data = Assessment::with([
                'student',
                'student.'.$type => function($query) use ($id) {
                    $query->where('period_id', $id);
                },
                'examiner',
                'scores.criteria',
                'scores.sub_scores',
                'scores.sub_scores.sub_criteria'
            ])
            ->whereHas('student.'.$type, function($query) use ($id) {
                $query->where('period_id', $id);
            })
            ->get();
            
            $studentIds = $data->pluck('student.id')->unique();

            $results = [];

            foreach ($studentIds as $studentId) {
                $result = $assessmentService->calculateScore($studentId, $type);
                $result = [
                    'student' => User::find($studentId)->toArray(),
                    'assessment' => $result
                ];
                $results[] = $result;
            }
            
            $period = Period::find($id)->name;

            if ($type === 'thesis') {
                $type = 'TUGAS AKHIR';
            } elseif ($type === 'proposal') {
                $type = 'PROPOSAL';
            } elseif ($type === 'guidance') {
                $type = 'BIMBINGAN';
            } else {
                $type = strtoupper($type);
            }
            
            if ($download === 'excel') {
                return $excel->download(new ScoreExport($results, $period, $type), 'nilai_penguji.xlsx');
            } elseif ($download === 'pdf') {
                $pdf = Pdf::loadView('exports.result.score_result_pdf', [
                    'title' => "LAPORAN PENILAIAN $type - $period",
                    'data' => $results
                ]);
    
                return $pdf->download('nilai_penguji.pdf');
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid download type.',
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
