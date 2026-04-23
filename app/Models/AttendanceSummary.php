<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceSummary extends Model
{
    protected $table = 'attendance_summaries';

    protected $fillable = [
        'userid',
        'record_date',
        'time_in',
        'time_out',
        'total_hours',
        'is_late',
        'late_minutes',
        'is_undertime',
        'undertime_minutes',
        'status',
    ];

    protected $casts = [
        'record_date' => 'date',
        'is_late' => 'boolean',
        'is_undertime' => 'boolean',
    ];

    public function userinfo(): BelongsTo
    {
        return $this->belongsTo(Userinfo::class, 'userid', 'Userid');
    }
}
