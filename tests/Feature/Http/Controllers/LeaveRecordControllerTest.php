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

class LeaveRecordControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithoutMiddleware;

    /**
     * Test calling the add leave endpoint
     * to add a new leave record
     */
    public function testAddLeave()
    {

        // Mock the current now date
        // Mock the current time
        $now = Carbon::parse('2024-01-01 10:00:00');
        Carbon::setTestNow($now);


        // 1. Create a user
        $user = User::factory()->create();

        // 2. Create a leave type
        $leaveType = LeaveType::factory()->create();

        // 3. Create a request with the required data
        $request = [
            'leave_type_id' => $leaveType->id,
            'start_date' => Carbon::now()->toDateString(),
            'end_date' => Carbon::now()->addDays(2)->toDateString(),
            'notes' => 'Trip to the beach',
        ];

        // Call the endpoint
        $this->actingAs($user);

        // Simulate a POST request to the controller
        $response = $this->post('/leave', $request);

        // Assert the database has the new leave record
        $this->assertDatabaseHas('leave_records', [
            'user_id' => $user->id,
            'leave_type_id' => $leaveType->id,
            'start_date' => $request['start_date'],
            'end_date' => $request['end_date'],
            'notes' => $request['notes'],
        ]);



    }

}

