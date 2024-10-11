<?php

namespace Tests\Feature\Http\Controllers;

use App\Contracts\LeaveRecordRepositoryInterface;
use App\Models\LeaveType;
use App\Models\User;
use App\Repositories\LeaveRecordRepository;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class TimeRecordControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithoutMiddleware;

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

        // Simulate a POST request to the controller
        $response = $this->post('/clock');

        $response->dump();

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
        $this->post('/clock');

        // Mock the current time
        $now = Carbon::parse('2024-01-01 18:00:00');
        Carbon::setTestNow($now);

        // Call the clock endpoint
        $this->post('/clock');

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

    /**
     * Test clock in with manually provided time
     */
    public function testHandleClockWithProvidedTime()
    {
        // Mock the current time
        $now = Carbon::parse('2024-01-01 10:00:00');
        Carbon::setTestNow($now);

        // Create a user
        $user = User::factory()->create();

        // Use another time to call the endpoint
        $time = '2024-01-01 09:00:00';

        // Call the clock endpoint with the provided time
        $this->actingAs($user);
        $this->post('/clock', ['time' => $time]);

        // Check if the user has a time record
        $this->assertDatabaseHas('time_records', [
            'user_id' => $user->id,
            'recorded_at' => $time,
            'type' => 'clock_in',
        ]);
    }

    /**
     * Test calling the clock endpoint a second time with a provided
     * time that is before the last time record returns a 400 response
     */
    public function testHandleClockThrowsExceptionIfProvidedTimeIsBeforeLastRecord()
    {
        // Mock the current time
        $now = Carbon::parse('2024-01-01 10:00:00');
        Carbon::setTestNow($now);

        // Create a user
        $user = User::factory()->create();

        // Create a clock in record for the user
        $this->actingAs($user);
        $this->post('/clock');

        // Mock the current time to be 3 hours after the clock in time
        $now = Carbon::parse('2024-01-01 13:00:00');
        Carbon::setTestNow($now);

        // Use another time to call the endpoint that is before the last record
        $time = '2024-01-01 09:00:00';

        // Call the clock endpoint with the provided time
        $response = $this->post('/clock', ['time' => $time]);

        // Check if the user has a time record (the original clock in record)
        $this->assertDatabaseCount('time_records', 1);

        // Check the content of the time records
        $this->assertDatabaseHas('time_records', [
            'user_id' => $user->id,
            'recorded_at' => '2024-01-01 10:00:00',
            'type' => 'clock_in',
        ]);

        // Assert the response status code is not 200
        $response->assertStatus(400);
    }

    /**
     * Test that calling the endpoint within the minimum session duration deletes the session
     */
    public function testHandleClockDeletesSessionIfWithinMinimumSessionDuration()
    {
        // Mock the current time
        $now = Carbon::parse('2024-01-01 10:00:00');
        Carbon::setTestNow($now);

        // Create a user
        $user = User::factory()->create();

        // Create a clock in record for the user
        $this->actingAs($user);
        $this->post('/clock');

        // Assert the user has a time record
        $this->assertDatabaseHas('time_records', [
            'user_id' => $user->id,
            'recorded_at' => '2024-01-01 10:00:00',
            'type' => 'clock_in',
        ]);

        // Mock the current time to be 20 seconds after the clock in time
        $now = Carbon::parse('2024-01-01 10:00:20');
        Carbon::setTestNow($now);

        // Call the clock endpoint
        $this->post('/clock');

        // Assert the user has no time records
        $this->assertDatabaseCount('time_records', 0);
    }
}

