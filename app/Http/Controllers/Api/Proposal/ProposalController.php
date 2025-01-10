<?php

namespace App\Http\Controllers\Api\Proposal;

use Carbon\Carbon;
use App\Models\Exam\Exam;
use Illuminate\Http\Request;
use App\Models\Proposal\Proposal;
use App\Http\Controllers\Controller;

class ProposalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function get()
    {
        try {
            $exams = Exam::with('student')->where('type', 'proposal')->get();
        
            $data = $exams->map(function($exam) {
                try {                 
                    $end_exam = Carbon::parse($exam->exam_date . ' ' . $exam->end_time);
        
                    return [
                        'id' => $exam->id,
                        'nim' => $exam->student->identity,
                        'status' => $end_exam->isPast()
                    ];
                } catch (\Exception $e) {                  
                    return [
                        'id' => $exam->id,
                        'nim' => $exam->student->identity ?? 'Unknown',
                        'status' => null,
                    ];
                }
            });
        
            return response()->json([
                'status' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {     
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat memproses data.',
            ], 500);
        }
    }
}
