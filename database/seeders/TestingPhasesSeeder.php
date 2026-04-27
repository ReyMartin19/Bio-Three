<?php

namespace Database\Seeders;

use App\Models\Checkinout;
use App\Models\Dept;
use App\Models\Userinfo;
use App\Models\WorkArrangementExt;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class TestingPhasesSeeder extends Seeder
{
    public function run(): void
    {
        $depts = [
            'IT Department',
            'HR Department',
            'Finance',
            'Engineering',
        ];

        $users = [];

        foreach ($depts as $idx => $deptName) {
            $dept = Dept::factory()->create(['Deptid' => 101 + $idx, 'DeptName' => $deptName]);

            // Add User 1: Full Flexi Setup
            $u1 = Userinfo::factory()->create([
                'Deptid' => $dept->Deptid,
                'Name' => fake()->firstName().' '.fake()->lastName().' (Full Flexi)',
            ]);

            WorkArrangementExt::create([
                'userid' => $u1->Userid,
                'arrangement_type' => 'Full Flexi',
                'status' => 'Approved',
            ]);

            // Add User 2: Fixed Flexi 8-5
            $u2 = Userinfo::factory()->create([
                'Deptid' => $dept->Deptid,
                'Name' => fake()->firstName().' '.fake()->lastName().' (Fixed 8-5)',
            ]);

            WorkArrangementExt::create([
                'userid' => $u2->Userid,
                'arrangement_type' => 'Fixed Flexi',
                'schclassid' => 2, // 8-5
                'status' => 'Approved',
            ]);

            $users[] = clone $u1;
            $users[] = clone $u2;
        }

        // Seed Biometric Logs for a whole month so DTR looks good
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->yesterday(); // up to yesterday

        if (Carbon::now()->day < 5) {
            // if it's too early in the current month, use last month to have more data
            $startOfMonth = Carbon::now()->subMonth()->startOfMonth();
            $endOfMonth = Carbon::now()->subMonth()->endOfMonth();
        }

        foreach ($users as $user) {
            $currentDate = $startOfMonth->copy();

            while ($currentDate <= $endOfMonth) {
                // Skip weekends
                if ($currentDate->isWeekend()) {
                    $currentDate->addDay();

                    continue;
                }

                // AM In
                Checkinout::factory()->create([
                    'Userid' => $user->Userid,
                    'CheckTime' => $currentDate->copy()->setTime(fake()->numberBetween(7, 8), fake()->numberBetween(0, 59)),
                    'CheckType' => 'I',
                ]);

                // AM Out (Lunch Out)
                Checkinout::factory()->create([
                    'Userid' => $user->Userid,
                    'CheckTime' => $currentDate->copy()->setTime(12, fake()->numberBetween(0, 15)),
                    'CheckType' => 'O',
                ]);

                // PM In (Lunch In)
                Checkinout::factory()->create([
                    'Userid' => $user->Userid,
                    'CheckTime' => $currentDate->copy()->setTime(12, fake()->numberBetween(45, 59)),
                    'CheckType' => 'I',
                ]);

                // PM Out
                Checkinout::factory()->create([
                    'Userid' => $user->Userid,
                    'CheckTime' => $currentDate->copy()->setTime(fake()->numberBetween(16, 17), fake()->numberBetween(0, 59)),
                    'CheckType' => 'O',
                ]);

                $currentDate->addDay();
            }
        }
    }
}
