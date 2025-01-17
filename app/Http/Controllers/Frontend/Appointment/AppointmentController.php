<?php

namespace App\Http\Controllers\Frontend\Appointment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index()
    {
        return view('frontend.appointment.index');
    }
}
