<?php

namespace App\Http\Controllers\Admin\Result;

use App\Models\User;
use App\Models\Period;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use App\Models\Exam\Assessment;
use Barryvdh\DomPDF\Facade\Pdf;
use Yajra\DataTables\DataTables;
use App\Exports\FinalScoreExport;
use App\Services\AssessmentService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class FinalScoreController extends Controller
{
    public function index(){
        return view('admin.result.final_score');
    }

    public function get(Request $request)
    {
        if ($request->ajax()) {
            try {
                $data = Period::with('generation')->select(['id', 'name', 'generation_id', 'type'])->where('type', 'proposal');
                return DataTables::of($data)  
                    ->addColumn('no', function ($row) {  
                        static $counter = 0;  
                        return ++$counter;  
                    }) 
                    ->addColumn('generation', function ($row) {  
                        return $row->generation->name;
                    })  
                    ->addColumn('action', function ($row) {  
                        $pdfUrl = route('admin.result.final_score.download', ['id' => $row->id, 'download' => 'pdf']);
                        $excelUrl = route('admin.result.final_score.download', ['id' => $row->id, 'download' => 'excel']);
                        
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

    public function download(AssessmentService $assessmentService, Excel $excel, $id, $download)
    {
        try {
            $data = Assessment::with([
                'student',
                'student.proposal' => function($query) use ($id) {
                    $query->where('period_id', $id);
                },
                'examiner',
                'scores.criteria',
                'scores.sub_scores',
                'scores.sub_scores.sub_criteria'
            ])
            ->whereHas('student.proposal', function($query) use ($id) {
                $query->where('period_id', $id);
            })
            ->get();
            
            $studentIds = $data->pluck('student.id')->unique();

            $results = [];

            foreach ($studentIds as $studentId) {
                $proposal_score = $assessmentService->calculateScore($studentId, 'proposal');
                $thesis_score = $assessmentService->calculateScore($studentId, 'thesis');
                $guidance_score = $assessmentService->calculateScoreGuidance($studentId, 'guidance');
                $result = [
                    'proposal_score' => $proposal_score ?? null,
                    'thesis_score' => $thesis_score ?? null,
                    'guidance_score' => $guidance_score ?? null,
                    'total_score' => array_sum([
                        $proposal_score['final_score'] ?? 0,
                        $thesis_score['final_score'] ?? 0,
                        $guidance_score['final_score'] ?? 0,
                    ])
                ];
                $result = [
                    'student' => User::find($studentId)->toArray(),
                    'assessment' => $result
                ];
                $results[] = $result;
            }
            
            $period = Period::find($id)->name;

            if ($download === 'excel') {
                return $excel->download(new FinalScoreExport($results, $period), 'nilai_akhir.xlsx');
            } elseif ($download === 'pdf') {
                $pdf = Pdf::loadView('exports.result.final_score_result_pdf', [
                    'title' => "LAPORAN PENILAIAN AKHIR - $period",
                    'data' => $results,
                    'convertToGrade' => function ($score) {
                        if ($score >= 81) {
                            return 'A (Istimewa)';
                        } elseif ($score >= 76) {
                            return 'AB (Baik Sekali)';
                        } elseif ($score >= 66) {
                            return 'B (Baik)';
                        } elseif ($score >= 61) {
                            return 'BC (Cukup Baik)';
                        } elseif ($score >= 56) {
                            return 'C (Cukup)';
                        } elseif ($score >= 41) {
                            return 'D (Kurang)';
                        } else {
                            return 'E (Kurang Sekali)';
                        }
                    }
                ]);
    
                return $pdf->download('nilai_akhir.pdf');
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
