<?php

namespace App\Http\Controllers;

use App\Services\LeaveRecordService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveRecordController extends Controller
{

    public LeaveRecordService $leaveRecordService;

    public function __construct(LeaveRecordService $leaveRecordService)
    {
        $this->leaveRecordService = $leaveRecordService;
    }

    /**
     * @throws Exception
     */
    public function addLeave(Request $request)
    {

        // Validate the request inputs
        $validated = $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|before_or_equal:end_date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'notes' => 'nullable|string|max:255',
        ]);

        // Get the authenticated user ID
        $userId = Auth::id();

        // Convert the date strings to Carbon objects
        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);

        // Use the service to add the leave record
        $this->leaveRecordService->addLeaveRecord($userId, $validated['leave_type_id'], $startDate, $endDate, $validated['notes']);


    }

}
