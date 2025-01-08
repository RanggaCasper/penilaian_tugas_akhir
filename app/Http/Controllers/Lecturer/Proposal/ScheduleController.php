<?php

namespace App\Http\Controllers\Lecturer\Proposal;

use Carbon\Carbon;
use Barryvdh\DomPDF\PDF;
use App\Models\Exam\Exam;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use App\Exports\ScheduleExport;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    /**
     * Display the proposal schedule view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('lecturer.proposal.schedule');
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
                $query = Exam::with('student', 'primary_examiner', 'secondary_examiner', 'tertiary_examiner')->where(function ($query) {
                    $query->where('primary_examiner_id', Auth::id())
                        ->orWhere('secondary_examiner_id', Auth::id())
                        ->orWhere('tertiary_examiner_id', Auth::id());
                })->where('type', 'proposal')->orderBy('start_time', 'asc');

                if ($request->has('exam_date')) {
                    $query->whereDate('exam_date', $request->input('exam_date'));
                }
                
                $data = $query->get();
                $date = $request->input('exam_date');
                $data = $data->map(function ($item) {
                    $item->type = 'PROPOSAL';
                    return $item;
                });
    
                if ($request->export === 'excel') {
                    return $excel->download(new ScheduleExport($data), "schedule_{$date}.xlsx");
                }

                if ($request->export === 'pdf') {
                    $date = Carbon::parse($date)->locale('id')->isoFormat('DD MMMM YYYY');

                    $title = "JADWAL UJIAN PROPOSAL - {$date}";

                    $pdf = $pdf->loadView('exports.proposal.schedule_pdf', [
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
                $data = Exam::with(['student', 'primary_examiner', 'secondary_examiner', 'tertiary_examiner'])
                            ->where(function ($query) {
                                $query->where('primary_examiner_id', Auth::id())
                                    ->orWhere('secondary_examiner_id', Auth::id())
                                    ->orWhere('tertiary_examiner_id', Auth::id());
                            })
                            ->where('type', 'proposal')
                            ->orderBy('exam_date', 'desc')
                            ->orderBy('start_time', 'asc')
                            ->get();
                return DataTables::of($data)  
                    ->addColumn('no', function ($row) {  
                        static $counter = 0;  
                        return ++$counter;  
                    })
                    ->editColumn('status', function ($row) {  
                        return $row->is_editable   
                        ? '<span class="badge bg-success">Aktif</span>'   
                        : '<span class="badge bg-danger">Dikunci</span>';  
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