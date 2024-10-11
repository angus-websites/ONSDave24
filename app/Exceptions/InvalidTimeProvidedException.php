<?php

namespace App\Exceptions;

use Exception;

class InvalidTimeProvidedException extends Exception
{
    protected $message = 'User provided time must be after the last time record';
}
