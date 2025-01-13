<?php

namespace App\Http\Controllers\Student\Result;

use App\Models\Mentor\Score;
use Illuminate\Http\Request;
use App\Services\AssessmentService;
use App\Http\Controllers\Controller;
use App\Models\Proposal\Proposal;
use Illuminate\Support\Facades\Auth;

class ResultController extends Controller
{
    public function index(AssessmentService $assessmentService)
    {
        $proposal_score = $assessmentService->calculateScore(Auth::user()->id, 'proposal');
        $thesis_score = $assessmentService->calculateScore(Auth::user()->id, 'thesis');
        $guidance_score = $assessmentService->calculateScoreGuidance(Auth::user()->id, 'guidance');
        
        $final_score = [
            'proposal_score' => $proposal_score ?? null,
            'thesis_score' => $thesis_score ?? null,
            'guidance_score' => $guidance_score ?? null,
            'total_score' => array_sum([
                $proposal_score['final_score'] ?? 0,
                $thesis_score['final_score'] ?? 0,
                $guidance_score['final_score'] ?? 0,
            ])
        ];

        return view('student.result.result', compact('proposal_score', 'thesis_score', 'guidance_score', 'final_score'));
    }
}
