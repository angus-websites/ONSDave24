<?php

namespace Tests\Feature\Services;

use App\Contracts\TimeRecordRepositoryInterface;
use App\Enums\TimeRecordType;
use App\Services\TimeRecordService;
use Database\Factories\UserFactory;
use PHPUnit\Framework\MockObject\Exception;
use Tests\TestCase;


class TimeRecordServiceTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testClockIn()
    {
        // Create a mock TimeRecordRepositoryInterface
        $timeRecordRepository = $this->createMock(TimeRecordRepositoryInterface::class);

        // Create a new instance of TimeRecordService
        $timeRecordService = new TimeRecordService($timeRecordRepository);

        // Create a user with the UserFactory
        $user = UserFactory::new()->create();

        // Call the clockIn method with the user id
        $timeRecordService->clockIn($user->id);

        // Assert the database has a record of the user clocking in
        $this->assertDatabaseHas('time_records', [
            'user_id' => $user->id,
            'recorded_at' => now()->toDateTimeString(),
            'type' => TimeRecordType::CLOCK_IN,
        ]);




    }
}
