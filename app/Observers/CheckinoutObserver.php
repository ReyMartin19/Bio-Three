<?php

namespace App\Observers;

use App\Jobs\UpdateAttendanceSummary;
use App\Models\Checkinout;
use App\Models\CheckInOutExt;
use Illuminate\Support\Carbon;

class CheckinoutObserver
{
    public function created(Checkinout $checkinout): void
    {
        CheckInOutExt::updateOrCreate(
            ['logid' => $checkinout->Logid],
            ['is_synced' => true]
        );

        $dateStr = Carbon::parse($checkinout->CheckTime)->toDateString();
        UpdateAttendanceSummary::dispatch($checkinout->Userid, $dateStr);
    }
}
