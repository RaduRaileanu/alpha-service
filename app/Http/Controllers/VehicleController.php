<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class VehicleController extends Controller
{
    public function store(Request $request){
        $validated = $request->validate([
            'vehicleType' => 'required|string', // Vehicle type must be a string. Required if he user didn't select a car from the list
            'brand' => 'required|string', // Brand must be a string. Required if he user didn't select a car from the list
            'model' => 'required|string', // Model must be a string. Required if he user didn't select a car from the list
            'chassisSeries' => 'required|string', // Chassis series must be a string. Required if he user didn't select a car from the list
            'manufacturingYear' => 'required|date_format:Y|', // Manufacturing year must be in the format of a year. Required if he user didn't select a car from the list
            'engine' => 'required|in:petrol,diesel,hybrid,electric,lng', // Engine must be one of the values mentioned here. . Required if he user didn't select a car from the list
            'returnsVehicle' => 'boolean' // Flag that tells if the method should return the newly created vehicle or respond directly to the api or web request
        ]);

        $vehicle = Vehicle::create([
            'type' => $request->input('vehicleType'),
            'brand' => $request->input('brand'),
            'model' => $request->input('model'),
            'chassis_series' => $request->input('chassisSeries'),
            'manufacturing_year' => $request->input('manufacturingYear'),
            'engine' => $request->input('engine'),
        ]);

        if(Auth::check() && Auth::user()->hasRole('client')){
            Auth::user()->vehicles()->save($vehicle);
            $vehicle->refresh();
        }
        else {
            Session::put('newVehicle', $vehicle);
        }

        if($request->input('returnsVehicle')){
            return $vehicle;
        }

        else {
            // logic for when the function would respond directly to a request
        }
    }
}
