<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Dept, \App\Models\Userinfo>
     */
    public function dept()
    {
        return $this->belongsTo(Dept::class, 'Deptid', 'Deptid');
    }
}
