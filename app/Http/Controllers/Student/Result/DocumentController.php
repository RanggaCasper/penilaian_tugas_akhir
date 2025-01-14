<?php

namespace App\Http\Controllers\Student\Result;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function index()
    {
        return view('student.result.document');
    }
}
