<?php

namespace Tests\Unit\Repositories;

use App\Contracts\LeaveRecordRepositoryInterface;
use App\Models\LeaveType;
use App\Models\User;
use App\Repositories\LeaveRecordRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveRecordRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected LeaveRecordRepositoryInterface $leaveRecordRepository;
    protected User $user;
    protected LeaveType $leaveType;

    protected function setUp(): void
    {
        parent::setUp();
        $this->leaveRecordRepository = new LeaveRecordRepository;
        $this->user = User::factory()->create();
        $this->leaveType = LeaveType::factory()->create();
    }

    /**
     * Test creating a new leave record
     *
     * @return void
     */
    public function testCreateLeaveRecord()
    {
        $data = [
            'user_id' => $this->user->id,
            'start_date' => '2021-01-01 00:00:00',
            'end_date' => '2021-01-02 00:00:00',
            'leave_type_id' => $this->leaveType->id,
        ];

        $leaveRecord = $this->leaveRecordRepository->createLeaveRecord($data);

        $this->assertDatabaseHas('leave_records', $data);
        $this->assertEquals($data['user_id'], $leaveRecord->user_id);
        $this->assertEquals($data['start_date'], $leaveRecord->start_date);
        $this->assertEquals($data['end_date'], $leaveRecord->end_date);
        $this->assertEquals($data['leave_type_id'], $leaveRecord->leave_type_id);
    }

    /**
     * Test deleting a leave record
     *
     * @return void
     */
    public function testDeleteLeaveRecord()
    {
        $data = [
            'user_id' => $this->user->id,
            'start_date' => '2021-01-01 00:00:00',
            'end_date' => '2021-01-02 00:00:00',
            'leave_type_id' => $this->leaveType->id,
        ];

        $leaveRecord = $this->leaveRecordRepository->createLeaveRecord($data);

        $this->assertDatabaseHas('leave_records', $data);

        $this->leaveRecordRepository->deleteLeaveRecord($leaveRecord->id);

        $this->assertDatabaseMissing('leave_records', $data);
    }

    /**
     * Test getting all leave records for a user
     *
     * @return void
     */
    public function testGetAllLeaveRecordsForUser()
    {
        $leaveRecordsData = [
            ['user_id' => $this->user->id, 'start_date' => '2021-01-01 00:00:00', 'end_date' => '2021-01-02 00:00:00', 'leave_type_id' => $this->leaveType->id],
            ['user_id' => $this->user->id, 'start_date' => '2021-01-03 00:00:00', 'end_date' => '2021-01-04 00:00:00', 'leave_type_id' => $this->leaveType->id],
        ];

        foreach ($leaveRecordsData as $leaveRecordData) {
            $this->leaveRecordRepository->createLeaveRecord($leaveRecordData);
        }

        $leaveRecords = $this->leaveRecordRepository->getAllLeaveRecordsForUser($this->user->id);

        $this->assertCount(2, $leaveRecords);
        $this->assertEquals($leaveRecordsData[0]['user_id'], $leaveRecords[0]->user_id);
        $this->assertEquals($leaveRecordsData[0]['start_date'], $leaveRecords[0]->start_date);
        $this->assertEquals($leaveRecordsData[0]['end_date'], $leaveRecords[0]->end_date);
        $this->assertEquals($leaveRecordsData[0]['leave_type_id'], $leaveRecords[0]->leave_type_id);
        $this->assertEquals($leaveRecordsData[1]['user_id'], $leaveRecords[1]->user_id);
        $this->assertEquals($leaveRecordsData[1]['start_date'], $leaveRecords[1]->start_date);
        $this->assertEquals($leaveRecordsData[1]['end_date'], $leaveRecords[1]->end_date);
        $this->assertEquals($leaveRecordsData[1]['leave_type_id'], $leaveRecords[1]->leave_type_id);
    }
}
