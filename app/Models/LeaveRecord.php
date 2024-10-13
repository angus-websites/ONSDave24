<?php

namespace App\Models;

use Database\Factories\LeaveRecordFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRecord extends Model
{
    /** @use HasFactory<LeaveRecordFactory> */
    use HasFactory;

    public static float $minimumLeaveDuration = 0.5; // Half a day

    protected $fillable = [
        'user_id', 'start_date', 'end_date', 'leave_type_id', 'notes',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }
}

