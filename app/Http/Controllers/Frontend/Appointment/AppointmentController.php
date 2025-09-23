<?php

namespace App\Http\Controllers\Frontend\Appointment;

use App\Http\Controllers\Controller;

class AppointmentController extends Controller
{
    public function index()
    {
        return view('frontend.appointment.index');
    }
}
