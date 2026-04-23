<?php

namespace Database\Seeders;

use App\Models\AttendanceRuleExt;
use Illuminate\Database\Seeder;

class AttendanceRuleExtSeeder extends Seeder
{
    public function run(): void
    {
        $rules = [
            ['rule_key' => 'MONDAY_CUTOFF', 'rule_value' => '08:00:00', 'description' => 'Mandatory Monday time-in for Flag Ceremony'],
            ['rule_key' => 'GRACE_PERIOD_MINS', 'rule_value' => '15', 'description' => 'Default grace period before considered LATE'],
            ['rule_key' => 'MIN_WORK_HOURS', 'rule_value' => '8', 'description' => 'Minimum hours required to avoid UNDERTIME'],
        ];

        foreach ($rules as $rule) {
            AttendanceRuleExt::firstOrCreate(
                ['rule_key' => $rule['rule_key']],
                ['rule_value' => $rule['rule_value'], 'description' => $rule['description']]
            );
        }
    }
}
