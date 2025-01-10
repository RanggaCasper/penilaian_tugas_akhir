<?php

namespace App\Http\Controllers\Admin\Lecturer;

use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Http\Request;
use App\Services\SIONService;
use Yajra\DataTables\DataTables;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LecturerController extends Controller
{
    /**
     * Display the lecturer list view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin.lecturer.lecturer');
    }
    
    /**
     * Retrieve and return a list of lecturers in DataTable format.
     *
     * @param \Illuminate\Http\Request $request The HTTP request instance.
     * 
     * @return \Illuminate\Http\JsonResponse The JSON response for DataTables containing
     */
    public function get(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            try {
                $data = User::select(['id', 'name', 'email', 'phone', 'identity', 'secondary_identity'])
                        ->where('role_id', 3)
                        ->where('program_study_id', Auth::user()->program_study_id)
                        ->orderBy('identity', 'asc');

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
                    'message' => 'Terjadi kesalahan saat mengambil data.',
                ], 500);
            }
        }

        abort(403);
    }
}
