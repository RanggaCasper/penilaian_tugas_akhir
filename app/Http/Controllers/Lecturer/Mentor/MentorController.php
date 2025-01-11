<?php

namespace App\Http\Controllers\Lecturer\Mentor;

use App\Exports\ScoreTemplateExport;
use App\Models\Mentor\Score;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use App\Imports\ScoresImport;
use Yajra\DataTables\DataTables;
use App\Models\Proposal\Proposal;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MentorController extends Controller
{
    public function index()
    {
        return view('lecturer.mentor.mentor');
    }

    /**
     * Get the list of students and their scores.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            try {
                $data = Proposal::with('student','student.generation', 'score')
                        ->where(function ($query) {
                            $query->where('primary_mentor_id', Auth::id())
                                ->orWhere('secondary_mentor_id', Auth::id());
                        })
                        ->where('status', 'disetujui')
                        ->get();
                return DataTables::of($data)  
                    ->addColumn('no', function ($row) {  
                        static $counter = 0;  
                        return ++$counter;  
                    })
                    ->editColumn('position', function ($row) {  
                        if ($row->primary_mentor_id == Auth::id()) {
                            return 'Pembimbing 1';
                        } elseif ($row->secondary_mentor_id == Auth::id()) {
                            return 'Pembimbing 2';
                        } else {
                            return '-';
                        }
                    })
                    ->editColumn('score', function ($row) {  
                        if ($row->score) {
                            return $row->score->score;
                        } else {
                            return '-';
                        }
                    })
                    ->addColumn('action', function ($row) {  
                        return '  
                            <button type="button" class="btn btn-primary btn-sm edit-btn" data-id="' . $row->id . '" data-bs-toggle="modal" data-bs-target="#modal">Nilai</button>  
                        ';  
                    })  
                    ->rawColumns(['action'])
                    ->make(true);
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
     * Get the specified score by ID.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getScoreById(Request $request, $id): JsonResponse
    {
        if ($request->ajax()) {
            try {
                $data = Score::where('mentor_id', Auth::user()->id)->where('proposal_id', $id)->first();

                return response()->json($data);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan',
                ], 404);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => "Terjadi kesalahan saat mengambil data",
                ], 500);
            }
        }

        abort(403);
    }

    /**
     * Store a new score in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            if(!$request->proposal_id) {
                return response()->json([
                    'status' => false,
                    'message' => "Data tidak valid.",
                ], 404);
            }
            
            $request->validate([
                'score' => 'required|numeric|min:0|max:100',
            ]);

            $proposal = Proposal::where('id', $request->proposal_id)->where('primary_mentor_id', Auth::id())->orWhere('secondary_mentor_id', Auth::id())->where('status', 'disetujui')->first();

            if(!$proposal) {
                return response()->json([
                    'status' => false,
                    'message' => "Data tidak valid.",
                ], 404);
            }
       
            Score::updateOrCreate(
                [
                    'proposal_id' => $proposal->id,
                    'mentor_id' => Auth::id(),
                ],
                [
                    'score' => $request->score,
                ]
            );            

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
     * Import scores from Excel.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function import(Request $request, Excel $excel)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv|max:2048',
        ]);

        try {
            $import = new ScoresImport();
            $excel->import($import, $request->file('file'));

            $errors = $import->getErrors();

            if (!empty($errors)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Beberapa baris gagal diproses.',
                    'is_file' => true,
                    'errors' => $errors,
                ], 422);
            }

            return response()->json([
                'status' => true,
                'message' => 'Data nilai berhasil diimport.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download the score template as an Excel file.
     *
     * @param \Maatwebsite\Excel\Excel $excel
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportTemplate(Excel $excel)
    {
        return $excel->download(new ScoreTemplateExport, 'template-nilai.xlsx');
    }
}
