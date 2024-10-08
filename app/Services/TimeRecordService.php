<?php

namespace App\Services;

use App\Contracts\TimeRecordRepositoryInterface;
use App\Enums\TimeRecordType;
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

    /**
     * Handle the clock in/out operation for the given user
     * @param int $userId
     * @param string $userLocation
     * @param Carbon|null $userProvidedTime
     * @return void
     */
    public function handleClock(int $userId, string $userLocation, ?Carbon $userProvidedTime = null)
    {
        // Pass
    }

    /**
     * Clock in the user.
     * @param int $userId
     * @return void
     */
    public function clockIn(int $userId): void
    {
        // Use the timeRecordRepository to clock in the user
        $this->timeRecordRepository->createTimeRecord(
            [
                'user_id' => $userId,
                'recorded_at' => now(),
                'type' => TimeRecordType::CLOCK_IN,
            ]
        );
    }

    /**
     * Clock out the user.
     * @param int $userId
     * @return void
     */
    public function clockOut(int $userId): void
    {
        // Use the timeRecordRepository to clock out the user
        $this->timeRecordRepository->createTimeRecord(
            [
                'user_id' => $userId,
                'recorded_at' => now(),
                'type' => TimeRecordType::CLOCK_OUT,
            ]
        );
    }

    /**
     * Check if the user clocks out too soon after clocking in. if so return true.
     * @param Carbon $clockInTime
     * @param Carbon $clockOutTime
     * @return bool
     */
    protected function isSessionDurationTooShort(Carbon $clockInTime, Carbon $clockOutTime): bool
    {
        return $clockInTime->diffInSeconds($clockOutTime) < 10;
    }

    /**
     * Convert the given time to UTC timezone.
     * @param string $location
     * @return Carbon
     */
    protected function convertToUTC(string $location): Carbon
    {
        $localTime = Carbon::now($location);
        return $localTime->setTimezone('UTC');
    }
}
