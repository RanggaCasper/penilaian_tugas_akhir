<?php

namespace App\Http\Controllers\Admin\FinalProject;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Models\FinalProject\FinalProject;
use Illuminate\Http\JsonResponse;

class FinalProjectRegisterController extends Controller
{
    public function index()
    {
        return view('admin.final_project.register');
    }

    /**
     * Display a listing of the data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            try {
                $data = FinalProject::with('user','user.generation')->get();
                return DataTables::of($data)  
                    ->addColumn('no', function ($row) {  
                        static $counter = 0;  
                        return ++$counter;  
                    })
                    ->editColumn('status', function ($row) {  
                        $badgeClass = match ($row->status) {
                            'pending' => 'bg-warning',
                            'approved' => 'bg-success',
                            'rejected' => 'bg-danger',
                            default => 'bg-secondary',
                        };
                    
                        return '<span class="badge ' . $badgeClass . '">' . ucfirst($row->status ?? 'Unknown') . '</span>';
                    })                    
                    ->addColumn('action', function ($row) {  
                        return '  
                            <button type="button" class="btn btn-primary btn-sm edit-btn" data-id="' . $row->id . '" data-bs-toggle="modal" data-bs-target="#modal">Lihat</button>  
                            <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="' . $row->id . '">Hapus</button>  
                        ';  
                    })  
                    ->rawColumns(['action', 'status'])
                    ->make(true);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Terjadi kesalahan saat mengambil data',
                ], 500);
            }
        }

        abort(403);
    }

    /**
     * Display the specified data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getById(Request $request, $id): JsonResponse
    {
        if ($request->ajax()) {
            try {
                $data = FinalProject::with('user','user.generation')->findOrFail($id);

                return response()->json($data);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan',
                ], 404);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Terjadi kesalahan saat mengambil data',
                ], 500);
            }
        }

        abort(403);
    }

    /**
     * Update the specified data in database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        if ($request->ajax()) {
            try {
                $request->validate([
                   'status' => 'required|in:pending,approved,rejected',
                   'is_editables' => 'required|boolean',
                ]);
                
                $data = FinalProject::findOrFail($id);
                
                $data->update([
                    'status' => $request->status,
                    'is_editables' => $request->is_editables
                ]);
                
                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil diubah',
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $e->errors(),
                ], 422);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan',
                ], 404);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Terjadi kesalahan saat memperbarui data',
                ], 500);
            }            
        }

        abort(403);
    }

    /**
     * Remove the specified data from database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        if ($request->ajax()) {
            try {
                $periode = FinalProject::findOrFail($id);
                $periode->delete();

                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil dihapus',
                ]);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan',
                ], 404);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Terjadi kesalahan saat menghapus data',
                ], 500);
            }
        }

        abort(403);
    }
}
