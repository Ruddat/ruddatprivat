<?php

namespace App\Http\Controllers\Frontend\CompleteIntake;

use App\Http\Controllers\Controller;

class CompleteIntakeController extends Controller
{
    public function index()
    {
        return view('frontend.completeintake.index');
    }
}
