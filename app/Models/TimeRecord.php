<?php

namespace App\Models;

use App\Enums\TimeRecordType;
use Illuminate\Database\Eloquent\Model;

class TimeRecord extends Model
{
    protected $fillable = [
        'employee_id', 'recorded_at', 'type', 'notes',
    ];

    protected $casts = [
        'type' => TimeRecordType::class,
        'recorded_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
