<?php

namespace App\Models;

use App\Enums\LeaveRecordType;
use Database\Factories\LeaveRecordFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRecord extends Model
{
    /** @use HasFactory<LeaveRecordFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id', 'start_date', 'end_date', 'leave_type', 'notes',
    ];

    protected $casts = [
        'leave_type' => LeaveRecordType::class,
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

