<?php

namespace App\Http\Controllers;

use App\Models\WorkArrangementExt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class WorkArrangementController extends Controller
{
    public function index()
    {
        $arrangements = WorkArrangementExt::with('userinfo')->paginate(15);
        
        return Inertia::render('WorkArrangements/Index', [
            'arrangements' => $arrangements
        ]);
    }

    public function create()
    {
        return Inertia::render('WorkArrangements/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'userid' => 'required|string|exists:userinfo,Userid',
            'arrangement_type' => 'required|in:Full Flexi,Fixed Flexi,WFH',
            'schclassid' => 'nullable|integer',
            'covered_period_start' => 'nullable|date',
            'covered_period_end' => 'nullable|date|after_or_equal:covered_period_start',
            'preferred_wfh_days' => 'nullable|string',
        ]);

        WorkArrangementExt::create($validated);

        return Redirect::route('work-arrangements.index')->with('success', 'Work Arrangement created.');
    }

    public function edit(WorkArrangementExt $workArrangement)
    {
         return Inertia::render('WorkArrangements/Edit', [
             'workArrangement' => $workArrangement
         ]);
    }

    public function update(Request $request, WorkArrangementExt $workArrangement)
    {
        $validated = $request->validate([
            'arrangement_type' => 'required|in:Full Flexi,Fixed Flexi,WFH',
            'schclassid' => 'nullable|integer',
            'covered_period_start' => 'nullable|date',
            'covered_period_end' => 'nullable|date|after_or_equal:covered_period_start',
            'preferred_wfh_days' => 'nullable|string',
            'status' => 'required|in:Pending,Approved,Denied',
        ]);

        $workArrangement->update($validated);

        return Redirect::route('work-arrangements.index')->with('success', 'Work Arrangement updated.');
    }

    public function destroy(WorkArrangementExt $workArrangement)
    {
        $workArrangement->delete();
        return Redirect::route('work-arrangements.index')->with('success', 'Deleted arrangement.');
    }
}
