<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Middleware\CheckIsServiceManager;
use App\Models\Appointment;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::view('/', 'welcome')->name('welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');


Route::get('appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
Route::post('appointments', [AppointmentController::class, 'store'])->name('store.appointment');
Route::get('appointments/success', [AppointmentController::class, 'showAppointmentCreated'])->name('show.appointment.created');

Route::middleware([CheckIsServiceManager::class])->group(function (){
    Route::get('appointments', [AppointmentController::class, 'index'])->name('appointments');
    Route::get('appointment/{appointment}', [AppointmentController::class, 'show'])->name('appointment.show');
    Route::patch('appointment/{appointment}', [AppointmentController::class, 'update'])->name('appointment.update');
    Route::delete('appointment/{appointment}', [AppointmentController::class, 'destroy'])->name('appointment.destroy');
});

require __DIR__.'/auth.php';
