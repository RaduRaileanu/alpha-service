<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

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

    public function store(Request $request){
        $validated = $request->validate([
            'selectedService' => 'required|exists:services,id', // Ensure service is selected and exists in the DB
            'selectedVehicle' => 'required_without_all:vehicleType,brand,model,chassisSeries,manufacturing_year,engine|exists:vehicles,id', // Ensure vehicle exists (optional)
            'vehicleType' => 'required_without:selectedVehicle|string', // Vehicle type must be a string. Required if he user didn't select a car from the list
            'brand' => 'required_without:selectedVehicle|string', // Brand must be a string. Required if he user didn't select a car from the list
            'model' => 'required_without:selectedVehicle|string', // Model must be a string. Required if he user didn't select a car from the list
            'chassisSeries' => 'required_without:selectedVehicle|string', // Chassis series must be a string. Required if he user didn't select a car from the list
            'manufacturingYear' => 'required_without:selectedVehicle|date_format:Y|', // Manufacturing year must be in the format of a year. Required if he user didn't select a car from the list
            'engine' => 'required_without:selectedVehicle|in:petrol,diesel,hybrid,electric,lng', // Engine must be one of the values mentioned here. . Required if he user didn't select a car from the list
            'appointmentType' => 'required|in:itp,repair', // Appointment type must be either 'itp' or 'repair'
            'date' => 'required|date|after_or_equal:today', // Date must be today or in the future
            'selectedTimeSlot' => 'required|string', // Time slot must be a string
            'observations' => 'nullable|string|max:500', // Optional observations with max length
        ]);

        $service = Service::find($request->input('selectedService'));

        $vehicle = null;
        if($request->has('selectedVehicle')){
            $vehicle = Vehicle::find($request->input('selectedVehicle'));
        }
        else {
            $vehicle = app(VehicleController::class)->store($request->merge(['returnsVehicle', true]));
        }

        $appointment = Appointment::create([
            'type' => $request->input('appointmentType'),
            'mentions' => $request->input('observations'),
            'date' => $request->input('date'),
            'time' => $request->input('selectedTimeSlot'),
            'vehicle_id' => $vehicle->id,
            'service_id' => $service->id
        ]);

        if(Auth::check()){
            return $request->expectsJson()
                    ? response('Appointment created successfully', 201)
                    : redirect()->route('show.appointment.created');
        }
        else {
            Session::put('newAppointment', $appointment);
            return redirect()->route('register');
        }
    }

    public function showAppointmentCreated(){
        return view('appointment-created');
    }
}
