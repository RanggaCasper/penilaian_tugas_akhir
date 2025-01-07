<?php

namespace App\Http\Controllers\Admin\Rubric;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\Rubric\Criteria;
use Illuminate\Routing\Controller;

class CriteriaController extends Controller
{
    /**
     * Show the list of rubric criteria.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.rubric.criteria');
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
                'has_sub' => $request->has('has_sub') ? 1 : 0,
            ]);
            
            $request->validate([
                'name' => 'required',
                'weight' => 'required|numeric|min:0|max:100',
                'rubric_id' => 'required'
            ]);
            
            $Rubric = Criteria::where('rubric_id', $request->rubric_id)->sum('weight');  

            if ($Rubric + $request->weight > 100) {  
                return response()->json([  
                    'status' => false,  
                    'message' => 'Total bobot kriteria tidak boleh lebih dari 100%',  
                ], 422);  
            }  

            Criteria::create($request->all());
        
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
                $data = Criteria::with('Rubric')->select(['id', 'name', 'weight', 'rubric_id', 'has_sub']);
                return DataTables::of($data)  
                    ->addColumn('no', function ($row) {  
                        static $counter = 0;  
                        return ++$counter;  
                    })
                    ->editColumn('has_sub', function ($row) {  
                        return $row->has_sub   
                            ? '<span class="badge bg-success">Aktif</span>'   
                            : '<span class="badge bg-danger">Tidak Aktif</span>';  
                    })  
                    ->addColumn('action', function ($row) {  
                        return '  
                            <button type="button" class="btn btn-primary btn-sm edit-btn" data-id="' . $row->id . '" data-bs-toggle="modal" data-bs-target="#modal">Edit</button>  
                            <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="' . $row->id . '">Hapus</button>  
                        ';  
                    })  
                    ->rawColumns(['has_sub','action'])
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
                $data = Criteria::with('rubric')->findOrFail($id);

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
                $request->merge([
                    'has_sub' => $request->has('has_sub') ? 1 : 0,
                ]);
                
                $request->validate([
                    'name' => 'required',
                    'weight' => 'required|numeric|min:0|max:100',
                    'rubric_id' => 'required'
                ]);
            
                $data = Criteria::findOrFail($id);  

                $Rubric = Criteria::where('rubric_id', $request->rubric_id)->where('id', '!=', $id)->sum('weight');  

                if ($Rubric + $request->weight > 100) {  
                    return response()->json([  
                        'status' => false,  
                        'message' => 'Total bobot kriteria tidak boleh lebih dari 100%',  
                    ], 422);  
                }  
            
                $data->update($request->only(['name', 'weight', 'rubric_id', 'has_sub']));
            
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
                $periode = Criteria::findOrFail($id);
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
