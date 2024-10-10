<?php

namespace App\Contracts;

/**
 * The LeaveRecordRepositoryInterface is a contract that defines the methods that should be implemented
 * for a LeaveRecordRepository, this is used to easily switch between different implementations of the
 * LeaveRecordRepository class and also mock the repository in tests.
 */
interface LeaveRecordRepositoryInterface
{
    public function createLeaveRecord(array $data);

    public function getAllLeaveRecordsForUser(int $userId) : iterable;
}
