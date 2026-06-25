<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PerformanceReport;
use App\Models\Team;
use Illuminate\Support\Str;

class TeamController extends Controller
{
    public function index()
    {
        $teams = Team::withCount('teamMembers')->get();
        
        $latestReportsWithRisks = PerformanceReport::orderBy('report_date', 'desc')->take(5)->get();
        $allRisks = [];
        foreach ($latestReportsWithRisks as $report) {
            if (!empty($report->risks) && is_array($report->risks)) {
                foreach ($report->risks as $risk) {
                    if (stripos(trim($risk), 'none') === 0 && (strlen(trim($risk)) < 6 || stripos(trim($risk), 'none.') === 0)) {
                        continue;
                    }
                    $allRisks[] = [
                        'report_id' => $report->id,
                        'date' => $report->report_date instanceof \Carbon\Carbon ? $report->report_date->format('M d, Y') : \Carbon\Carbon::parse($report->report_date)->format('M d, Y'),
                        'risk' => $risk
                    ];
                }
            }
        }
        $riskCount = count($allRisks);

        return view('manager.teams.index', compact('teams', 'riskCount', 'allRisks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        
        // Ensure unique slug
        $baseSlug = $validated['slug'];
        $counter = 1;
        while (Team::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $baseSlug . '-' . $counter;
            $counter++;
        }

        $validated['status'] = 'Good';
        $validated['status_color'] = 'primary';
        $validated['icon_bg'] = 'primary';

        Team::create($validated);

        return back()->with('success', 'Team created successfully!');
    }

    public function show($slug)
    {
        $team = Team::where('slug', $slug)->with('teamMembers')->firstOrFail();
        
        $latestReportsWithRisks = PerformanceReport::where('team_id', $team->id)->orderBy('report_date', 'desc')->take(5)->get();
        $allRisks = [];
        foreach ($latestReportsWithRisks as $report) {
            if (!empty($report->risks) && is_array($report->risks)) {
                foreach ($report->risks as $risk) {
                    if (stripos(trim($risk), 'none') === 0 && (strlen(trim($risk)) < 6 || stripos(trim($risk), 'none.') === 0)) {
                        continue;
                    }
                    $allRisks[] = [
                        'report_id' => $report->id,
                        'date' => $report->report_date instanceof \Carbon\Carbon ? $report->report_date->format('M d, Y') : \Carbon\Carbon::parse($report->report_date)->format('M d, Y'),
                        'risk' => $risk
                    ];
                }
            }
        }
        $riskCount = count($allRisks);

        return view('manager.teams.show', compact('team', 'riskCount', 'allRisks'));
    }

    public function addMember(Request $request, $slug)
    {
        $team = Team::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:team_members,email',
            'role' => 'required|string|max:255',
        ]);

        $validated['team_id'] = $team->id;

        \App\Models\TeamMember::create($validated);

        return back()->with('success', 'Member added to team successfully!');
    }
}
