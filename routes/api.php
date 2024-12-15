<?php

use App\Http\Controllers\AppointmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function (){
    Route::get('appointments', [AppointmentController::class, 'index'])->name('appointments');
    Route::get('appointment/{appointment}', [AppointmentController::class, 'show'])->name('appointment.show');
    Route::patch('appointment/{appointment}', [AppointmentController::class, 'update'])->name('appointment.update');
    Route::delete('appointment/{appointment}', [AppointmentController::class, 'destroy'])->name('appointment.destroy');
});
