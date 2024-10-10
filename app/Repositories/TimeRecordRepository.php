<?php

namespace App\Repositories;

use App\Contracts\TimeRecordRepositoryInterface;
use App\Models\TimeRecord;
use Illuminate\Support\Collection;

/**
 * The TimeRecordRepository class is responsible for handling database operations
 * related to time records. It is a concrete implementation of the TimeRecordRepositoryInterface.
 */
class TimeRecordRepository implements TimeRecordRepositoryInterface
{
    public function createTimeRecord(array $data)
    {
        return TimeRecord::create($data);
    }

    public function getLastRecordForUser(int $userId): ?TimeRecord
    {
        return TimeRecord::where('user_id', $userId)->latest()->first();
    }

    public function removeLastRecordForUser(int $userId): void
    {
        $lastRecord = $this->getLastRecordForUser($userId);
        $lastRecord?->delete();
    }

    public function getAllRecordsForUser(int $userId): Collection
    {
        return TimeRecord::where('user_id', $userId)->get();
    }
}
