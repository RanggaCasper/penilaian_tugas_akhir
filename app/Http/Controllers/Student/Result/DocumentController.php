<?php

namespace App\Http\Controllers\Student\Result;

use Illuminate\Http\Request;
use App\Models\Exam\Assessment;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\AssessmentService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller
{
    public function index(AssessmentService $assessmentService)
    {
        $proposal_score = $assessmentService->calculateScore(Auth::user()->id, 'proposal');
        $thesis_score = $assessmentService->calculateScore(Auth::user()->id, 'thesis');
        $guidance_score = $assessmentService->calculateScoreGuidance(Auth::user()->id, 'guidance');
        // dd($guidance_score);
        return view('student.result.document', compact('proposal_score', 'thesis_score', 'guidance_score'));
    }

    public function generatePdf($type, $id)
    {
        try {
            $data = Assessment::with('student', 'student.proposal', 'examiner', 'scores.criteria', 'scores.sub_scores', 'scores.sub_scores.sub_criteria')
                ->where('student_id', Auth::id())
                ->where('examiner_id', $id)
                ->firstOrFail();

            $data = $data->toArray();

            if ($type === 'proposal') {
                $pdf = Pdf::loadView('exports.proposal.assessment_pdf', compact('data'));
            } elseif ($type === 'thesis') {
                $pdf = Pdf::loadView('exports.thesis.assessment_pdf', compact('data'));
            } elseif ($type === 'guidance') {
                $pdf = Pdf::loadView('exports.mentor.assessment_pdf', compact('data'));
            }
            
            return $pdf->download('penilaian_pembimbing.pdf');
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
