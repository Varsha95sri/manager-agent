<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Designation;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DesignationController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:designations,name',
            'level' => 'nullable|string|max:50',
            'description' => 'nullable|string'
        ]);

        Designation::create($validated);
        return back()->with('success', 'Designation created successfully!');
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $designation = Designation::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:designations,name,' . $id,
            'level' => 'nullable|string|max:50',
            'description' => 'nullable|string'
        ]);

        $designation->update($validated);
        return back()->with('success', 'Designation updated successfully!');
    }

    public function destroy($id): RedirectResponse
    {
        Designation::findOrFail($id)->delete();
        return back()->with('success', 'Designation deleted successfully!');
    }
}
