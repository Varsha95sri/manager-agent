<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Skill;
use Illuminate\Http\RedirectResponse;

class SkillController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:skills,name',
            'category' => 'nullable|string|max:255'
        ]);

        Skill::create($validated);
        return back()->with('success', 'Skill created successfully!');
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $skill = Skill::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:skills,name,' . $id,
            'category' => 'nullable|string|max:255'
        ]);

        $skill->update($validated);
        return back()->with('success', 'Skill updated successfully!');
    }

    public function destroy($id): RedirectResponse
    {
        Skill::findOrFail($id)->delete();
        return back()->with('success', 'Skill deleted successfully!');
    }
}
