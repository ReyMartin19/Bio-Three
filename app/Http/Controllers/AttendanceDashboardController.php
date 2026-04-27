<?php

namespace App\Http\Controllers;

use App\Models\AttendanceSummary;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class AttendanceDashboardController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->input('user_id');

        $summariesQuery = AttendanceSummary::with('userinfo');

        if ($userId) {
            $summariesQuery->where('userid', $userId);
        }

        $summaries = $summariesQuery->orderBy('record_date', 'desc')->paginate(20);

        // Dynamic stats for today
        $today = Carbon::today()->toDateString();
        $todayStats = AttendanceSummary::whereDate('record_date', $today);

        $stats = [
            'total_employees' => AttendanceSummary::distinct('userid')->count('userid'),
            'punctual_today' => (clone $todayStats)->where('is_late', false)->where('status', '!=', 'Absent')->count(),
            'flagged_late' => (clone $todayStats)->where('is_late', true)->count(),
            'absences_today' => (clone $todayStats)->where('status', 'Absent')->count(),
            'total_today' => (clone $todayStats)->count(),
        ];

        return Inertia::render('Attendance/Dashboard', [
            'summaries' => $summaries,
            'stats' => $stats,
        ]);
    }
}
