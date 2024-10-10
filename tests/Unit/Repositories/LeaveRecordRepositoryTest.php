<?php

namespace Tests\Unit\Repositories;

use App\Contracts\LeaveRecordRepositoryInterface;
use App\Enums\LeaveRecordType;
use App\Models\User;
use App\Repositories\LeaveRecordRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveRecordRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected LeaveRecordRepositoryInterface $leaveRecordRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->leaveRecordRepository = new LeaveRecordRepository();
    }

    /**
     * Test creating a new leave record
     * @return void
     */
    public function testCreateLeaveRecord()
    {
        $user = User::factory()->create();
        $data = [
            'user_id' => $user->id,
            'start_date' => '2021-01-01 00:00:00',
            'end_date' => '2021-01-02 00:00:00',
            'leave_type' => LeaveRecordType::ANNUAL,
        ];

        $leaveRecord = $this->leaveRecordRepository->createLeaveRecord($data);

        $this->assertDatabaseHas('leave_records', $data);
        $this->assertEquals($data['user_id'], $leaveRecord->user_id);
        $this->assertEquals($data['start_date'], $leaveRecord->start_date);
        $this->assertEquals($data['end_date'], $leaveRecord->end_date);
        $this->assertEquals($data['leave_type'], $leaveRecord->leave_type);
    }

    /**
     * Test deleting a leave record
     * @return void
     */
    public function testDeleteLeaveRecord()
    {
        $user = User::factory()->create();
        $data = [
            'user_id' => $user->id,
            'start_date' => '2021-01-01 00:00:00',
            'end_date' => '2021-01-02 00:00:00',
            'leave_type' => LeaveRecordType::ANNUAL,
        ];

        $leaveRecord = $this->leaveRecordRepository->createLeaveRecord($data);

        $this->assertDatabaseHas('leave_records', $data);

        $this->leaveRecordRepository->deleteLeaveRecord($leaveRecord->id);

        $this->assertDatabaseMissing('leave_records', $data);
    }

    /**
     * Test getting all leave records for a user
     * @return void
     */
    public function testGetAllLeaveRecordsForUser()
    {
        $user = User::factory()->create();
        $leaveRecordsData = [
            ['user_id' => $user->id, 'start_date' => '2021-01-01 00:00:00', 'end_date' => '2021-01-02 00:00:00', 'leave_type' => LeaveRecordType::ANNUAL],
            ['user_id' => $user->id, 'start_date' => '2021-01-03 00:00:00', 'end_date' => '2021-01-04 00:00:00', 'leave_type' => LeaveRecordType::SICK],
        ];

        foreach ($leaveRecordsData as $leaveRecordData) {
            $this->leaveRecordRepository->createLeaveRecord($leaveRecordData);
        }

        $leaveRecords = $this->leaveRecordRepository->getAllLeaveRecordsForUser($user->id);

        $this->assertCount(2, $leaveRecords);
        $this->assertEquals($leaveRecordsData[0]['user_id'], $leaveRecords[0]->user_id);
        $this->assertEquals($leaveRecordsData[0]['start_date'], $leaveRecords[0]->start_date);
        $this->assertEquals($leaveRecordsData[0]['end_date'], $leaveRecords[0]->end_date);
        $this->assertEquals($leaveRecordsData[0]['leave_type'], $leaveRecords[0]->leave_type);
        $this->assertEquals($leaveRecordsData[1]['user_id'], $leaveRecords[1]->user_id);
        $this->assertEquals($leaveRecordsData[1]['start_date'], $leaveRecords[1]->start_date);
        $this->assertEquals($leaveRecordsData[1]['end_date'], $leaveRecords[1]->end_date);
        $this->assertEquals($leaveRecordsData[1]['leave_type'], $leaveRecords[1]->leave_type);
    }


}
