<?php

namespace App\Services;

use App\Contracts\TimeRecordRepositoryInterface;
use App\Enums\TimeRecordType;
use App\Models\TimeRecord;
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
     * @throws Exception
     */
    public function handleClock(int $userId, string $userLocation, ?Carbon $userProvidedTime = null): void
    {
        // If userProvidedTime is not provided, use the current time
        if ($userProvidedTime === null) {
            $userProvidedTime = Carbon::now();
        }

        // If the userProvidedTime is passed, convert it to UTC
        if ($userProvidedTime !== null) {
            $userProvidedTime = $this->convertToUtc($userProvidedTime, $userLocation);
        }

        // Call clockIn or clockOut based on the user's last time record
        $lastTimeRecord = $this->timeRecordRepository->getLastRecordForUser($userId);

        // If userProvidedTime is provided, it must be after the last time record
        if ($lastTimeRecord !== null && $userProvidedTime !== null && $userProvidedTime->lt($lastTimeRecord->recorded_at)) {
            throw new Exception("User provided time must be after the last time record");
        }

        // If userProvidedTime is provided, use isSessionDurationTooShort to check if the session is too short
        if ($lastTimeRecord !== null && $userProvidedTime !== null && $this->isSessionDurationTooShort($lastTimeRecord->recorded_at, $userProvidedTime)) {
            // Remove the last time record
            $this->timeRecordRepository->removeLastRecordForUser($userId);
            return;
        }

        // If the last time record is clock out or null, clock in the user
        if ($lastTimeRecord === null || $lastTimeRecord->type === TimeRecordType::CLOCK_OUT) {
            $this->clockIn($userId, $userProvidedTime);
        } else {
            // If the last time record is clock in, clock out the user
            $this->clockOut($userId, $userProvidedTime);
        }

    }

    /**
     * Clock in the user.
     * @param int $userId
     * @param Carbon $providedTime
     * @return void
     */
    private function clockIn(int $userId, Carbon $providedTime): void
    {

        // Use the timeRecordRepository to clock in the user
        $this->timeRecordRepository->createTimeRecord(
            [
                'user_id' => $userId,
                'recorded_at' => $providedTime,
                'type' => TimeRecordType::CLOCK_IN,
            ]
        );
    }

    /**
     * Clock out the user.
     * @param int $userId
     * @param Carbon $providedTime
     * @return void
     */
    private function clockOut(int $userId, Carbon $providedTime): void
    {
        // Use the timeRecordRepository to clock out the user
        $this->timeRecordRepository->createTimeRecord(
            [
                'user_id' => $userId,
                'recorded_at' => $providedTime,
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
    private function isSessionDurationTooShort(Carbon $clockInTime, Carbon $clockOutTime): bool
    {
        return $clockInTime->diffInSeconds($clockOutTime) < TimeRecord::$minimumSessionSeconds;
    }

    /**
     * Convert the provided clock time to UTC based on the user's time zone.
     *
     * @param DateTime $clockTime
     * @param string $userTimeZone
     * @return Carbon
     * @throws Exception
     */
    private function convertToUtc(DateTime $clockTime, String $userTimeZone): Carbon
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
