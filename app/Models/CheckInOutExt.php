<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CheckInOutExt extends Model
{
    protected $table = 'check_in_out_exts';

    protected $primaryKey = 'logid';

    public $incrementing = false;

    protected $fillable = [
        'logid',
        'is_synced',
    ];

    protected $casts = [
        'is_synced' => 'boolean',
    ];

    public function checkinout(): BelongsTo
    {
        return $this->belongsTo(Checkinout::class, 'logid', 'Logid');
    }
}
