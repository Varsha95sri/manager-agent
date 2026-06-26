<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Skill;
use App\Models\Designation;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function index(): View
    {
        $departments = Department::orderBy('name')->get();
        $skills = Skill::orderBy('category')->orderBy('name')->get();
        $designations = Designation::orderBy('level')->orderBy('name')->get();
        return view('manager.departments', compact('departments', 'skills', 'designations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
            'description' => 'nullable|string'
        ]);

        Department::create($validated);
        return back()->with('success', 'Department created successfully!');
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $department = Department::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $id,
            'description' => 'nullable|string'
        ]);

        $department->update($validated);
        return back()->with('success', 'Department updated successfully!');
    }

    public function destroy($id): RedirectResponse
    {
        Department::findOrFail($id)->delete();
        return back()->with('success', 'Department deleted successfully!');
    }
}
