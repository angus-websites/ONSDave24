<?php

namespace App\Contracts;

/**
 * The TimeRecordRepositoryInterface is a contract that defines the methods that should be implemented
 * for a TimeRecordRepository, this is used to easily switch between different implementations of the
 * TimeRecordRepository class and also mock the repository in tests.
 */
interface TimeRecordRepositoryInterface
{
    public function createTimeRecord(array $data);

    public function getLastRecordForUser(int $userId);

    public function getAllRecordsForUser(int $userId);
}
