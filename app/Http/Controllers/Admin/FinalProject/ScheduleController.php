<?php

namespace App\Http\Controllers\Admin\FinalProject;

use App\Models\User;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use App\Exports\ScheduleExport;
use Yajra\DataTables\DataTables;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\FinalProject\Schedule;

class ScheduleController extends Controller
{
    /**
     * Display the final project schedule view.
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin.final_project.schedule');
    }

    /**
     * Retrieve a list of students based on a search query.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStudent(Request $request): JsonResponse
    {
        if($request->ajax()) {
            $search = $request->get('q');

            $data = User::where('role_id', 4)
                ->where('name', 'like', '%' . $search . '%')
                ->orderBy('name', 'asc')
                ->get(['id', 'name', 'nim']);

            return response()->json(
                $data->map(function ($data) {
                    return [
                        'id' => $data->id,
                        'name' => $data->name . ' - ' . $data->nim
                    ];
                })
            );                
        }

        abort(403);
    }

    /**
     * Retrieve a list of examiners based on a search query.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getExaminer(Request $request): JsonResponse
    {
        if($request->ajax()) {
            $search = $request->get('q');

            $data = User::where('role_id', 3)
                ->where('name', 'like', '%' . $search . '%')
                ->orderBy('name', 'asc')
                ->get(['id', 'name']);

            return response()->json($data);
        }

        abort(403);
    }

    /**
     * Store a newly created schedule in the database.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->merge([
                'status' => $request->has('status') ? 1 : 0,
            ]);

            $request->validate([
                'exam_date' => 'required|date',
                'start_time' => 'required',
                'end_time' => 'required',
                'room' => 'required',
                'status' => 'required',
                'student_id' => 'required|exists:users,id',
                'primary_examiner_id' => 'required|exists:users,id',
                'secondary_examiner_id' => 'required|exists:users,id',
                'tertiary_examiner_id' => 'required|exists:users,id',
            ]);
        
            Schedule::create($request->all());
        
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

    /**
     * Display a listing of the data.
     *
     * @param Request $request
     * @param Excel $excel
     * @param PDF $pdf
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function get(Request $request, Excel $excel, PDF $pdf)
    {
        if ($request->has('export')) {
            try {
                $query = Schedule::with('student', 'primary_examiner', 'secondary_examiner', 'tertiary_examiner');

                if ($request->has('exam_date')) {
                    $query->whereDate('exam_date', $request->input('exam_date'));
                }
    
                $data = $query->get();
                $date = $request->input('exam_date');
    
                if ($request->export === 'excel') {
                    return $excel->download(new ScheduleExport($data), "schedule_{$date}.xlsx");
                }

                if ($request->export === 'pdf') {
                    $title = "Jadwal Ujian - {$date}";

                    $pdf = $pdf->loadView('exports.schedule_pdf', [
                        'data' => $data,
                        'title' => $title,
                    ]);
        
                    $fileName = "schedule_{$date}.pdf";
                    return $pdf->download($fileName);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage(),
                ], 500);
            }
        }
            
        if ($request->ajax()) {
            try {
                $data = Schedule::with('student', 'primary_examiner', 'secondary_examiner', 'tertiary_examiner')->get();
                return DataTables::of($data)  
                    ->addColumn('no', function ($row) {  
                        static $counter = 0;  
                        return ++$counter;  
                    })
                    ->editColumn('status', function ($row) {  
                        return $row->status   
                        ? '<span class="badge bg-success">Active</span>'   
                        : '<span class="badge bg-danger">Locked</span>';  
                    })                    
                    ->addColumn('action', function ($row) {  
                        return '  
                            <button type="button" class="btn btn-primary btn-sm edit-btn" data-id="' . $row->id . '" data-bs-toggle="modal" data-bs-target="#modal">Lihat</button>  
                            <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="' . $row->id . '">Hapus</button>  
                        ';  
                    })  
                    ->rawColumns(['action', 'status'])
                    ->make(true);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage(),
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function getById(Request $request, $id): JsonResponse
    {
        if ($request->ajax()) {
            try {
                $data = Schedule::with('student', 'primary_examiner', 'secondary_examiner', 'tertiary_examiner')->findOrFail($id);

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
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        if ($request->ajax()) {
            try {
                $schedule = Schedule::findOrFail($id);

                $request->merge([
                    'status' => $request->has('status') ? 1 : 0,
                ]);

                $request->validate([
                    'exam_date' => 'required|date',
                    'start_time' => 'required',
                    'end_time' => 'required',
                    'room' => 'required',
                    'status' => 'required',
                    'student_id' => 'required|exists:users,id',
                    'primary_examiner_id' => 'required|exists:users,id',
                    'secondary_examiner_id' => 'required|exists:users,id',
                    'tertiary_examiner_id' => 'required|exists:users,id',
                ]);

                $schedule->update($request->all());

                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil diperbarui',
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

        abort(403);
    }

    /**
     * Remove the specified data from database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        if ($request->ajax()) {
            try {
                $periode = Schedule::findOrFail($id);
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
