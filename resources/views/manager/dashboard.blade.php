@extends('layouts.manager')
<!-- resources/views/manager/dashboard.blade.php -->

@section('title', 'Manager Dashboard - Manager Agent')
@section('page_title', 'Performance Dashboard')

@section('content')
@php
    $pct = $latestReport ? $latestReport->team_productivity : 0;
    $offset = 314.16 - ($pct / 100) * 314.16;

    if ($pct >= 80) {
        $colorText = 'text-emerald-400';
        $colorStroke = 'stroke-emerald-400';
        $statusLabel = '🟢 Strong Team Momentum';
    } elseif ($pct >= 60) {
        $colorText = 'text-warning';
        $colorStroke = 'stroke-warning';
        $statusLabel = '🟡 Moderate Output';
    } else {
        $colorText = 'text-rose-400';
        $colorStroke = 'stroke-rose-400';
        $statusLabel = '🔴 Alert: High Blockers Found';
    }

    // Task and meeting stats
    $totalTasksCount = $totalTasks;
@endphp

<div class="row g-4 align-items-center mb-4">
    <div class="col-md-8">
        <h2 class="h3 font-outfit text-white mb-0">Management Overview</h2>
        <p class="text-secondary small mb-0">AI-generated performance analytics and evening dashboard reports.</p>
    </div>
    <div class="col-md-4 text-md-end">
        <form method="POST" action="{{ route('manager.generate') }}" id="generate-form" class="d-flex align-items-center justify-content-md-end gap-2">
            @csrf
            <input 
                type="date" 
                name="date" 
                class="form-control border-slate-700 bg-slate-900 text-white rounded-3 px-3 py-2" 
                value="{{ request('date', $targetDate) }}"
                style="color-scheme: dark; width: 150px; font-size: 13px;"
                onchange="window.location.href = '{{ route('manager.dashboard') }}?date=' + this.value"
            >
            <button type="submit" class="btn accent-btn d-inline-flex align-items-center" onclick="generateReport(event, this)">
                <span id="generate-icon-container" class="me-2 d-inline-flex align-items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"/>
                        <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z"/>
                    </svg>
                </span>
                <span id="generate-text">Generate Evening Report</span>
            </button>
        </form>
    </div>
</div>

<!-- Metrics Cards Row -->
<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-4">
    <!-- Card 1: Team Productivity -->
    <div class="col">
        <div class="card glass-card p-3 h-100 hover-card d-flex flex-row align-items-center gap-3" style="--accent-border-hover: rgba(99, 102, 241, 0.5); --accent-glow: rgba(99, 102, 241, 0.25); border-left: 4px solid #6366f1 !important;">
            <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; background-color: rgba(99, 102, 241, 0.12); border: 1px solid rgba(99, 102, 241, 0.2);">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="text-indigo-400" viewBox="0 0 16 16">
                    <path d="M11 2a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                    <path d="M14 14V12a3 3 0 0 0-3-3H5a3 3 0 0 0-3 3v2h12z"/>
                </svg>
            </div>
            <div class="min-w-0 flex-grow-1">
                <span class="text-uppercase text-slate-400 font-semibold" style="font-size: 10px; letter-spacing: 0.05em; display: block;">Team Productivity</span>
                <h3 class="h2 font-outfit mt-1 mb-0 {{ $latestReport ? $colorText : 'text-secondary' }} font-weight-bold">
                    {{ $latestReport ? $pct . '%' : 'N/A' }}
                </h3>
                <span class="text-slate-300 small d-block mt-0.5" style="font-size: 11px; font-weight: 500;">Daily evaluated average</span>
            </div>
        </div>
    </div>

    <!-- Card 2: Total Members -->
    <div class="col" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#membersModal">
        <div class="card glass-card p-3 h-100 hover-card d-flex flex-row align-items-center gap-3" style="--accent-border-hover: rgba(6, 182, 212, 0.5); --accent-glow: rgba(6, 182, 212, 0.25); border-left: 4px solid #06b6d4 !important;">
            <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; background-color: rgba(6, 182, 212, 0.12); border: 1px solid rgba(6, 182, 212, 0.2);">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="text-cyan-400" viewBox="0 0 16 16">
                    <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8Zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002a.274.274 0 0 1-.014.002H7.022ZM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM6.936 9.28a5.88 5.88 0 0 0-1.23-.247A7.35 7.35 0 0 0 5 9c-4 0-5 3-5 4 0 .667.333 1 1 1h4.216A2.238 2.238 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816ZM8.228 11h-1.21a2 2 0 0 1 0-.008c-.001-.246.154-.986.714-1.62.42-.477 1.187-.978 2.502-.978.07 0 .141.001.211.003-.502.5-.838 1.171-.97 1.898-.103.568-.18 1.127-.247 1.705ZM6 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                </svg>
            </div>
            <div class="min-w-0 flex-grow-1">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-uppercase text-slate-400 font-semibold" style="font-size: 10px; letter-spacing: 0.05em;">Total Members</span>
                    <span class="text-xs font-semibold" style="font-size: 10px; color: #06b6d4; opacity: 0.95;">View Table &rarr;</span>
                </div>
                <h3 class="h2 font-outfit mt-1 mb-0 text-white font-weight-bold">
                    {{ $totalMembers }}
                </h3>
                <span class="text-slate-300 small d-block mt-0.5" style="font-size: 11px; font-weight: 500;">Active resources registered</span>
            </div>
        </div>
    </div>

    <!-- Card 3: Total Tasks -->
    <div class="col" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#tasksModal">
        <div class="card glass-card p-3 h-100 hover-card d-flex flex-row align-items-center gap-3" style="--accent-border-hover: rgba(168, 85, 247, 0.5); --accent-glow: rgba(168, 85, 247, 0.25); border-left: 4px solid #a855f7 !important;">
            <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; background-color: rgba(168, 85, 247, 0.12); border: 1px solid rgba(168, 85, 247, 0.2);">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="text-purple-400" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
                </svg>
            </div>
            <div class="min-w-0 flex-grow-1">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-uppercase text-slate-400 font-semibold" style="font-size: 10px; letter-spacing: 0.05em;">Total Tasks</span>
                    <span class="text-xs font-semibold" style="font-size: 10px; color: #c084fc; opacity: 0.95;">View List &rarr;</span>
                </div>
                <h3 class="h2 font-outfit mt-1 mb-0 text-white font-weight-bold">
                    {{ $totalTasks }}
                </h3>
                <span class="text-slate-300 small d-block mt-0.5" style="font-size: 11px; font-weight: 500;">Assigned workflow items</span>
            </div>
        </div>
    </div>

    <!-- Card 4: Complete / Pending Tasks -->
    <div class="col" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#tasksModal">
        <div class="card glass-card p-3 h-100 hover-card d-flex flex-row align-items-center gap-3" style="--accent-border-hover: rgba(16, 185, 129, 0.5); --accent-glow: rgba(16, 185, 129, 0.25); border-left: 4px solid #10b981 !important;">
            <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; background-color: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.2);">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="text-emerald-400" viewBox="0 0 16 16">
                    <path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-5.446z"/>
                </svg>
            </div>
            <div class="min-w-0 flex-grow-1">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-uppercase text-slate-400 font-semibold" style="font-size: 10px; letter-spacing: 0.05em;">Complete / Pending</span>
                    <span class="text-xs font-semibold" style="font-size: 10px; color: #34d399; opacity: 0.95;">Status &rarr;</span>
                </div>
                <h3 class="h2 font-outfit mt-1 mb-0 text-white font-weight-bold d-flex align-items-baseline gap-1">
                    <span class="text-emerald-400">{{ $completedTasksCount }}</span>
                    <span class="text-secondary" style="font-size: 16px;">/</span>
                    <span class="text-warning" style="font-size: 20px;">{{ $pendingTasksCount }}</span>
                </h3>
                <span class="text-slate-300 small d-block mt-0.5" style="font-size: 11px; font-weight: 500;">Completed vs Pending Tasks</span>
            </div>
        </div>
    </div>

    <!-- Card 5: Total Commits -->
    <div class="col" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#commitsModal">
        <div class="card glass-card p-3 h-100 hover-card d-flex flex-row align-items-center gap-3" style="--accent-border-hover: rgba(59, 130, 246, 0.5); --accent-glow: rgba(59, 130, 246, 0.25); border-left: 4px solid #3b82f6 !important;">
            <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; background-color: rgba(59, 130, 246, 0.12); border: 1px solid rgba(59, 130, 246, 0.2);">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="text-blue-400" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M6 3.5A1.5 1.5 0 0 1 7.5 2h1A1.5 1.5 0 0 1 10 3.5v1A1.5 1.5 0 0 1 8.5 6h-1A1.5 1.5 0 0 1 6 4.5v-1zM8.5 5a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1zM14 7.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 7.5v-1A1.5 1.5 0 0 1 3.5 5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h9a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 1 0-1h1a.5.5 0 0 1 1.5 1.5v1z"/>
                </svg>
            </div>
            <div class="min-w-0 flex-grow-1">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-uppercase text-slate-400 font-semibold" style="font-size: 10px; letter-spacing: 0.05em;">Total Commits</span>
                    <span class="text-xs font-semibold" style="font-size: 10px; color: #60a5fa; opacity: 0.95;">View Log &rarr;</span>
                </div>
                <h3 class="h2 font-outfit mt-1 mb-0 text-white font-weight-bold">
                    {{ $totalCommits }}
                </h3>
                <span class="text-slate-300 small d-block mt-0.5" style="font-size: 11px; font-weight: 500;">Daily repository updates</span>
            </div>
        </div>
    </div>

    <!-- Card 6: Meetings -->
    <div class="col" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#meetingsModal">
        <div class="card glass-card p-3 h-100 hover-card d-flex flex-row align-items-center gap-3" style="--accent-border-hover: rgba(244, 63, 94, 0.5); --accent-glow: rgba(244, 63, 94, 0.25); border-left: 4px solid #f43f5e !important;">
            <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; background-color: rgba(244, 63, 94, 0.12); border: 1px solid rgba(244, 63, 94, 0.2);">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="text-rose-400" viewBox="0 0 16 16">
                    <path d="M11 6.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1z"/>
                    <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                </svg>
            </div>
            <div class="min-w-0 flex-grow-1">
                <span class="text-uppercase text-slate-400 font-semibold" style="font-size: 10px; letter-spacing: 0.05em; display: block;">Meetings</span>
                <h3 class="h2 font-outfit mt-1 mb-0 text-white font-weight-bold">
                    {{ $totalMeetingsCount }}
                </h3>
                <span class="text-slate-300 small d-block mt-0.5" style="font-size: 11px; font-weight: 500;">Daily logged meeting notes</span>
            </div>
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
                <div class="col-md-6">
                    <div class="card glass-card p-4 h-100">
                        <h4 class="h5 font-outfit text-white mb-3 d-flex align-items-center justify-content-between">
                            <span class="d-flex align-items-center">
                                <span class="d-inline-block bg-success rounded-circle me-2 shadow-lg" style="width: 10px; height: 10px;"></span>
                                Top Performers
                            </span>
                            @if(!empty($latestReport->top_performers))
                                <span class="badge rounded-pill" style="background:rgba(16,185,129,0.12);color:#34d399;border:1px solid rgba(16,185,129,0.25);font-size:9px;">{{ count($latestReport->top_performers) }} entries</span>
                            @endif
                        </h4>
                        
                        <div style="max-height: 260px; overflow-y: auto; padding-right: 4px;" class="custom-scroll">
                        @if(!empty($latestReport->top_performers) && is_array($latestReport->top_performers))
                            <ul class="list-unstyled mb-0">
                                @foreach($latestReport->top_performers as $performer)
                                    <li class="d-flex align-items-center justify-content-between mb-3 text-white small">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 24px; height: 24px; background-color: rgba(16, 185, 129, 0.1) !important; flex-shrink:0;">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                                                    <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/>
                                                </svg>
                                            </div>
                                            <span class="font-weight-medium">{{ $performer }}</span>
                                        </div>
                                        <span class="badge text-success border border-success-subtle bg-success bg-opacity-10 px-2 py-1 text-uppercase" style="font-size: 8px; flex-shrink:0;">Star</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-secondary small italic mb-0">No entries logged today.</p>
                        @endif
                        </div>
                    </div>
                </div>

                <!-- Attention Required List -->
                <div class="col-md-6">
                    <div class="card glass-card p-4 h-100">
                        <h4 class="h5 font-outfit text-white mb-3 d-flex align-items-center justify-content-between">
                            <span class="d-flex align-items-center">
                                <span class="d-inline-block bg-danger rounded-circle me-2 shadow-lg" style="width: 10px; height: 10px;"></span>
                                Attention Required
                            </span>
                            @if(!empty($latestReport->attention_required))
                                <span class="badge rounded-pill" style="background:rgba(244,63,94,0.12);color:#fb7185;border:1px solid rgba(244,63,94,0.25);font-size:9px;">{{ count($latestReport->attention_required) }} alerts</span>
                            @endif
                        </h4>

                        <div style="max-height: 260px; overflow-y: auto; padding-right: 4px;" class="custom-scroll">
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
            <div id="narrative-content" class="mt-4 text-slate-300 small max-h-96 overflow-y-auto pr-2" style="line-height: 1.625;" data-raw-text="{{ $latestReport->full_report }}">
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

    <!-- Individual Employee AI Audits Card -->
    <div class="card glass-card p-4 mb-4">
        <h4 class="h5 font-outfit text-white mb-3 d-flex align-items-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="me-2 text-primary">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
            Individual Employee AI Audits
        </h4>
        <p class="text-secondary small mb-4">Generate and view real-time AI-synthesized daily performance reports for individual team members.</p>
        
        <div class="row g-3">
            @foreach($allMembers as $member)
                <div class="col-md-6 col-lg-4">
                    <div class="p-3 rounded-4 border border-slate-800 bg-slate-900/40 d-flex justify-content-between align-items-center hover-card" style="transition: all 0.2s;">
                        <div style="min-width: 0; flex-grow: 1; margin-right: 12px;">
                             <h6 class="text-white font-outfit font-semibold mb-0.5 text-truncate">{{ $member->name }}</h6>
                             <span class="text-secondary small text-truncate d-block">{{ $member->role }}</span>
                        </div>
                        <button class="btn btn-sm btn-primary py-1.5 px-3 rounded-3 shrink-0" onclick="showEmployeeReport({{ $member->id }}, '{{ addslashes($member->name) }}')">
                            AI Report
                        </button>
                    </div>
                </div>
            @endforeach
            @if($allMembers->isEmpty())
                <div class="col-12 text-center text-secondary py-3 italic small">No team members registered to audit.</div>
            @endif
        </div>
        
        <div class="mt-4 d-flex justify-content-center">
            {!! $allMembers->appends(request()->except('members_page'))->links() !!}
        </div>
    </div>

    @php
        $groupTasks = [];
        foreach ($groupTasksPaginated as $task) {
            $members = $task->teamMembers;
            if ($members->count() > 1) {
                $sortedMembers = $members->sortBy('id');
                $memberIds = $sortedMembers->pluck('id')->join(',');
                $memberNames = $sortedMembers->pluck('name')->join(' & ');
                
                if (!isset($groupTasks[$memberIds])) {
                    $groupTasks[$memberIds] = [
                        'names' => $memberNames,
                        'tasks' => collect(),
                        'roles' => $sortedMembers->pluck('role')->unique()->join(', ')
                    ];
                }
                $groupTasks[$memberIds]['tasks']->push($task);
            }
        }
    @endphp

    <!-- Group Productivity Analytics Card -->
    <div class="card glass-card p-4 mb-4" style="border-left: 4px solid var(--accent-color) !important;">
        <h4 class="h5 font-outfit text-white mb-3 d-flex align-items-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="me-2 text-accent">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 1 1-6 0 3 3 0 0 1 6 0zm6 3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM7 10a2 2 0 1 1-4 0 2 2 0 0 1 4 0z" />
            </svg>
            Group Productivity Analytics
        </h4>
        <p class="text-secondary small mb-4">Real-time productivity index and checklist completions calculated for developer groups and teams.</p>
        
        <div class="row g-3">
            @forelse($groupTasks as $gId => $gData)
                @php
                    $gTasks = $gData['tasks'];
                    $gTotal = $gTasks->count();
                    $gCompleted = $gTasks->where('status', 'completed')->count();
                    $gPct = $gTotal > 0 ? (int)(($gCompleted / $gTotal) * 100) : 100;
                    
                    if ($gPct >= 80) {
                        $progressColor = 'bg-success';
                        $textColor = 'text-emerald-400';
                    } elseif ($gPct >= 60) {
                        $progressColor = 'bg-warning';
                        $textColor = 'text-warning';
                    } else {
                        $progressColor = 'bg-danger';
                        $textColor = 'text-rose-400';
                    }
                @endphp
                <div class="col-md-6 col-lg-4">
                    <div class="p-3 rounded-4 border border-slate-800 bg-slate-900/40 hover-card" style="cursor: pointer; transition: all 0.2s; --accent-border-hover: rgba(168, 85, 247, 0.4); --accent-glow: rgba(168, 85, 247, 0.1);" onclick="showGroupReport('{{ $gId }}', '{{ addslashes($gData['names']) }}')">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div style="min-width: 0; flex-grow: 1; margin-right: 12px;">
                                <h6 class="text-white font-outfit font-semibold mb-0.5 text-truncate" title="{{ $gData['names'] }}">{{ $gData['names'] }}</h6>
                                <span class="text-slate-400 small text-truncate d-block" style="font-size: 11px;">{{ $gData['roles'] }}</span>
                            </div>
                            <span class="font-outfit font-bold {{ $textColor }}" style="font-size: 16px;">{{ $gPct }}%</span>
                        </div>
                        <div class="progress mb-2" style="height: 6px; background-color: #1e293b;">
                            <div class="progress-bar {{ $progressColor }}" role="progressbar" style="width: {{ $gPct }}%" aria-valuenow="{{ $gPct }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex justify-content-between text-secondary mb-2" style="font-size: 10px;">
                            <span>Tasks: {{ $gCompleted }} / {{ $gTotal }} completed</span>
                            @if($gTasks->first())
                                <span class="text-slate-400" style="font-size:10px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" fill="currentColor" viewBox="0 0 16 16" class="me-1 align-middle"><path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/></svg>
                                    Due: {{ \Carbon\Carbon::parse($gTasks->first()->due_date)->format('M d, Y') }}
                                </span>
                            @endif
                        </div>
                        <div class="d-flex justify-content-end align-items-center border-top border-slate-800/60 pt-2">
                            <span class="text-primary small font-semibold" style="font-size: 11px;">AI Report &rarr;</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center text-slate-400 py-3 italic small">No group tasks logged yet. Assign a task to multiple developers to start tracking team group productivity.</div>
            @endforelse
        </div>
        
        <div class="mt-4 d-flex justify-content-center">
            {!! $groupTasksPaginated->appends(request()->except('groups_page'))->links() !!}
        </div>
    </div>

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
    .hover-slate-800:hover {
        background-color: rgba(255, 255, 255, 0.06);
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

                <!-- Search Input -->
                <div class="mb-3">
                    <input type="text" id="membersModalSearch" class="form-control form-control-sm" placeholder="Search members by name, email, or role..." oninput="onMembersSearchChange(this.value)">
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
                        <tbody id="membersModalTableBody">
                            <!-- Populated via AJAX -->
                        </tbody>
                    </table>
                </div>

                <div id="membersModalPagination" class="d-flex justify-content-between align-items-center mt-3 text-secondary small">
                    <!-- Populated via AJAX -->
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
                            <div class="row g-2 align-items-center">
                                <div class="col-md-3">
                                    <input type="text" name="title" class="form-control form-control-sm" placeholder="Task Title" required>
                                </div>
                                <div class="col-md-3">
                                    <input type="text" name="email" class="form-control form-control-sm" placeholder="Assignee Email(s) (comma separated)" required>
                                </div>
                                <div class="col-md-2">
                                    <select name="status" class="form-select form-select-sm" required>
                                        <option value="pending">Pending</option>
                                        <option value="in_progress">In Progress</option>
                                        <option value="completed">Completed</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="date" name="due_date" class="form-control form-control-sm" required>
                                </div>
                                <div class="col-md-2 text-end">
                                    <button type="submit" class="btn btn-sm btn-success w-100">Save Task</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Search Input -->
                <div class="mb-3">
                    <input type="text" id="tasksModalSearch" class="form-control form-control-sm" placeholder="Search tasks by title or developer..." oninput="onTasksSearchChange(this.value)">
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-white" style="--bs-table-bg: transparent; --bs-table-hover-bg: rgba(255, 255, 255, 0.02); --bs-table-border-color: #1e293b;">
                        <thead class="text-secondary" style="font-size: 11px;">
                            <tr>
                                <th scope="col" class="pb-3 uppercase font-semibold tracking-wider">#</th>
                                <th scope="col" class="pb-3 uppercase font-semibold tracking-wider">Task Title</th>
                                <th scope="col" class="pb-3 uppercase font-semibold tracking-wider">Assigned Employee(s)</th>
                                <th scope="col" class="pb-3 uppercase font-semibold tracking-wider">Status</th>
                                <th scope="col" class="pb-3 uppercase font-semibold tracking-wider">Due Date</th>
                                <th scope="col" class="pb-3 uppercase font-semibold tracking-wider text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="tasksModalTableBody">
                            <!-- Populated via AJAX -->
                        </tbody>
                    </table>
                </div>

                <div id="tasksModalPagination" class="d-flex justify-content-between align-items-center mt-3 text-secondary small">
                    <!-- Populated via AJAX -->
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
                                    <input type="email" name="email" class="form-control form-control-sm" placeholder="Developer Email" required>
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

                <!-- Search Input -->
                <div class="mb-3">
                    <input type="text" id="commitsModalSearch" class="form-control form-control-sm" placeholder="Search commits by message, hash, repository, or developer..." oninput="onCommitsSearchChange(this.value)">
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
                        <tbody id="commitsModalTableBody">
                            <!-- Populated via AJAX -->
                        </tbody>
                    </table>
                </div>

                <div id="commitsModalPagination" class="d-flex justify-content-between align-items-center mt-3 text-secondary small">
                    <!-- Populated via AJAX -->
                </div>
            </div>
            <div class="modal-footer border-top border-slate-800 p-4">
                <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Meetings Modal -->
<div class="modal fade" id="meetingsModal" tabindex="-1" aria-labelledby="meetingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content text-white" style="background-color: #0b0f19; border: 1px solid #334155; border-radius: 20px;">
            <div class="modal-header border-bottom border-slate-800 p-4">
                <h5 class="modal-title font-outfit text-white" id="meetingsModalLabel">Scheduled Meetings Ledger</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-white" style="--bs-table-bg: transparent; --bs-table-hover-bg: rgba(255, 255, 255, 0.02); --bs-table-border-color: #1e293b;">
                        <thead class="text-secondary" style="font-size: 11px;">
                            <tr>
                                <th scope="col" class="pb-3 uppercase font-semibold tracking-wider">#</th>
                                <th scope="col" class="pb-3 uppercase font-semibold tracking-wider">Meeting Title</th>
                                <th scope="col" class="pb-3 uppercase font-semibold tracking-wider">Date</th>
                                <th scope="col" class="pb-3 uppercase font-semibold tracking-wider">Time</th>
                                <th scope="col" class="pb-3 uppercase font-semibold tracking-wider">Group / Team Members</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($allMeetings as $meeting)
                                <tr>
                                    <td class="py-3 text-secondary">{{ $loop->iteration }}</td>
                                    <td class="py-3 font-semibold text-slate-100">{{ $meeting->title }}</td>
                                    <td class="py-3 text-slate-300">{{ \Carbon\Carbon::parse($meeting->meeting_date)->format('M d, Y') }}</td>
                                    <td class="py-3 text-slate-300">{{ $meeting->meeting_time ? \Carbon\Carbon::parse($meeting->meeting_time)->format('h:i A') : '—' }}</td>
                                    <td class="py-3">
                                        <div class="d-flex flex-wrap gap-1">
                                            @forelse($meeting->teamMembers as $m)
                                                <span class="badge bg-indigo-500 bg-opacity-10 text-indigo-400 border border-indigo-500 border-opacity-20 px-2 py-1" style="font-size: 10px;">{{ $m->name }}</span>
                                            @empty
                                                <span class="text-secondary small italic">All Members / General</span>
                                            @endforelse
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-secondary italic">No meetings scheduled in log.</td>
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

<!-- Employee AI Report Modal -->
<div class="modal fade" id="employeeReportModal" tabindex="-1" aria-labelledby="employeeReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content text-white shadow-2xl" style="background-color: #0b0f19; border: 1px solid #334155; border-radius: 20px;">
            <div class="modal-header border-bottom border-slate-800 p-4">
                <div>
                    <h5 class="modal-title font-outfit text-white mb-0.5" id="employeeReportModalLabel">Employee Evening AI Audit</h5>
                    <span id="employee-report-meta" class="text-secondary small">Generating report...</span>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="background-color: #0f172a; min-height: 250px;">
                <div id="employee-report-spinner" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="text-secondary mt-3 mb-0">Analyzing today's activity log...</p>
                </div>
                <div id="employee-report-content" class="text-slate-300 small d-none" style="line-height: 1.625;">
                    <!-- Rendered markdown content goes here -->
                </div>
            </div>
            <div class="modal-footer border-top border-slate-800 p-4">
                <button type="button" class="btn btn-secondary rounded-3 px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function generateReport(event, btn) {
        event.preventDefault();
        btn.disabled = true;

        const textSpan = document.getElementById('generate-text');
        const iconContainer = document.getElementById('generate-icon-container');

        textSpan.innerText = "Analyzing team data...";
        iconContainer.innerHTML = `
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        `;

        const form = document.getElementById('generate-form');
        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (response.ok) {
                return response.json();
            }
            return response.json().then(data => {
                throw new Error(data.message || 'Server error occurred.');
            });
        })
        .then(data => {
            // Successfully generated report, reload the page
            window.location.reload();
        })
        .catch(error => {
            console.error('Error generating report:', error);
            alert('Failed to generate report: ' + error.message);
            btn.disabled = false;
            textSpan.innerText = "Generate Evening Report";
            iconContainer.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"/>
                    <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z"/>
                </svg>
            `;
        });
    }

    // Toggle view/edit mode for inline table fields
    function toggleEditMode(rowId, type) {
        const row = document.getElementById(`${type}-row-${rowId}`);
        if (!row) return;
        row.querySelectorAll('.view-mode').forEach(el => el.classList.toggle('d-none'));
        row.querySelectorAll('.edit-mode').forEach(el => el.classList.toggle('d-none'));
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const narrativeContent = document.getElementById('narrative-content');
        if (narrativeContent && typeof marked !== 'undefined') {
            narrativeContent.innerHTML = marked.parse(narrativeContent.getAttribute('data-raw-text'));
        }
    });

    // Show AI report for employee
    function showEmployeeReport(memberId, memberName) {
        const modalEl = document.getElementById('employeeReportModal');
        const modal = new bootstrap.Modal(modalEl);
        
        // Reset/Update Modal Header Title
        document.getElementById('employeeReportModalLabel').innerText = 'Employee Evening AI Audit';
        
        const metaEl = document.getElementById('employee-report-meta');
        const spinnerEl = document.getElementById('employee-report-spinner');
        const contentEl = document.getElementById('employee-report-content');
        
        metaEl.innerText = `${memberName} — Preparing report...`;
        spinnerEl.classList.remove('d-none');
        contentEl.classList.add('d-none');
        contentEl.innerHTML = '';
        
        modal.show();
        
        const urlParams = new URLSearchParams(window.location.search);
        const filterDate = urlParams.get('date') || urlParams.get('filter_date') || '';
        const targetUrl = `{{ url('/manager-agent/employee-report') }}/${memberId}${filterDate ? '?date=' + filterDate : ''}`;
        
        fetch(targetUrl, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (response.ok) {
                return response.json();
            }
            return response.json().then(data => {
                throw new Error(data.message || 'Failed to fetch employee report.');
            });
        })
        .then(data => {
            metaEl.innerText = `${data.name} (${data.role}) — Report for ${data.date}`;
            spinnerEl.classList.add('d-none');
            contentEl.classList.remove('d-none');
            
            // Render using marked library
            if (typeof marked !== 'undefined') {
                contentEl.innerHTML = marked.parse(data.report);
            } else {
                contentEl.innerHTML = data.report.replace(/\n/g, '<br>');
            }
        })
        .catch(error => {
            console.error('Error fetching employee report:', error);
            metaEl.innerText = `${memberName} — Generation Failed`;
            spinnerEl.classList.add('d-none');
            contentEl.classList.remove('d-none');
            contentEl.innerHTML = `
                <div class="alert alert-danger border-0 p-3 text-white" style="background-color: rgba(244, 63, 94, 0.15); border-left: 4px solid #f43f5e !important;">
                    <strong>Error:</strong> ${error.message}
                </div>
            `;
        });
    }

    // Show AI report for group
    function showGroupReport(gId, groupName) {
        const modalEl = document.getElementById('employeeReportModal');
        const modal = new bootstrap.Modal(modalEl);
        
        // Reset/Update Modal Header Title
        document.getElementById('employeeReportModalLabel').innerText = 'Group Evening AI Audit';
        
        const metaEl = document.getElementById('employee-report-meta');
        const spinnerEl = document.getElementById('employee-report-spinner');
        const contentEl = document.getElementById('employee-report-content');
        
        metaEl.innerText = `${groupName} — Preparing group report...`;
        spinnerEl.classList.remove('d-none');
        contentEl.classList.add('d-none');
        contentEl.innerHTML = '';
        
        modal.show();
        
        const urlParams = new URLSearchParams(window.location.search);
        const filterDate = urlParams.get('date') || urlParams.get('filter_date') || '';
        const targetUrl = `{{ url('/manager-agent/group-report') }}?ids=${gId}${filterDate ? '&date=' + filterDate : ''}`;
        
        fetch(targetUrl, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (response.ok) {
                return response.json();
            }
            return response.json().then(data => {
                throw new Error(data.message || 'Failed to fetch group report.');
            });
        })
        .then(data => {
            metaEl.innerText = `${data.group_name} — Report for ${data.date}`;
            spinnerEl.classList.add('d-none');
            contentEl.classList.remove('d-none');
            
            // Render using marked library
            if (typeof marked !== 'undefined') {
                contentEl.innerHTML = marked.parse(data.report);
            } else {
                contentEl.innerHTML = data.report.replace(/\n/g, '<br>');
            }
        })
        .catch(error => {
            console.error('Error fetching group report:', error);
            metaEl.innerText = `${groupName} — Generation Failed`;
            spinnerEl.classList.add('d-none');
            contentEl.classList.remove('d-none');
            contentEl.innerHTML = `
                <div class="alert alert-danger border-0 p-3 text-white" style="background-color: rgba(244, 63, 94, 0.15); border-left: 4px solid #f43f5e !important;">
                    <strong>Error:</strong> ${error.message}
                </div>
            `;
        });
    }

    // AJAX Pagination and search for dashboard modals
    let membersSearch = '';
    let tasksSearch = '';
    let commitsSearch = '';

    document.getElementById('membersModal').addEventListener('shown.bs.modal', function () {
        loadMembers(1);
    });
    document.getElementById('tasksModal').addEventListener('shown.bs.modal', function () {
        loadTasks(1);
    });
    document.getElementById('commitsModal').addEventListener('shown.bs.modal', function () {
        loadCommits(1);
    });

    let searchTimeout;
    function onMembersSearchChange(value) {
        membersSearch = value;
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => loadMembers(1), 300);
    }
    function onTasksSearchChange(value) {
        tasksSearch = value;
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => loadTasks(1), 300);
    }
    function onCommitsSearchChange(value) {
        commitsSearch = value;
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => loadCommits(1), 300);
    }

    function loadMembers(page) {
        const tbody = document.getElementById('membersModalTableBody');
        const pagEl = document.getElementById('membersModalPagination');
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary" role="status"></div> Loading...</td></tr>';
        
        fetch(`{{ url('/manager-agent/api/members') }}?page=${page}&search=${encodeURIComponent(membersSearch)}`)
            .then(res => res.json())
            .then(res => {
                let html = '';
                res.data.forEach((member, index) => {
                    const i = (res.current_page - 1) * 10 + index + 1;
                    html += `
                        <tr id="member-row-${member.id}">
                            <td class="py-3 text-secondary">${i}</td>
                            <td class="py-3"><span class="font-semibold text-slate-100">${member.name}</span></td>
                            <td class="py-3"><span class="text-slate-300">${member.role}</span></td>
                            <td class="py-3"><span class="text-slate-400">${member.email}</span></td>
                            <td class="py-3"><span class="font-mono text-purple-400">${member.github_id || 'N/A'}</span></td>
                            <td class="py-3 text-end">
                                <a href="{{ route('manager.employees.index') }}?search=${encodeURIComponent(member.email)}" class="btn btn-xs btn-outline-info">Manage</a>
                            </td>
                        </tr>
                    `;
                });
                if (res.data.length === 0) {
                    html = '<tr><td colspan="6" class="text-center py-4 text-secondary italic">No team members found.</td></tr>';
                }
                tbody.innerHTML = html;
                renderPagination(pagEl, res.current_page, res.last_page, 'loadMembers');
            });
    }

    function loadTasks(page) {
        const tbody = document.getElementById('tasksModalTableBody');
        const pagEl = document.getElementById('tasksModalPagination');
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary" role="status"></div> Loading...</td></tr>';

        fetch(`{{ url('/manager-agent/api/tasks') }}?page=${page}&search=${encodeURIComponent(tasksSearch)}`)
            .then(res => res.json())
            .then(res => {
                let html = '';
                res.data.forEach((task, index) => {
                    const i = (res.current_page - 1) * 10 + index + 1;
                    let badge = '';
                    if (task.status === 'completed') {
                        badge = '<span class="badge rounded-pill bg-success bg-opacity-10 text-success border border-success border-opacity-20 px-2.5 py-1" style="font-size: 10px;">Completed</span>';
                    } else if (task.status === 'in_progress') {
                        badge = '<span class="badge rounded-pill bg-warning bg-opacity-10 text-warning border border-warning border-opacity-20 px-2.5 py-1" style="font-size: 10px;">In Progress</span>';
                    } else {
                        badge = '<span class="badge rounded-pill bg-danger bg-opacity-10 text-danger border border-danger border-opacity-20 px-2.5 py-1" style="font-size: 10px;">Pending</span>';
                    }
                    html += `
                        <tr id="task-row-${task.id}">
                            <td class="py-3 text-secondary">${i}</td>
                            <td class="py-3"><span class="font-semibold text-slate-100">${task.title}</span></td>
                            <td class="py-3"><span class="badge bg-indigo-500 bg-opacity-10 text-indigo-400 border border-indigo-500 border-opacity-20 px-2 py-0.5" style="font-size: 10px;">${task.member_name}</span></td>
                            <td class="py-3">${badge}</td>
                            <td class="py-3 text-slate-400">${task.due_date}</td>
                            <td class="py-3 text-end">
                                <a href="{{ route('manager.task-entry') }}?search=${encodeURIComponent(task.title)}" class="btn btn-xs btn-outline-info">Manage</a>
                            </td>
                        </tr>
                    `;
                });
                if (res.data.length === 0) {
                    html = '<tr><td colspan="6" class="text-center py-4 text-secondary italic">No tasks found.</td></tr>';
                }
                tbody.innerHTML = html;
                renderPagination(pagEl, res.current_page, res.last_page, 'loadTasks');
            });
    }

    function loadCommits(page) {
        const tbody = document.getElementById('commitsModalTableBody');
        const pagEl = document.getElementById('commitsModalPagination');
        tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary" role="status"></div> Loading...</td></tr>';

        fetch(`{{ url('/manager-agent/api/commits') }}?page=${page}&search=${encodeURIComponent(commitsSearch)}`)
            .then(res => res.json())
            .then(res => {
                let html = '';
                res.data.forEach((commit, index) => {
                    const i = (res.current_page - 1) * 10 + index + 1;
                    html += `
                        <tr id="commit-row-${commit.id}">
                            <td class="py-3 text-secondary">${i}</td>
                            <td class="py-3 font-mono text-primary" style="font-size: 13px;">${commit.commit_hash.substring(0, 7)}</td>
                            <td class="py-3 text-slate-300 font-semibold">${commit.repository_name || 'N/A'}</td>
                            <td class="py-3 text-slate-300">${commit.member_name}</td>
                            <td class="py-3 font-mono text-purple-400" style="font-size: 12px;">git_user_${commit.team_member_id}</td>
                            <td class="py-3 text-slate-100" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${commit.message}</td>
                            <td class="py-3 text-slate-400" style="font-size: 12px;">${commit.committed_at}</td>
                            <td class="py-3 text-end">
                                <a href="{{ route('manager.commits.index') }}?search=${encodeURIComponent(commit.commit_hash)}" class="btn btn-xs btn-outline-info">Manage</a>
                            </td>
                        </tr>
                    `;
                });
                if (res.data.length === 0) {
                    html = '<tr><td colspan="8" class="text-center py-4 text-secondary italic">No commits found.</td></tr>';
                }
                tbody.innerHTML = html;
                renderPagination(pagEl, res.current_page, res.last_page, 'loadCommits');
            });
    }

    function renderPagination(el, currentPage, lastPage, funcName) {
        if (lastPage <= 1) {
            el.innerHTML = '<div></div><div></div>';
            return;
        }
        let html = `<div>Page ${currentPage} of ${lastPage}</div>`;
        html += '<div class="btn-group">';
        if (currentPage > 1) {
            html += `<button type="button" class="btn btn-xs btn-outline-secondary" onclick="${funcName}(${currentPage - 1})">Prev</button>`;
        } else {
            html += `<button type="button" class="btn btn-xs btn-outline-secondary" disabled>Prev</button>`;
        }
        if (currentPage < lastPage) {
            html += `<button type="button" class="btn btn-xs btn-outline-secondary" onclick="${funcName}(${currentPage + 1})">Next</button>`;
        } else {
            html += `<button type="button" class="btn btn-xs btn-outline-secondary" disabled>Next</button>`;
        }
        html += '</div>';
        el.innerHTML = html;
    }
</script>
@endsection
