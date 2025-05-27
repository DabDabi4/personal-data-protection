<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TheoryController extends Controller
{
    public function index()
    {
        return view('theory.index');
    }
}
