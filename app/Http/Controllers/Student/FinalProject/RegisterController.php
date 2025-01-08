<?php

namespace App\Http\Controllers\Student\FinalProject;

use App\Models\Period;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\FinalProject\FinalProject;

class RegisterController extends Controller
{
    private $period;

    /**
     * Construct a new controller instance.
     * 
     * This method is used to get the active period of final project for the current
     * user's generation. The period is used in the index method to display the
     * registration form.
     */
    public function __construct()
    {
        $this->period = Period::whereHas('generation', function ($query) {
                                    $query->where('name', '>=', Auth::user()->generation->name);
                                })
                                ->where('type', 'final_project')
                                ->where('start_date', '<=', now())
                                ->where('end_date', '>=', now())
                                ->where('is_active', '=', 1)
                                ->first();
    }

    /**
     * Display the final project registration view.
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $existingFinalProject = FinalProject::with('period')->where('student_id', Auth::user()->id)
            ->where('period_id', $this->period->id ?? null)
            ->first();

        $data = $existingFinalProject ?? $this->period;
        return view('student.final_project.register', compact('data'));
    }

    /**
     * Store a new final project in the database.
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
                'title' => 'required|unique:final_projects,title',
                'document' => [
                    'required',
                    'regex:/^https:\/\/drive\.google\.com\//',
                ],
                'support_document' => [
                    'required',
                    'regex:/^https:\/\/drive\.google\.com\//',
                ],
            ]);

            FinalProject::create([
                'student_id' => Auth::user()->id,
                'title' => $request['title'],
                'document' => $request['document'],
                'support_document' => $request['support_document'],
                'period_id' => $this->period->id,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Anda berhasil mendaftar. Jika ingin memperbaharui data, hubungi administrator.',
                'redirect_url' => route('student.final_project|.register.index'),
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
                'message' => 'Terjadi kesalahan saat menyimpan data.',
            ], 500);
        }
    }

    /**
     * Update the specified final project in database.
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
            $data = FinalProject::where('student_id', Auth::user()->id)->first();

            if (!$data) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan atau Anda tidak memiliki akses.',
                ], 404);
            }

            $request->validate([
                'title' => 'required|unique:final_projects,title,' . $data->id,
                'document' => [
                    'required',
                    'regex:/^https:\/\/drive\.google\.com\//',
                ],
                'support_document' => [
                    'required',
                    'regex:/^https:\/\/drive\.google\.com\//',
                ],
            ]);

            $data->update([
                'title' => $request->title,
                'document' => $request->document,
                'support_document' => $request->support_document,
                'is_editable' => false
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Anda berhasil mendaftar. Jika ingin memperbaharui data, hubungi administrator.',
                'redirect_url' => route('student.final_project|.register.index'),
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
}
