<?php

namespace Database\Factories;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class VehicleFactory extends Factory
{
    protected $model = Vehicle::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'type' => $this->faker->randomElement(['van','hatchback','coupe','break','SUV']),  
            'brand' => $this->faker->company,
            'model' => $this->faker->word,
            'chassis_series' => strtoupper($this->faker->bothify('??###??')), // Example: "AB123CD"
            'manufacturing_year' => $this->faker->year,
            'engine' => $this->faker->randomElement(['petrol','diesel','hybrid','electric','lng'])
        ];
    }
}
