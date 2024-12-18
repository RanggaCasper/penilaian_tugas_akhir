<?php

namespace App\Http\Controllers\Admin\Student;

use App\Models\User;
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
                $data = User::select(['id', 'name', 'email', 'phone', 'nim', 'generation'])->where('role_id', 4)->orderBy('nim', 'asc');
                return DataTables::of($data)  
                    ->addColumn('no', function ($row) {  
                        static $counter = 0;  
                        return ++$counter;  
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
            $sion = new SIONService();
            $response = $sion->getMahasiswa('20241', '40', '58302');
            $skipped = [];
            $inserted = [];

            foreach ($response as $mahasiswa) {
                $existingUser = User::where('nim', $mahasiswa['nim'])->first();

                if ($existingUser) {
                    $skipped[] = $mahasiswa['nim'];
                    continue;
                }

                User::create([
                    'name' => $mahasiswa['nama'],
                    'nim' => $mahasiswa['nim'],
                    'email' => $mahasiswa['email'],
                    'phone' => $mahasiswa['telepon'],
                    'generation' => '20' . substr($mahasiswa['nim'], 0, 2),
                    'role_id' => 4,
                ]);

                $inserted[] = $mahasiswa['nim'];
            }

            if (count($inserted) == 0) {
                return response()->json([
                    'status' => true,
                    'message' => 'Tidak ada data baru yang ditambahkan.',
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Data berhasil ditambahkan, berhasil menambahkan ' . count($inserted) . ' data.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat mengambil data.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
