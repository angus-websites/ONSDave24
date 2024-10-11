<?php

namespace Tests\Feature\Http\Controllers;

use App\Contracts\LeaveRecordRepositoryInterface;
use App\Models\LeaveType;
use App\Models\User;
use App\Repositories\LeaveRecordRepository;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TimeRecordControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test calling the clock endpoint
     * as a first time user with no arguments
     * @return void
     */
    public function testHandleClockFirstTimeUser()
    {
        // Mock the current time
        $now = Carbon::parse('2024-01-01 10:00:00');
        Carbon::setTestNow($now);

        // Create a user
        $user = User::factory()->create();

        // Call the clock endpoint
        $this->actingAs($user);

        $this->get('/clock');

        // Check if the user has a time record
        $this->assertDatabaseHas('time_records', [
            'user_id' => $user->id,
            'recorded_at' => $now,
            'type' => 'clock_in',
        ]);

    }

    /**
     * Test calling the clock endpoint twice
     * as a user with a previous clock in record creates a clock
     * in record and a clock out record
     */
    public function testHandleClockSecondTimeUser()
    {
        // Mock the current time
        $now = Carbon::parse('2024-01-01 10:00:00');
        Carbon::setTestNow($now);

        // Create a user
        $user = User::factory()->create();

        // Create a clock in record for the user
        $this->actingAs($user);
        $this->get('/clock');

        // Mock the current time
        $now = Carbon::parse('2024-01-01 18:00:00');
        Carbon::setTestNow($now);

        // Call the clock endpoint
        $this->get('/clock');

        //Check two time records exist for the user
        $this->assertDatabaseCount('time_records', 2);

        // Check the content of the time records
        $this->assertDatabaseHas('time_records', [
            'user_id' => $user->id,
            'recorded_at' => '2024-01-01 10:00:00',
            'type' => 'clock_in',
        ]);

        $this->assertDatabaseHas('time_records', [
            'user_id' => $user->id,
            'recorded_at' => '2024-01-01 18:00:00',
            'type' => 'clock_out',
        ]);

    }
}
