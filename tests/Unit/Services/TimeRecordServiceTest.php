<?php

namespace Tests\Unit\Services;

use App\Contracts\TimeRecordRepositoryInterface;
use App\Enums\TimeRecordType;
use App\Models\User;
use App\Services\TimeRecordService;
use Carbon\Carbon;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\MockObject\Exception;
use Tests\TestCase;

class TimeRecordServiceTest extends TestCase
{
    use RefreshDatabase;

    protected TimeRecordRepositoryInterface $timeRecordRepository;
    protected User $user;
    protected Carbon $testNow;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Set up common objects for all tests
        $this->timeRecordRepository = $this->createMock(TimeRecordRepositoryInterface::class);
        $this->user = UserFactory::new()->create();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Reset the time after each test
        Carbon::setTestNow();
    }
    

    /**
     * Test a new user clocking in for the first time in the UK without providing a time
     */
    public function testHandleClockUkNewUserFirstTimeNow(){

        // Mock the current time
        $now = Carbon::parse('2024-01-01 10:00:00');
        Carbon::setTestNow($now);

        // Expect the createTimeRecord method to be called once with the correct data
        $this->timeRecordRepository->expects($this->once())
            ->method('createTimeRecord')
            ->with($this->callback(function ($data) use ($now) {
                return $data['user_id'] === $this->user->id
                    && $data['recorded_at'] instanceof Carbon
                    && $data['recorded_at'] == $now
                    && $data['type'] === TimeRecordType::CLOCK_IN;
            }));

        // Create a new instance of TimeRecordService
        $timeRecordService = new TimeRecordService($this->timeRecordRepository);

        // Call the handleClock method with the user id located in the UK
        $timeRecordService->handleClock($this->user->id, 'Europe/London');

    }

    /**
     * Test a new user clocking in for the first time in the UK providing a specific time
     */
    public function testHandleClockUkNewUserFirstTimeSpecificTime()
    {

        // Create a custom time
        $customTime = Carbon::parse('2024-01-01 9:00:00');

        // Expect the createTimeRecord method to be called once with the correct data
        $this->timeRecordRepository->expects($this->once())
            ->method('createTimeRecord')
            ->with($this->callback(function ($data) use ($customTime) {
                return $data['user_id'] === $this->user->id
                    && $data['recorded_at'] instanceof Carbon
                    && $data['recorded_at'] == $customTime
                    && $data['type'] === TimeRecordType::CLOCK_IN;
            }));

        // Create a new instance of TimeRecordService
        $timeRecordService = new TimeRecordService($this->timeRecordRepository);

        // Call the handleClock method with the user id located in the UK and a specific time
        $timeRecordService->handleClock($this->user->id, 'Europe/London', $customTime);

    }

    /**
     * Test a new user clocking in for the first time in UK summer without providing a time
     */
//    public function testHandleClockUkSummerTimeNewUserFirstTimeNow()
//    {
//        // Create a custom time
//        $customTime = Carbon::parse('2024-08-09 9:00:00');
//        $utcTime = Carbon::parse('2024-08-09 8:00:00');
//
//        // Expect the createTimeRecord method to be called once with the correct data
//        $this->timeRecordRepository->expects($this->once())
//            ->method('createTimeRecord')
//            ->with($this->callback(function ($data) use ($utcTime) {
//                return $data['user_id'] === $this->user->id
//                    && $data['recorded_at'] instanceof Carbon
//                    && $data['recorded_at'] == $utcTime
//                    && $data['type'] === TimeRecordType::CLOCK_IN;
//            }));
//
//        // Create a new instance of TimeRecordService
//        $timeRecordService = new TimeRecordService($this->timeRecordRepository);
//
//        // Call the handleClock method with the user id located in the UK and a specific time
//        $timeRecordService->handleClock($this->user->id, 'Europe/London', $customTime);
//    }


}
