<?php

namespace Tests\Unit\Services;
use App\Contracts\LeaveRecordRepositoryInterface;
use App\Enums\LeaveRecordType;
use App\Models\User;
use App\Services\LeaveRecordService;
use Carbon\Carbon;
use Database\Factories\UserFactory;
use PHPUnit\Framework\MockObject\Exception as MockObjectException;
use Exception;
use Tests\TestCase;

class LeaveRecordServiceTest extends TestCase
{

    protected LeaveRecordRepositoryInterface $timeRecordRepository;
    protected User $user;

    /**
     * @throws MockObjectException
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Set up common objects for all tests
        $this->leaveRecordRepository = $this->createMock(LeaveRecordRepositoryInterface::class);
        $this->user = UserFactory::new()->create();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Reset the time after each test
        Carbon::setTestNow();
    }

    /**
     * Test creating a new leave record
     * @return void
     * @throws Exception
     */
    public function testAddLeaveRecord()
    {
        // Set up the data for the test
        $leaveType = LeaveRecordType::ANNUAL;
        $startDate = Carbon::parse('2024-01-01');
        $endDate = Carbon::now()->addDays(2);

        // Mock the repository method
        $this->leaveRecordRepository->expects($this->once())
            ->method('createLeaveRecord')
            ->with([
                'user_id' => $this->user->id,
                'leave_type' => $leaveType,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);

        // Call the service method
        $leaveRecordService = new LeaveRecordService($this->leaveRecordRepository);
        $leaveRecordService->addLeaveRecord($this->user->id, $leaveType, $startDate, $endDate);
    }

    /**
     * Test that specifying an end date before the start date throws an exception
     * @throws Exception
     */
    public function testAddLeaveRecordEndDateBeforeStartDateThrowsException()
    {
        // Set up the data for the test
        $leaveType = LeaveRecordType::ANNUAL;
        $startDate = Carbon::parse('2024-01-01');
        $endDate = Carbon::parse('2024-01-01')->subDay();

        // Call the service method
        $leaveRecordService = new LeaveRecordService($this->leaveRecordRepository);

        // 3. Expect an exception to be thrown
        $this->expectException(Exception::class);
        $leaveRecordService->addLeaveRecord($this->user->id, $leaveType, $startDate, $endDate);
    }

    /**
     * Test deleting a leave record
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


}
