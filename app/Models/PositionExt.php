<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PositionExt extends Model
{
    protected $table = 'position_exts';

    protected $primaryKey = 'position_id';

    protected $fillable = [
        'unit_id',
        'position_title',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(UnitExt::class, 'unit_id', 'unit_id');
    }
}
