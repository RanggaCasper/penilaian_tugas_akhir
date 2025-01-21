<?php  

namespace App\Http\Controllers\Api\Proposal;  

use Carbon\Carbon;  
use App\Models\Exam\Exam;  
use Illuminate\Http\Request;  
use App\Models\Proposal\Proposal;  
use App\Http\Controllers\Controller;  
use Illuminate\Support\Facades\RateLimiter;  

class ProposalController extends Controller  
{  
    private $maxAttempts = 30;
    private $decayMinutes = 1;

    /**  
     * Display a listing of the resource.  
     */  
    public function get(Request $request)  
    {  
        $key = 'proposal-api:' . $request->ip(); 

        if (RateLimiter::tooManyAttempts($key, $this->maxAttempts)) {  
            return response()->json([  
                'status' => false,  
                'message' => 'Too many requests. Please try again later.'  
            ], 429);  
        }  

        RateLimiter::hit($key, $this->decayMinutes);  

        try {  
            $query = Exam::with('student')->where('type', 'proposal');  
            
            if ($request->has('nim')) {  
                $query->whereHas('student', function ($query) use ($request) {  
                    $query->where('identity', $request->nim);  
                });  
            }  

            $exams = $query->get();  

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