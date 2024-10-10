<?php

namespace Database\Factories;

use App\Models\LeaveType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LeaveType>
 */
class LeaveTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Make code three letters long
        return [
            'code' =>  $this->faker->unique()->lexify('???'),
            'name' => $this->faker->unique()->word,
            'description' => $this->faker->sentence,
            'paid' => $this->faker->boolean,
            'core' => $this->faker->boolean,
        ];
    }
}
