<?php

namespace App\Http\Controllers\Admin\Period;

use App\Models\Period;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PeriodController extends Controller
{
    /**
     * Display the periode proposal view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin.period.period');
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
                'is_active' => $request->has('is_active') ? 1 : 0,
                'program_study_id' => Auth::user()->program_study_id
            ]);

            $request->validate([
                'name' => 'required',
                'start_date' => 'required|date|before_or_equal:end_date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'generation_id' => 'required|exists:generations,id',  
                'type' => 'required|in:proposal,thesis',
            ]);
        
            Period::create($request->all());
        
            return response()->json([
                'status' => true,
                'message' => 'Data berhasil ditambahkan',
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
                $data = Period::with('generation')->select(['id', 'name', 'start_date', 'end_date', 'generation_id', 'type', 'is_active']);
                return DataTables::of($data)  
                    ->addColumn('no', function ($row) {  
                        static $counter = 0;  
                        return ++$counter;  
                    }) 
                    ->addColumn('generation', function ($row) {  
                        return $row->generation->name;
                    })  
                    ->addColumn('type', function ($row) {  
                        return $row->type == 'proposal' ? 'Proposal' : 'Tugas Akhir';                
                    })  
                    ->editColumn('is_active', function ($row) {  
                        return $row->is_active   
                            ? '<span class="badge bg-success">Active</span>'   
                            : '<span class="badge bg-danger">Inactive</span>';  
                    })  
                    ->addColumn('action', function ($row) {  
                        return '  
                            <button type="button" class="btn btn-primary btn-sm edit-btn" data-id="' . $row->id . '" data-bs-toggle="modal" data-bs-target="#modal">Edit</button>  
                            <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="' . $row->id . '">Hapus</button>  
                        ';  
                    })  
                    ->rawColumns(['action', 'is_active'])
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
     * @return \Illuminate\Http\Response
     */
    public function getById(Request $request, $id)
    {
        if ($request->ajax()) {
            try {
                $data = Period::findOrFail($id);

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
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if ($request->ajax()) {
            try {
                $request->merge([
                    'is_active' => $request->has('is_active') ? 1 : 0,
                ]);
                
                $request->validate([
                    'name' => 'required',
                    'start_date' => 'required|date|before_or_equal:end_date',
                    'end_date' => 'required|date|after_or_equal:start_date',
                    'generation_id' => 'required|exists:generations,id',
                    'type' => 'required|in:proposal,thesis',
                ]);
            
                $data = Period::findOrFail($id);
            
                $data->update($request->all());
            
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
    public function destroy(Request $request, $id)
    {
        if ($request->ajax()) {
            try {
                $periode = Period::with('proposals')->findOrFail($id);
            
                if ($periode->proposals()->count() > 0 || $periode->thesis()->count() > 0) {  
                    return response()->json([  
                        'status' => false,  
                        'message' => 'Tidak dapat menghapus periode ini karena ada entri yang terkait.',  
                    ], 409);
                }  

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
                    'message' => $e->getMessage(),
                ], 500);
            }
        }

        abort(403);
    }
}
