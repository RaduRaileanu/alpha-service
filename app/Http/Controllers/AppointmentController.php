<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class AppointmentController extends Controller
{
    public function create(Request $request){

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

    public function index(Request $request)
    {

        // Handle API request
        if ($request->expectsJson()) {

            $services = Auth::user()->services;
            $serviceIds = $services->pluck('id')->toArray();

            // Query with optional filters and paginate results
            $appointments = Appointment::whereIn('service_id', $serviceIds)
                ->when($request->input('service_id'), function ($query, $request) {
                    $query->where('service_id', $request->input('service_id'));
                })
                ->when($request->input('status'), function ($query, $request) {
                    $query->where('status', $request->input('status'));
                })
                ->when($request->input('date'), function ($query, $request) {
                    $query->whereDate('date', $request->input('date'));
                })
                ->paginate(10);

            return response()->json([
                'appointments' => $appointments,
                'services' => $services
            ]);
        }

        // Return view for web request
        return view('index-appointments');
    }

    public function store(Request $request){

        // validate request fields
        $validated = $request->validate([
            'service' => 'required|exists:services,id', // Ensure service is selected and exists in the DB
            'vehicle' => 'required_without_all:vehicleType,brand,model,chassisSeries,manufacturing_year,engine|exists:vehicles,id', // Ensure vehicle exists (optional)
            'vehicle-type' => 'required_without:selectedVehicle|string', // Vehicle type must be a string. Required if he user didn't select a car from the list
            'brand' => 'required_without:selectedVehicle|string', // Brand must be a string. Required if he user didn't select a car from the list
            'model' => 'required_without:selectedVehicle|string', // Model must be a string. Required if he user didn't select a car from the list
            'chassis-series' => 'required_without:selectedVehicle|string', // Chassis series must be a string. Required if he user didn't select a car from the list
            'manufacturing-year' => 'required_without:selectedVehicle|date_format:Y|', // Manufacturing year must be in the format of a year. Required if he user didn't select a car from the list
            'engine' => 'required_without:selectedVehicle|in:petrol,diesel,hybrid,electric,lng', // Engine must be one of the values mentioned here. . Required if he user didn't select a car from the list
            'appointment-type' => 'required|in:itp,repair', // Appointment type must be either 'itp' or 'repair'
            'date' => 'required|date|after_or_equal:today', // Date must be today or in the future
            'time-slot' => 'required|string', // Time slot must be a string
            'observations' => 'nullable|string|max:500', // Optional observations with max length
        ]);

        // retrieves the service from the database
        $service = Service::find($request->input('service'));

        /* 
        * if the vehicle already exists (i.e., its id was sent with the request), retrieves it from the DB
        * otherwise, calls the VehicleController function that creates a new vehicle 
        * and adds the 'returnsVehicle' key to the request to let the function now it must return a vehicle, not a redirect or response
        */
        $vehicle = null;
        if($request->has('vehicle')){
            $vehicle = Vehicle::find($validated['vehicle']);
        }
        else {
            $vehicle = app(VehicleController::class)->store($request->merge(['returnsVehicle' => true]));
        }

        // creates the new appointment
        $appointment = Appointment::create([
            'type' => $validated['appointment-type'],
            'mentions' => $validated['observations'],
            'date' => $validated['date'],
            'time' => $validated['time-slot'],
            'vehicle_id' => $vehicle->id,
            'service_id' => $service->id
        ]);

        /*
        * if a user is logged in, it redirects to the view where the appointment is confirmed, or it responds with a success status, depending on the request type
        * otherwise, redirects to the user to the registration page; to create an account; the appointment is stored in session for use during the registration process
        */
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

    public function show(Request $request, Appointment $appointment){
        if($request->expectsJson()){
            response()->json(['appointment' => $appointment]);
        }

        return view('appointement', [['appointment' => $appointment]]);
    }

    public function update(Request $request, Appointment $appointment){

        $validated = $request->validate([
            'vehicle_id' => 'exists:vehicles,id', // Ensure vehicle exists in the database
            'date' => 'date|after_or_equal:today', // Date must be today or in the future
            'time-slot' => 'string', // Time slot must be a string
            'mentions' => 'string|max:500', // Optional mentions with max length
            'status' => 'in:registered,received,finalized', // Status must be one of the values mentioned here.
        ]);
        
        if($request->has('vehicle_id')){
            $appointment->vehicle_id = $validated['vehicle_id'];
        }
        if($request->has('date')){
            $appointment->date = $validated['date'];
        }
        if($request->has('time')){
            $appointment->time = $validated['time'];
        }
        if($request->has('status')){
            $appointment->status = $validated['status'];
        }
        if($request->has('mentions')){
            $appointment->mentions = $validated['mentions'];
        }

        $appointment->save();

        return $request->expectsJson()
                    ? response(['message' => 'Appointment updated successfully', 'appointment' => $appointment->fresh()])
                    : redirect()->route('appointment.show', ['appointment' => $appointment->id]);
    }

    public function destroy(Request $request, Appointment $appointment){
        $appointment->delete();

        return $request->expectsJson()
                    ? response('Appointment deleted successfully')
                    : redirect()->route('appointments');
    }

    public function showAppointmentCreated(){
        return view('appointment-created');
    }
}
