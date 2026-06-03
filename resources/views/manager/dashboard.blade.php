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
    <div class="col-sm-6 col-lg-3">
        <div class="card glass-card p-4 h-100">
            <span class="text-uppercase text-secondary small font-weight-bold">Total Members</span>
            <h3 class="h2 font-outfit mt-2 mb-1 text-white">{{ $totalMembers }}</h3>
            <p class="text-secondary small mb-0 mt-auto">Active resources registered</p>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card glass-card p-4 h-100">
            <span class="text-uppercase text-secondary small font-weight-bold">Total Tasks</span>
            <h3 class="h2 font-outfit mt-2 mb-1 text-white">{{ $totalTasks }}</h3>
            <p class="text-secondary small mb-0 mt-auto">Assigned workflow items</p>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card glass-card p-4 h-100">
            <span class="text-uppercase text-secondary small font-weight-bold">Git Commits Today</span>
            <h3 class="h2 font-outfit mt-2 mb-1 text-white">{{ $totalCommits }}</h3>
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
                        <td class="py-3 font-semibold text-slate-100">{{ $report->report_date->format('M d, Y') }}</td>
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

@endsection

@section('scripts')
<script>
    function setGeneratingState(btn) {
        btn.disabled = true;
        document.getElementById('generate-text').innerText = "Analyzing team data...";
        document.getElementById('generate-form').submit();
    }
</script>
@endsection
