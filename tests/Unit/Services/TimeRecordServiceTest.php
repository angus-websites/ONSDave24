<?php

namespace Tests\Unit\Services;

use App\Contracts\TimeRecordRepositoryInterface;
use App\Enums\TimeRecordType;
use App\Models\TimeRecord;
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
        $customTime = Carbon::parse('2024-01-01 9:00:00', 'Europe/London');

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
     * Test a new user clocking in for the first time in the UK summer providing a specific time
     * @throws \Exception
     */
    public function testHandleClockUkSummerTimeNewUserFirstTimeSpecificTime()
    {
        // Create a custom time in UK Summer Time (BST)
        $customTime = Carbon::parse('2024-06-01 10:00:00', 'Europe/London');

        // Create the UTC equivalent of the custom time
        $utcTime = Carbon::parse('2024-06-01 09:00:00');


        // Expect the createTimeRecord method to be called once with the correct data
        $this->timeRecordRepository->expects($this->once())
            ->method('createTimeRecord')
            ->with($this->callback(function ($data) use ($utcTime) {
                return $data['user_id'] === $this->user->id
                    && $data['recorded_at'] instanceof Carbon
                    && $data['recorded_at'] == $utcTime
                    && $data['type'] === TimeRecordType::CLOCK_IN;
            }));

        // Create a new instance of TimeRecordService
        $timeRecordService = new TimeRecordService($this->timeRecordRepository);

        // Call the handleClock method with the user id located in the UK and a specific time
        $timeRecordService->handleClock($this->user->id, 'Europe/London', $customTime);
    }

    /**
     * Test a new user clocking in, then clocking out in the UK without providing a time
     * @throws \Exception
     * @throws Exception
     */
    public function testHandleClockUkNewUserTwiceNow()
    {
        // Mock the current time for clock in (start of the day in the UK timezone)
        $start = Carbon::parse('2024-01-01 09:00:00', 'Europe/London');
        $end = Carbon::parse('2024-01-01 17:00:00', 'Europe/London');


        // Define the expected calls to the mock repository, this should be called twice
        // The first call should be for clock in and the second call should be for clock out
        // Define the expectation for the mock repository
        $matcher = $this->exactly(2);
        $this->timeRecordRepository->expects($matcher)
            ->method('createTimeRecord')
            ->willReturnCallback(function ($data) use ($start, $end, $matcher) {

                // Define assertion callbacks for each invocation
                $assertionCallback = function (callable $callback) use ($data) {
                    try {
                        $callback($data); // Call the assertion callback
                    } catch (\Throwable $e) {
                        // Log or handle the error as needed
                        throw new \Exception("Assertion failed: " . $e->getMessage());
                    }
                };

                // Match parameters based on the invocation count
                match ($matcher->numberOfInvocations()) {
                    1 => $assertionCallback(function ($data) use ($start) {
                        $this->assertEquals($this->user->id, $data['user_id']);
                        $this->assertInstanceOf(Carbon::class, $data['recorded_at']);
                        $this->assertTrue($data['recorded_at']->eq($start));
                        $this->assertEquals(TimeRecordType::CLOCK_IN, $data['type']);
                    }),

                    2 => $assertionCallback(function ($data) use ($end) {
                        $this->assertEquals($this->user->id, $data['user_id']);
                        $this->assertInstanceOf(Carbon::class, $data['recorded_at']);
                        $this->assertTrue($data['recorded_at']->eq($end));
                        $this->assertEquals(TimeRecordType::CLOCK_IN, $data['type']);
                    }),
                };
            });


        // Create a new instance of TimeRecordService
        $timeRecordService = new TimeRecordService($this->timeRecordRepository);

        // Set the current time to the start of the day
        Carbon::setTestNow($start);

        // Call the handleClock method to clock in
        $timeRecordService->handleClock($this->user->id, 'Europe/London');

        // Mock the time for clock out
        Carbon::setTestNow($end);

        // Create a mock TimeRecord object with the type of TimeRecordType::CLOCK_IN
        $clock__in_mock = $this->createMock(TimeRecord::class);
        $clock__in_mock->type = TimeRecordType::CLOCK_IN;

        // Mock the getLastRecordForUser method to return the mock TimeRecord object
        $this->timeRecordRepository->method('getLastRecordForUser')->willReturn($clock__in_mock);

        // Call the handleClock method to clock out
        $timeRecordService->handleClock($this->user->id, 'Europe/London');
    }





}
