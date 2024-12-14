<?php

namespace App\Livewire;

use App\Models\Service;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class AppointmentForm extends Component
{
    /**
     * Declare variabled used by livewire
     */
    public $services;
    public $vehicles;
    public $timeSlots = [];
    public $showNewVehicleForm = false;
    public $selectedVehicle = null;
    public $selectedService = null;
    public $vehicleType = null;
    public $appointmentType = null;
    public $brand;
    public $model;
    public $chassisSeries;
    public $manufacturingYear;
    public $engine;
    public $observations;
    public $date;
    public $selectedTimeSlot;
    public $cost = 0;

    public $user;

    /**
     * Validation rules for form fields
     */
    protected $rules = [
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
    ];

    /**
     * Prepare the list of services and the list of the current user's cars
     */
    public function mount()
    {
        $this->services = Service::all();
        $this->vehicles = Auth::check() ? Auth::user()->vehicles : [];

        $this->user = Auth::check() ? Auth::user() : [];
    }

    public function calculateCost(){
        // $this->validate();
        if($this->selectedService && ($this->selectedVehicle || $this->vehicleType)){
            $selectedService = Service::find($this->selectedService);
            $vehicleType = $this->selectedVehicle ? Vehicle::find($this->selectedVehicle)->type : $this->vehicleType;
            $this->cost = $selectedService->prices()->where('type', $vehicleType)->first()->price ?? 0;
        }
        
    }

    // public function update($propertyName){
    //     $this->validateOnly($propertyName, $this->rules);
    //     $this->validate();
    // }

    public function render()
    {
        return view('livewire.appointment-form');
    }

    public function getTimeSlots(){
        // Check if the required properties are set before proceeding. 
        // If any of the properties are not set, return an empty array.
        if(!$this->selectedService || !$this->date || !$this->appointmentType){
            return [];
        }

        // Retrieve the service model from the database using the selected service ID.
        $selectedService = Service::find($this->selectedService);

        // Determine the appointment duration based on the appointment type ('itp' or 'repair').
        // This selects the appropriate duration from the service model.
        $duration = $this->appointmentType == 'itp' ? $selectedService->itp_duration : $selectedService->repair_duration;

        // Fetch all appointments for the selected service on the selected date.
        // This will be used to check for conflicts when generating available time slots.
        $dayAppointments = $selectedService->appointments()->where('date', $this->date)->get();

        // Define the start and end times for the workday (from 9 AM to 5 PM).
        $workdayStart = Carbon::createFromFormat('H:i', '9:00');
        $workdayEnd = Carbon::createFromFormat('H:i', '17:00');

        // Initialize the time counter to the start of the workday.
        $timeCounter = $workdayStart;

        // Loop to generate potential time slots within the workday.
        // The loop continues as long as the timeCounter is before the workday end time.
        while($timeCounter->lessThan($workdayEnd)){
            array_push($this->timeSlots, $timeCounter);
            $timeCounter = $timeCounter->copy()->addMinutes($duration);
        }

        // Loop through all the generated time slots to filter out those that are already occupied by appointments.
        foreach($this->timeSlots as $index => $currentSlotStartTime){
            // Calculate the end time of the current slot.
            $currentSlotEndTime = $currentSlotStartTime->copy()->addMinutes($duration);

            // Filter out appointments that overlap with the current time slot.
            $currentSlotAppointments = $dayAppointments->filter(function ($appointment) use ($selectedService, $currentSlotStartTime, $currentSlotEndTime){
                // Convert the appointment start time into a Carbon instance.
                $appointmentStartTime = Carbon::createFromFormat('H:i', $appointment->time);

                // Determine the appointment duration based on the type ('itp' or 'repair').
                $appointmentDuration = $appointment->type == 'itp' ? $selectedService->itp_duration : $selectedService->repair_duration;

                // Calculate the appointment end time based on the start time and duration.
                $appointmentEndTime = $appointmentStartTime->copy()->addMinutes($appointmentDuration);

                // Check if the appointment overlaps with the current time slot.
                return ($appointmentStartTime->greaterThan($currentSlotStartTime) && $appointmentStartTime->lessThan($currentSlotEndTime))
                        || ($appointmentEndTime->lessThan($currentSlotEndTime) && $appointmentEndTime->greaterThan($currentSlotStartTime))
                        || ($appointmentStartTime->lessThan($currentSlotEndTime) && $appointmentEndTime->greaterThan($currentSlotStartTime));
            });

            // Count the number of appointments that overlap with the current time slot.
            $currentSlotAppointmentsNumber = count($currentSlotAppointments);

            // If the number of overlapping appointments is greater than or equal to the maximum slots available for the service,
            // remove the current time slot from the available slots.
            if($currentSlotAppointmentsNumber >= $selectedService->slots){
                unset($this->timeSlots[$index]);
            }
            // Otherwise, format the time slot to only show the hour and minute (e.g., "09:00").
            else {
                $this->timeSlots[$index] = $this->timeSlots[$index]->format('H:i');
            }
        }

        // Reindex the timeSlots array to remove any gaps in the array after the unset operation.
        $this->timeSlots = array_values($this->timeSlots);

        // Return the available time slots as an array of formatted strings.
        return $this->timeSlots;
    }
}
