<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserInfoExt extends Model
{
    protected $table = 'user_info_exts';

    protected $primaryKey = 'userid';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'userid',
        'type_id',
        'unit_id',
        'position_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function userinfo(): BelongsTo
    {
        return $this->belongsTo(Userinfo::class, 'userid', 'Userid');
    }

    public function employmentType(): BelongsTo
    {
        return $this->belongsTo(EmploymentTypeExt::class, 'type_id', 'type_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(UnitExt::class, 'unit_id', 'unit_id');
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(PositionExt::class, 'position_id', 'position_id');
    }
}
