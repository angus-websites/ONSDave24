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
        // Get the authenticated user ID
        $userId = Auth::id();

        // Fetch the parameters from the request
        $leaveTypeId = $request->input('leave_type_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $notes = $request->input('notes');

        // Convert the date strings to Carbon objects
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        // Use the service to add the leave record
        $this->leaveRecordService->addLeaveRecord($userId, $leaveTypeId, $startDate, $endDate, $notes);



    }

}
