<?php

namespace App\Http\Controllers\Student\Register;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\FinalProject\FinalProject;
use App\Models\FinalProject\FinalProjectPeriod;

class RegisterFinalProjectController extends Controller
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

    public function index()
    {
        $existingFinalProject = FinalProject::with('period')->where('user_id', Auth::user()->id)
            ->where('final_project_period_id', $this->active_period->id ?? null)
            ->first();

        $data = $existingFinalProject ?? $this->active_period;
        return view('student.register.final_project', compact('data'));
    }

    public function update(Request $request)
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
                'document' => 'required',
                'support_document' => 'required',
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
