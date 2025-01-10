<?php

namespace App\Http\Controllers\Super;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $program_study = User::whereHas('program_study')
                            ->selectRaw('program_study_id, COUNT(*) as total')
                            ->groupBy('program_study_id')
                            ->with('program_study')
                            ->get();

        $data_prodi = $program_study->map(function ($item) {
            return [
                'program_study' => $item->program_study->name,
                'total' => $item->total,
            ];
        });

        $generation = User::whereHas('generation')
                        ->selectRaw('generation_id, COUNT(*) as total')
                        ->groupBy('generation_id')
                        ->with('generation')
                        ->get()
                        ->sortByDesc(function ($item) {
                            return $item->generation->name;
                        });

        $data_generation = $generation->map(function ($item) {
            return [
                'generation' => $item->generation->name,
                'total' => $item->total,
            ];
        });

        return view('super.dashboard', compact('data_prodi', 'data_generation'));
    }
}
