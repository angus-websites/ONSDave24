<?php

namespace Tests\Unit\Services;

use App\Contracts\TimeRecordRepositoryInterface;
use App\Enums\TimeRecordType;
use App\Services\TimeRecordService;
use Carbon\Carbon;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\MockObject\Exception;
use Tests\TestCase;


class TimeRecordServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the clockIn method
     * @throws Exception
     */
    public function testClockIn()
    {
        // Create a mock TimeRecordRepositoryInterface
        $timeRecordRepository = $this->createMock(TimeRecordRepositoryInterface::class);

        // Create a user with the UserFactory
        $user = UserFactory::new()->create();

        // Set a fixed time for testing
        $testNow = Carbon::parse('2024-01-01 10:00:00');
        Carbon::setTestNow($testNow);

        // Expect the createTimeRecord method to be called once with the correct data
        $timeRecordRepository->expects($this->once())
            ->method('createTimeRecord')
            ->with($this->callback(function($data) use ($user, $testNow) {
                return $data['user_id'] === $user->id
                    && $data['recorded_at'] instanceof Carbon
                    && $data['recorded_at'] == $testNow
                    && $data['type'] === TimeRecordType::CLOCK_IN;
            }));

        // Create a new instance of TimeRecordService
        $timeRecordService = new TimeRecordService($timeRecordRepository);

        // Call the clockIn method with the user id
        $timeRecordService->clockIn($user->id);

        // Reset the time after the test
        Carbon::setTestNow();


    }

    /**
     * Test the clockOut method
     * @throws Exception
     */
    public function testClockOut()
    {
        // Create a mock TimeRecordRepositoryInterface
        $timeRecordRepository = $this->createMock(TimeRecordRepositoryInterface::class);

        // Create a user with the UserFactory
        $user = UserFactory::new()->create();

        // Set a fixed time for testing
        $testNow = Carbon::parse('2024-01-01 10:00:00');
        Carbon::setTestNow($testNow);

        // Expect the createTimeRecord method to be called once with the correct data
        $timeRecordRepository->expects($this->once())
            ->method('createTimeRecord')
            ->with($this->callback(function($data) use ($user, $testNow) {
                return $data['user_id'] === $user->id
                    && $data['recorded_at'] instanceof Carbon
                    && $data['recorded_at'] == $testNow
                    && $data['type'] === TimeRecordType::CLOCK_OUT;
            }));

        // Create a new instance of TimeRecordService
        $timeRecordService = new TimeRecordService($timeRecordRepository);

        // Call the clockOut method with the user id
        $timeRecordService->clockOut($user->id);

        // Reset the time after the test
        Carbon::setTestNow();
    }

    /**
     * Test the isSessionDurationTooShort method
     * @throws Exception
     */
    public function testIsSessionDurationTooShortWithLongDuration()
    {
        // Create a mock TimeRecordRepositoryInterface
        $timeRecordRepository = $this->createMock(TimeRecordRepositoryInterface::class);

        // Create a TimeRecordService instance
        $timeRecordService = new TimeRecordService($timeRecordRepository);

        // Call the isSessionDurationTooShort method with a duration of 1 hour
        $clockInTime = Carbon::parse('2024-01-01 10:00:00');
        $clockOutTime = Carbon::parse('2024-01-01 11:00:00');

        $result = $timeRecordService->isSessionDurationTooShort($clockInTime, $clockOutTime);

        // Assert that the result is false
        $this->assertFalse($result);

    }

    /**
     * Test the isSessionDurationTooShort method
     * @throws Exception
     */
    public function testIsSessionDurationTooShortWithShortDuration()
    {
        // Create a mock TimeRecordRepositoryInterface
        $timeRecordRepository = $this->createMock(TimeRecordRepositoryInterface::class);

        // Create a TimeRecordService instance
        $timeRecordService = new TimeRecordService($timeRecordRepository);

        // Call the isSessionDurationTooShort method with a duration of 5 seconds
        $clockInTime = Carbon::parse('2024-01-01 10:00:00');
        $clockOutTime = Carbon::parse('2024-01-01 10:00:05');

        $result = $timeRecordService->isSessionDurationTooShort($clockInTime, $clockOutTime);

        // Assert that the result is true
        $this->assertTrue($result);
    }
}
