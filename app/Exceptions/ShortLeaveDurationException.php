<?php

namespace App\Exceptions;

use App\Models\LeaveRecord;
use Exception;

class ShortLeaveDurationException extends Exception
{
    public function __construct()
    {
        parent::__construct();
        $this->message = sprintf('The leave duration is too short, minimum is %d days', LeaveRecord::$minimumLeaveDuration);
    }
}
