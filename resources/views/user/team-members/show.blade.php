@extends('layouts.manager')

@section('title', $teamMember->name . ' - Team Member')
@section('page_title', 'Team Member Profile')

@section('content')
<div class="container-fluid">
    <div class="mb-4 d-flex align-items-center">
        <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm me-3 rounded-circle p-2 d-flex align-items-center" style="width:32px; height:32px;">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <h4 class="mb-0 font-outfit text-slate-900">{{ $teamMember->name }}</h4>
    </div>

    <div class="row">
        <!-- Basic Info -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 premium-shadow">
                <div class="card-body text-center p-4">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px; font-size: 32px;">
                        {{ substr($teamMember->name, 0, 1) }}
                    </div>
                    <h5 class="font-outfit text-slate-900 mb-1">{{ $teamMember->name }}</h5>
                    <p class="text-secondary mb-3">{{ $teamMember->designation ? $teamMember->designation->name : $teamMember->role }}</p>
                    
                    <a href="mailto:{{ $teamMember->email }}" class="btn btn-outline-primary rounded-pill px-4 mb-4">
                        <i class="bi bi-envelope me-2"></i> Send Email
                    </a>

                    <hr class="border-secondary-subtle">
                    
                    <div class="text-start mt-4">
                        <h6 class="font-outfit fw-bold text-slate-900 mb-3">Skills & Expertise</h6>
                        <div class="d-flex flex-wrap gap-2">
                            @forelse($teamMember->skills as $skill)
                                <span class="badge bg-light text-dark border px-2 py-1">{{ $skill->name }}</span>
                            @empty
                                <span class="text-muted small">No skills listed.</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4 premium-shadow">
                <div class="card-header bg-transparent border-0 pt-4 pb-0">
                    <h5 class="font-outfit fw-bold text-slate-900 mb-0">Performance Overview</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row text-center">
                        <div class="col-md-6 mb-3">
                            <div class="p-3 border rounded-3 bg-light h-100">
                                <h4 class="mb-0 text-slate-900">{{ $completedTasks }} / {{ $totalTasks }}</h4>
                                <span class="text-secondary small">Tasks Completed</span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="p-3 border rounded-3 bg-light h-100">
                                <h4 class="mb-0 text-slate-900">{{ $commits->count() }}</h4>
                                <span class="text-secondary small">Recent Commits</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 premium-shadow">
                <div class="card-header bg-transparent border-0 pt-4 pb-0">
                    <h5 class="font-outfit fw-bold text-slate-900 mb-0">Active Projects</h5>
                </div>
                <div class="card-body p-4">
                    <ul class="list-group list-group-flush">
                        @forelse($teamMember->projectAllocations as $alloc)
                            @if(strtolower($alloc->status) == 'active')
                                <li class="list-group-item px-0 py-3 border-secondary-subtle">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1 fw-bold text-slate-900">{{ $alloc->project->name }}</h6>
                                            <span class="text-secondary small"><i class="bi bi-briefcase me-1"></i> Role: {{ $alloc->role }}</span>
                                        </div>
                                        <div>
                                            <span class="badge bg-primary rounded-pill">{{ $alloc->allocation_percentage }}% Allocated</span>
                                        </div>
                                    </div>
                                </li>
                            @endif
                        @empty
                            <li class="list-group-item px-0 py-3 border-0 text-muted">No active projects assigned.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
