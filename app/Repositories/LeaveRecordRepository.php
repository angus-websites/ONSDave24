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
    public function createLeaveRecord(array $data)
    {
        return LeaveRecord::create($data);
    }

    public function getAllLeaveRecordsForUser(int $userId): Collection
    {
        return LeaveRecord::where('user_id', $userId)->get();
    }
}

