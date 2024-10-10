<?php

namespace Database\Factories;

use App\Enums\LeaveRecordType;
use App\Models\LeaveRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<LeaveRecord>
 */
class LeaveRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => $this->faker->randomElement(LeaveRecordType::getValues()),
            'start' => Carbon::now(),
            'end' => Carbon::now()->addDays(1),
            'notes' => $this->faker->sentence,
        ];
    }
}
