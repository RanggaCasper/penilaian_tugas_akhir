<?php

namespace App\Http\Controllers\Student\Result;

use Illuminate\Http\Request;
use App\Models\Exam\Assessment;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\AssessmentService;
use App\Http\Controllers\Controller;
use App\Models\Rubric\Rubric;
use Illuminate\Support\Facades\Auth;

class RevisiController extends Controller
{
    public function index(AssessmentService $assessmentService)
    {
        $proposal_score = $assessmentService->calculateScore(Auth::user()->id, 'proposal', false);
        $thesis_score = $assessmentService->calculateScore(Auth::user()->id, 'thesis', false);
        return view('student.result.revisi', compact('proposal_score', 'thesis_score'));
    }

    public function generatePdf($type, $id)
    {
        try {
        $data = Assessment::with('student', 'student.proposal', 'student.thesis', 'student.program_study', 'rubric', 'examiner', 'revisions', 'questions')
                ->where('student_id', Auth::id())
                ->where('examiner_id', $id)
                ->whereHas('questions')
                ->whereHas('rubric', function ($query) use ($type) {
                    $query->where('type', $type);
                })
                ->firstOrFail();

            $data = $data->toArray();

            $pdf = Pdf::loadView('exports.result.revisi_pdf', compact('data'));
            
            return $pdf->download('lembar_perbaikan.pdf');
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
