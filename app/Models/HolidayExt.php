<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HolidayExt extends Model
{
    protected $table = 'holiday_exts';

    protected $primaryKey = 'holidayid';

    public $incrementing = false;

    protected $fillable = [
        'holidayid',
        'holiday_type',
        'is_work_suspended',
    ];

    protected $casts = [
        'is_work_suspended' => 'boolean',
    ];
}
