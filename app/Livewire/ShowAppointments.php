<?php

namespace App\Livewire;

use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;


class ShowAppointments extends Component
{
    use WithPagination;

    public $services;
    public $service_id = '';
    public $status = '';
    public $date = '';

    protected $queryString = [
        'service_name' => ['except' => ''],
        'status' => ['except' => ''],
        'date' => ['except' => ''],
    ];

    /**
     * Prepare the list of services and the list of the current user's cars
     */
    public function mount()
    {
        $this->services = Auth::user()->services;
    }

    public function updating($property)
    {
        $this->resetPage(); // Reset pagination on filter update
    }

    public function render()
    {
        // Query with optional filters and paginate results
        $serviceIds = Auth::user()->services->pluck('id')->toArray();
        $appointments = Appointment::whereIn('service_id', $serviceIds)
            ->when($this->service_id, function ($query, $service_id) {
                $query->where('service_id', $service_id);
            })
            ->when($this->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($this->date, function ($query, $date) {
                $query->whereDate('date', $date);
            })
            ->paginate(5);

        return view('livewire.show-appointments',[
            'appointments' => $appointments,
        ]);
    }
}
