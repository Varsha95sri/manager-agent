@extends('layouts.manager')

@section('title', 'GitLab Integration - Manager Agent')
@section('page_title', 'GitLab Integration')

@section('styles')
<style>
    .nav-tabs-custom {
        border-bottom: 1px solid #1e293b;
    }
    .nav-tabs-custom .nav-link {
        color: #94a3b8;
        border: none;
        background: transparent;
        padding: 12px 20px;
        font-weight: 500;
        border-bottom: 2px solid transparent;
        transition: all 0.2s ease;
    }
    .nav-tabs-custom .nav-link:hover {
        color: #ffffff;
    }
    .nav-tabs-custom .nav-link.active {
        color: #a855f7;
        border-bottom: 2px solid #a855f7;
        background: transparent;
    }
</style>
@endsection

@section('content')
<div class="card glass-card p-0 overflow-hidden">
    <!-- Header with Tabs -->
    <div class="px-4 pt-4 border-bottom border-slate-800 bg-slate-950/20">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <div>
                <h4 class="h5 font-outfit text-white mb-0">GitLab Integration Control Panel</h4>
                <p class="text-secondary small mb-0 mt-1">Manage credentials, link projects/employees, and view synced git commits.</p>
            </div>
        </div>

        <ul class="nav nav-tabs nav-tabs-custom" id="gitlabTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="credentials-tab" data-bs-toggle="tab" data-bs-target="#credentials" type="button" role="tab" aria-controls="credentials" aria-selected="true">
                    Setup
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="projects-tab" data-bs-toggle="tab" data-bs-target="#projects" type="button" role="tab" aria-controls="projects" aria-selected="false">
                    Repo Links
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="employees-tab" data-bs-toggle="tab" data-bs-target="#employees" type="button" role="tab" aria-controls="employees" aria-selected="false">
                    Developer Links
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="commits-tab" data-bs-toggle="tab" data-bs-target="#commits" type="button" role="tab" aria-controls="commits" aria-selected="false">
                    Activity Feed
                </button>
            </li>
        </ul>
    </div>

    <!-- Tab Contents -->
    <div class="tab-content p-4" id="gitlabTabsContent">
        
        {{-- ==================== TAB A: SETUP (CREDENTIALS) ==================== --}}
        <div class="tab-pane fade show active" id="credentials" role="tabpanel" aria-labelledby="credentials-tab">
            <div class="row g-4 mb-4">
                <div class="col-md-7">
                    <div class="p-4 rounded-4 border border-slate-800 bg-slate-900/40">
                        <h5 class="h6 text-white font-outfit mb-3">API Connection Settings</h5>
                        <form action="{{ route('manager.gitlab.credentials.save') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label text-slate-400 small">GitLab Base URL</label>
                                <input type="url" name="gitlab_base_url" class="form-control form-control-sm" value="{{ config('services.gitlab.base_url') ?: 'https://gitlab.com' }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-slate-400 small">Personal Access Token</label>
                                <input type="password" name="gitlab_access_token" class="form-control form-control-sm" value="{{ config('services.gitlab.access_token') }}" placeholder="glpat-xxxxxxxxxxxxxxxxxxxx" required>
                                <div class="form-text text-secondary small" style="font-size: 11px;">Requires 'api' and 'read_repository' scopes.</div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label text-slate-400 small">Webhook Secret Token</label>
                                <input type="text" name="gitlab_webhook_secret" class="form-control form-control-sm" value="{{ config('services.gitlab.webhook_secret') }}" placeholder="Random secure string (e.g. MySecretToken123)">
                                <div class="form-text text-secondary small" style="font-size: 11px;">Used to verify incoming webhooks from GitLab.</div>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-sm btn-primary px-4">Save Credentials</button>
                                <button type="button" class="btn btn-sm btn-outline-info px-4" id="testConnectionBtn">Test Connection</button>
                            </div>
                            <div id="testConnectionResult" class="mt-3 small d-none p-2 rounded-2"></div>
                        </form>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="p-3 rounded-3 border border-slate-800 bg-slate-900/60 d-flex justify-content-between align-items-center">
                                <span class="text-slate-400 small">Access Token Status</span>
                                @if(config('services.gitlab.access_token'))
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 px-2.5 py-1">Connected</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-20 px-2.5 py-1">Missing</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-3 rounded-3 border border-slate-800 bg-slate-900/60 d-flex justify-content-between align-items-center">
                                <span class="text-slate-400 small">Webhook Secret Status</span>
                                @if(config('services.gitlab.webhook_secret'))
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 px-2.5 py-1">Configured</span>
                                @else
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-20 px-2.5 py-1">Not Configured</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-12 mt-4">
                            <div class="p-4 rounded-4 border border-slate-800 bg-slate-900/20">
                                <h5 class="h6 text-white font-outfit mb-3">Webhook Configuration Guide</h5>
                                <p class="text-slate-300 small">Automate commit syncing by configuring a webhook in your GitLab repository:</p>
                                <ol class="text-slate-400 small ps-3 mb-0">
                                    <li class="mb-2">Go to GitLab Repo &rarr; <strong>Settings</strong> &rarr; <strong>Webhooks</strong>.</li>
                                    <li class="mb-2">URL: <code class="text-primary bg-slate-950 px-2 py-1 rounded">{{ url('/api/webhooks/gitlab') }}</code></li>
                                    <li class="mb-2">Secret Token: <code class="text-primary bg-slate-950 px-2 py-1 rounded">{{ config('services.gitlab.webhook_secret') ?: 'not-set' }}</code></li>
                                    <li class="mb-2">Trigger: <strong>Push events</strong></li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ==================== TAB B: REPO LINKS ==================== --}}
        <div class="tab-pane fade" id="projects" role="tabpanel" aria-labelledby="projects-tab">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="h6 text-white font-outfit mb-1">Map Projects to GitLab Repositories</h5>
                    <p class="text-secondary small mb-0">Select the corresponding GitLab project for each internal database project. This enables commit tracking and webhook integration.</p>
                </div>
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addProjectModal">+ Add Project</button>
            </div>

            <div class="table-responsive">
                <table class="table align-middle text-white mb-0" style="--bs-table-bg: transparent; --bs-table-border-color: #334155;">
                    <thead class="text-secondary" style="font-size: 11px;">
                        <tr>
                            <th style="width: 30%;">Internal Project</th>
                            <th style="width: 50%;">GitLab Repository Mapping</th>
                            <th style="width: 20%;" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projects as $project)
                            <tr>
                                <td class="py-3">
                                    <strong class="text-slate-100 d-block">{{ $project->name }}</strong>
                                    <span class="text-secondary small d-block text-truncate" style="max-width: 220px;">{{ $project->description }}</span>
                                </td>
                                <td class="py-3">
                                    <form action="{{ route('manager.gitlab.project.update', $project->id) }}" method="POST" id="update-project-form-{{ $project->id }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="row g-2">
                                            <div class="col-md-6">
                                                @if(count($gitlabProjects) > 0)
                                                    <select name="gitlab_project_id" class="form-select form-select-sm border-slate-700 bg-slate-900 text-white" onchange="document.getElementById('repo_url_{{ $project->id }}').value = this.options[this.selectedIndex].getAttribute('data-url') || '';">
                                                        <option value="">-- Unmapped --</option>
                                                        @foreach($gitlabProjects as $gp)
                                                            <option value="{{ $gp['id'] }}" data-url="{{ $gp['web_url'] ?? '' }}" {{ $project->gitlab_project_id == $gp['id'] ? 'selected' : '' }}>
                                                                {{ $gp['name_with_namespace'] ?? $gp['name'] }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                    <input type="number" name="gitlab_project_id" class="form-control form-control-sm border-slate-700 bg-slate-900 text-white" placeholder="Project ID" value="{{ $project->gitlab_project_id }}">
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <input type="text" name="gitlab_repo_url" id="repo_url_{{ $project->id }}" class="form-control form-control-sm border-slate-700 bg-slate-900 text-white" placeholder="GitLab Repository URL" value="{{ $project->gitlab_repo_url }}">
                                            </div>
                                        </div>
                                    </form>
                                </td>
                                <td class="py-3 text-end">
                                    <div class="d-flex gap-1 justify-content-end">
                                        <button type="submit" form="update-project-form-{{ $project->id }}" class="btn btn-sm btn-primary py-1 px-3" style="font-size: 11px;">Save</button>
                                        @if($project->gitlab_project_id)
                                            <form action="{{ route('manager.gitlab.project.sync', $project->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success py-1 px-2.5" style="font-size: 11px;">Sync</button>
                                            </form>
                                        @endif
                                        <button type="button" class="btn btn-sm btn-outline-info py-1 px-2.5" style="font-size: 11px;" data-bs-toggle="modal" data-bs-target="#editProjectModal-{{ $project->id }}">Edit</button>
                                        <form action="{{ route('manager.gitlab.project.destroy', $project->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this project?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2.5" style="font-size: 11px;">Del</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-secondary italic small">No projects registered. Create projects in dashboard/tasks.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {!! $projects->links() !!}
            </div>

            {{-- Modals for Projects --}}
            @foreach($projects as $project)
            <div class="modal fade" id="editProjectModal-{{ $project->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content bg-slate-900 border-slate-700">
                        <div class="modal-header border-slate-800">
                            <h5 class="modal-title text-white font-outfit">Edit Project Details</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('manager.gitlab.project.update_details', $project->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label text-slate-400 small">Project Name</label>
                                    <input type="text" name="name" class="form-control border-slate-700 bg-slate-800 text-white" value="{{ $project->name }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-slate-400 small">Description</label>
                                    <textarea name="description" class="form-control border-slate-700 bg-slate-800 text-white" rows="3">{{ $project->description }}</textarea>
                                </div>
                            </div>
                            <div class="modal-footer border-slate-800">
                                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-sm btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach

            <div class="modal fade" id="addProjectModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content bg-slate-900 border-slate-700">
                        <div class="modal-header border-slate-800">
                            <h5 class="modal-title text-white font-outfit">Add New Project</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('manager.gitlab.project.store') }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label text-slate-400 small">Project Name</label>
                                    <input type="text" name="name" class="form-control border-slate-700 bg-slate-800 text-white" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-slate-400 small">Description</label>
                                    <textarea name="description" class="form-control border-slate-700 bg-slate-800 text-white" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer border-slate-800">
                                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-sm btn-primary">Add Project</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- ==================== TAB C: DEVELOPER LINKS ==================== --}}
        <div class="tab-pane fade" id="employees" role="tabpanel" aria-labelledby="employees-tab">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="h6 text-white font-outfit mb-1">Map Employees to GitLab User accounts</h5>
                    <p class="text-secondary small mb-0">Set GitLab ID and Username to match pushing authors with employees automatically.</p>
                </div>
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">+ Add Employee</button>
            </div>

            <div class="table-responsive">
                <table class="table align-middle text-white mb-0" style="--bs-table-bg: transparent; --bs-table-border-color: #334155;">
                    <thead class="text-secondary" style="font-size: 11px;">
                        <tr>
                            <th style="width: 30%;">Employee</th>
                            <th style="width: 50%;">GitLab Config</th>
                            <th style="width: 20%;" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $employee)
                            <tr>
                                <td class="py-3">
                                    <strong class="text-slate-100 d-block">{{ $employee->name }}</strong>
                                    <span class="text-secondary small d-block">{{ $employee->email }}</span>
                                    @if($employee->role) <span class="badge bg-slate-800 text-slate-400 mt-1">{{ $employee->role }}</span> @endif
                                </td>
                                <td class="py-3">
                                    <form action="{{ route('manager.gitlab.employee.update', $employee->id) }}" method="POST" id="update-employee-form-{{ $employee->id }}" class="row g-2 align-items-center">
                                        @csrf
                                        @method('PUT')
                                        <div class="col-md-6">
                                            <input type="number" name="gitlab_user_id" class="form-control form-control-sm border-slate-700 bg-slate-900 text-white" placeholder="GitLab User ID" value="{{ $employee->gitlab_user_id }}">
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" name="gitlab_username" class="form-control form-control-sm border-slate-700 bg-slate-900 text-white" placeholder="GitLab Username" value="{{ $employee->gitlab_username }}">
                                        </div>
                                    </form>
                                </td>
                                <td class="py-3 text-end">
                                    <div class="d-flex gap-1 justify-content-end">
                                        <button type="submit" form="update-employee-form-{{ $employee->id }}" class="btn btn-sm btn-primary py-1 px-3" style="font-size: 11px;">Save</button>
                                        <button type="button" class="btn btn-sm btn-outline-info py-1 px-2.5" style="font-size: 11px;" data-bs-toggle="modal" data-bs-target="#editEmployeeModal-{{ $employee->id }}">Edit</button>
                                        <form action="{{ route('manager.gitlab.employee.destroy', $employee->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this employee?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2.5" style="font-size: 11px;">Del</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-secondary italic small">No team members registered.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {!! $employees->links() !!}
            </div>

            {{-- Modals for Employees --}}
            @foreach($employees as $employee)
            <div class="modal fade" id="editEmployeeModal-{{ $employee->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content bg-slate-900 border-slate-700">
                        <div class="modal-header border-slate-800">
                            <h5 class="modal-title text-white font-outfit">Edit Employee Details</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('manager.gitlab.employee.update_details', $employee->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label text-slate-400 small">Full Name</label>
                                    <input type="text" name="name" class="form-control border-slate-700 bg-slate-800 text-white" value="{{ $employee->name }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-slate-400 small">Email Address</label>
                                    <input type="email" name="email" class="form-control border-slate-700 bg-slate-800 text-white" value="{{ $employee->email }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-slate-400 small">Role (Optional)</label>
                                    <input type="text" name="role" class="form-control border-slate-700 bg-slate-800 text-white" value="{{ $employee->role }}">
                                </div>
                            </div>
                            <div class="modal-footer border-slate-800">
                                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-sm btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach

            <div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content bg-slate-900 border-slate-700">
                        <div class="modal-header border-slate-800">
                            <h5 class="modal-title text-white font-outfit">Add New Employee</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('manager.gitlab.employee.store') }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label text-slate-400 small">Full Name</label>
                                    <input type="text" name="name" class="form-control border-slate-700 bg-slate-800 text-white" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-slate-400 small">Email Address</label>
                                    <input type="email" name="email" class="form-control border-slate-700 bg-slate-800 text-white" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-slate-400 small">Role (Optional)</label>
                                    <input type="text" name="role" class="form-control border-slate-700 bg-slate-800 text-white" placeholder="e.g. Frontend Developer">
                                </div>
                            </div>
                            <div class="modal-footer border-slate-800">
                                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-sm btn-primary">Add Employee</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- ==================== TAB D: COMMITS LOG ==================== --}}
        <div class="tab-pane fade" id="commits" role="tabpanel" aria-labelledby="commits-tab">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
                <h5 class="h6 text-white font-outfit mb-0">GitLab Commits Ingested</h5>
                
                <div class="d-flex gap-2">
                    <form method="GET" action="{{ route('manager.commits.index') }}" class="d-flex gap-2" style="max-width: 300px;">
                        <input type="text" name="search" class="form-control form-control-sm border-slate-700 bg-slate-900 text-white rounded-3 px-3" placeholder="Search SHA or message..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-sm btn-primary px-3 rounded-3">Search</button>
                    </form>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addCommitModal">+ Log Manual Commit</button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle text-white mb-0" style="--bs-table-bg: transparent; --bs-table-border-color: #334155;">
                    <thead class="text-secondary" style="font-size: 11px;">
                        <tr>
                            <th>SHA</th>
                            <th>Project</th>
                            <th>Employee</th>
                            <th>Message</th>
                            <th>Committed At</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($commits as $commit)
                            <tr>
                                <td class="py-3 font-mono text-primary" style="font-size: 13px;">
                                    {{ substr($commit->commit_sha, 0, 8) }}
                                </td>
                                <td class="py-3">
                                    <span class="font-semibold text-slate-200">{{ $commit->project->name ?? 'N/A' }}</span>
                                </td>
                                <td class="py-3">
                                    @if($commit->employee)
                                        <span class="badge bg-indigo-500 bg-opacity-10 text-indigo-400 border border-indigo-500 border-opacity-20 px-2 py-1" style="font-size: 10px;">{{ $commit->employee->name }}</span>
                                    @else
                                        <span class="text-slate-500 small">Unmapped</span>
                                    @endif
                                </td>
                                <td class="py-3 text-slate-100" style="max-width: 280px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $commit->message }}">
                                    {{ $commit->message }}
                                </td>
                                <td class="py-3 text-slate-400 small">
                                    {{ $commit->committed_at->timezone(config('app.timezone', 'Asia/Kolkata'))->format('M d, Y h:i A') }}
                                </td>
                                <td class="py-3 text-end">
                                    <div class="d-flex gap-1 justify-content-end">
                                        @if($commit->commit_url)
                                            <a href="{{ $commit->commit_url }}" target="_blank" class="btn btn-sm btn-outline-info py-1 px-2.5" style="font-size: 11px;">View</a>
                                        @endif
                                        <button type="button" class="btn btn-sm btn-outline-info py-1 px-2.5" style="font-size: 11px;" data-bs-toggle="modal" data-bs-target="#editCommitModal-{{ $commit->id }}">Edit</button>
                                        <form action="{{ route('manager.gitlab.commit.destroy', $commit->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this commit log?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2.5" style="font-size: 11px;">Del</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-secondary italic small">No commits recorded in database yet. Try pushing commits via webhook or syncing manually!</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {!! $commits->links() !!}
            </div>

            {{-- Modals for Commits --}}
            @foreach($commits as $commit)
            <div class="modal fade" id="editCommitModal-{{ $commit->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content bg-slate-900 border-slate-700">
                        <div class="modal-header border-slate-800">
                            <h5 class="modal-title text-white font-outfit">Edit Commit Log</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('manager.gitlab.commit.update', $commit->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label text-slate-400 small">Project</label>
                                    <select name="project_id" class="form-select border-slate-700 bg-slate-800 text-white" required>
                                        @foreach($dropdownProjects as $p)
                                            <option value="{{ $p->id }}" {{ $commit->project_id == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-slate-400 small">Employee</label>
                                    <select name="employee_id" class="form-select border-slate-700 bg-slate-800 text-white">
                                        <option value="">-- None --</option>
                                        @foreach($dropdownEmployees as $e)
                                            <option value="{{ $e->id }}" {{ $commit->employee_id == $e->id ? 'selected' : '' }}>{{ $e->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-slate-400 small">Commit SHA</label>
                                    <input type="text" name="commit_sha" class="form-control border-slate-700 bg-slate-800 text-white" value="{{ $commit->commit_sha }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-slate-400 small">Commit Message</label>
                                    <textarea name="message" class="form-control border-slate-700 bg-slate-800 text-white" rows="2" required>{{ $commit->message }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-slate-400 small">Committed At</label>
                                    <input type="datetime-local" name="committed_at" class="form-control border-slate-700 bg-slate-800 text-white" value="{{ \Carbon\Carbon::parse($commit->committed_at)->format('Y-m-d\TH:i') }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-slate-400 small">Commit URL (Optional)</label>
                                    <input type="url" name="commit_url" class="form-control border-slate-700 bg-slate-800 text-white" value="{{ $commit->commit_url }}">
                                </div>
                            </div>
                            <div class="modal-footer border-slate-800">
                                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-sm btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach

            <div class="modal fade" id="addCommitModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content bg-slate-900 border-slate-700">
                        <div class="modal-header border-slate-800">
                            <h5 class="modal-title text-white font-outfit">Log Manual Commit</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('manager.gitlab.commit.store') }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label text-slate-400 small">Project</label>
                                    <select name="project_id" class="form-select border-slate-700 bg-slate-800 text-white" required>
                                        <option value="">-- Select Project --</option>
                                        @foreach($dropdownProjects as $p)
                                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-slate-400 small">Employee</label>
                                    <select name="employee_id" class="form-select border-slate-700 bg-slate-800 text-white">
                                        <option value="">-- Select Employee --</option>
                                        @foreach($dropdownEmployees as $e)
                                            <option value="{{ $e->id }}">{{ $e->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-slate-400 small">Commit SHA</label>
                                    <input type="text" name="commit_sha" class="form-control border-slate-700 bg-slate-800 text-white" placeholder="e.g. a1b2c3d4" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-slate-400 small">Commit Message</label>
                                    <textarea name="message" class="form-control border-slate-700 bg-slate-800 text-white" rows="2" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-slate-400 small">Committed At</label>
                                    <input type="datetime-local" name="committed_at" class="form-control border-slate-700 bg-slate-800 text-white" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-slate-400 small">Commit URL (Optional)</label>
                                    <input type="url" name="commit_url" class="form-control border-slate-700 bg-slate-800 text-white" placeholder="https://gitlab.com/...">
                                </div>
                            </div>
                            <div class="modal-footer border-slate-800">
                                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-sm btn-primary">Log Commit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('testConnectionBtn')?.addEventListener('click', function() {
        const btn = this;
        const resultDiv = document.getElementById('testConnectionResult');
        
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Testing...';
        btn.disabled = true;
        resultDiv.classList.add('d-none');
        resultDiv.className = 'mt-3 small p-2 rounded-2'; // Reset classes
        
        fetch('{{ route('manager.gitlab.credentials.test') }}', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            resultDiv.classList.remove('d-none');
            if(data.success) {
                resultDiv.classList.add('bg-success', 'bg-opacity-10', 'text-success', 'border', 'border-success', 'border-opacity-20');
                resultDiv.innerHTML = `<strong>Success:</strong> ${data.message}`;
            } else {
                resultDiv.classList.add('bg-danger', 'bg-opacity-10', 'text-danger', 'border', 'border-danger', 'border-opacity-20');
                resultDiv.innerHTML = `<strong>Failed:</strong> ${data.message}`;
            }
        })
        .catch(error => {
            resultDiv.classList.remove('d-none');
            resultDiv.classList.add('bg-danger', 'bg-opacity-10', 'text-danger', 'border', 'border-danger', 'border-opacity-20');
            resultDiv.innerHTML = `<strong>Error:</strong> Network or server error occurred.`;
        })
        .finally(() => {
            btn.innerHTML = 'Test Connection';
            btn.disabled = false;
        });
    });

    // Handle URL ?tab= parameter to activate correct tab on load
    document.addEventListener('DOMContentLoaded', function () {
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('tab');
        if (activeTab) {
            const tabButton = document.querySelector(`button[data-bs-target="#${activeTab}"]`);
            if (tabButton) {
                const tabInstance = new bootstrap.Tab(tabButton);
                tabInstance.show();
            }
        }
    });
</script>
@endsection
