<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class VehicleController extends Controller
{
    public function store(Request $request){
        // validate request fields
        $validated = $request->validate([
            'vehicle-type' => 'required|string', // Vehicle type must be a string. Required if he user didn't select a car from the list
            'brand' => 'required|string', // Brand must be a string. Required if he user didn't select a car from the list
            'model' => 'required|string', // Model must be a string. Required if he user didn't select a car from the list
            'chassis-series' => 'required|string', // Chassis series must be a string. Required if he user didn't select a car from the list
            'manufacturing-year' => 'required|date_format:Y|', // Manufacturing year must be in the format of a year. Required if he user didn't select a car from the list
            'engine' => 'required|in:petrol,diesel,hybrid,electric,lng', // Engine must be one of the values mentioned here. . Required if he user didn't select a car from the list
            'returnsVehicle' => 'boolean' // Flag that tells if the method should return the newly created vehicle or respond directly to the api or web request
        ]);

        // create new vehicle
        $vehicle = Vehicle::create([
            'type' => $validated['vehicle-type'],
            'brand' => $validated['brand'],
            'model' => $validated['model'],
            'chassis_series' => $validated['chassis-series'],
            'manufacturing_year' => $validated['manufacturing-year'],
            'engine' => $validated['engine'],
        ]);

        /* 
        * if the user is authenticated and is registered as a client, attaches the vechicle to the user;
        * otherwise puts the vehicle in the session to be used during the user registration process 
        */
        if(Auth::check() && Auth::user()->hasRole('client')){
            Auth::user()->vehicles()->save($vehicle);
            $vehicle->refresh();
        }
        else {
            Session::put('newVehicle', $vehicle);
        }

        // if the flag that tells the function to return the vehicle was set, returns the vehicle; otherwise it will return a redirect or a response to the request
        if($validated['returnsVehicle']){
            return $vehicle;
        }

        else {
            // logic for when the function would respond directly to a request
        }
    }
}
