<?php

namespace App\Jobs;

use App\Models\AttendanceSummary;
use App\Models\Userinfo;
use App\Services\AttendanceService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Carbon;

class UpdateAttendanceSummary implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $userId,
        public string $dateStr
    ) {}

    public function handle(AttendanceService $service): void
    {
        $user = Userinfo::find($this->userId);
        if (! $user) {
            return;
        }

        $date = Carbon::parse($this->dateStr);
        $summaryData = $service->calculateDailySummary($user, $date);

        AttendanceSummary::updateOrCreate(
            ['userid' => $this->userId, 'record_date' => $summaryData['record_date']],
            $summaryData
        );
    }
}
