<?php

namespace App\Console\Commands;

use App\Jobs\UpdateAttendanceSummary;
use App\Models\CheckInOutExt;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SyncBiometricsCommand extends Command
{
    protected $signature = 'sync:biometrics';

    protected $description = 'Sync raw biometrics to compute attendance summaries';

    public function handle(): void
    {
        $this->info('Starting biometric sync...');

        $unsyncedLogs = DB::table('checkinout')
            ->leftJoin('check_in_out_exts', 'checkinout.Logid', '=', 'check_in_out_exts.logid')
            ->whereNull('check_in_out_exts.logid')
            ->orWhere('check_in_out_exts.is_synced', false)
            ->limit(1000)
            ->get(['checkinout.Logid', 'checkinout.Userid', 'checkinout.CheckTime']);

        if ($unsyncedLogs->isEmpty()) {
            $this->info('No new biometric logs to process.');

            return;
        }

        $userIdsDates = [];

        foreach ($unsyncedLogs as $log) {
            $dateStr = Carbon::parse($log->CheckTime)->toDateString();
            $key = $log->Userid.'|'.$dateStr;
            $userIdsDates[$key] = [
                'userid' => $log->Userid,
                'date' => $dateStr,
            ];

            CheckInOutExt::updateOrCreate(
                ['logid' => $log->Logid],
                ['is_synced' => true]
            );
        }

        foreach ($userIdsDates as $data) {
            UpdateAttendanceSummary::dispatch($data['userid'], $data['date']);
        }

        $this->info(count($unsyncedLogs).' logs processed. Dispatched '.count($userIdsDates).' summary computing jobs.');
    }
}
