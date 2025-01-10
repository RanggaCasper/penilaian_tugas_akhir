<?php

namespace App\Http\Controllers\Admin\Student;

use App\Models\User;
use App\Models\Generation;
use Illuminate\Http\Request;
use App\Services\SIONService;
use Yajra\DataTables\DataTables;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    /**
     * Display the student list view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin.student.student');
    }

    /**
     * Display a listing of the data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function get(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            try {
                $data = User::with('generation')->select(['id', 'name', 'email', 'phone', 'identity', 'generation_id'])
                        ->where('role_id', 4)
                        ->where('program_study_id', Auth::user()->program_study_id)
                        ->orderBy('identity', 'asc');
                return DataTables::of($data)  
                    ->addColumn('no', function ($row) {  
                        static $counter = 0;  
                        return ++$counter;  
                    })
                    ->addColumn('generation', function ($row) {  
                        return $row->generation->name;  
                    })
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
