<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable=[
        'type',
        'mentions', 
        'date', 
        'time',
        'vehicle_id'
    ];

    /**
     * Model relation definitions
     */
    
    public function vehicle(){
        return $this->belongsTo(Vehicle::class);
    }

    public function service(){
        return $this->belongsTo(Service::class);
    }

    /**
     * Custom accessors
     */
    public function getUserAttribute(){
        return $this->vehicle->user()->first();
    }
}
