<?php

namespace App\Services;

use App\Contracts\LeaveRecordRepositoryInterface;
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
     *
     * @throws Exception
     */
    public function addLeaveRecord(int $userId, int $leaveTypeId, Carbon $startDate, Carbon $endDate): void
    {

        // Validate that end date is after start date
        if ($endDate->lt($startDate)) {
            throw new Exception('End date must be after start date');
        }

        $this->leaveRecordRepository->createLeaveRecord(
            [
                'user_id' => $userId,
                'leave_type_id' => $leaveTypeId,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]
        );
    }

    /**
     * Delete a leave record by its ID
     */
    public function deleteLeaveRecord(int $leaveRecordId): void
    {
        $this->leaveRecordRepository->deleteLeaveRecord($leaveRecordId);
    }
}
