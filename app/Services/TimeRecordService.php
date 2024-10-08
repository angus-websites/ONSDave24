<?php

namespace App\Services;

use App\Contracts\TimeRecordRepositoryInterface;
use Carbon\Carbon;

/**
 * The TimeRecordService class is responsible for handling the business logic
 * related to time records.
 */
class TimeRecordService
{
    protected TimeRecordRepositoryInterface $timeRecordRepository;

    public function __construct(TimeRecordRepositoryInterface $timeRecordRepository)
    {
        $this->timeRecordRepository = $timeRecordRepository;
    }

    public function clockIn(int $userId)
    {
        // Pass
    }

    public function clockOut(int $userId)
    {
        // Pass
    }
}
