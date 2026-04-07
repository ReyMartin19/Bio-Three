<?php

namespace App\Http\Controllers;

use App\Models\Checkinout;
use App\Models\Userinfo;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        return inertia('Dashboard', [
            'users' => Userinfo::with('dept')->get(),
            'checkinouts' => Checkinout::with('user')->latest('CheckTime')->take(100)->get(),
        ]);
    }
}
