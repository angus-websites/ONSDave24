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
}
