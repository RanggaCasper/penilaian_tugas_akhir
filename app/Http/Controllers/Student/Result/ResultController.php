<?php

namespace App\Http\Controllers\Student\Result;

use Illuminate\Http\Request;
use App\Models\Exam\Assessment;
use App\Services\AssessmentService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ResultController extends Controller
{
    public function index(AssessmentService $assessmentService)
    {
        $proposal_score = $assessmentService->calculateScore(Auth::user()->id, 'proposal');
        $final_project_score = $assessmentService->calculateScore(Auth::user()->id, 'final_project');
    
        return view('student.result.result', compact('proposal_score', 'final_project_score'));
    }
}
