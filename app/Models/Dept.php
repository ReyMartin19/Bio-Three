<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dept extends Model
{
    protected $table = 'dept';

    protected $primaryKey = 'Deptid';

    public $timestamps = false;

    protected $fillable = [
        'Deptid',
        'DeptName',
        'SupDeptid',
    ];

    /**
     * Get the users who belong to this department.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Userinfo, \App\Models\Dept>
     */
    public function users()
    {
        return $this->hasMany(Userinfo::class, 'Deptid', 'Deptid');
    }
}
