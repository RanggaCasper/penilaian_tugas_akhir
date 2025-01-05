<?php

namespace App\Http\Controllers\Lecturer\FinalProject;

use App\Models\Exam\Score;
use Illuminate\Http\Request;
use App\Models\Exam\SubScore;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\FinalProject\Exam;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Evaluation\Evaluation;
use App\Models\Evaluation\SubCriteria;
use App\Models\Exam\Evaluation as ExamEvaluation;

class ExamController extends Controller
{
    public function index()
    {
        $exams = Exam::with('student.final_project', 'evaluations','student', 'primary_examiner', 'secondary_examiner', 'tertiary_examiner')->where(function ($query) {
            $query->where('primary_examiner_id', Auth::id())
                ->orWhere('secondary_examiner_id', Auth::id())
                ->orWhere('tertiary_examiner_id', Auth::id());
        })->orderBy('start_time', 'asc')->get();

        return view('lecturer.final_project.exam', compact('exams'));
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'exam_id' => 'required|exists:exams,id',
                'exam_evaluation_id' => 'required|exists:evaluations,id',
                'scores' => 'nullable|array',
                'scores.*' => 'numeric|min:0|max:100',
                'sub_scores' => 'nullable|array',
                'sub_scores.*' => 'numeric|min:0|max:100',
            ]);

            $exam = Exam::findOrFail($data['exam_id']);
            
            $evaluation = ExamEvaluation::updateOrCreate(
                [
                    'exam_id' => $exam->id,
                    'student_id' => $exam->student->id,
                    'examiner_id' => Auth::id(),
                ],
                []
            );

            if (!empty($data['scores'])) {
                foreach ($data['scores'] as $criteriaId => $score) {
                    Score::updateOrCreate(
                        [
                            'exam_evaluation_id' => $evaluation->id,
                            'evaluation_criteria_id' => $criteriaId,
                        ],
                        ['score' => $score]
                    );
                }
            }

            if (!empty($data['sub_scores'])) {
                foreach ($data['sub_scores'] as $subCriteriaId => $score) {
                    $evaluationCriteriaId = SubCriteria::where('id', $subCriteriaId)
                        ->value('evaluation_criteria_id');

                    if (!$evaluationCriteriaId) {
                        continue;
                    }

                    $evaluationScore = Score::firstOrCreate(
                        [
                            'exam_evaluation_id' => $evaluation->id,
                            'evaluation_criteria_id' => $evaluationCriteriaId,
                        ],
                        ['has_sub' => true]
                    );

                    SubScore::updateOrCreate(
                        [
                            'score_id' => $evaluationScore->id,
                            'sub_evaluation_criteria_id' => $subCriteriaId,
                        ],
                        ['score' => $score]
                    );

                    $evaluationScore->update(['has_sub' => true]);
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Data berhasil disimpan',
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


    public function getEvaluation($id, Request $request)
    {
        $examId = $request->query('exam_id');
        $evaluation = Evaluation::with('evaluation_criterias.sub_evaluation_criterias')->findOrFail($id);

        $scores = [
            'scores' => Score::where('exam_evaluation_id', $examId)->pluck('score', 'evaluation_criteria_id'),
            'sub_scores' => SubScore::whereIn('score_id', Score::where('exam_evaluation_id', $examId)->pluck('id'))
                ->pluck('score', 'sub_evaluation_criteria_id'),
        ];

        return response()->json([
            'evaluation_criterias' => $evaluation->evaluation_criterias,
            'scores' => $scores,
        ]);
    }

    public function generatePdf($examEvaluationId)
    {
        $data = ExamEvaluation::with('student', 'exam', 'examiner', 'scores', 'scores.criteria', 'scores.sub_scores', 'scores.sub_scores.sub_criteria')->findOrFail($examEvaluationId);

        $data = $data->toArray();
        // dd($data);
        // return response()->json($evaluation);    
        // dd($data);
        $pdf = Pdf::loadView('exports.evaluation_pdf', compact('data'));
        return $pdf->download('penilaian_ujian.pdf');
    }

}
