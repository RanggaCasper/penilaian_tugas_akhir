<?php

namespace App\Http\Controllers\Api\Proposal;

use App\Http\Controllers\Controller;
use App\Models\Proposal\Proposal;
use Illuminate\Http\Request;

class ProposalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $proposals = Proposal::with('student')->get();

        $data = $proposals->map(function($proposal) {
            return [
                'id' => $proposal->id,
                'nim' => $proposal->student->identity,
                'status' => $proposal->status,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }
}
