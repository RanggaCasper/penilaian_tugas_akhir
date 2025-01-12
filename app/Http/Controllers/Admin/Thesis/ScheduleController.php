<?php

namespace App\Http\Controllers\Admin\Thesis;

use Carbon\Carbon;
use App\Models\User;
use Barryvdh\DomPDF\PDF;
use App\Models\Exam\Exam;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use App\Models\Rubric\Rubric;
use App\Exports\ScheduleExport;
use Yajra\DataTables\DataTables;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    /**
     * Display the thesis schedule view.
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin.thesis.schedule');
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

            $data = User::where('name', 'like', '%' . $search . '%')
            ->whereHas('role', function ($query) {
                $query->where('name', 'Student');
            })
            ->whereHas('thesis', function ($query) {
                $query->where('status', 'disetujui');
            })
            ->whereDoesntHave('exam', function ($query) {
                $query->where('type', 'thesis');
            })
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'identity']);            

            return response()->json(
                $data->map(function ($data) {
                    return [
                        'id' => $data->id,
                        'name' => $data->name . ' - ' . $data->identity
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

            $data = User::where('name', 'like', '%' . $search . '%')
                        ->where('program_study_id', Auth::user()->program_study_id)
                        ->whereHas('role', function ($query) {
                            $query->where('name', 'Lecturer');
                        })
                        ->orderBy('name', 'asc')
                        ->get(['id', 'name']);


            return response()->json($data);
        }

        abort(403);
    }

    public function getRubric(Request $request): JsonResponse
    {
        if($request->ajax()) {
            $search = $request->get('q');

            $data = Rubric::where('name', 'like', '%' . $search . '%')
                ->where('type', 'thesis')
                ->where('program_study_id', Auth::user()->program_study_id)
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
                'is_editable' => $request->has('is_editable') ? 1 : 0,
                'type' => 'thesis',
            ]);

            $request->validate([
                'exam_date' => 'required|date',
                'start_time' => 'required|date_format:H:i',
                'room' => 'required',
                'is_editable' => 'required',
                'student_id' => [
                    'required',
                    'exists:users,id',
                    function ($attribute, $value, $fail) use ($request) {
                        $exists = Exam::where('student_id', $value)
                            ->where('type', 'thesis')
                            ->exists();
            
                        if ($exists) {
                            $fail(__('validation.unique'));
                        }
                    },
                ],
                'primary_examiner_id' => 'required|exists:users,id',
                'secondary_examiner_id' => 'required|exists:users,id',
                'tertiary_examiner_id' => 'required|exists:users,id',
                'rubric_id' => 'required|exists:rubrics,id',
            ]);         
            
            $examiner = [
                $request->primary_examiner_id,
                $request->secondary_examiner_id,
                $request->tertiary_examiner_id,
            ];
            
            if (count($examiner) !== count(array_unique($examiner))) {
                return response()->json([
                    'status' => false,
                    'message' => 'Penguji 1, Penguji 2, dan Penguji 3 tidak boleh sama.',
                ], 422);
            }

            $request->merge([
                'end_time' => Carbon::createFromFormat('H:i', $request->start_time)->addHour()->format('H:i'),
                'type' => 'thesis',
            ]);
            
            Exam::create($request->all());
        
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
                $query = Exam::with(
                    'student',
                    'student.thesis',
                    'student.program_study',
                    'primary_examiner',
                    'secondary_examiner',
                    'tertiary_examiner'
                )->where('type', 'thesis')
                ->whereHas('student.program_study', function($query) {
                    $query->where('id', Auth::user()->program_study->id);
                });

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

                    $pdf = $pdf->loadView('exports.thesis.schedule_pdf', [
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
                $data = Exam::with('student', 'student.thesis', 'primary_examiner', 'secondary_examiner', 'tertiary_examiner')
                        ->whereHas('student.program_study', function($query) {
                            $query->where('id', Auth::user()->program_study->id);
                        })->where('type','thesis')->get();
                return DataTables::of($data)  
                    ->addColumn('no', function ($row) {  
                        static $counter = 0;  
                        return ++$counter;  
                    })
                    ->editColumn('is_editable', function ($row) {  
                        return $row->is_editable   
                        ? '<span class="badge bg-success">Aktif</span>'   
                        : '<span class="badge bg-danger">Dikunci</span>';  
                    })         
                    ->addColumn('type', function ($row) {
                        return $row->type == 'proposal' ? 'Proposal' : 'Tugas Akhir';
                    })   
                    ->addColumn('action', function ($row) {  
                        return '  
                            <button type="button" class="btn btn-primary btn-sm edit-btn" data-id="' . $row->id . '" data-bs-toggle="modal" data-bs-target="#modal">Edit</button>  
                            <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="' . $row->id . '">Hapus</button>  
                        ';  
                    })  
                    ->rawColumns(['action', 'is_editable'])
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
                $data = Exam::with('student', 'primary_examiner', 'secondary_examiner', 'tertiary_examiner')->findOrFail($id);

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
                $schedule = Exam::findOrFail($id);

                $request->merge([
                    'is_editable' => $request->has('is_editable') ? 1 : 0,
                ]);

                $request->validate([
                    'exam_date' => 'required|date',
                    'start_time' => 'required',
                    'end_time' => 'required',
                    'room' => 'required',
                    'is_editable' => 'required',
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
                $periode = Exam::findOrFail($id);
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
