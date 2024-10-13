<?php

namespace App\Exceptions;

use Exception;

class InvalidLeaveDateProvidedException extends Exception
{
    protected $message = 'The leave end date must be after the start date';
}
