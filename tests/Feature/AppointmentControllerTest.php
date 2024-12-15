<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AppointmentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_creates_appointment_for_authenticated_user()
    {
        // Create a user and authenticate
        $user = User::factory()->create();
        $user->assignRole('client');
        Auth::login($user);

        // Create a service and vehicle
        $service = Service::factory()->create();
        $vehicle = Vehicle::factory()->create();

        // Define input data
        $requestData = [
            'service' => $service->id,
            'vehicle' => $vehicle->id,
            'appointment-type' => 'itp',
            'date' => now()->addDay()->toDateString(),
            'time-slot' => '10:00 AM',
            'observations' => 'Test observations',
        ];

        // Perform the request as an HTML form submission
        $response = $this->post(route('store.appointment'), $requestData);

        // Assert response and database changes
        $response->assertStatus(302) // Redirect status
            ->assertRedirect(route('show.appointment.created')); // Assert the redirect

        $this->assertDatabaseHas('appointments', [
            'service_id' => $service->id,
            'vehicle_id' => $vehicle->id,
            'type' => 'itp',
            'mentions' => 'Test observations',
        ]);
    }
}
