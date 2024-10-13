<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use DateTimeZone;

class ValidTimezone implements Rule
{
    public function passes($attribute, $value): bool
    {
        return in_array($value, DateTimeZone::listIdentifiers());
    }

    public function message(): string
    {
        return 'The :attribute must be a valid timezone.';
    }
}
