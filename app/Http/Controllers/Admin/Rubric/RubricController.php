<?php

namespace App\Http\Controllers\Admin\Rubric;

use Illuminate\Http\Request;
use App\Models\Rubric\Rubric;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RubricController extends Controller
{
    /**
     * Show the list of Rubric.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.rubric.rubric');
    }

    /**
     * Store a new data in database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $request->merge([
                'program_study_id' => Auth::user()->program_study_id
            ]);
            
            $request->validate([
                'name' => 'required',
            ]);
            
            Rubric::create($request->all());
        
            return response()->json([
                'status' => true,
                'message' => 'Kriteria Penilaian berhasil ditambahkan',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'errors' => $e->getMessage(),
                'message' => 'Terjadi kesalahan saat menyimpan data',
            ], 500);
        }        
    }

    /**
     * Display a listing of the data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function get(Request $request)
    {
        if ($request->ajax()) {
            try {
                $data = Rubric::select(['id', 'name', 'type'])->where('program_study_id', Auth::user()->program_study_id);
                return DataTables::of($data)  
                    ->addColumn('no', function ($row) {  
                        static $counter = 0;  
                        return ++$counter;  
                    })  
                    ->addColumn('type', function ($row) {  
                        if ($row->type == 'proposal') {
                            return 'Proposal';
                        } else if ($row->type == 'thesis') {
                            return 'Tugas Akhir';
                        } else if ($row->type == 'guidance') {
                            return 'Bimbingan';
                        }
                    })  
                    ->addColumn('action', function ($row) {  
                        return '  
                            <button type="button" class="btn btn-primary btn-sm edit-btn" data-id="' . $row->id . '" data-bs-toggle="modal" data-bs-target="#modal">Edit</button>  
                            <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="' . $row->id . '">Hapus</button>  
                        ';  
                    })  
                    ->rawColumns(['action'])
                    ->make(true);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Terjadi kesalahan saat mengambil data',
                ], 500);
            }
        }

        abort(401);
    }

    /**
     * Retrieve the specified Rubric Criteria by ID.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getById(Request $request, $id)
    {
        if ($request->ajax()) {
            try {
                $data = Rubric::findOrFail($id);

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

        abort(401);
    }

    /**
     * Update the specified Rubric Criteria in database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if ($request->ajax()) {
            try {
                $request->validate([
                    'name' => 'required',
                ]);
            
                $data = Rubric::findOrFail($id);
            
                $data->update($request->all());
            
                return response()->json([
                    'status' => true,
                    'message' => 'Kriteria Penilaian berhasil diubah',
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

        abort(401);
    }

    /**
     * Remove the specified data from database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        if ($request->ajax()) {
            try {
                $periode = Rubric::findOrFail($id);
                $periode->delete();

                return response()->json([
                    'status' => true,
                    'message' => 'Kriteria Penilaian berhasil dihapus',
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

        abort(401);
    }
}
