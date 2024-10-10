<?php

namespace App\Models;

use App\Enums\TimeRecordType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeRecord extends Model
{
    use HasFactory;

    public static int $minimumSessionSeconds = 30;

    protected $fillable = [
        'user_id', 'recorded_at', 'type', 'notes',
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
