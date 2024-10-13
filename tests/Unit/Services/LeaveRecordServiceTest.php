<?php

namespace Tests\Unit\Services;

use App\Contracts\LeaveRecordRepositoryInterface;
use App\Exceptions\InvalidLeaveDateProvidedException;
use App\Exceptions\ShortLeaveDurationException;
use App\Models\LeaveType;
use App\Models\User;
use App\Services\LeaveRecordService;
use Carbon\Carbon;
use Database\Factories\UserFactory;
use Exception;
use PHPUnit\Framework\MockObject\Exception as MockObjectException;
use Tests\TestCase;

class LeaveRecordServiceTest extends TestCase
{
    protected LeaveRecordRepositoryInterface $timeRecordRepository;

    protected User $user;
    protected LeaveType $leaveType;

    /**
     * @throws MockObjectException
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Set up common objects for all tests
        $this->leaveRecordRepository = $this->createMock(LeaveRecordRepositoryInterface::class);
        $this->user = UserFactory::new()->create();
        $this->leaveType = LeaveType::factory()->create();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Reset the time after each test
        Carbon::setTestNow();
    }

    /**
     * Test creating a new leave record
     *
     * @return void
     *
     * @throws Exception
     */
    public function testAddLeaveRecord()
    {
        // Set up the data for the test
        $leaveTypeId = $this->leaveType->id;
        $startDate = Carbon::parse('2024-01-01');
        $endDate = Carbon::now()->addDays(2);

        // Mock the repository method
        $this->leaveRecordRepository->expects($this->once())
            ->method('createLeaveRecord')
            ->with([
                'user_id' => $this->user->id,
                'leave_type_id' => $leaveTypeId,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'notes' => null,
            ]);

        // Call the service method
        $leaveRecordService = new LeaveRecordService($this->leaveRecordRepository);
        $leaveRecordService->addLeaveRecord($this->user->id, $leaveTypeId, $startDate, $endDate);
    }

    /**
     * Test that specifying an end date before the start date throws an exception
     *
     * @throws Exception
     */
    public function testAddLeaveRecordEndDateBeforeStartDateThrowsException()
    {
        // Set up the data for the test
        $leaveTypeId = $this->leaveType->id;
        $startDate = Carbon::parse('2024-01-01');
        $endDate = Carbon::parse('2024-01-01')->subDay();

        // Call the service method
        $leaveRecordService = new LeaveRecordService($this->leaveRecordRepository);

        // 3. Expect an exception to be thrown
        $this->expectException(InvalidLeaveDateProvidedException::class);
        $leaveRecordService->addLeaveRecord($this->user->id, $leaveTypeId, $startDate, $endDate);
    }

    /**
     * Test deleting a leave record
     *
     * @return void
     */
    public function testDeleteLeaveRecord()
    {
        // Set up the data for the test
        $leaveRecordId = 1;

        // Mock the repository method
        $this->leaveRecordRepository->expects($this->once())
            ->method('deleteLeaveRecord')
            ->with($leaveRecordId);

        // Call the service method
        $leaveRecordService = new LeaveRecordService($this->leaveRecordRepository);
        $leaveRecordService->deleteLeaveRecord($leaveRecordId);
    }

    /**
     * Test that adding leave shorter than the minimum duration throws an exception
     * @throws Exception
     */
    public function testAddLeaveRecordShorterThanMinimumDurationThrowsException()
    {
        // Set up the data for the test
        $leaveTypeId = $this->leaveType->id;
        $startDate = Carbon::parse('2024-01-01 09:00:00');
        $endDate = Carbon::parse('2024-01-01 10:00:00');

        // Call the service method
        $leaveRecordService = new LeaveRecordService($this->leaveRecordRepository);

        // 3. Expect an exception to be thrown
        $this->expectException(ShortLeaveDurationException::class);

        $leaveRecordService->addLeaveRecord($this->user->id, $leaveTypeId, $startDate, $endDate);
    }
}
