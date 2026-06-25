<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProjectController extends Controller
{
    /**
     * Display the dedicated Projects Dashboard.
     */
    public function index(): View
    {
        $projects = Project::withCount('tasks')->latest('updated_at')->get();
        return view('manager.projects.index', compact('projects'));
    }

    /**
     * Store a newly created project.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:planning,active,on_hold,completed,archived',
            'progress_percent' => 'nullable|integer|min:0|max:100',
            'deadline' => 'nullable|date',
            'category' => 'nullable|string|max:255',
        ]);
        
        if (!isset($validated['status'])) $validated['status'] = 'planning';
        if (!isset($validated['progress_percent'])) $validated['progress_percent'] = 0;

        Project::create($validated);
        return redirect()->route('manager.projects.index')->with('success', 'Project created successfully.');
    }

    /**
     * Update the specified project.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:planning,active,on_hold,completed,archived',
            'progress_percent' => 'nullable|integer|min:0|max:100',
            'deadline' => 'nullable|date',
            'category' => 'nullable|string|max:255',
        ]);

        $project = Project::findOrFail($id);
        $project->update($validated);
        return redirect()->route('manager.projects.index')->with('success', 'Project updated successfully.');
    }

    /**
     * Remove the specified project.
     */
    public function destroy($id): RedirectResponse
    {
        Project::findOrFail($id)->delete();
        return redirect()->route('manager.projects.index')->with('success', 'Project deleted.');
    }

    /**
     * Display the Project Reports including Team-wise reports.
     */
    public function reports(): View
    {
        $projects = Project::withCount('tasks')->get();
        
        $completedProjects = $projects->where('status', 'completed');
        $delayedProjects = $projects->where('risk_level', 'high');
        $activeProjects = $projects->where('status', 'active');
        
        // Basic analytics
        $completionRate = $projects->count() > 0 ? round(($completedProjects->count() / $projects->count()) * 100) : 0;

        // Team-wise Project Report (Grouping by TeamMember roles)
        $roleTaskCounts = \Illuminate\Support\Facades\DB::table('tasks')
            ->join('team_members', 'tasks.team_member_id', '=', 'team_members.id')
            ->select('tasks.project_id', 'team_members.role', \Illuminate\Support\Facades\DB::raw('count(*) as role_tasks_count'))
            ->groupBy('tasks.project_id', 'team_members.role')
            ->get();
            
        $roleTaskCountsGrouped = $roleTaskCounts->groupBy('role');

        $teamReports = [];
        foreach($roleTaskCountsGrouped as $role => $items) {
            if (!$role) continue;
            
            $projectIds = $items->pluck('project_id')->toArray();
            $countsByProject = $items->keyBy('project_id');
            
            $projectsForRole = $projects->whereIn('id', $projectIds)->map(function($project) use ($countsByProject) {
                $cloned = clone $project;
                $cloned->tasks_count = $countsByProject->get($project->id)->role_tasks_count ?? 0;
                return $cloned;
            });
            
            if ($projectsForRole->count() > 0) {
                $teamReports[ucfirst($role)] = $projectsForRole;
            }
        }
        
        return view('manager.projects.reports', compact(
            'projects',
            'completedProjects',
            'delayedProjects',
            'activeProjects',
            'completionRate',
            'teamReports'
        ));
    }
}
