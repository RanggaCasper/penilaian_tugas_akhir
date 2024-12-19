<?php

namespace App\Http\Controllers\Student\FinalProject;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\FinalProject\FinalProject;
use App\Models\FinalProject\FinalProjectPeriod;
use Illuminate\Http\JsonResponse;

class FinalProjectRegisterController extends Controller
{
    private $active_period;

    public function __construct()
    {
        $this->active_period = FinalProjectPeriod::where([
            ['generation_id', '=', Auth::user()->generation_id],
            ['start_date', '<=', now()],
            ['end_date', '>=', now()],
            ['is_active', '=', 1],
        ])->first();
    }

    /**
     * Display the final project registration view.
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $existingFinalProject = FinalProject::with('period')->where('user_id', Auth::user()->id)
            ->where('final_project_period_id', $this->active_period->id ?? null)
            ->first();

        $data = $existingFinalProject ?? $this->active_period;
        return view('student.register.final_project', compact('data'));
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

            $data = FinalProject::create([
                'user_id' => Auth::user()->id,
                'title' => $request['title'],
                'document' => $request['document'],
                'support_document' => $request['support_document'],
                'final_project_period_id' => $this->active_period->id,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Data Tugas Akhir berhasil disimpan.',
                'data' => $data,
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
            $data = FinalProject::where('user_id', Auth::user()->id)->first();

            if (!$data) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data Tugas Akhir tidak ditemukan atau Anda tidak memiliki akses.',
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
                'is_editables' => false
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Data Tugas Akhir berhasil diperbarui.',
                'data' => $data,
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
