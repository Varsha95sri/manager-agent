@extends('layouts.manager')

@section('title', $employee->name . ' - Profile')
@section('page_title', 'Employee Profile')

@section('content')
<div class="mb-4 d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center">
        <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm me-3 rounded-circle p-2 d-flex align-items-center" style="width:32px; height:32px;">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <h4 class="mb-0 font-outfit text-slate-900">{{ $employee->name }}'s Profile</h4>
    </div>
</div>

<div class="row">
    <!-- Basic Info -->
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body text-center p-4">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px; font-size: 32px;">
                    {{ substr($employee->name, 0, 1) }}
                </div>
                <h5 class="font-outfit text-slate-900 mb-1">{{ $employee->name }}</h5>
                <p class="text-secondary mb-3">{{ $employee->designation ? $employee->designation->name : $employee->role }}</p>
                <hr>
                <div class="text-start">
                    <p class="mb-2"><small class="text-secondary">Email</small><br><span class="text-slate-900">{{ $employee->email }}</span></p>
                    <p class="mb-2"><small class="text-secondary">Department</small><br><span class="text-slate-900">{{ $employee->department ? $employee->department->name : 'N/A' }}</span></p>
                    <p class="mb-2"><small class="text-secondary">GitLab User</small><br><span class="text-slate-900">{{ $employee->gitlab_username ?? 'Not Linked' }}</span></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Skills & Performance -->
    <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 font-outfit text-slate-900">Skills & Performance</h6>
            </div>
            <div class="card-body">
                <h6 class="small text-secondary mb-3">Skill Matrix</h6>
                <div class="d-flex flex-wrap gap-2 mb-4">
                    @forelse($employee->skills as $skill)
                        <span class="badge bg-light text-slate-900 border px-3 py-2">
                            {{ $skill->name }} <span class="text-primary ms-1">Level {{ $skill->pivot->proficiency }}</span>
                        </span>
                    @empty
                        <span class="text-muted small">No skills recorded.</span>
                    @endforelse
                </div>

                <h6 class="small text-secondary mb-3">Performance Overview</h6>
                <div class="row text-center">
                    <div class="col-6 col-md-4 mb-3">
                        <div class="p-3 border rounded-3 bg-light h-100">
                            <h4 class="mb-0 text-slate-900">{{ number_format($employee->calculated_score) }}</h4>
                            <small class="text-secondary">Leaderboard Score</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 mb-3">
                        <div class="p-3 border rounded-3 bg-light h-100">
                            <h4 class="mb-0 text-slate-900">{{ $employee->tasks_count }}</h4>
                            <small class="text-secondary">Tasks Completed</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 mb-3">
                        <div class="p-3 border rounded-3 bg-light h-100">
                            <h4 class="mb-0 text-slate-900">{{ $employee->commits_count }}</h4>
                            <small class="text-secondary">Total Commits</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-6 mb-3">
                        <div class="p-3 border rounded-3 bg-light h-100">
                            <h4 class="mb-0 text-slate-900">{{ $employee->present_days }}</h4>
                            <small class="text-secondary">Days Present</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-6 mb-3">
                        <div class="p-3 border rounded-3 bg-light h-100">
                            <h4 class="mb-0 text-slate-900">{{ min(100, round(($employee->tasks_count * 5 + $employee->commits_count * 2))) }}%</h4>
                            <small class="text-secondary">Productivity Est.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Project Allocations -->
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 font-outfit text-slate-900">Project Allocation History</h6>
                <button class="btn btn-sm btn-primary rounded-3" data-bs-toggle="modal" data-bs-target="#allocateModal">Allocate to Project</button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-secondary small">
                            <tr>
                                <th class="ps-4">Project</th>
                                <th>Role on Project</th>
                                <th>Allocated From</th>
                                <th>Allocated To</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($employee->projectAllocations as $alloc)
                                <tr>
                                    <td class="ps-4 text-slate-900 fw-medium">{{ $alloc->project->name }}</td>
                                    <td>{{ $alloc->role_on_project ?: 'Member' }}</td>
                                    <td>{{ $alloc->allocated_from->format('M d, Y') }}</td>
                                    <td>{{ $alloc->allocated_to ? $alloc->allocated_to->format('M d, Y') : 'Present' }}</td>
                                    <td>
                                        @if($alloc->status == 'active')
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success px-2 rounded-pill">Active</span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary px-2 rounded-pill">Rolled Off</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        @if($alloc->status == 'active')
                                            <form action="{{ route('manager.project-allocations.update', $alloc->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Roll off from this project?')">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="status" value="rolled_off">
                                                <input type="hidden" name="allocated_to" value="{{ now()->format('Y-m-d') }}">
                                                <button class="btn btn-sm btn-outline-warning rounded-3 py-1">Roll Off</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted py-4">No project allocation history.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Allocate Modal -->
<div class="modal fade" id="allocateModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('manager.project-allocations.store') }}" method="POST" class="modal-content rounded-4 border-0 shadow">
            @csrf
            <input type="hidden" name="team_member_id" value="{{ $employee->id }}">
            <div class="modal-header">
                <h5 class="modal-title font-outfit">Allocate Project</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Project</label>
                    <select name="project_id" class="form-select" required>
                        <option value="">Select Project</option>
                        @foreach($projects as $proj)
                            <option value="{{ $proj->id }}">{{ $proj->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Role on Project</label>
                    <input type="text" name="role_on_project" class="form-control" placeholder="e.g. Frontend Dev">
                </div>
                <div class="mb-3">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="allocated_from" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary rounded-3">Allocate</button>
            </div>
        </form>
    </div>
</div>
@endsection
