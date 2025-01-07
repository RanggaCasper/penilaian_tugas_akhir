<?php

namespace App\Http\Controllers\Admin\Proposal;

use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\Proposal\Proposal;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class RegisterController extends Controller
{
    public function index()
    {
        return view('admin.proposal.register');
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
                $data = Proposal::with('student', 'primary_mentor', 'secondary_mentor', 'student.generation')->get();
                return DataTables::of($data)  
                    ->addColumn('no', function ($row) {  
                        static $counter = 0;  
                        return ++$counter;  
                    })
                    ->addColumn('secondary_mentor', function ($row) {  
                        return $row->secondary_mentor ? $row->secondary_mentor->name : '-';  
                    })
                    ->editColumn('status', function ($row) {  
                        $badgeClass = match ($row->status) {
                            'menunggu' => 'bg-warning',
                            'disetujui' => 'bg-success',
                            'ditolak' => 'bg-danger',
                            default => 'bg-secondary',
                        };
                    
                        return '<span class="badge ' . $badgeClass . '">' . ucfirst($row->status ?? 'Unknown') . '</span>';
                    })  
                    ->editColumn('is_editable', function ($row) {  
                        return $row->is_editable   
                            ? '<span class="badge bg-success">Aktif</span>'   
                            : '<span class="badge bg-danger">Dikunci</span>';  
                    })               
                    ->addColumn('action', function ($row) {  
                        return '  
                            <button type="button" class="btn btn-primary btn-sm edit-btn" data-id="' . $row->id . '" data-bs-toggle="modal" data-bs-target="#modal">Lihat</button>  
                            <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="' . $row->id . '">Hapus</button>  
                        ';  
                    })  
                    ->rawColumns(['action', 'is_editable', 'status'])
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
                $data = Proposal::with('student', 'primary_mentor', 'secondary_mentor','student.generation')->findOrFail($id);

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
     * Retrieve a list of mentors based on a search query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMentor(Request $request): JsonResponse
    {
        if($request->ajax()) {
            $search = $request->get('q');

            $data = User::where('role_id', 3)
                ->where('name', 'like', '%' . $search . '%')
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
                    'primary_mentor_id' => 'required|exists:users,id',
                    'secondary_mentor_id' => 'nullable|exists:users,id',
                    'status' => 'required|in:menunggu,disetujui,ditolak',
                    'is_editable' => 'required|boolean',
                ]);

                $mentor = [
                    $request->primary_mentor_id,
                    $request->secondary_mentor_id
                ];
                
                if (count($mentor) !== count(array_unique($mentor))) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Pembimbing 1 dan Pembimbing 2 tidak boleh sama.',
                    ], 422);
                }
                
                $data = Proposal::findOrFail($id);
                
                $data->update([
                    'primary_mentor_id' => $request->primary_mentor_id,
                    'secondary_mentor_id' => $request->secondary_mentor_id,
                    'status' => $request->status,
                    'is_editable' => $request->is_editable
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
                $periode = Proposal::findOrFail($id);
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
