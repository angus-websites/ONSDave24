<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidTimeProvidedException;
use App\Exceptions\ShortSessionDurationException;
use App\Rules\ValidTimezone;
use App\Services\TimeRecordService;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TimeRecordController extends Controller
{

    public TimeRecordService $timeRecordService;

    public function __construct(TimeRecordService $timeRecordService)
    {
        $this->timeRecordService = $timeRecordService;
    }

    /**
     * @throws Exception
     */
    public function handleClock(Request $request)
    {

        // Validate the request inputs
        $validated = $request->validate([
            'time' => 'nullable|date',
            'location' => ['nullable', 'string', 'max:255', new ValidTimezone],
        ]);

        // Get the authenticated user ID
        $userId = Auth::id();

        // Extract inputs with defaults and type casting
        $time = isset($validated['time']) ? new Carbon($validated['time']) : null;
        $location = $validated['location'] ?? 'Europe/London';

        // Catch and handle service exceptions
        try {
            $this->timeRecordService->handleClock($userId, $location, $time);
        }
        catch (InvalidTimeProvidedException$e) {
            throw ValidationException::withMessages([
                'time' => "The time provided is before the last clock in/out time",
            ]);
        }
        catch (ShortSessionDurationException $e) {
            throw ValidationException::withMessages([
                'time' => "The session duration was too short, it was deleted",
            ]);
        }

        // Return a success response
        return response()->json(['message' => 'Clock operation successful.'], 200);
    }

}
