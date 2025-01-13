<?php

namespace App\Http\Controllers\Student\Proposal;

use App\Models\User;
use App\Models\Period;
use Illuminate\Http\Request;
use App\Models\Proposal\Proposal;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\Rubric\Rubric;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    private $period;
    
     /**
     * Construct a new controller instance.
     * 
     * This method is used to get the active period of thesis for the current
     * user's generation. The period is used in the index method to display the
     * registration form.
     */
    public function __construct()
    {
        $this->period = Period::whereHas('generation', function ($query) {
                                    $query->where('name', '>=', Auth::user()->generation->name);
                                })
                                ->where('type', 'proposal')
                                ->where('start_date', '<=', now())
                                ->where('end_date', '>=', now())
                                ->where('is_active', '=', 1)
                                ->first();
    }

    /**
     * Display the thesis registration view.
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $exsiting = Proposal::with('period')->where('student_id', Auth::user()->id)
            ->where('period_id', $this->period->id ?? null)
            ->first();

        $data = $exsiting ?? $this->period;
        return view('student.proposal.register', compact('data'));
    }

    /**
     * Store a new thesis in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     * 
     * @throws \Illuminate\Validation\ValidationException if validation fails.
     * @throws \Exception if there is an error during the creation process.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'title' => 'required|unique:proposals,title',
                'rubric_id' => 'required|exists:rubrics,id',
                'document' => [
                    'required',
                    'regex:/^https:\/\/drive\.google\.com\//',
                ],
                'support_document' => [
                    'required',
                    'regex:/^https:\/\/drive\.google\.com\//',
                ],
                'primary_mentor_id' => 'required|exists:users,id',
            ]);

            Proposal::create([
                'student_id' => Auth::user()->id,
                'title' => $request['title'],
                'document' => $request['document'],
                'support_document' => $request['support_document'],
                'period_id' => $this->period->id,
                'rubric_id' => $request['rubric_id'],
                'primary_mentor_id' => $request['primary_mentor_id']
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Anda berhasil mendaftar. Jika ingin memperbaharui data, hubungi administrator.',
                'redirect_url' => route('student.proposal.register.index'),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified thesis in database.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     * 
     * @throws \Illuminate\Validation\ValidationException if validation fails.
     * @throws \Exception if there is an error during the update process.
     */
    public function update(Request $request): JsonResponse
    {
        try {
            $data = Proposal::where('student_id', Auth::user()->id)->first();

            if (!$data) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan atau Anda tidak memiliki akses.',
                ], 404);
            }

            $request->validate([
                'title' => 'required|unique:proposals,title,' . $data->id,
                'document' => [
                    'required',
                    'regex:/^https:\/\/drive\.google\.com\//',
                ],
                'support_document' => [
                    'required',
                    'regex:/^https:\/\/drive\.google\.com\//',
                ],
                'primary_mentor_id' => 'required|exists:users,id',
            ]);

            $data->update([
                'title' => $request->title,
                'document' => $request->document,
                'support_document' => $request->support_document,
                'primary_mentor_id' => $request->primary_mentor_id,
                'is_editable' => false
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Anda berhasil mendaftar. Jika ingin memperbaharui data, hubungi administrator.',
                'redirect_url' => route('student.proposal.register.index'),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data.',
            ], 500);
        }
    }

    /**
     * Retrieve a list of mentors based on a search query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMentor(Request $request): JsonResponse
    {
        if($request->ajax()) {
            $search = $request->get('q');

            $data = User::where('name', 'like', '%' . $search . '%')
                ->whereHas('role', function ($query) {
                    $query->where('name', 'Student');
                })
                ->orWhere('secondary_identity', 'like', '%' . $search . '%')
                ->orderBy('name', 'asc')
                ->get(['id', 'name', 'secondary_identity']);

                return response()->json(
                    $data->map(function ($data) {
                        return [
                            'id' => $data->id,
                            'name' => $data->name . ' - ' . $data->secondary_identity
                        ];
                    })
                );  
        }

        abort(403);
    }
}
