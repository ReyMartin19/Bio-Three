<?php

namespace App\Http\Controllers;

use App\Models\Checkinout;
use App\Models\Userinfo;
use Illuminate\Http\Request;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $query = Checkinout::with('user')->latest('CheckTime');

        if ($request->filled('start_date')) {
            $query->whereDate('CheckTime', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('CheckTime', '<=', $request->end_date);
        }

        return inertia('Dashboard', [
            'checkinouts' => $query->take(500)->get(),
            'filters' => $request->only(['start_date', 'end_date']),
        ]);
    }
}
