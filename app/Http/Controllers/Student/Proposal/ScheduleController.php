<?php

namespace App\Http\Controllers\Student\Proposal;

use App\Models\Exam\Exam;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use App\Exports\ScheduleExport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    /**
     * Display the proposal registration view.
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $schedule = Exam::where('student_id', Auth::user()->id)->where('type', 'proposal')->first();
        return view('student.proposal.schedule', compact('schedule'));
    }

    /**
     * Download the exam schedule as an Excel file.
     *
     * @param Excel $excel
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download(Excel $excel)
    {
        try {
            $query = Exam::with('student', 'primary_examiner', 'secondary_examiner', 'tertiary_examiner')->where('student_id', Auth::user()->id)->where('type', 'proposal');

            $data = $query->get();

            if ($data->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Jadwal ujian belum tersedia.',
                ], 404);
            }

            return $excel->download(new ScheduleExport($data), "schedule.xlsx");
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
