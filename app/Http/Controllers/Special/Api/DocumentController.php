<?php

namespace App\Http\Controllers\Special\Api;

use App\Models\ApiKey;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller
{
    public function index()
    {
        $data = ApiKey::where('user_id', Auth::user()->id)->first();
        return view('special.api.document', compact('data'));
    }
}
