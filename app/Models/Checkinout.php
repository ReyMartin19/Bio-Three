<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Checkinout extends Model
{
    use HasFactory;

    protected $table = 'checkinout';

    protected $primaryKey = 'Logid';

    public $timestamps = false;

    protected $fillable = [
        'Userid',
        'CheckTime',
        'CheckType',
        'Sensorid',
        'WorkType',
        'AttFlag',
        'Checked',
        'Exported',
        'OpenDoorFlag',
    ];

    protected $casts = [
        'CheckTime' => 'datetime',
        'Checked' => 'boolean',
        'Exported' => 'boolean',
        'OpenDoorFlag' => 'boolean',
    ];

    /**
     * Get the user that owns the checkinout.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(Userinfo::class, 'Userid', 'Userid');
    }
}
