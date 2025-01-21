<?php

namespace App\Http\Controllers\Super\ProgramStudy;

use App\Models\Role;
use App\Models\User;
use App\Models\Generation;
use Illuminate\Http\Request;
use App\Services\SIONService;
use Yajra\DataTables\DataTables;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\ProgramStudy;

class ProgramStudyController extends Controller
{
    /**
     * Display the program study list view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('super.program_study.program_study');
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
                $data = ProgramStudy::with(['users' => function($query) {  
                    $query->whereHas('role', function($q) {  
                        $q->where('name', 'Student');  
                    });  
                }])->get();
                return DataTables::of($data)  
                    ->addColumn('no', function ($row) {  
                        static $counter = 0;  
                        return ++$counter;  
                    })
                    ->addColumn('users', function ($row) {  
                        return $row->users->count();  
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

    /**
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getData(Request $request): JsonResponse  
    {  
        try {
            $sion = new SIONService();

            $response = $sion->getProdi('40');
            foreach ($response as $prodi) {
                ProgramStudy::firstOrCreate(
                    ['id' => (int) $prodi['kodeProdi']],
                    ['name' => $prodi['namaProdi']]
                );
            }

            return response()->json([  
                'status' => true,  
                'message' => 'Data berhasil ditambahkan'
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
                'message' => $e->getMessage(),
            ], 500);  
        }  
    }
}
