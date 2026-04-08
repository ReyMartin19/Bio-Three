<?php

namespace App\Http\Controllers;

use App\Models\Userinfo;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        return inertia('Employees/Index', [
            'employees' => Userinfo::with('dept')->get(),
        ]);
    }
}
