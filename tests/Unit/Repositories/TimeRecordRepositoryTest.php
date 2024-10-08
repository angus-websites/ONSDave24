<?php

namespace Tests\Unit\Repositories;

use App\Contracts\TimeRecordRepositoryInterface;
use App\Enums\TimeRecordType;
use App\Models\TimeRecord;
use App\Models\User;
use App\Repositories\TimeRecordRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TimeRecordRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected TimeRecordRepositoryInterface $timeRecordRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->timeRecordRepository = new TimeRecordRepository();
    }

    public function testCreateTimeRecord()
    {
        $user = User::factory()->create();
        $data = [
            'user_id' => $user->id,
            'recorded_at' => now(),
            'type' => TimeRecordType::CLOCK_IN,
            'notes' => 'Test note',
        ];

        $timeRecord = $this->timeRecordRepository->createTimeRecord($data);

        $this->assertDatabaseHas('time_records', $data);
        $this->assertInstanceOf(TimeRecord::class, $timeRecord);
    }

}
