<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkArrangementExt extends Model
{
    protected $table = 'work_arrangement_exts';

    protected $primaryKey = 'arrangement_id';

    protected $fillable = [
        'userid',
        'arrangement_type',
        'schclassid',
        'covered_period_start',
        'covered_period_end',
        'preferred_wfh_days',
        'status',
        'recommended_by',
        'approved_by',
    ];

    protected $casts = [
        'covered_period_start' => 'date',
        'covered_period_end' => 'date',
    ];

    public function userinfo(): BelongsTo
    {
        return $this->belongsTo(Userinfo::class, 'userid', 'Userid');
    }
}
