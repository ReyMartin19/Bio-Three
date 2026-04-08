<?php

namespace App\Http\Controllers;

use App\Models\Checkinout;
use App\Models\Userinfo;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DtrController extends Controller
{
    /**
     * Export the DTR for a specific user and month.
     */
    public function export(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:userinfo,Userid',
            'month' => 'nullable|integer|between:1,12',
            'year' => 'nullable|integer',
        ]);

        $userid = $request->user_id;
        $month = $request->month ?? Carbon::now()->month;
        $year = $request->year ?? Carbon::now()->year;

        $user = Userinfo::with('dept')->where('Userid', $userid)->firstOrFail();

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $logs = Checkinout::where('Userid', $userid)
            ->whereBetween('CheckTime', [$startDate->toDateTimeString(), $endDate->toDateTimeString()])
            ->orderBy('CheckTime')
            ->get();

        // Group logs by day
        $dtrData = [];
        $daysInMonth = $startDate->daysInMonth;

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::createFromDate($year, $month, $day)->format('Y-m-d');
            $dayLogs = $logs->filter(function ($log) use ($date) {
                return Carbon::parse($log->CheckTime)->format('Y-m-d') === $date;
            });

            $dtrData[$day] = [
                'am_in' => null,
                'am_out' => null,
                'pm_in' => null,
                'pm_out' => null,
            ];

            foreach ($dayLogs as $log) {
                $time = Carbon::parse($log->CheckTime);
                $hour = $time->hour;

                // Simple heuristic for AM/PM slots
                if ($hour < 12) {
                    if ($log->CheckType == 0 && ! $dtrData[$day]['am_in']) {
                        $dtrData[$day]['am_in'] = $time->format('H:i');
                    } elseif ($log->CheckType == 1) {
                        $dtrData[$day]['am_out'] = $time->format('H:i');
                    }
                } else {
                    if ($log->CheckType == 0 || $log->CheckType == 3) {
                        if (! $dtrData[$day]['pm_in']) {
                            $dtrData[$day]['pm_in'] = $time->format('H:i');
                        }
                    } elseif ($log->CheckType == 1 || $log->CheckType == 2) {
                        $dtrData[$day]['pm_out'] = $time->format('H:i');
                    }
                }
            }
        }

        $pdf = Pdf::loadView('pdf.dtr', [
            'user' => $user,
            'monthName' => $startDate->format('F'),
            'year' => $year,
            'dtrData' => $dtrData,
        ]);

        return $pdf->download("DTR_{$user->Name}_{$startDate->format('Y_m')}.pdf");
    }
}
