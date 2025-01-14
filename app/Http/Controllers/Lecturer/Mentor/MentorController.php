<?php

namespace App\Http\Controllers\Lecturer\Mentor;

use App\Models\Mentor\Score;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use App\Imports\ScoresImport;
use App\Models\Exam\SubScore;
use App\Models\Rubric\Rubric;
use App\Models\Exam\Assessment;
use Barryvdh\DomPDF\Facade\Pdf;
use Yajra\DataTables\DataTables;
use App\Models\Proposal\Proposal;
use Illuminate\Http\JsonResponse;
use App\Exports\ScoreTemplateExport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MentorController extends Controller
{
    public function index()
    {
        return view('lecturer.mentor.mentor');
    }

    /**
     * Get the list of students and their scores.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            try {
                $data = Proposal::with('student','student.generation')
                        ->where(function ($query) {
                            $query->where('primary_mentor_id', Auth::user()->id)
                                ->orWhere('secondary_mentor_id', Auth::user()->id);
                        })
                        ->where('status', 'disetujui')
                        ->get();
                return DataTables::of($data)  
                    ->addColumn('no', function ($row) {  
                        static $counter = 0;  
                        return ++$counter;  
                    })
                    ->editColumn('position', function ($row) {  
                        if ($row->primary_mentor_id == Auth::user()->id) {
                            return 'Pembimbing 1';
                        } elseif ($row->secondary_mentor_id == Auth::user()->id) {
                            return 'Pembimbing 2';
                        } else {
                            return '-';
                        }
                    })
                    ->addColumn('action', function ($row) {
                        $assessment = $row->student->assessments()->where('examiner_id', Auth::user()->id)->first();
                        $buttons = '<button type="button" class="btn btn-primary btn-sm edit-btn" data-id="' . $row->id . '" data-bs-toggle="modal" data-bs-target="#modal">Nilai</button>';
                    
                        if ($assessment) {
                            $buttons .= ' <a href="' . route('lecturer.mentor.generatePDF', $row->student->id) . '" class="btn btn-success btn-sm">Download</a>';
                        }
                    
                        return $buttons;
                    })                                     
                    ->rawColumns(['action'])
                    ->make(true);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage(),
                ], 500);
            }
        }

        abort(403);
    }

    /**
     * Store a new score in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        if(!$request->proposal_id) {
            return response()->json([
                'status' => false,
                'message' => "Data tidak valid.",
            ], 404);
        }
        
        try {
            $proposal = Proposal::where('id', $request->proposal_id)->where('primary_mentor_id', Auth::user()->id)->orWhere('secondary_mentor_id', Auth::user()->id)->where('status', 'disetujui')->first();

            if(!$proposal) {
                return response()->json([
                    'status' => false,
                    'message' => "Data tidak valid.",
                ], 404);
            }

            $request->validate([
                'proposal_id' => 'required|integer|exists:proposals,id',
                'scores' => $request->has('scores') ? 'required|array' : 'nullable',
                'scores.*' => $request->has('scores') ? 'required|integer|min:0|max:100' : 'nullable',
                'sub_scores' => $request->has('sub_scores') ? 'required|array' : 'nullable',
                'sub_scores.*' => $request->has('sub_scores') ? 'required|array' : 'nullable',
                'sub_scores.*.*' => $request->has('sub_scores') ? 'required|integer|min:0|max:100' : 'nullable',
            ]);
            
            if (!$request->scores) {
                return response()->json([
                    'status' => false,
                    'message' => 'Tidak ada aksi penilaian.',
                ], 422);
            } 

            $assessment = Assessment::firstOrCreate(
                [
                    'rubric_id' => $proposal->guidance_rubric_id,
                    'student_id' => $proposal->student_id,
                    'examiner_id' => Auth::user()->id,
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

    /**
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    public function getRubric($id, Request $request): JsonResponse
    {
        try {
            $proposal = Proposal::where('id', $id)->where('primary_mentor_id', Auth::user()->id)->orWhere('secondary_mentor_id', Auth::user()->id)->where('status', 'disetujui')->first();

            if (!$proposal) {
                return response()->json([
                    'status' => false,
                    'message' => 'Proposal tidak ditemukan.',
                ], 404);
            }

            $rubric = Rubric::with('criterias.sub_criterias')
                ->where('type', 'guidance')
                ->where('id', $proposal->guidance_rubric_id)
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
            ->where('examiner_id', Auth::user()->id)
            ->where('student_id', $proposal->student_id)
            ->where('rubric_id', $rubric->id)
            ->first();

            return response()->json([
                'rubric' => $rubric,
                'proposal' => $proposal,
                'assessment' => $assessment,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat mengambil data.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function generatePdf($id)
    {
        try {
            $data = Assessment::with('student', 'student.proposal', 'examiner', 'scores.criteria', 'scores.sub_scores', 'scores.sub_scores.sub_criteria')
                ->where('student_id', $id)
                ->where('examiner_id', Auth::id())
                ->firstOrFail();

            $data = $data->toArray();

            $pdf = Pdf::loadView('exports.mentor.assessment_pdf', compact('data'));
            
            return $pdf->download('penilaian_pembimbing.pdf');
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
