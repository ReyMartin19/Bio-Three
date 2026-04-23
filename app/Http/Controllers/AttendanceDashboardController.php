<?php

namespace App\Http\Controllers;

use App\Models\AttendanceSummary;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AttendanceDashboardController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->input('user_id'); 
        
        $summariesQuery = AttendanceSummary::with('userinfo');
        
        if ($userId) {
            $summariesQuery->where('userid', $userId);
            $summariesQuery->orderBy('record_date', 'desc');
        } else {
            $summariesQuery->orderBy('record_date', 'desc');
        }

        $summaries = $summariesQuery->paginate(20);

        return Inertia::render('Attendance/Dashboard', [
            'summaries' => $summaries
        ]);
    }
}
