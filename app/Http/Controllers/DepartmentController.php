<?php

namespace App\Http\Controllers;

use App\Models\Dept;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return inertia('departments/Index', [
            'departments' => Dept::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'DeptName' => 'required|string|max:50|unique:dept,DeptName',
            'SupDeptid' => 'nullable|integer',
        ]);

        Dept::create([
            'DeptName' => $validated['DeptName'],
            'SupDeptid' => $validated['SupDeptid'] ?? 0,
        ]);

        return redirect()->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Dept $department): RedirectResponse
    {
        $validated = $request->validate([
            'DeptName' => 'required|string|max:50|unique:dept,DeptName,'.$department->Deptid.',Deptid',
            'SupDeptid' => 'nullable|integer',
        ]);

        $department->update([
            'DeptName' => $validated['DeptName'],
            'SupDeptid' => $validated['SupDeptid'] ?? 0,
        ]);

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Dept $department): RedirectResponse
    {
        $department->delete();

        return redirect()->back();
    }
}
