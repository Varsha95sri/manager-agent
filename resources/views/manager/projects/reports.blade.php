@extends('layouts.manager')

@section('title', 'Project Reports - Manager Agent')
@section('page_title', 'Project Reports')

@section('content')
<div class="row g-4 mb-4">
    <!-- Summary Cards -->
    <div class="col-md-3">
        <div class="card glass-card h-100 p-4 border-0">
            <h6 class="text-secondary font-outfit mb-2">Total Projects</h6>
            <h2 class="text-dark mb-0">{{ $projects->count() }}</h2>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card glass-card h-100 p-4 border-0">
            <h6 class="text-success font-outfit mb-2">Completion Rate</h6>
            <h2 class="text-dark mb-0">{{ $completionRate }}%</h2>
            <div class="progress mt-2 bg-light" style="height: 4px;">
                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $completionRate }}%;"></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card glass-card h-100 p-4 border-0 border-start border-4 {{ $delayedProjects->count() > 0 ? 'border-danger' : 'border-success' }}">
            <h6 class="text-secondary font-outfit mb-2">Delayed / High Risk</h6>
            <h2 class="{{ $delayedProjects->count() > 0 ? 'text-danger' : 'text-success' }} mb-0">{{ $delayedProjects->count() }}</h2>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card glass-card h-100 p-4 border-0">
            <h6 class="text-primary font-outfit mb-2">Active Projects</h6>
            <h2 class="text-dark mb-0">{{ $activeProjects->count() }}</h2>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card glass-card p-4 h-100 border-0">
            <h5 class="text-dark font-outfit mb-4">Delayed / High Risk Projects</h5>
            @if($delayedProjects->count() > 0)
                <div class="list-group list-group-flush bg-transparent">
                    @foreach($delayedProjects as $project)
                        <div class="list-group-item bg-transparent border-secondary-subtle px-0 py-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h6 class="text-dark mb-0">{{ $project->name }}</h6>
                                <span class="badge bg-danger/20 text-danger border border-danger/30">High Risk</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center small mt-2">
                                <span class="text-secondary">Progress: <strong class="text-dark">{{ $project->progress_percent }}%</strong></span>
                                <span class="text-secondary">Deadline: <strong class="text-danger">{{ $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('M d, Y') : 'N/A' }}</strong></span>
                            </div>
                            <div class="progress mt-2 bg-light" style="height: 4px;">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $project->progress_percent }}%;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <div class="text-success mb-2"><i class="fas fa-check-circle fa-2x"></i></div>
                    <p class="text-secondary mb-0">No delayed or high-risk projects!</p>
                </div>
            @endif
        </div>
    </div>
    <div class="col-md-6">
        <div class="card glass-card p-4 h-100 border-0">
            <h5 class="text-dark font-outfit mb-4">Active Projects Status</h5>
            @if($activeProjects->count() > 0)
                <div class="list-group list-group-flush bg-transparent">
                    @foreach($activeProjects as $project)
                        <div class="list-group-item bg-transparent border-secondary-subtle px-0 py-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h6 class="text-dark mb-0">{{ $project->name }}</h6>
                                <span class="text-secondary small">{{ $project->category ?? 'Uncategorized' }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center small mt-2">
                                <span class="text-secondary">Health: 
                                    <strong class="{{ $project->health_score > 70 ? 'text-success' : ($project->health_score > 40 ? 'text-warning' : 'text-danger') }}">
                                        {{ $project->health_score }} / 100
                                    </strong>
                                </span>
                                <span class="text-secondary">{{ $project->progress_percent }}% Complete</span>
                            </div>
                            <div class="progress mt-2 bg-light" style="height: 4px;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $project->progress_percent }}%;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <p class="text-secondary mb-0">No active projects currently.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="row mt-2 g-4">
    <div class="col-12">
        <div class="card glass-card p-4 border-0">
            <h5 class="text-dark font-outfit mb-4">Team-wise Project Workload</h5>
            @if(!empty($teamReports) && count($teamReports) > 0)
                <div class="row g-4">
                    @foreach($teamReports as $role => $roleProjects)
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light hover-card h-100">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="font-outfit text-primary mb-0"><i class="fas fa-users me-2"></i>{{ $role }} Team</h6>
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">{{ $roleProjects->count() }} Projects</span>
                                </div>
                                <div class="small">
                                    <ul class="list-unstyled mb-0">
                                        @foreach($roleProjects->take(4) as $p)
                                            <li class="mb-2 d-flex justify-content-between align-items-center">
                                                <span class="text-dark">{{ Str::limit($p->name, 25) }}</span>
                                                <span class="text-secondary">{{ $p->tasks_count }} Tasks</span>
                                            </li>
                                        @endforeach
                                        @if($roleProjects->count() > 4)
                                            <li class="text-muted fst-italic">...and {{ $roleProjects->count() - 4 }} more</li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4">
                    <p class="text-secondary mb-0">No team data available. Assign tasks to employees with roles to generate workload reports.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
