@extends('layouts.manager')
<!-- resources/views/manager/dashboard.blade.php -->

@section('title', 'Manager Dashboard - Manager Agent')
@section('page_title', 'Performance Dashboard')

@section('content')
@php
    $pct = $latestReport ? $latestReport->team_productivity : 0;
    $offset = 314.16 - ($pct / 100) * 314.16;

    if ($pct >= 80) {
        $colorText = 'text-success';
        $colorStroke = 'stroke-success';
        $statusLabel = '🟢 Strong Team Momentum';
    } elseif ($pct >= 60) {
        $colorText = 'text-warning';
        $colorStroke = 'stroke-warning';
        $statusLabel = '🟡 Moderate Output';
    } else {
        $colorText = 'text-danger';
        $colorStroke = 'stroke-danger';
        $statusLabel = '🔴 Alert: High Blockers Found';
    }
@endphp

<div class="row g-4 align-items-center mb-4">
    <div class="col-md-8">
        <h2 class="h3 font-outfit text-white mb-0">Management Overview</h2>
        <p class="text-secondary small mb-0">AI-generated performance analytics and evening dashboard reports.</p>
    </div>
    <div class="col-md-4 text-md-end">
        <form method="POST" action="{{ route('manager.generate') }}" id="generate-form">
            @csrf
            <button type="submit" class="btn accent-btn d-inline-flex align-items-center" onclick="setGeneratingState(this)">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"/>
                    <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z"/>
                </svg>
                <span id="generate-text">Generate Evening Report</span>
            </button>
        </form>
    </div>
</div>

<!-- Metrics Cards Row -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card glass-card p-4 h-100">
            <span class="text-uppercase text-secondary small font-weight-bold">Team Productivity</span>
            <h3 class="h2 font-outfit mt-2 mb-1 {{ $latestReport ? $colorText : 'text-secondary' }}">
                {{ $latestReport ? $pct . '%' : 'N/A' }}
            </h3>
            <p class="text-secondary small mb-0 mt-auto">Daily evaluated average</p>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#membersModal">
        <div class="card glass-card p-4 h-100 hover-card">
            <span class="text-uppercase text-secondary small font-weight-bold">Total Members</span>
            <h3 class="h2 font-outfit mt-2 mb-1 text-white d-flex justify-content-between align-items-center">
                <span>{{ $totalMembers }}</span>
                <span class="text-xs text-primary font-weight-normal font-sans" style="font-size: 10px;">View Table &rarr;</span>
            </h3>
            <p class="text-secondary small mb-0 mt-auto">Active resources registered</p>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#tasksModal">
        <div class="card glass-card p-4 h-100 hover-card">
            <span class="text-uppercase text-secondary small font-weight-bold">Total Tasks</span>
            <h3 class="h2 font-outfit mt-2 mb-1 text-white d-flex justify-content-between align-items-center">
                <span>{{ $totalTasks }}</span>
                <span class="text-xs text-primary font-weight-normal font-sans" style="font-size: 10px;">View List &rarr;</span>
            </h3>
            <p class="text-secondary small mb-0 mt-auto">Assigned workflow items</p>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#commitsModal">
        <div class="card glass-card p-4 h-100 hover-card">
            <span class="text-uppercase text-secondary small font-weight-bold">Total Commits</span>
            <h3 class="h2 font-outfit mt-2 mb-1 text-white d-flex justify-content-between align-items-center">
                <span>{{ $allCommits->count() }}</span>
                <span class="text-xs text-primary font-weight-normal font-sans" style="font-size: 10px;">View Log &rarr;</span>
            </h3>
            <p class="text-secondary small mb-0 mt-auto">Daily repository updates</p>
        </div>
    </div>
</div>

@if($latestReport)
    <div class="row g-4 mb-4">
        <!-- Circular Progress Ring Card -->
        <div class="col-lg-4">
            <div class="card glass-card p-4 h-100 text-center d-flex flex-column align-items-center justify-content-center">
                <span class="text-uppercase text-secondary small font-weight-bold mb-4">Productivity Index</span>
                
                <div class="position-relative d-inline-flex align-items-center justify-content-center" style="width: 180px; height: 180px;">
                    <svg width="180" height="180" class="position-absolute" style="transform: rotate(-90deg);">
                        <!-- Background Circle -->
                        <circle cx="90" cy="90" r="70" stroke="#334155" stroke-width="10" fill="transparent" />
                        <!-- Value Circle -->
                        <circle cx="90" cy="90" r="70" class="{{ $colorStroke }}" stroke-width="10" 
                                stroke-dasharray="439.82" stroke-dashoffset="{{ 439.82 - ($pct / 100) * 439.82 }}" 
                                stroke-linecap="round" fill="transparent" style="transition: stroke-dashoffset 1s ease-out;" />
                    </svg>
                    <div class="d-flex flex-column align-items-center justify-content-center">
                        <span class="h1 font-outfit text-white mb-0 font-weight-bold">{{ $pct }}%</span>
                        <span class="text-uppercase text-secondary small mt-1 font-weight-semibold">Rating</span>
                    </div>
                </div>
                
                <span class="text-secondary small mt-4 font-weight-medium">{{ $statusLabel }}</span>
            </div>
        </div>

        <!-- Performing and Warnings Grids -->
        <div class="col-lg-8">
            <div class="row g-4 h-100">
                <!-- Top Performers List -->
                <div class="col-md-6">
                    <div class="card glass-card p-4 h-100">
                        <h4 class="h5 font-outfit text-white mb-4 d-flex align-items-center">
                            <span class="d-inline-block bg-success rounded-circle me-2 shadow-lg" style="width: 10px; height: 10px;"></span>
                            Top Performers
                        </h4>
                        
                        @if(!empty($latestReport->top_performers) && is_array($latestReport->top_performers))
                            <ul class="list-unstyled mb-0">
                                @foreach($latestReport->top_performers as $performer)
                                    <li class="d-flex align-items-center justify-content-between mb-3 text-white small">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 24px; height: 24px; background-color: rgba(16, 185, 129, 0.1) !important;">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                                                    <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/>
                                                </svg>
                                            </div>
                                            <span class="font-weight-medium">{{ $performer }}</span>
                                        </div>
                                        <span class="badge text-success border border-success-subtle bg-success bg-opacity-10 px-2 py-1 text-uppercase" style="font-size: 8px;">Star Badge</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-secondary small italic mb-0">No entries logged today.</p>
                        @endif
                    </div>
                </div>

                <!-- Attention Required List -->
                <div class="col-md-6">
                    <div class="card glass-card p-4 h-100">
                        <h4 class="h5 font-outfit text-white mb-4 d-flex align-items-center">
                            <span class="d-inline-block bg-danger rounded-circle me-2 shadow-lg" style="width: 10px; height: 10px;"></span>
                            Attention Required
                        </h4>

                        @if(!empty($latestReport->attention_required) && is_array($latestReport->attention_required))
                            <ul class="list-unstyled mb-0">
                                @foreach($latestReport->attention_required as $needsAttention)
                                    <li class="d-flex align-items-start mb-3 text-white small">
                                        <div class="bg-danger-subtle text-danger rounded-circle d-flex align-items-center justify-content-center me-2 mt-0.5" style="width: 24px; height: 24px; flex-shrink: 0; background-color: rgba(244, 63, 94, 0.1) !important;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                                                <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/>
                                            </svg>
                                        </div>
                                        <span class="font-weight-medium align-self-center">{{ $needsAttention }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-secondary small italic mb-0">No alerts logged today.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Identified Risks Panel -->
    <div class="card glass-card p-4 mb-4">
        <h4 class="h5 font-outfit text-white mb-3 d-flex align-items-center">
            <span class="d-inline-block bg-warning rounded-circle me-2 shadow-lg" style="width: 10px; height: 10px;"></span>
            Identified Risks & Roadblocks
        </h4>
        
        @if(!empty($latestReport->risks) && is_array($latestReport->risks))
            <ul class="list-unstyled mb-0">
                @foreach($latestReport->risks as $risk)
                    <li class="d-flex align-items-start mb-2.5 text-slate-300 small">
                        <div class="bg-warning rounded-circle me-2.5 mt-1.5 shadow-md shadow-warning-500/50" style="width: 8px; height: 8px; flex-shrink: 0;"></div>
                        <span class="font-weight-medium">{{ $risk }}</span>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-secondary small italic mb-0">No risks identified.</p>
        @endif
    </div>

    <!-- Full AI Narrative Container -->
    <div class="card glass-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center pb-3 border-bottom border-slate-800">
            <h4 class="h5 font-outfit text-white mb-0 d-flex align-items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2 text-primary" viewBox="0 0 16 16">
                    <path d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h13zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13z"/>
                    <path d="M7 5.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm-1.496-.854a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 1 1 .708-.708l.146.147 1.146-1.147a.5.5 0 0 1 .708 0zM7 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm-1.496-.854a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 0 1 .708-.708l.146.147 1.146-1.147a.5.5 0 0 1 .708 0z"/>
                </svg>
                Complete Narrative AI Review
            </h4>
            <button class="btn btn-sm btn-outline-secondary border-0" type="button" data-bs-toggle="collapse" data-bs-target="#narrativeCollapse" aria-expanded="true" aria-controls="narrativeCollapse">
                Toggle Collapse
            </button>
        </div>
        
        <div class="collapse show" id="narrativeCollapse">
            <div class="mt-4 text-slate-300 small whitespace-pre-wrap max-h-96 overflow-y-auto pr-2" style="line-height: 1.625;">
                {!! nl2br(e($latestReport->full_report)) !!}
            </div>
        </div>
    </div>
@else
    <!-- Empty State -->
    <div class="card glass-card p-5 mb-4 text-center border-dashed border-slate-700 py-5">
        <div class="d-inline-flex align-items-center justify-content-center rounded-4 bg-slate-900 border border-slate-800 text-primary mb-4 shadow" style="width: 64px; height: 64px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" viewBox="0 0 16 16">
                <path d="M6 12.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5ZM3 8.062C3 6.7 4 5.65 5.5 5.65h5c1.5 0 2.5 1.05 2.5 2.412v3.838a1.5 1.5 0 0 1-1.5 1.5h-8A1.5 1.5 0 0 1 3 11.9V8.062Zm2.5-.912C4.338 7.15 3.5 7.9 3.5 9v1.5a.5.5 0 0 0 1 0v-1.5c0-.276.224-.5.5-.5h5c.276 0 .5.224.5.5v1.5a.5.5 0 0 0 1 0v-1.5c0-1.1-.838-1.85-2-1.85h-5Z"/>
            </svg>
        </div>
        <h4 class="h5 font-outfit text-white">No performance evaluations found</h4>
        <p class="text-secondary small mx-auto mt-1 mb-4" style="max-width: 380px;">Click the button in the upper right corner to fetch daily tasks, check-in logs, git commit indices, and generate the report.</p>
    </div>
@endif

<!-- History Table -->
<div class="card glass-card p-4">
    <h4 class="h5 font-outfit text-white mb-4 d-flex align-items-center">
        <span class="d-inline-block bg-primary rounded-circle me-2 shadow-lg" style="width: 10px; height: 10px;"></span>
        Report History
    </h4>
    
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 text-white" style="--bs-table-bg: transparent; --bs-table-hover-bg: rgba(255, 255, 255, 0.02); --bs-table-border-color: #334155;">
            <thead class="text-secondary" style="font-size: 11px;">
                <tr>
                    <th scope="col" class="pb-3 border-slate-800 uppercase font-semibold tracking-wider">Report Date</th>
                    <th scope="col" class="pb-3 border-slate-800 uppercase font-semibold tracking-wider">Productivity Index</th>
                    <th scope="col" class="pb-3 border-slate-800 uppercase font-semibold tracking-wider">Status</th>
                    <th scope="col" class="pb-3 border-slate-800 uppercase font-semibold tracking-wider text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reports as $report)
                    @php
                        $histPct = $report->team_productivity;
                        if ($histPct >= 80) {
                            $badgeClass = 'bg-success bg-opacity-10 text-success border border-success border-opacity-20';
                            $textClass = 'text-success';
                            $label = 'Stable';
                        } elseif ($histPct >= 60) {
                            $badgeClass = 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-20';
                            $textClass = 'text-warning';
                            $label = 'Warning';
                        } else {
                            $badgeClass = 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-20';
                            $textClass = 'text-danger';
                            $label = 'Critical';
                        }
                    @endphp
                    <tr>
                        <td class="py-3 font-semibold text-slate-100">
                            <div>{{ $report->report_date->format('M d, Y') }}</div>
                            <div class="text-secondary small font-normal mt-0.5" style="font-size: 10px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" class="text-secondary me-1 align-middle" viewBox="0 0 16 16">
                                    <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/>
                                    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/>
                                </svg>
                                <span class="align-middle">{{ $report->created_at->format('h:i A') }}</span>
                            </div>
                        </td>
                        <td class="py-3 font-bold {{ $textClass }}">
                            {{ $histPct }}%
                            <div class="progress d-none d-sm-inline-flex ms-2 align-self-center" style="width: 60px; height: 5px; background-color: #334155;">
                                <div class="progress-bar {{ $histPct >= 80 ? 'bg-success' : ($histPct >= 60 ? 'bg-warning' : 'bg-danger') }}" role="progressbar" style="width: {{ $histPct }}%" aria-valuenow="{{ $histPct }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </td>
                        <td class="py-3">
                            <span class="badge rounded-pill {{ $badgeClass }} px-2.5 py-1" style="font-size: 10px;">{{ $label }}</span>
                        </td>
                        <td class="py-3 text-end">
                            <a href="{{ route('manager.report-detail', $report->id) }}" class="text-primary font-semibold text-decoration-none small">View Details</a>
                        </td>
                    </tr>
                @endforeach
                @if($reports->isEmpty())
                    <tr>
                        <td colspan="4" class="text-center py-5 text-secondary italic small">No performance history logged yet.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

<!-- Custom Styles for Hover Effects -->
@section('styles')
<style>
    .hover-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }
    .hover-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 20px -3px rgba(0, 0, 0, 0.4), 0 4px 10px -4px rgba(168, 85, 247, 0.1);
        border-color: rgba(168, 85, 247, 0.4) !important;
    }
    .btn-xs {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        border-radius: 0.25rem;
    }
</style>
@endsection

<!-- Team Members Modal -->
<div class="modal fade" id="membersModal" tabindex="-1" aria-labelledby="membersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content text-white" style="background-color: #0b0f19; border: 1px solid #334155; border-radius: 20px;">
            <div class="modal-header border-bottom border-slate-800 p-4">
                <h5 class="modal-title font-outfit text-white" id="membersModalLabel">Team Members Registry</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Add Member Collapse Form -->
                <button type="button" class="btn btn-sm btn-primary mb-3" data-bs-toggle="collapse" data-bs-target="#addMemberCollapse" aria-expanded="false" aria-controls="addMemberCollapse">
                    + Add Team Member
                </button>
                <div class="collapse mb-3" id="addMemberCollapse">
                    <div class="card p-3" style="background-color: #1e293b; border: 1px solid #334155;">
                        <form action="{{ route('manager.store-team-member') }}" method="POST">
                            @csrf
                            <div class="row g-2">
                                <div class="col-md-3">
                                    <input type="text" name="name" class="form-control form-control-sm" placeholder="Name" required>
                                </div>
                                <div class="col-md-3">
                                    <input type="text" name="role" class="form-control form-control-sm" placeholder="Role (e.g., Backend Dev)" required>
                                </div>
                                <div class="col-md-3">
                                    <input type="email" name="email" class="form-control form-control-sm" placeholder="Email" required>
                                </div>
                                <div class="col-md-3">
                                    <input type="text" name="github_id" class="form-control form-control-sm" placeholder="GitHub Username">
                                </div>
                                <div class="col-12 text-end mt-2">
                                    <button type="submit" class="btn btn-sm btn-success">Save Member</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-white" style="--bs-table-bg: transparent; --bs-table-hover-bg: rgba(255, 255, 255, 0.02); --bs-table-border-color: #1e293b;">
                        <thead class="text-secondary" style="font-size: 11px;">
                            <tr>
                                <th scope="col" class="pb-3 uppercase font-semibold tracking-wider">#</th>
                                <th scope="col" class="pb-3 uppercase font-semibold tracking-wider">Name</th>
                                <th scope="col" class="pb-3 uppercase font-semibold tracking-wider">Role</th>
                                <th scope="col" class="pb-3 uppercase font-semibold tracking-wider">Email</th>
                                <th scope="col" class="pb-3 uppercase font-semibold tracking-wider">GitHub ID</th>
                                <th scope="col" class="pb-3 uppercase font-semibold tracking-wider text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($allMembers as $member)
                                <tr id="member-row-{{ $member->id }}">
                                    <td class="py-3 text-secondary">{{ $loop->iteration }}</td>
                                    <td class="py-3">
                                        <span class="view-mode font-semibold text-slate-100">{{ $member->name }}</span>
                                        <input type="text" name="name" form="edit-member-form-{{ $member->id }}" value="{{ $member->name }}" class="form-control form-control-sm edit-mode d-none" required>
                                    </td>
                                    <td class="py-3">
                                        <span class="view-mode text-slate-300">{{ $member->role }}</span>
                                        <input type="text" name="role" form="edit-member-form-{{ $member->id }}" value="{{ $member->role }}" class="form-control form-control-sm edit-mode d-none" required>
                                    </td>
                                    <td class="py-3">
                                        <span class="view-mode text-slate-400">{{ $member->email }}</span>
                                        <input type="email" name="email" form="edit-member-form-{{ $member->id }}" value="{{ $member->email }}" class="form-control form-control-sm edit-mode d-none" required>
                                    </td>
                                    <td class="py-3">
                                        <span class="view-mode font-mono text-purple-400">{{ $member->github_id ?? 'N/A' }}</span>
                                        <input type="text" name="github_id" form="edit-member-form-{{ $member->id }}" value="{{ $member->github_id }}" class="form-control form-control-sm edit-mode d-none">
                                    </td>
                                    <td class="py-3 text-end">
                                        <form id="edit-member-form-{{ $member->id }}" action="{{ route('manager.update-team-member', $member->id) }}" method="POST" class="d-none">
                                            @csrf
                                            @method('PUT')
                                        </form>
                                        <button type="button" class="btn btn-xs btn-outline-info view-mode" onclick="toggleEditMode({{ $member->id }}, 'member')">Edit</button>
                                        <button type="submit" form="edit-member-form-{{ $member->id }}" class="btn btn-xs btn-success edit-mode d-none">Save</button>
                                        <button type="button" class="btn btn-xs btn-outline-secondary edit-mode d-none ms-1" onclick="toggleEditMode({{ $member->id }}, 'member')">Cancel</button>
                                        
                                        <form action="{{ route('manager.destroy-team-member', $member->id) }}" method="POST" class="d-inline ms-1" onsubmit="return confirm('Are you sure you want to delete this member?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-outline-danger view-mode">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-secondary italic">No team members registered.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-top border-slate-800 p-4">
                <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Total Tasks Modal -->
<div class="modal fade" id="tasksModal" tabindex="-1" aria-labelledby="tasksModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content text-white" style="background-color: #0b0f19; border: 1px solid #334155; border-radius: 20px;">
            <div class="modal-header border-bottom border-slate-800 p-4">
                <h5 class="modal-title font-outfit text-white" id="tasksModalLabel">Workflow Tasks Ledger</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Add Task Collapse Form -->
                <button type="button" class="btn btn-sm btn-primary mb-3" data-bs-toggle="collapse" data-bs-target="#addTaskCollapse" aria-expanded="false" aria-controls="addTaskCollapse">
                    + Add New Task
                </button>
                <div class="collapse mb-3" id="addTaskCollapse">
                    <div class="card p-3" style="background-color: #1e293b; border: 1px solid #334155;">
                        <form action="{{ route('manager.store-task') }}" method="POST">
                            @csrf
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <input type="text" name="title" class="form-control form-control-sm" placeholder="Task Title" required>
                                </div>
                                <div class="col-md-3">
                                    <select name="team_member_id" class="form-select form-select-sm" required>
                                        <option value="" disabled selected>Assign Employee</option>
                                        @foreach($allMembers as $m)
                                            <option value="{{ $m->id }}">{{ $m->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="status" class="form-select form-select-sm" required>
                                        <option value="pending">Pending</option>
                                        <option value="in_progress">In Progress</option>
                                        <option value="completed">Completed</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input type="date" name="due_date" class="form-control form-control-sm" required>
                                </div>
                                <div class="col-12 text-end mt-2">
                                    <button type="submit" class="btn btn-sm btn-success">Save Task</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-white" style="--bs-table-bg: transparent; --bs-table-hover-bg: rgba(255, 255, 255, 0.02); --bs-table-border-color: #1e293b;">
                        <thead class="text-secondary" style="font-size: 11px;">
                            <tr>
                                <th scope="col" class="pb-3 uppercase font-semibold tracking-wider">#</th>
                                <th scope="col" class="pb-3 uppercase font-semibold tracking-wider">Task Title</th>
                                <th scope="col" class="pb-3 uppercase font-semibold tracking-wider">Assigned Employee</th>
                                <th scope="col" class="pb-3 uppercase font-semibold tracking-wider">Status</th>
                                <th scope="col" class="pb-3 uppercase font-semibold tracking-wider">Due Date</th>
                                <th scope="col" class="pb-3 uppercase font-semibold tracking-wider text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($allTasks as $task)
                                <tr id="task-row-{{ $task->id }}">
                                    <td class="py-3 text-secondary">{{ $loop->iteration }}</td>
                                    <td class="py-3">
                                        <span class="view-mode font-semibold text-slate-100">{{ $task->title }}</span>
                                        <input type="text" name="title" form="edit-task-form-{{ $task->id }}" value="{{ $task->title }}" class="form-control form-control-sm edit-mode d-none" required>
                                    </td>
                                    <td class="py-3">
                                        <span class="view-mode text-slate-300">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-indigo-500 bg-opacity-10 text-indigo-400 rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 22px; height: 22px; font-size: 10px; background-color: rgba(99, 102, 241, 0.1) !important;">
                                                    {{ substr($task->teamMember?->name ?? '?', 0, 1) }}
                                                </div>
                                                <span>{{ $task->teamMember?->name ?? 'Unassigned' }}</span>
                                            </div>
                                        </span>
                                        <select name="team_member_id" form="edit-task-form-{{ $task->id }}" class="form-select form-select-sm edit-mode d-none" required>
                                            @foreach($allMembers as $m)
                                                <option value="{{ $m->id }}" {{ $task->team_member_id == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="py-3">
                                        <span class="view-mode">
                                            @if($task->status === 'completed')
                                                <span class="badge rounded-pill bg-success bg-opacity-10 text-success border border-success border-opacity-20 px-2.5 py-1" style="font-size: 10px;">Completed</span>
                                            @elseif($task->status === 'in_progress')
                                                <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning border border-warning border-opacity-20 px-2.5 py-1" style="font-size: 10px;">In Progress</span>
                                            @else
                                                <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger border border-danger border-opacity-20 px-2.5 py-1" style="font-size: 10px;">Pending</span>
                                            @endif
                                        </span>
                                        <select name="status" form="edit-task-form-{{ $task->id }}" class="form-select form-select-sm edit-mode d-none" required>
                                            <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                            <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                        </select>
                                    </td>
                                    <td class="py-3 text-slate-400">
                                        <span class="view-mode">{{ \Carbon\Carbon::parse($task->due_date)->format('M d, Y') }}</span>
                                        <input type="date" name="due_date" form="edit-task-form-{{ $task->id }}" value="{{ \Carbon\Carbon::parse($task->due_date)->format('Y-m-d') }}" class="form-control form-control-sm edit-mode d-none" required>
                                    </td>
                                    <td class="py-3 text-end">
                                        <form id="edit-task-form-{{ $task->id }}" action="{{ route('manager.update-task', $task->id) }}" method="POST" class="d-none">
                                            @csrf
                                            @method('PUT')
                                        </form>
                                        <button type="button" class="btn btn-xs btn-outline-info view-mode" onclick="toggleEditMode({{ $task->id }}, 'task')">Edit</button>
                                        <button type="submit" form="edit-task-form-{{ $task->id }}" class="btn btn-xs btn-success edit-mode d-none">Save</button>
                                        <button type="button" class="btn btn-xs btn-outline-secondary edit-mode d-none ms-1" onclick="toggleEditMode({{ $task->id }}, 'task')">Cancel</button>
                                        
                                        <form action="{{ route('manager.destroy-task', $task->id) }}" method="POST" class="d-inline ms-1" onsubmit="return confirm('Are you sure you want to delete this task?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-outline-danger view-mode">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-secondary italic">No tasks logged in system.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-top border-slate-800 p-4">
                <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Git Commits Modal -->
<div class="modal fade" id="commitsModal" tabindex="-1" aria-labelledby="commitsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content text-white" style="background-color: #0b0f19; border: 1px solid #334155; border-radius: 20px;">
            <div class="modal-header border-bottom border-slate-800 p-4">
                <h5 class="modal-title font-outfit text-white" id="commitsModalLabel">Version Control Commit Log</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Add Commit Collapse Form -->
                <button type="button" class="btn btn-sm btn-primary mb-3" data-bs-toggle="collapse" data-bs-target="#addCommitCollapse" aria-expanded="false" aria-controls="addCommitCollapse">
                    + Add New Commit
                </button>
                <div class="collapse mb-3" id="addCommitCollapse">
                    <div class="card p-3" style="background-color: #1e293b; border: 1px solid #334155;">
                        <form action="{{ route('manager.store-commit') }}" method="POST">
                            @csrf
                            <div class="row g-2">
                                <div class="col-md-2">
                                    <input type="text" name="commit_hash" class="form-control form-control-sm" placeholder="Hash (e.g. d51a672)" required>
                                </div>
                                <div class="col-md-3">
                                    <input type="text" name="repository_name" class="form-control form-control-sm" placeholder="Repository Name" value="manager-agent" required>
                                </div>
                                <div class="col-md-3">
                                    <select name="team_member_id" class="form-select form-select-sm" required>
                                        <option value="" disabled selected>Developer</option>
                                        @foreach($allMembers as $m)
                                            <option value="{{ $m->id }}">{{ $m->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="message" class="form-control form-control-sm" placeholder="Commit Message" required>
                                </div>
                                <div class="col-md-4 mt-2">
                                    <input type="datetime-local" name="committed_at" class="form-control form-control-sm" required>
                                </div>
                                <div class="col-md-8 text-end mt-2">
                                    <button type="submit" class="btn btn-sm btn-success">Save Commit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-white" style="--bs-table-bg: transparent; --bs-table-hover-bg: rgba(255, 255, 255, 0.02); --bs-table-border-color: #1e293b;">
                        <thead class="text-secondary" style="font-size: 11px;">
                            <tr>
                                <th scope="col" class="pb-3 uppercase font-semibold tracking-wider">#</th>
                                <th scope="col" class="pb-3 uppercase font-semibold tracking-wider">Commit Hash</th>
                                <th scope="col" class="pb-3 uppercase font-semibold tracking-wider">Repository Name</th>
                                <th scope="col" class="pb-3 uppercase font-semibold tracking-wider">Developer</th>
                                <th scope="col" class="pb-3 uppercase font-semibold tracking-wider">GitHub ID</th>
                                <th scope="col" class="pb-3 uppercase font-semibold tracking-wider">Message</th>
                                <th scope="col" class="pb-3 uppercase font-semibold tracking-wider">Committed At</th>
                                <th scope="col" class="pb-3 uppercase font-semibold tracking-wider text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($allCommits as $commit)
                                <tr id="commit-row-{{ $commit->id }}">
                                    <td class="py-3 text-secondary">{{ $loop->iteration }}</td>
                                    <td class="py-3 font-mono text-primary" style="font-size: 13px;">
                                        <span class="view-mode">{{ $commit->commit_hash }}</span>
                                        <input type="text" name="commit_hash" form="edit-commit-form-{{ $commit->id }}" value="{{ $commit->commit_hash }}" class="form-control form-control-sm edit-mode d-none" required>
                                    </td>
                                    <td class="py-3 text-slate-300 font-semibold">
                                        <span class="view-mode">{{ $commit->repository_name ?? 'manager-agent' }}</span>
                                        <input type="text" name="repository_name" form="edit-commit-form-{{ $commit->id }}" value="{{ $commit->repository_name ?? 'manager-agent' }}" class="form-control form-control-sm edit-mode d-none" required>
                                    </td>
                                    <td class="py-3 text-slate-300">
                                        <span class="view-mode">{{ $commit->teamMember?->name ?? 'Unknown' }}</span>
                                        <select name="team_member_id" form="edit-commit-form-{{ $commit->id }}" class="form-select form-select-sm edit-mode d-none" required>
                                            @foreach($allMembers as $m)
                                                <option value="{{ $m->id }}" {{ $commit->team_member_id == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="py-3 font-mono text-purple-400" style="font-size: 12px;">
                                        <span class="view-mode">{{ $commit->teamMember?->github_id ?? 'N/A' }}</span>
                                        <span class="edit-mode d-none text-secondary">Linked to Member</span>
                                    </td>
                                    <td class="py-3 text-slate-100" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        <span class="view-mode">{{ $commit->message }}</span>
                                        <input type="text" name="message" form="edit-commit-form-{{ $commit->id }}" value="{{ $commit->message }}" class="form-control form-control-sm edit-mode d-none" required>
                                    </td>
                                    <td class="py-3 text-slate-400" style="font-size: 12px;">
                                        <span class="view-mode">{{ $commit->committed_at->format('M d, Y h:i A') }}</span>
                                        <input type="datetime-local" name="committed_at" form="edit-commit-form-{{ $commit->id }}" value="{{ $commit->committed_at->format('Y-m-d\TH:i') }}" class="form-control form-control-sm edit-mode d-none" required>
                                    </td>
                                    <td class="py-3 text-end">
                                        <form id="edit-commit-form-{{ $commit->id }}" action="{{ route('manager.update-commit', $commit->id) }}" method="POST" class="d-none">
                                            @csrf
                                            @method('PUT')
                                        </form>
                                        <button type="button" class="btn btn-xs btn-outline-info view-mode" onclick="toggleEditMode({{ $commit->id }}, 'commit')">Edit</button>
                                        <button type="submit" form="edit-commit-form-{{ $commit->id }}" class="btn btn-xs btn-success edit-mode d-none">Save</button>
                                        <button type="button" class="btn btn-xs btn-outline-secondary edit-mode d-none ms-1" onclick="toggleEditMode({{ $commit->id }}, 'commit')">Cancel</button>
                                        
                                        <form action="{{ route('manager.destroy-commit', $commit->id) }}" method="POST" class="d-inline ms-1" onsubmit="return confirm('Are you sure you want to delete this commit?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-outline-danger view-mode">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-secondary italic">No commits recorded in log.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-top border-slate-800 p-4">
                <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function setGeneratingState(btn) {
        btn.disabled = true;
        document.getElementById('generate-text').innerText = "Analyzing team data...";
        document.getElementById('generate-form').submit();
    }

    // Toggle view/edit mode for inline table fields
    function toggleEditMode(rowId, type) {
        const row = document.getElementById(`${type}-row-${rowId}`);
        if (!row) return;
        row.querySelectorAll('.view-mode').forEach(el => el.classList.toggle('d-none'));
        row.querySelectorAll('.edit-mode').forEach(el => el.classList.toggle('d-none'));
    }
</script>
@endsection
