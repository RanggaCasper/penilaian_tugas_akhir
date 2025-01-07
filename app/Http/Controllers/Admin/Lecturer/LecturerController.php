<?php

namespace App\Http\Controllers\Admin\Lecturer;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\SIONService;
use Yajra\DataTables\DataTables;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Faker\Factory as Faker;

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
     * Store a newly created resource in database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */    
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'phone' => 'required|unique:users',
                'identity' => 'required|unique:users',
                'secondary_identity' => 'required|unique:users',
            ]);
        
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'identity' => $request->identity,
                'secondary_identity' => $request->secondary_identity,
                'password' => bcrypt("dosen123"),
                'role_id' => 3
            ]);
        
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
                'message' => $e->getMessage(),
            ], 500);
        }        
    }

    public function getData(): JsonResponse  
    {  
        try {  
            set_time_limit(300);
            $sion = new SIONService();  
            $response = $sion->getDosen('20241', '40', '58302');  
            $skipped = [];  
            $inserted = [];  
            $updated = [];

            foreach ($response as $data) {  
                $existingUser = User::where('identity', $data['nidn'])->first();  

                if ($existingUser) {
                    if (is_null($existingUser->password)) {
                        $existingUser->update([
                            'password' => bcrypt($data['email']),
                        ]);
                        $updated[] = $data['nidn'];
                    } else {
                        $skipped[] = $data['nidn'];
                    }
                    continue;  
                }  

                $faker = Faker::create('id_ID');

                User::create([
                    'name' => $data['nama'],
                    'identity' => $data['nidn'],
                    'secondary_identity' => $data['nip'],
                    'email' => strtolower(substr(explode(' ', $data['nama'])[0] ?? '', 0, 4)) . strtolower(substr(explode(' ', $data['nama'])[1] ?? '', 0, 4)) . rand(10, 99) . '@pnb.ac.id',
                    'phone' => '08' . $faker->numerify('##########'),
                    'role_id' => 3,
                    'password' => bcrypt($data['nip']),
                ]);                

                $inserted[] = $data['nidn'];  
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
                $data = User::select(['id', 'name', 'email', 'phone', 'identity','secondary_identity'])->where('role_id', 3)->orderBy('identity', 'asc');
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

    /**
     * Get the specified Lecturer by ID.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getById(Request $request, $id)
    {
        if ($request->ajax()) {
            try {
                $data = User::findOrFail($id);

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
                $request->validate([
                    'name' => 'required',
                    'email' => 'required|email|unique:users,email,' . $id,
                    'phone' => 'required|unique:users,phone,' . $id,
                    'identity' => 'required|unique:users,identity,' . $id,
                    'secondary_identity' => 'required|unique:users,secondary_identity,' . $id,
                ]);
            
                $data = User::findOrFail($id);
            
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
                $periode = User::findOrFail($id);
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
                    'message' => 'Terjadi kesalahan saat menghapus data',
                ], 500);
            }
        }

        abort(403);
    }
}
