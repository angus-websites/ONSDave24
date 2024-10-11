<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidTimeProvidedException;
use App\Exceptions\ShortSessionDurationException;
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
        $userId = Auth::id();

        // Extract inputs with defaults and type casting
        $time = $request->input('time') ? new Carbon($request->input('time')) : null;
        $location = $request->input('location', 'Europe/London');

        // Catch and handle service exceptions
        try {
            $this->timeRecordService->handleClock($userId, $location, $time);
        } catch (InvalidTimeProvidedException|ShortSessionDurationException $e) {
            return response()->json(['message' => $e->getMessage()], 422); // Unprocessable Entity
        } catch (Exception $e) {
            return response()->json(['message' => 'An unexpected error occurred'], 500); // General fallback
        }

        // Return a success response
        return response()->json(['message' => 'Clock operation successful.'], 200);
    }

}
