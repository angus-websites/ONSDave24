<?php

namespace App\Services;

use App\Contracts\TimeRecordRepositoryInterface;
use App\Enums\TimeRecordType;
use Carbon\Carbon;
use DateTime;
use Exception;

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
     * Handle the clock in/out operation for the given user,
     * the userProvidedTime is optional and can be used to override the current time
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
     * @param Carbon|null $providedTime
     * @return void
     */
    public function clockIn(int $userId, ?Carbon $providedTime = null): void
    {
        // If the provided time is null, use the current time
        $recordedAt = $providedTime ?? now();

        // Use the timeRecordRepository to clock in the user
        $this->timeRecordRepository->createTimeRecord(
            [
                'user_id' => $userId,
                'recorded_at' => $recordedAt,
                'type' => TimeRecordType::CLOCK_IN,
            ]
        );
    }

    /**
     * Clock out the user.
     * @param int $userId
     * @param Carbon|null $providedTime
     * @return void
     */
    public function clockOut(int $userId, ?Carbon $providedTime = null): void
    {

        $recordedAt = $providedTime ?? now();

        // Use the timeRecordRepository to clock out the user
        $this->timeRecordRepository->createTimeRecord(
            [
                'user_id' => $userId,
                'recorded_at' => $recordedAt,
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
    public function isSessionDurationTooShort(Carbon $clockInTime, Carbon $clockOutTime): bool
    {
        return $clockInTime->diffInSeconds($clockOutTime) < 10;
    }

    /**
     * Convert the provided clock time to UTC based on the user's time zone.
     *
     * @param DateTime $clockTime
     * @param string $userTimeZone
     * @return Carbon
     * @throws Exception
     */
    public function convertToUtc(DateTime $clockTime, String $userTimeZone): Carbon
    {
        // Validate the provided timezone
        if (!in_array($userTimeZone, timezone_identifiers_list())) {
            $userTimeZone = 'Europe/London';
        }

        // Convert the time to UTC
        try {
            return Carbon::parse($clockTime, $userTimeZone)->setTimezone('UTC');
        } catch (Exception $e) {
            throw new Exception("Error converting time to UTC: " . $e->getMessage());
        }
    }
}
