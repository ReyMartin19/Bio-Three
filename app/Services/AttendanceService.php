<?php

namespace App\Services;

use App\Models\Userinfo;
use App\Models\WorkArrangementExt;
use Illuminate\Support\Carbon;

class AttendanceService
{
    public function calculateDailySummary(Userinfo $user, Carbon $date): array
    {
        $punches = $user->punches()
            ->whereDate('CheckTime', $date->toDateString())
            ->orderBy('CheckTime', 'asc')
            ->get();

        if ($punches->isEmpty()) {
            return [
                'userid' => $user->Userid,
                'record_date' => $date->toDateString(),
                'time_in' => null,
                'time_out' => null,
                'total_hours' => 0,
                'is_late' => false,
                'late_minutes' => 0,
                'is_undertime' => false,
                'undertime_minutes' => 0,
                'status' => 'Absent',
            ];
        }

        $timeIn = Carbon::parse($punches->first()->CheckTime);
        $timeOut = $punches->count() > 1 ? Carbon::parse($punches->last()->CheckTime) : null;

        $computableTimeIn = $timeIn->copy();
        $startTimeLimit = Carbon::parse($date->toDateString() . ' 07:00:00');
        if ($computableTimeIn->lessThan($startTimeLimit)) {
            $computableTimeIn = $startTimeLimit;
        }

        $computableTimeOut = $timeOut ? $timeOut->copy() : null;
        $endTimeLimit = Carbon::parse($date->toDateString() . ' 18:00:00');
        if ($computableTimeOut && $computableTimeOut->greaterThan($endTimeLimit)) {
            $computableTimeOut = $endTimeLimit;
        }

        $arrangement = WorkArrangementExt::where('userid', $user->Userid)
            ->where('status', 'Approved')
            ->where(function ($query) use ($date) {
                $query->whereNull('covered_period_start')
                    ->orWhere(function ($q) use ($date) {
                        $q->where('covered_period_start', '<=', $date->toDateString())
                            ->where('covered_period_end', '>=', $date->toDateString());
                    });
            })
            ->first();

        $arrangementType = $arrangement ? $arrangement->arrangement_type : 'Fixed Flexi';
        $schClassId = $arrangement ? $arrangement->schclassid : 2; // Default to 8-5 (schclassid = 2)

        $lateThreshold = Carbon::parse($date->toDateString().' 08:15:00');
        $baseTimeForLate = Carbon::parse($date->toDateString().' 08:00:00');

        if ($date->isMonday()) {
            $lateThreshold = Carbon::parse($date->toDateString().' 08:00:00');
            $baseTimeForLate = $lateThreshold->copy();
        } else {
            if ($arrangementType === 'Full Flexi') {
                $lateThreshold = Carbon::parse($date->toDateString().' 09:15:00');
            } elseif ($arrangementType === 'Fixed Flexi') {
                if ($schClassId == 1) {
                    $lateThreshold = Carbon::parse($date->toDateString().' 07:15:00');
                }
                if ($schClassId == 2) {
                    $lateThreshold = Carbon::parse($date->toDateString().' 08:15:00');
                }
                if ($schClassId == 3) {
                    $lateThreshold = Carbon::parse($date->toDateString().' 09:15:00');
                }
            } elseif ($arrangementType === 'WFH') {
                $lateThreshold = Carbon::parse($date->toDateString().' 08:15:00');
            }
            $baseTimeForLate = $lateThreshold->copy()->subMinutes(15);
        }

        $isLate = false;
        $lateMinutes = 0;

        if ($timeIn->greaterThan($lateThreshold)) {
            $isLate = true;
            $lateMinutes = (int) $timeIn->diffInMinutes($baseTimeForLate, true);
        }

        $totalHours = 0;
        $isUndertime = false;
        $undertimeMinutes = 0;

        if ($computableTimeOut) {
            $rawHours = $computableTimeIn->diffInMinutes($computableTimeOut) / 60;
            $workHours = max(0, $rawHours > 5 ? $rawHours - 1 : $rawHours);
            $totalHours = round($workHours, 2);

            if ($totalHours < 8) {
                $isUndertime = true;
                $undertimeMinutes = (int) ((8 - $totalHours) * 60);
            }
        } else {
            $isUndertime = true;
            $undertimeMinutes = 8 * 60;
        }

        $status = 'Present';
        if ($isLate && $isUndertime) {
            $status = 'Late/Undertime';
        } elseif ($isLate) {
            $status = 'Late';
        } elseif ($isUndertime) {
            $status = 'Undertime';
        }

        if (! $timeOut) {
            $status = 'Incomplete';
        }

        return [
            'userid' => $user->Userid,
            'record_date' => $date->toDateString(),
            'time_in' => $timeIn->toTimeString(),
            'time_out' => $timeOut ? $timeOut->toTimeString() : null,
            'total_hours' => $totalHours,
            'is_late' => $isLate,
            'late_minutes' => $lateMinutes,
            'is_undertime' => $isUndertime,
            'undertime_minutes' => $undertimeMinutes,
            'status' => $status,
        ];
    }
}
