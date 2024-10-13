<?php

namespace App\Exceptions;

use Exception;

class InvalidTimeProvidedException extends Exception
{
    protected $message = 'User provided clock time must be after the last time record';
}
