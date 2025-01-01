<?php

namespace App\Http\Controllers\Admin\Student;

use App\Models\User;
use App\Models\Generation;
use Illuminate\Http\Request;
use App\Services\SIONService;
use Yajra\DataTables\DataTables;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

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
                $data = User::with('generation')->select(['id', 'name', 'email', 'phone', 'identity', 'generation_id'])->where('role_id', 4)->orderBy('identity', 'asc');
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

    /**
     * Mengambil data mahasiswa dari SION dan menyimpannya ke database,
     * jika data sudah ada maka akan dilewati.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getData(): JsonResponse  
    {  
        try {  
            set_time_limit(300);
            $sion = new SIONService();  
            $response = $sion->getMahasiswa('20241', '40', '58302');  
            $skipped = [];  
            $inserted = [];  
            $updated = [];

            foreach ($response as $mahasiswa) {  
                $existingUser = User::where('identity', $mahasiswa['nim'])->first();  

                if ($existingUser) {
                    if (is_null($existingUser->password)) {
                        $existingUser->update([
                            'password' => bcrypt($mahasiswa['email']),
                        ]);
                        $updated[] = $mahasiswa['nim'];
                    } else {
                        $skipped[] = $mahasiswa['nim'];
                    }
                    continue;  
                }  

                $generationYear = '20' . substr($mahasiswa['nim'], 0, 2);  
                $generation = Generation::firstOrCreate(  
                    ['name' => $generationYear]  
                );  

                User::create([  
                    'name' => $mahasiswa['nama'],  
                    'identity' => $mahasiswa['nim'],  
                    'email' => $mahasiswa['email'],  
                    'phone' => $mahasiswa['telepon'],  
                    'generation_id' => $generation->id,  
                    'role_id' => 4,  
                    'password' => bcrypt($mahasiswa['email']),  
                ]);  

                $inserted[] = $mahasiswa['nim'];  
            }  

            return response()->json([  
                'status' => true,  
                'message' => count($inserted) > 0 ? 'Data berhasil ditambahkan, berhasil menambahkan '.count($inserted).' data.' : 'Tidak ada data baru yang ditambahkan.',  
            ]);  
        } catch (\Exception $e) {  
            return response()->json([  
                'status' => false,  
                'message' => 'Terjadi kesalahan saat mengambil data.',
            ], 500);  
        }  
    }

}
