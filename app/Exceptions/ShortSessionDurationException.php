<?php

namespace App\Exceptions;

use Exception;

class ShortSessionDurationException extends Exception
{
    protected $message = 'The session duration is too short to record';
}
