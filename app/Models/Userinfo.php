<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Userinfo extends Model
{
    protected $table = 'userinfo';

    protected $primaryKey = 'Userid';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'Userid',
        'UserCode',
        'Name',
        'Sex',
        'Pwd',
        'Deptid',
        'Nation',
        'Birthday',
        'EmployDate',
        'Telephone',
        'Duty',
        'NativePlace',
        'IDCard',
        'Address',
        'Mobile',
        'Educated',
        'Polity',
        'Specialty',
        'IsAtt',
        'Isovertime',
        'Isrest',
        'Remark',
        'MgFlag',
        'CardNum',
        'Picture',
        'UserFlag',
        'Groupid',
        'ClassFlag',
        'OtherInfo',
        'admingroupid',
    ];

    /**
     * Get the department that the user belongs to.
     *
     * @return BelongsTo<Dept, Userinfo>
     */
    public function dept()
    {
        return $this->belongsTo(Dept::class, 'Deptid', 'Deptid');
    }

    /**
     * Get the biometric punches for this user.
     *
     * @return HasMany<Checkinout, Userinfo>
     */
    public function punches()
    {
        return $this->hasMany(Checkinout::class, 'Userid', 'Userid');
    }
}
