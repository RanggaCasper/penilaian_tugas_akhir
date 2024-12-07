<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EvaluationCretariaController extends Controller
{
    public function index()
    {
        return view('admin.evaluation.cretaria');
    }
}
