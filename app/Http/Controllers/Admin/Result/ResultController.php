<?php

namespace App\Http\Controllers\Admin\Result;

use App\Models\Period;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use App\Exports\ScoresExport;
use App\Models\Exam\Assessment;
use Barryvdh\DomPDF\Facade\Pdf;
use Yajra\DataTables\DataTables;
use App\Services\AssessmentService;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ResultController extends Controller
{
    public function index(){
        return view('admin.result.result');
    }

    public function get(Request $request)
    {
        if ($request->ajax()) {
            try {
                $data = Period::with('generation')->select(['id', 'name', 'start_date', 'end_date', 'generation_id', 'type', 'is_active']);
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
                    ->editColumn('is_active', function ($row) {  
                        return $row->is_active   
                            ? '<span class="badge bg-success">Active</span>'   
                            : '<span class="badge bg-danger">Inactive</span>';  
                    })  
                    ->addColumn('action', function ($row) {  
                        $pdfUrl = route('admin.result.download', ['id' => $row->id, 'type' => $row->type, 'download' => 'pdf']);
                        $excelUrl = route('admin.result.download', ['id' => $row->id, 'type' => $row->type, 'download' => 'excel']);
                        
                        return '
                            <a href="'.$pdfUrl.'" class="btn btn-primary btn-sm">PDF</a>  
                            <a href="'.$excelUrl.'" class="btn btn-success btn-sm">Excel</a>
                        ';  
                    })                    
                    ->rawColumns(['action', 'is_active'])
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
                return $excel->download(new ScoresExport($results, $period, $type), 'nilai_penguji.xlsx');
            } elseif ($download === 'pdf') {
                $pdf = Pdf::loadView('exports.scores_result_pdf', [
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
