<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function showAppointmentForm(Request $request){

        $services = Service::all();
        $vehicles = Auth::user() ? Auth::user()->vehicles() : [];

        // Check if the request is an API request
        if ($request->expectsJson()) {
            return response()->json([
                'services' => $services,
                'vehicles' => $vehicles,
            ]);
        }

        // Handle a standard web request
        return view('appointment-form', [
            'services' => $services,
            'vehicles' => $vehicles,
        ]);
    }
}
