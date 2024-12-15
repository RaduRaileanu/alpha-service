<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class ServiceFactory extends Factory
{
    protected $model = Service::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company . ' Service', 
            'itp_duration' => $this->faker->numberBetween(30, 120), 
            'repair_duration' => $this->faker->numberBetween(60, 240), 
            'slots' => $this->faker->numberBetween(1, 10)
        ];
    }
}
