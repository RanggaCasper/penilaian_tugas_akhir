<?php

namespace App\Http\Controllers\Lecturer\FinalProject;

use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use App\Exports\ScheduleExport;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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
        return view('lecturer.final_project.schedule');
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
                $query = Schedule::with('student', 'primary_examiner', 'secondary_examiner', 'tertiary_examiner')->where(function ($query) {
                    $query->where('primary_examiner_id', Auth::id())
                        ->orWhere('secondary_examiner_id', Auth::id())
                        ->orWhere('tertiary_examiner_id', Auth::id());
                })->orderBy('start_time', 'asc');

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
                $data = Schedule::with(['student', 'primary_examiner', 'secondary_examiner', 'tertiary_examiner'])
                            ->where(function ($query) {
                                $query->where('primary_examiner_id', Auth::id())
                                    ->orWhere('secondary_examiner_id', Auth::id())
                                    ->orWhere('tertiary_examiner_id', Auth::id());
                            })
                            ->orderBy('exam_date', 'desc')
                            ->orderBy('start_time', 'asc')
                            ->get();
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
                    ->addColumn('position', function ($row) {  
                        if ($row->primary_examiner_id == Auth::id()) {
                            return 'Penguji 1';
                        } elseif ($row->secondary_examiner_id == Auth::id()) {
                            return 'Penguji 2';
                        } elseif ($row->tertiary_examiner_id == Auth::id()) {
                            return 'Penguji 3';
                        } else {
                            return '-';
                        }
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
}
