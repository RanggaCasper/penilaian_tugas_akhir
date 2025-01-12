<?php

namespace App\Http\Controllers\Lecturer\Thesis;

use App\Models\Exam\Exam;
use App\Models\Exam\Score;
use Illuminate\Http\Request;
use App\Models\Exam\SubScore;
use App\Models\Exam\Assesment;
use App\Models\Exam\Assessment;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Rubric\SubCriteria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Evaluation\Evaluation;
use Illuminate\Http\JsonResponse;

class ExamController extends Controller
{
    public function index()
    {
        return view('lecturer.thesis.exam');
    }

    public function get(Request $request)
    {
        try {
            $exams = Exam::with(
                'student.thesis',
                'rubric',
                'assessments',
                'student',
                'primary_examiner',
                'secondary_examiner',
                'tertiary_examiner'
            )
            ->where(function ($query) {
                $query->where('primary_examiner_id', Auth::id())
                    ->orWhere('secondary_examiner_id', Auth::id())
                    ->orWhere('tertiary_examiner_id', Auth::id());
            })
            ->where('type', 'thesis')
            ->orderBy('start_time', 'asc')
            ->get();

            return response()->json([
                'status' => true,
                'html' => view('lecturer.thesis.exam_ajax', compact('exams'))->render(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat memuat data, silakan coba lagi dengan menekan tombol "Muat Ulang". Jika masalah berlanjut, hubungi pengembang untuk bantuan lebih lanjut.',
            ], 500);
        }
    }


    public function store(Request $request)
    {
        try {
            $request->validate([
                'exam_id' => 'required|integer|exists:exams,id',
                'scores' => $request->has('scores') ? 'required|array' : 'nullable',
                'scores.*' => $request->has('scores') ? 'required|integer|min:0|max:100' : 'nullable',
                'sub_scores' => $request->has('sub_scores') ? 'required|array' : 'nullable',
                'sub_scores.*' => $request->has('sub_scores') ? 'required|array' : 'nullable',
                'sub_scores.*.*' => $request->has('sub_scores') ? 'required|integer|min:0|max:100' : 'nullable',
            ]);
            
            if (!$request->has('scores')) {
                return response()->json([
                    'status' => false,
                    'message' => 'Tidak ada aksi penilaian.',
                ]);
            }
            
            $exam = Exam::find($request->exam_id);

            if (!$exam) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data ujian tidak ditemukan.',
                ], 404);
            }

            if (!$exam->is_editable) {
                return response()->json([
                    'status' => false,
                    'message' => 'Penilaian tidak dapat diedit.',
                ], 403);
            }            

            $assessment = Assessment::updateOrCreate(
                [
                    'exam_id' => $request->exam_id,
                    'student_id' => $exam->student_id,
                    'examiner_id' => Auth::id(),
                ],
                [
                    'feedback' => $request->feedback ?? null,
                ]
            );

            foreach ($request->scores as $criteriaId => $score) {
                $assessment->scores()->updateOrCreate(
                    ['criteria_id' => $criteriaId],
                    ['score' => $score]
                );
            }

            if ($request->has('sub_scores') && is_array($request->sub_scores)) {
                foreach ($request->sub_scores as $subCriteriaId => $subScores) {
                    $subScoresRecord = $assessment->scores()->updateOrCreate(
                        ['criteria_id' => $subCriteriaId],
                        ['score' => null, 'has_sub' => 1]
                    );
    
                    foreach ($subScores as $subScoreId => $subScore) {
                        SubScore::updateOrCreate(
                            ['score_id' => $subScoresRecord->id, 'sub_criteria_id' => $subScoreId],
                            ['score' => $subScore]
                        );
                    }
                }
            }    

            return response()->json([
                'status' => true,
                'message' => 'Data berhasil disimpan.',
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function getRubric($id, Request $request)
    {
        try {  
            $rubric = Exam::with('rubric', 'rubric.criterias', 'rubric.criterias.sub_criterias')  
                ->where('id', $id)  
                ->first();  
            
            if (!$rubric->is_editable) {
                return response()->json([  
                    'status' => false,  
                    'message' => 'Nilai tidak dapat di perbarui.'  
                ], 404);  
            }

            if (!$rubric) {  
                return response()->json([  
                    'status' => false,  
                    'message' => 'Data tidak ditemukan.'  
                ], 404);  
            }  
    
            $assessment = Assessment::with('scores.criteria', 'scores.sub_scores', 'scores.sub_scores.sub_criteria')  
                ->where('exam_id', $id)  
                ->where('examiner_id', Auth::id())
                ->first();  
    
            return response()->json([  
                'rubric' => $rubric->rubric,  
                'assessment' => $assessment  
            ]);  
        } catch (\Exception $e) {
            return response()->json([  
                'status' => false,  
                'message' => 'Terjadi kesalahan saat mengambil data. Harap coba lagi nanti.'  
            ], 500);  
        }  
    }

    public function generatePdf($id)
    {
        try {
            $data = Assessment::with('student', 'student.thesis', 'examiner', 'scores.criteria', 'scores.sub_scores', 'scores.sub_scores.sub_criteria')
                ->where('exam_id', $id)
                ->where('examiner_id', Auth::id())
                ->firstOrFail();

            $data = $data->toArray();

            $pdf = Pdf::loadView('exports.assessment_pdf', compact('data'));
            return $pdf->download('penilaian_ujian.pdf');
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Tidak dapat membuat PDF.',
            ], 500);
        }
    }
}
