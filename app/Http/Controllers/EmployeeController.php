<?php
// app/Http/Controllers/EmployeeController.php

namespace App\Http\Controllers;

use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    /**
     * Display a listing of employees.
     */
    public function index(Request $request): View
    {
        $query = TeamMember::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%")
                  ->orWhere('gitlab_id', 'like', "%{$search}%");
            });
        }

        $employees = $query->orderBy('name')->paginate(10)->withQueryString();
        
        $departments = \App\Models\Department::orderBy('name')->get();
        $skills = \App\Models\Skill::orderBy('category')->orderBy('name')->get();

        return view('manager.employees', compact('employees', 'departments', 'skills'));
    }

    /**
     * Store a newly created employee.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:team_members,email',
            'role' => 'required|string|max:255',
            'gitlab_id' => 'nullable|string|max:255',
            'task_title' => 'nullable|string|max:255',
            'task_commit' => 'nullable|string|max:255',
            'attendance' => 'nullable|string|max:255',
            'meeting_date' => 'nullable|date',
            'meeting_title' => 'nullable|string|max:255',
            'task_assign_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'login_timing' => 'nullable|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'skills' => 'nullable|array',
            'skills.*.id' => 'required|exists:skills,id',
            'skills.*.proficiency' => 'required|integer|min:1|max:5',
        ]);

        $employee = TeamMember::create($validated);

        if ($request->has('skills')) {
            $syncData = [];
            foreach ($request->input('skills') as $skillData) {
                $syncData[$skillData['id']] = ['proficiency' => $skillData['proficiency']];
            }
            $employee->skills()->sync($syncData);
        }

        return redirect()->route('manager.employees.index')->with('success', 'Employee created successfully!');
    }

    /**
     * Update the specified employee.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:team_members,email,' . $id,
            'role' => 'required|string|max:255',
            'gitlab_id' => 'nullable|string|max:255',
            'task_title' => 'nullable|string|max:255',
            'task_commit' => 'nullable|string|max:255',
            'attendance' => 'nullable|string|max:255',
            'meeting_date' => 'nullable|date',
            'meeting_title' => 'nullable|string|max:255',
            'task_assign_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'login_timing' => 'nullable|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'skills' => 'nullable|array',
            'skills.*.id' => 'required|exists:skills,id',
            'skills.*.proficiency' => 'required|integer|min:1|max:5',
        ]);

        $employee = TeamMember::findOrFail($id);
        $employee->update($validated);

        if ($request->has('skills')) {
            $syncData = [];
            foreach ($request->input('skills') as $skillData) {
                $syncData[$skillData['id']] = ['proficiency' => $skillData['proficiency']];
            }
            $employee->skills()->sync($syncData);
        } else {
            $employee->skills()->detach();
        }

        return redirect()->route('manager.employees.index')->with('success', 'Employee updated successfully!');
    }

    /**
     * Remove the specified employee.
     */
    public function destroy($id): RedirectResponse
    {
        $employee = TeamMember::findOrFail($id);
        $employee->delete();

        return redirect()->route('manager.employees.index')->with('success', 'Employee deleted successfully!');
    }

    /**
     * Export employees to CSV.
     */
    public function export()
    {
        $headers = [
            'Content-type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename=employees_export_' . date('Y-m-d') . '.csv',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0'
        ];

        $columns = [
            'Name', 'Email', 'Role', 'GitLab ID', 'Task Title', 
            'Task Commit', 'Attendance', 'Meeting Date', 'Meeting Title', 
            'Task Assign Date', 'Due Date', 'Login Timing', 'Performance Score', 'Performance Grade'
        ];

        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            // Use lazy cursor for chunked streaming with low memory footprint
            TeamMember::query()->lazy()->each(function($employee) use ($file) {
                fputcsv($file, [
                    $employee->name,
                    $employee->email,
                    $employee->role,
                    $employee->gitlab_id,
                    $employee->task_title,
                    $employee->task_commit,
                    $employee->attendance,
                    $employee->meeting_date,
                    $employee->meeting_title,
                    $employee->task_assign_date,
                    $employee->due_date,
                    $employee->login_timing,
                    $employee->performance_score ?? 0,
                    $employee->performance_grade ?? 'N/A',
                ]);
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import employees from CSV.
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt'
        ]);

        $file = $request->file('file');
        $filePath = $file->getRealPath();

        $fileHandle = fopen($filePath, 'r');
        $header = fgetcsv($fileHandle, 1000, ',');

        if (!$header) {
            fclose($fileHandle);
            return redirect()->back()->with('error', 'The uploaded CSV file is empty or invalid.');
        }

        $importedCount = 0;
        while (($row = fgetcsv($fileHandle, 1000, ',')) !== false) {
            if (empty($row) || count($row) < 3) continue;

            $email = trim($row[1] ?? '');
            if (empty($email)) continue;

            TeamMember::updateOrCreate(
                ['email' => $email],
                [
                    'name' => trim($row[0] ?? ''),
                    'role' => trim($row[2] ?? ''),
                    'gitlab_id' => trim($row[3] ?? null),
                    'task_title' => isset($row[4]) && trim($row[4]) !== '' ? trim($row[4]) : null,
                    'task_commit' => isset($row[5]) && trim($row[5]) !== '' ? trim($row[5]) : null,
                    'attendance' => isset($row[6]) && trim($row[6]) !== '' ? trim($row[6]) : null,
                    'meeting_date' => isset($row[7]) && trim($row[7]) !== '' ? trim($row[7]) : null,
                    'meeting_title' => isset($row[8]) && trim($row[8]) !== '' ? trim($row[8]) : null,
                    'task_assign_date' => isset($row[9]) && trim($row[9]) !== '' ? trim($row[9]) : null,
                    'due_date' => isset($row[10]) && trim($row[10]) !== '' ? trim($row[10]) : null,
                    'login_timing' => isset($row[11]) && trim($row[11]) !== '' ? trim($row[11]) : null,
                ]
            );
            $importedCount++;
        }

        fclose($fileHandle);

        return redirect()->route('manager.employees.index')->with('success', "Successfully imported {$importedCount} employee records.");
    }
}
