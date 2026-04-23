<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceRuleExt extends Model
{
    protected $table = 'attendance_rule_exts';

    protected $primaryKey = 'rule_key';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'rule_key',
        'rule_value',
        'description',
    ];
}
