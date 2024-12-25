<?php

namespace App\Http\Controllers\Admin\Evaluation;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Routing\Controller;
use App\Models\Evaluation\Criteria;
use App\Models\Evaluation\SubCriteria;

class SubCriteriaController extends Controller
{
    /**
     * Show the list of evaluation criteria.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.evaluation.sub_criteria');
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
            $request->validate([  
                'name' => 'required',
                'score' => 'required|numeric',
                'evaluation_criteria_id' => 'required'
            ]);   
            
            $subCriteria = SubCriteria::where('evaluation_criteria_id', $request->evaluation_criteria_id)->sum('score');  
        
            $criteria = Criteria::findOrFail($request->evaluation_criteria_id); 
            $maxScore = $criteria->score; 
            
            $remainingScore = $maxScore - $subCriteria;  
            
            if ($request->score > $remainingScore) {  
                return response()->json([  
                    'status' => false,  
                    'message' => 'Total bobot sub penilaian tidak boleh lebih dari ' . $remainingScore . '.',  
                ], 422);  
            }  
           
            SubCriteria::create($request->all());
        
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
                $data = SubCriteria::with('evaluation_criteria')->select(['id', 'name', 'score', 'evaluation_criteria_id']);
                return DataTables::of($data)  
                    ->addColumn('no', function ($row) {  
                        static $counter = 0;  
                        return ++$counter;  
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
     * Retrieve the specified Evaluation Criteria by ID.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getById(Request $request, $id)
    {
        if ($request->ajax()) {
            try {
                $data = SubCriteria::with('evaluation_criteria')->findOrFail($id);

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
     * Update the specified Evaluation Criteria in database.
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
                    'score' => 'required|numeric|min:0',
                    'evaluation_criteria_id' => 'required'
                ]);

                $data = SubCriteria::findOrFail($id);

                $subCriteria = SubCriteria::where('evaluation_criteria_id', $request->evaluation_criteria_id)->where('id', '!=', $id)->sum('score');

                $criteria = Criteria::findOrFail($request->evaluation_criteria_id);
                $maxScore = $criteria->score;

                $remainingScore = $maxScore - $subCriteria;

                if ($request->score > $remainingScore) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Total bobot sub penilaian tidak boleh lebih dari ' . $remainingScore . '.',
                    ], 422);
                }

                $data->update($request->only(['name', 'score', 'evaluation_criteria_id']));

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
                $periode = SubCriteria::findOrFail($id);
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
