<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmploymentTypeExt extends Model
{
    protected $table = 'employment_type_exts';

    protected $primaryKey = 'type_id';

    protected $fillable = [
        'type_name',
    ];

    public function userInfos(): HasMany
    {
        return $this->hasMany(UserInfoExt::class, 'type_id', 'type_id');
    }
}
