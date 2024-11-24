<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\PeriodeProposal;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;

class PeriodeProposalController extends Controller
{
    /**
     * Display the periode proposal view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin.periode_proposal');
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
            ]);

            $request->validate([
                'name' => 'required',
                'start_date' => 'required|date|before_or_equal:end_date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);
        
            PeriodeProposal::create($request->all());
        
            return response()->json([
                'status' => true,
                'message' => 'Periode Ujian Proposal berhasil ditambahkan',
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
                $data = PeriodeProposal::select(['id', 'name', 'start_date', 'end_date', 'is_active']);
                return DataTables::of($data)
                    ->addColumn('no', function ($row) {
                        static $counter = 0;
                        return ++$counter;
                    })
                    ->editColumn('is_active', function ($row) {
                        return $row->is_active ? 'Active' : 'Inactive';
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
                $data = PeriodeProposal::findOrFail($id);

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
                ]);
            
                $data = PeriodeProposal::findOrFail($id);
            
                $data->update($request->all());
            
                return response()->json([
                    'status' => true,
                    'message' => 'Periode Ujian Proposal berhasil diubah',
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
                $periode = PeriodeProposal::findOrFail($id);
                $periode->delete();

                return response()->json([
                    'status' => true,
                    'message' => 'Periode Ujian Proposal berhasil dihapus',
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
