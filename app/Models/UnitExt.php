<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnitExt extends Model
{
    protected $table = 'unit_exts';

    protected $primaryKey = 'unit_id';

    protected $fillable = [
        'dept_id',
        'unit_name',
    ];

    public function dept(): BelongsTo
    {
        return $this->belongsTo(Dept::class, 'dept_id', 'Deptid');
    }

    public function positions(): HasMany
    {
        return $this->hasMany(PositionExt::class, 'unit_id', 'unit_id');
    }
}
