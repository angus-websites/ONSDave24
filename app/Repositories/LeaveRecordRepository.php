<?php

namespace App\Repositories;

use App\Contracts\LeaveRecordRepositoryInterface;
use App\Models\LeaveRecord;
use Illuminate\Support\Collection;

/**
 * The LeaveRecordRepository class is responsible for handling database operations
 * related to leave records. It is a concrete implementation of the LeaveRecordRepositoryInterface.
 */
class LeaveRecordRepository implements LeaveRecordRepositoryInterface
{
    /**
     * Create a new leave record
     * @param array $data
     * @return mixed
     */
    public function createLeaveRecord(array $data)
    {
        return LeaveRecord::create($data);
    }

    /**
     * Delete a leave record by its ID
     * @param int $leaveRecordId
     * @return int
     */
    public function deleteLeaveRecord(int $leaveRecordId): int
    {
        return LeaveRecord::destroy($leaveRecordId);
    }

    /**
     * Get all leave records for a user
     * @param int $userId
     * @return Collection
     */
    public function getAllLeaveRecordsForUser(int $userId): Collection
    {
        return LeaveRecord::where('user_id', $userId)->get();
    }
}

