<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dept extends Model
{
    use HasFactory;

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
     * @return HasMany<Userinfo, Dept>
     */
    public function users()
    {
        return $this->hasMany(Userinfo::class, 'Deptid', 'Deptid');
    }
}
