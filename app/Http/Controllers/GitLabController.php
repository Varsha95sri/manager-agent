<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\TeamMember;
use App\Models\Commit;
use App\Services\GitLabService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GitLabController extends Controller
{
    protected GitLabService $gitlabService;

    public function __construct(GitLabService $gitlabService)
    {
        $this->gitlabService = $gitlabService;
    }

    /**
     * Display the GitLab Integration dashboard.
     */
    public function index(Request $request): View
    {
        $projects = Project::orderBy('name')->paginate(15, ['*'], 'projects_page')->withQueryString();
        $employees = TeamMember::orderBy('name')->paginate(15, ['*'], 'employees_page')->withQueryString();
        
        $dropdownProjects = Project::orderBy('name')->select('id', 'name')->take(500)->get();
        $dropdownEmployees = TeamMember::orderBy('name')->select('id', 'name')->take(500)->get();

        $commitsQuery = Commit::with(['project', 'employee'])->orderBy('committed_at', 'desc');
        
        if ($request->filled('search')) {
            $search = $request->input('search');
            $commitsQuery->where('commit_sha', 'like', "%{$search}%")
                         ->orWhere('message', 'like', "%{$search}%");
        }
        
        $commits = $commitsQuery->paginate(15, ['*'], 'commits_page')->withQueryString();

        // Fetch direct projects from GitLab
        $gitlabProjects = config('services.gitlab.access_token') ? $this->gitlabService->getProjects() : [];

        return view('manager.gitlab', compact('projects', 'employees', 'commits', 'gitlabProjects', 'dropdownProjects', 'dropdownEmployees'));
    }

    /**
     * Update GitLab details for a project.
     */
    public function updateProject(Request $request, $id): RedirectResponse
    {
        $validated = $request->validate([
            'gitlab_project_id' => 'nullable|integer',
            'gitlab_repo_url' => 'nullable|string|url|max:255',
        ]);

        $project = Project::findOrFail($id);
        $project->update($validated);

        return redirect()->route('manager.gitlab.index')->with('success', "Project '{$project->name}' GitLab settings updated successfully!");
    }

    /**
     * Update GitLab details for an employee.
     */
    public function updateEmployee(Request $request, $id): RedirectResponse
    {
        $validated = $request->validate([
            'gitlab_user_id' => 'nullable|integer',
            'gitlab_username' => 'nullable|string|max:255',
        ]);

        $employee = TeamMember::findOrFail($id);
        $employee->update($validated);

        return redirect()->route('manager.gitlab.index')->with('success', "Employee '{$employee->name}' GitLab settings updated successfully!");
    }

    /**
     * Trigger manual commit sync for a project.
     */
    public function syncProjectCommits(Request $request, $id): RedirectResponse
    {
        $project = Project::findOrFail($id);
        
        if (!$project->gitlab_project_id) {
            return redirect()->route('manager.gitlab.index')->with('error', "Cannot sync commits. Project '{$project->name}' does not have a GitLab Project ID.");
        }

        $imported = $this->gitlabService->syncCommits($project);

        return redirect()->route('manager.gitlab.index')->with('success', "Successfully synced commits for '{$project->name}'! Imported {$imported} new commits.");
    }
    /**
     * Save GitLab credentials to .env file.
     */
    public function saveCredentials(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'gitlab_base_url' => 'required|url',
            'gitlab_access_token' => 'required|string',
            'gitlab_webhook_secret' => 'nullable|string',
        ]);

        $envPath = base_path('.env');
        if (\Illuminate\Support\Facades\File::exists($envPath)) {
            $envContent = \Illuminate\Support\Facades\File::get($envPath);

            // Update or add each key
            $envContent = $this->updateEnvKey($envContent, 'GITLAB_BASE_URL', $validated['gitlab_base_url']);
            $envContent = $this->updateEnvKey($envContent, 'GITLAB_ACCESS_TOKEN', $validated['gitlab_access_token']);
            $envContent = $this->updateEnvKey($envContent, 'GITLAB_WEBHOOK_SECRET', $validated['gitlab_webhook_secret'] ?? '');

            // Defer writing to .env and clearing config until AFTER the HTTP response is sent.
            // This prevents the 'php artisan serve' watcher from killing the server mid-request and causing an ERR_CONNECTION_RESET.
            app()->terminating(function () use ($envPath, $envContent) {
                \Illuminate\Support\Facades\File::put($envPath, $envContent);
                \Illuminate\Support\Facades\Artisan::call('config:clear');
            });
        }

        return redirect()->route('manager.gitlab.index')->with('success', 'GitLab credentials saved successfully!');
    }

    private function updateEnvKey(string $envContent, string $key, string $value): string
    {
        // Quote the value if it has spaces
        $value = preg_match('/\s/', $value) ? "\"{$value}\"" : $value;
        
        // If key exists, replace it
        if (preg_match("/^{$key}=/m", $envContent)) {
            return preg_replace("/^{$key}=.*/m", "{$key}={$value}", $envContent);
        }
        
        // Else append it
        return $envContent . "\n{$key}={$value}";
    }

    /**
     * Test the GitLab API connection using saved credentials.
     */
    public function testConnection(): \Illuminate\Http\JsonResponse
    {
        $baseUrl = config('services.gitlab.base_url', 'https://gitlab.com');
        $token = config('services.gitlab.access_token');

        if (!$token) {
            return response()->json(['success' => false, 'message' => 'Access token is missing. Please save credentials first.']);
        }

        try {
            $response = \Illuminate\Support\Facades\Http::withoutVerifying()
                ->withToken($token)
                ->timeout(10)
                ->get(rtrim($baseUrl, '/') . '/api/v4/user');

            if ($response->successful()) {
                $user = $response->json();
                return response()->json([
                    'success' => true, 
                    'message' => "Connection successful! Authenticated as {$user['name']} (@{$user['username']})."
                ]);
            }

            return response()->json([
                'success' => false, 
                'message' => "Failed to connect. Status: {$response->status()}. " . ($response->json('message') ?? 'Invalid token or URL.')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Connection error: ' . $e->getMessage()
            ]);
        }
    }

    // ==========================================
    // PROJECTS CRUD
    // ==========================================
    public function storeProject(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        Project::create($validated);
        return redirect()->route('manager.gitlab.index', ['tab' => 'projects'])->with('success', 'Project created successfully.');
    }

    public function updateProjectDetails(Request $request, $id): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        Project::findOrFail($id)->update($validated);
        return redirect()->route('manager.gitlab.index', ['tab' => 'projects'])->with('success', 'Project details updated.');
    }

    public function destroyProject($id): RedirectResponse
    {
        Project::findOrFail($id)->delete();
        return redirect()->route('manager.gitlab.index', ['tab' => 'projects'])->with('success', 'Project deleted.');
    }

    // ==========================================
    // EMPLOYEES CRUD
    // ==========================================
    public function storeEmployee(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:team_members,email',
            'role' => 'nullable|string|max:255',
        ]);
        TeamMember::create($validated);
        return redirect()->route('manager.gitlab.index', ['tab' => 'employees'])->with('success', 'Employee created successfully.');
    }

    public function updateEmployeeDetails(Request $request, $id): RedirectResponse
    {
        $employee = TeamMember::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:team_members,email,' . $id,
            'role' => 'nullable|string|max:255',
        ]);
        $employee->update($validated);
        return redirect()->route('manager.gitlab.index', ['tab' => 'employees'])->with('success', 'Employee details updated.');
    }

    public function destroyEmployee($id): RedirectResponse
    {
        TeamMember::findOrFail($id)->delete();
        return redirect()->route('manager.gitlab.index', ['tab' => 'employees'])->with('success', 'Employee deleted.');
    }

    // ==========================================
    // COMMITS CRUD
    // ==========================================
    public function storeCommit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'employee_id' => 'nullable|exists:team_members,id',
            'commit_sha' => 'required|string|max:255',
            'message' => 'required|string',
            'committed_at' => 'required|date',
            'commit_url' => 'nullable|url',
        ]);
        Commit::create($validated);
        return redirect()->route('manager.gitlab.index', ['tab' => 'commits'])->with('success', 'Manual commit logged.');
    }

    public function updateCommit(Request $request, $id): RedirectResponse
    {
        $commit = Commit::findOrFail($id);
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'employee_id' => 'nullable|exists:team_members,id',
            'commit_sha' => 'required|string|max:255',
            'message' => 'required|string',
            'committed_at' => 'required|date',
            'commit_url' => 'nullable|url',
        ]);
        $commit->update($validated);
        return redirect()->route('manager.gitlab.index', ['tab' => 'commits'])->with('success', 'Commit log updated.');
    }

    public function destroyCommit($id): RedirectResponse
    {
        Commit::findOrFail($id)->delete();
        return redirect()->route('manager.gitlab.index', ['tab' => 'commits'])->with('success', 'Commit deleted.');
    }
}
