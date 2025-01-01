<?php

namespace App\Http\Controllers\Student\FinalProject;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use App\Exports\ScheduleExport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\FinalProject\Schedule;
use App\Models\FinalProject\FinalProject;

class ScheduleController extends Controller
{
    /**
     * Display the final project registration view.
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $schedule = Schedule::where('student_id', Auth::user()->id)->first();
        return view('student.final_project.schedule', compact('schedule'));
    }

    public function download(Excel $excel)
    {
        try {
            $query = Schedule::with('student', 'primary_examiner', 'secondary_examiner', 'tertiary_examiner')->where('student_id', Auth::user()->id);

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
