<?php

namespace App\Http\Controllers;

use App\Services\TimeRecordService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

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
        // Get the user
        $userId = $request->user()->id;

        // Get the optional user provided time
        $time = $request->input('time', null);

        // If Time is provided, convert it to a Carbon instance else null
        $time = $time ? new Carbon($time) : null;

        // Get the user location
        $location = $request->input('location', 'Europe/London');

        // Use the service to handle the clock operation
        $this->timeRecordService->handleClock($userId, $location, $time);


    }
}
