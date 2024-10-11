<?php

namespace App\Http\Controllers;

use App\Services\TimeRecordService;
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
        // Get the authenticated user ID
        $userId = Auth::user()->id;

        // Get the optional user-provided time from the request body
        $time = $request->input('time');

        // If time is provided, convert it to a Carbon instance; else set to null
        $time = $time ? new Carbon($time) : null;

        // Get the user location from the request body, defaulting to 'Europe/London' if not provided
        $location = $request->input('location', 'Europe/London');

        // Catch any exceptions thrown by the service
        try {
            // Use the service to handle the clock operation
            $this->timeRecordService->handleClock($userId, $location, $time);
        } catch (Exception $e) {
            // Return a response with the exception message
            return response()->json(['message' => $e->getMessage()], 400);
        }

        // Return a response if needed (e.g., success message)
        return response()->json(['message' => 'Clock operation successful.'], 200);
    }
}
