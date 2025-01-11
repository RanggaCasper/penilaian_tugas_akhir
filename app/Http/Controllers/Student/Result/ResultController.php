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
        $final_project_score = $assessmentService->calculateScore(Auth::user()->id, 'final_project');
        
        $proposal = Proposal::with(['scores.mentor'])
            ->where('student_id', Auth::user()->id)
            ->first();

        $mentor_scores = $proposal && $proposal->scores
            ? $proposal->scores->map(function ($score) {
                return [
                    'mentor' => $score->mentor->name ?? 'N/A',
                    'position' => $score->proposal->primary_mentor_id == $score->mentor_id
                        ? 'Pembimbing 1'
                        : ($score->proposal->secondary_mentor_id == $score->mentor_id
                            ? 'Pembimbing 2'
                            : '-'),
                    'score' => $score->score ?? 0,
                    'final_score' => ($score->score ?? 0) * 0.3,
                ];
            })
            : collect([]);
        
            $final_score = [
                'proposal_score' => $proposal_score['final_score'] ?? 0,
                'final_project_score' => $final_project_score['final_score'] ?? 0,
                'mentor_scores' => $mentor_scores->toArray(), // Gunakan hasil map langsung
                'total_score' => ($proposal_score['final_score'] ?? 0)
                               + ($final_project_score['final_score'] ?? 0)
                               + $mentor_scores->sum('final_score'), // Langsung gunakan sum
            ];

        return view('student.result.result', compact('proposal_score', 'final_project_score', 'mentor_scores', 'final_score'));
    }
}
