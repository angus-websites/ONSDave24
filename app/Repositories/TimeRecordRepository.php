<?php

namespace App\Repositories;

use App\Models\TimeRecord;
use App\Contracts\TimeRecordRepositoryInterface;
use Illuminate\Support\Collection;

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

    public function getAllRecordsForUser(int $userId): Collection
    {
        return TimeRecord::where('user_id', $userId)->get();
    }
}

