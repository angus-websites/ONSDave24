<?php

namespace App\Services;
use App\Contracts\LeaveRecordRepositoryInterface;
use App\Enums\LeaveRecordType;
use Carbon\Carbon;
use Exception;

class LeaveRecordService
{
    protected LeaveRecordRepositoryInterface $leaveRecordRepository;

    public function __construct(LeaveRecordRepositoryInterface $leaveRecordRepository)
    {
        $this->leaveRecordRepository = $leaveRecordRepository;
    }

    /**
     * Add a leave record for the given user
     * @param int $userId
     * @param LeaveRecordType $leaveType
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return void
     * @throws Exception
     */
    public function addLeaveRecord(int $userId, LeaveRecordType $leaveType, Carbon $startDate, Carbon $endDate): void
    {

        // Validate that end date is after start date
        if ($endDate->lt($startDate)) {
            throw new Exception("End date must be after start date");
        }

        $this->leaveRecordRepository->createLeaveRecord(
            [
                'user_id' => $userId,
                'leave_type' => $leaveType,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]
        );
    }

    /**
     * Delete a leave record by its ID
     * @param int $leaveRecordId
     * @return void
     */
    public function deleteLeaveRecord(int $leaveRecordId): void
    {
        $this->leaveRecordRepository->deleteLeaveRecord($leaveRecordId);
    }
}
