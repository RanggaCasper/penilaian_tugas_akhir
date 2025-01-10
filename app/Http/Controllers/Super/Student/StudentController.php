<?php

namespace App\Http\Controllers\Super\Student;

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
        return view('super.student.student');
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
                $data = User::with('program_study','generation')->select(['id', 'name', 'email', 'phone', 'identity', 'program_study_id', 'generation_id'])->where('role_id', 4)->orderBy('identity', 'asc');
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
    public function getData(Request $request): JsonResponse  
    {  
        try {
            $request->validate([
                'program_study_id' => 'required|exists:program_studies,id',
            ]);

            set_time_limit(300);
            $sion = new SIONService();  
            $response = $sion->getMahasiswa('20241', '40', $request->program_study_id);  

            $skipped = [];  
            $inserted = [];  
            $updated = [];  

            foreach ($response as $mahasiswa) {  
                $generationYear = '20' . substr($mahasiswa['nim'], 0, 2);  
                $generation = Generation::firstOrCreate(['name' => $generationYear]);  
                $user = User::updateOrCreate(
                    [ 'identity' => $mahasiswa['nim'] ],
                    [
                        'name' => $mahasiswa['nama'],
                        'email' => $mahasiswa['email'],
                        'phone' => $mahasiswa['telepon'],
                        'generation_id' => $generation->id,
                        'program_study_id' => $request->program_study_id,
                        'role_id' => 4,
                        'password' => bcrypt($mahasiswa['email']),
                    ]
                );

                if ($user->wasRecentlyCreated) {
                    $inserted[] = $mahasiswa['nim'];
                } elseif (is_null($user->password)) {
                    $user->update([
                        'password' => bcrypt($mahasiswa['email']),
                    ]);
                    $updated[] = $mahasiswa['nim'];
                } else {
                    $skipped[] = $mahasiswa['nim'];
                }
            }  

            return response()->json([  
                'status' => true,  
                'message' => (count($inserted) > 0 || count($updated) > 0) ? 
                                'Data berhasil diproses. ' . 
                                (count($inserted) > 0 ? 'Berhasil menambahkan ' . count($inserted) . ' data baru. ' : '') . 
                                (count($updated) > 0 ? 'Berhasil memperbarui ' . count($updated) . ' data. ' : '') : 
                                'Tidak ada data baru yang ditambahkan atau diperbarui.',
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
