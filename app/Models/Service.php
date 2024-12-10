<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable=[
        'name', 
        'itp_duration', 
        'repair_duration', 
        'slots'
    ];

    /**
     * Model relation definitions
     */
    
    public function manager(){
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function appointments(){
        return $this->hasMany(Appointment::class);
    }

    public function prices(){
        return $this->hasMany(Price::class);
    }

}
