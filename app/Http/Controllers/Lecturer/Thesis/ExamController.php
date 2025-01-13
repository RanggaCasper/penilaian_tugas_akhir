<?php

namespace App\Http\Controllers\Lecturer\Thesis;

use App\Models\Exam\Exam;
use App\Models\Exam\Score;
use Illuminate\Http\Request;
use App\Models\Exam\SubScore;
use App\Models\Rubric\Rubric;
use App\Models\Exam\Assesment;
use App\Models\Exam\Assessment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use App\Models\Rubric\SubCriteria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Evaluation\Evaluation;
use App\Models\Thesis\Thesis;

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
                'questions' => $request->has('questions') ? 'array' : 'nullable',
                'questions.*.question' => $request->has('questions') ? 'required|string' : 'nullable',
                'questions.*.weight' => $request->has('questions') ? 'required|numeric|min:0|max:100' : 'nullable',
                'revisions' => $request->has('revisions') ? 'array' : 'nullable',
                'revisions.*.description' => $request->has('revisions') ? 'required|string' : 'nullable',
                'revisions.*.chapter' => $request->has('revisions') ? 'required|string' : 'nullable',
                'revisions.*.page' => $request->has('revisions') ? 'required|string' : 'nullable',
            ]);

            if (!$request->has('scores') && !$request->has('questions') && !$request->has('revisions')) {
                return response()->json([
                    'status' => false,
                    'message' => 'Tidak ada aksi yang dilakukan.',
                ]);
            }

            $exam = Exam::with('student', 'student.thesis')->find($request->exam_id);

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
                    'rubric_id' => $exam->student->thesis->rubric_id,
                    'student_id' => $exam->student_id,
                    'examiner_id' => Auth::id(),
                ]
            );

            if ($request->has('scores')) {
                foreach ($request->scores as $criteriaId => $score) {
                    $assessment->scores()->updateOrCreate(
                        ['criteria_id' => $criteriaId],
                        ['score' => $score]
                    );
                }
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

            if ($request->has('questions') && is_array($request->questions)) {
                foreach ($request->questions as $questionData) {
                    $assessment->questions()->firstOrCreate([
                        'question' => $questionData['question'],
                        'weight' => $questionData['weight'],
                    ]);
                }
            }

            if ($request->has('revisions') && is_array($request->revisions)) {
                foreach ($request->revisions as $revisionData) {
                    $assessment->revisions()->firstOrCreate([
                        'description' => $revisionData['description'],
                        'chapter' => $revisionData['chapter'],
                        'page' => $revisionData['page'],
                    ]);
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
            $exam = Exam::with('student','student.thesis')->where('id', $id)->where('primary_examiner_id', Auth::id())->orWhere('secondary_examiner_id', Auth::id())->orWhere('tertiary_examiner_id', Auth::id())->first();
            $thesis = $exam->student->thesis;
            
            if (!$thesis) {
                return response()->json([
                    'status' => false,
                    'message' => 'Tugas Akhir tidak ditemukan.',
                ], 404);
            }

            $rubric = Rubric::with('criterias.sub_criterias')
                ->where('type', 'thesis')
                ->where('id', $thesis->rubric_id)
                ->first();

            if (!$rubric) {
                return response()->json([
                    'status' => false,
                    'message' => 'Rubrik tidak ditemukan.',
                ], 404);
            }

            $assessment = Assessment::with([
                'scores.criteria',
                'scores.sub_scores.sub_criteria',
                'questions',
                'revisions',
            ])
            ->where('student_id', $thesis->student_id)
            ->where('examiner_id', Auth::user()->id)
            ->where('rubric_id', $rubric->id)
            ->first();

            return response()->json([
                'rubric' => $rubric,
                'thesis' => $thesis,
                'assessment' => $assessment,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'error' => $e->getMessage(),
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

            $pdf = Pdf::loadView('exports.thesis.assessment_pdf', compact('data'));
            return $pdf->download('penilaian_ujian.pdf');
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
