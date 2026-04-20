<?php

namespace App\Http\Controllers;

use App\Models\Checkinout;
use App\Models\Dept;
use App\Models\Userinfo;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DtrController extends Controller
{
    /**
     * Display the DTR generator UI.
     */
    public function index()
    {
        return inertia('Dtr/Index', [
            'users' => Userinfo::orderBy('Name')->get(['Userid', 'Name', 'Deptid']),
            'departments' => Dept::orderBy('DeptName')->get(['Deptid', 'DeptName']),
        ]);
    }

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
        $month = $request->month;
        $year = $request->year;

        return $this->generatePdf($userid, $month, $year);
    }

    /**
     * Bulk export DTRs for a whole department.
     */
    public function bulkExport(Request $request)
    {
        $request->validate([
            'dept_id' => 'required|exists:dept,Deptid',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer',
        ]);

        $deptId = $request->dept_id;
        $month = $request->month;
        $year = $request->year;

        $users = Userinfo::where('Deptid', $deptId)->get();

        if ($users->isEmpty()) {
            return back()->with('error', 'No employees found in this department.');
        }

        $allDtrData = [];
        foreach ($users as $user) {
            $allDtrData[] = $this->prepareDtrData($user->Userid, $month, $year);
        }

        $pdf = Pdf::loadView('pdf.dtr_bulk', [
            'usersData' => $allDtrData,
            'monthName' => Carbon::createFromDate($year, $month, 1)->format('F'),
            'year' => $year,
        ]);

        return $pdf->download("DTR_Bulk_Dept_{$deptId}_{$year}_{$month}.pdf");
    }

    private function generatePdf($userid, $month, $year)
    {
        $data = $this->prepareDtrData($userid, $month, $year);

        $pdf = Pdf::loadView('pdf.dtr', [
            'user' => $data['user'],
            'monthName' => $data['monthName'],
            'year' => $data['year'],
            'dtrData' => $data['dtrData'],
        ]);

        return $pdf->setPaper('a4')->download("DTR_{$data['user']->Name}_{$year}_{$month}.pdf");
    }

    private function prepareDtrData($userid, $month, $year)
    {
        $user = Userinfo::with('dept')->where('Userid', $userid)->firstOrFail();
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $logs = Checkinout::where('Userid', $userid)
            ->whereBetween('CheckTime', [$startDate->toDateTimeString(), $endDate->toDateTimeString()])
            ->orderBy('CheckTime')
            ->get();

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

            // Better heuristic:
            // AM In: earliest punch before 10 AM
            // AM Out: first punch between 10 AM and 1 PM
            // PM In: first punch between 12 PM and 3 PM (if AM Out is before this)
            // PM Out: latest punch after 3 PM

            $am_in = $dayLogs->filter(fn($l) => Carbon::parse($l->CheckTime)->hour < 10)->first();
            $am_out = $dayLogs->filter(fn($l) => Carbon::parse($l->CheckTime)->hour >= 10 && Carbon::parse($l->CheckTime)->hour < 13)->first();
            $pm_in = $dayLogs->filter(fn($l) => Carbon::parse($l->CheckTime)->hour >= 12 && Carbon::parse($l->CheckTime)->hour < 15)->first();
            $pm_out = $dayLogs->filter(fn($l) => Carbon::parse($l->CheckTime)->hour >= 15)->last();

            if ($am_in) {
                $dtrData[$day]['am_in'] = Carbon::parse($am_in->CheckTime)->format('H:i');
            }
            if ($am_out) {
                $dtrData[$day]['am_out'] = Carbon::parse($am_out->CheckTime)->format('H:i');
            }
            if ($pm_in) {
                $dtrData[$day]['pm_in'] = Carbon::parse($pm_in->CheckTime)->format('H:i');
            }
            if ($pm_out) {
                $dtrData[$day]['pm_out'] = Carbon::parse($pm_out->CheckTime)->format('H:i');
            }
        }

        return [
            'user' => $user,
            'monthName' => $startDate->format('F'),
            'year' => $year,
            'dtrData' => $dtrData,
        ];
    }
}
