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
        $this->testNow = Carbon::parse('2024-01-01 10:00:00');
        Carbon::setTestNow($this->testNow);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Reset the time after each test
        Carbon::setTestNow();
    }

    /**
     * Test the clockIn method
     * @throws Exception
     */
    public function testClockIn()
    {
        // Expect the createTimeRecord method to be called once with the correct data
        $this->timeRecordRepository->expects($this->once())
            ->method('createTimeRecord')
            ->with($this->callback(function ($data) {
                return $data['user_id'] === $this->user->id
                    && $data['recorded_at'] instanceof Carbon
                    && $data['recorded_at'] == $this->testNow
                    && $data['type'] === TimeRecordType::CLOCK_IN;
            }));

        // Create a new instance of TimeRecordService
        $timeRecordService = new TimeRecordService($this->timeRecordRepository);

        // Call the clockIn method with the user id
        $timeRecordService->clockIn($this->user->id);
    }

    /**
     * Test the clockOut method
     * @throws Exception
     */
    public function testClockOut()
    {
        // Expect the createTimeRecord method to be called once with the correct data
        $this->timeRecordRepository->expects($this->once())
            ->method('createTimeRecord')
            ->with($this->callback(function ($data) {
                return $data['user_id'] === $this->user->id
                    && $data['recorded_at'] instanceof Carbon
                    && $data['recorded_at'] == $this->testNow
                    && $data['type'] === TimeRecordType::CLOCK_OUT;
            }));

        // Create a new instance of TimeRecordService
        $timeRecordService = new TimeRecordService($this->timeRecordRepository);

        // Call the clockOut method with the user id
        $timeRecordService->clockOut($this->user->id);
    }

    /**
     * Test the isSessionDurationTooShort method with long duration
     */
    public function testIsSessionDurationTooShortWithLongDuration()
    {
        // Create a TimeRecordService instance
        $timeRecordService = new TimeRecordService($this->timeRecordRepository);

        // Call the isSessionDurationTooShort method with a duration of 1 hour
        $clockInTime = Carbon::parse('2024-01-01 10:00:00');
        $clockOutTime = Carbon::parse('2024-01-01 11:00:00');

        $result = $timeRecordService->isSessionDurationTooShort($clockInTime, $clockOutTime);

        // Assert that the result is false
        $this->assertFalse($result);
    }

    /**
     * Test the isSessionDurationTooShort method with short duration
     */
    public function testIsSessionDurationTooShortWithShortDuration()
    {
        // Create a TimeRecordService instance
        $timeRecordService = new TimeRecordService($this->timeRecordRepository);

        // Call the isSessionDurationTooShort method with a duration of 5 seconds
        $clockInTime = Carbon::parse('2024-01-01 10:00:00');
        $clockOutTime = Carbon::parse('2024-01-01 10:00:05');

        $result = $timeRecordService->isSessionDurationTooShort($clockInTime, $clockOutTime);

        // Assert that the result is true
        $this->assertTrue($result);
    }

    /**
     * Test converting UK time to UTC
     * @throws \Exception
     */
    public function testConvertToUtcWithUkTime()
    {
        // Create a TimeRecordService instance
        $timeRecordService = new TimeRecordService($this->timeRecordRepository);

        // Call the convertToUtc method with a UK time
        $ukTime = Carbon::parse('2024-01-01 10:00:00', 'Europe/London');
        $expectedUtcTime = Carbon::parse('2024-01-01 10:00:00', 'UTC');

        $utcTime = $timeRecordService->convertToUtc($ukTime, 'Europe/London');

        // Assert that the result is the same as the UK time
        $this->assertEquals($expectedUtcTime, $utcTime);
    }

    /**
     * Test converting UK time in summer to UTC
     * @throws \Exception
     */
    public function testConvertToUtcWithUkTimeInSummer()
    {
        // Create a TimeRecordService instance
        $timeRecordService = new TimeRecordService($this->timeRecordRepository);

        // Call the convertToUtc method with a UK time in summer
        $ukTime = Carbon::parse('2024-07-01 10:00:00', 'Europe/London');
        $expectedUtcTime = Carbon::parse('2024-07-01 09:00:00', 'UTC');

        $utcTime = $timeRecordService->convertToUtc($ukTime, 'Europe/London');

        // Assert that the result is the same as the UK time
        $this->assertEquals($expectedUtcTime, $utcTime);
    }

    /**
     * Test converting french time to UTC
     * @throws \Exception
     */
    public function testConvertToUtcWithFrenchTime()
    {
        // Create a TimeRecordService instance
        $timeRecordService = new TimeRecordService($this->timeRecordRepository);

        // Call the convertToUtc method with a French time
        $frenchTime = Carbon::parse('2024-01-01 10:00:00', 'Europe/Paris');
        $expectedUtcTime = Carbon::parse('2024-01-01 09:00:00', 'UTC');

        $utcTime = $timeRecordService->convertToUtc($frenchTime, 'Europe/Paris');

        // Assert that the result is the same as the UK time
        $this->assertEquals($expectedUtcTime, $utcTime);
    }
}
