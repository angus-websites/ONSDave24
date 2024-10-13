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
        $this->$leaveRecordService = $leaveRecordService;
    }

    /**
     * @throws Exception
     */
    public function addLeave(Request $request)
    {
        // Get the authenticated user ID
        $userId = Auth::id();

        
    }

}
