<?php

namespace App\Http\Controllers\Frontend\CompleteIntake;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CompleteIntakeController extends Controller
{
    public function index()
    {
        return view('frontend.completeintake.index');
    }
}
