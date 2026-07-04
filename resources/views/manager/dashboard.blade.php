@extends('layouts.manager')

@section('title', 'Executive Summary Dashboard')
@section('page_title', 'Executive Summary Dashboard')

@section('styles')
<style>
    .hover-lift {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important;
    }
    .skeleton {
        position: relative;
        overflow: hidden;
        background-color: #f1f5f9;
        color: transparent !important;
        border-color: transparent !important;
        pointer-events: none;
    }
    .skeleton::after {
        content: "";
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        transform: translateX(-100%);
        background-image: linear-gradient(
            90deg,
            rgba(255, 255, 255, 0) 0,
            rgba(255, 255, 255, 0.2) 20%,
            rgba(255, 255, 255, 0.5) 60%,
            rgba(255, 255, 255, 0)
        );
        animation: shimmer 1.5s infinite;
    }
    @keyframes shimmer {
        100% {
            transform: translateX(100%);
        }
    }
    .toast-container {
        z-index: 1055;
    }
    .premium-shadow {
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01) !important;
    }
    .icon-circle {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .glass-pill {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
</style>
@endsection

@section('content')
@php
    $rangeLabel = 'Today';
    if (isset($rangeType)) {
        if ($rangeType === 'week_wise') $rangeLabel = 'This Week';
        elseif ($rangeType === 'month_wise') $rangeLabel = 'This Month';
        elseif ($rangeType === 'year_wise') $rangeLabel = 'This Year';
        elseif ($rangeType === 'all_time') $rangeLabel = 'All Time';
        elseif ($rangeType === 'custom_range') $rangeLabel = 'Custom Range';
    }
@endphp
<div class="row mb-5">
    <div class="col-12 d-flex flex-column flex-xxl-row justify-content-between align-items-start align-items-xxl-center gap-4">
        <div>
            <h2 class="h2 font-outfit text-dark fw-bold mb-2">Executive Summary Dashboard</h2>
            <p class="text-secondary mb-0 fs-6">High-level KPIs, organization performance, and AI-driven insights.</p>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-3 w-100 w-xxl-auto">
              <!-- Quick Search Date -->
              <form action="{{ route('manager.dashboard') }}" method="GET" class="d-flex align-items-center gap-2 bg-white px-3 py-2 rounded-pill premium-shadow border border-secondary-subtle m-0 flex-grow-1 flex-md-grow-0" id="quickDateForm">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="text-primary flex-shrink-0" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/></svg>
                  <input type="hidden" name="range_type" value="date_wise">
                  <input type="date" name="date" class="form-control border-0 text-dark bg-transparent p-0 shadow-none w-100" style="outline: none;" onchange="fetchDashboardData('quickDateForm')" value="{{ $targetDate ?? '' }}">
              </form>
              
              <!-- Date Picker UI -->
            <form action="{{ route('manager.dashboard') }}" method="GET" class="d-flex align-items-center gap-2 bg-white px-3 py-2 rounded-pill premium-shadow border border-secondary-subtle m-0 flex-grow-1 flex-md-grow-0" id="filterForm" onsubmit="event.preventDefault(); fetchDashboardData('filterForm');">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="text-secondary" viewBox="0 0 16 16"><path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/></svg>
                <select name="range_type" id="rangeType" class="form-select border-0 shadow-none bg-transparent fw-semibold text-dark p-0 pe-3" style="cursor: pointer; outline: none; min-width: 100px;" onchange="toggleCustomDates()">
                    <option value="all_time" {{ (isset($rangeType) && $rangeType === 'all_time') ? 'selected' : '' }}>All Time</option>
                    <option value="date_wise" {{ (isset($rangeType) && $rangeType === 'date_wise') ? 'selected' : '' }}>Daily</option>
                    <option value="week_wise" {{ (isset($rangeType) && $rangeType === 'week_wise') ? 'selected' : '' }}>Weekly</option>
                    <option value="month_wise" {{ (isset($rangeType) && $rangeType === 'month_wise') ? 'selected' : '' }}>Monthly</option>
                    <option value="year_wise" {{ (isset($rangeType) && $rangeType === 'year_wise') ? 'selected' : '' }}>Yearly</option>
                    <option value="custom_range" {{ (isset($rangeType) && $rangeType === 'custom_range') ? 'selected' : '' }}>Custom Range</option>
                </select>

                <div id="customDateContainer" class="align-items-center gap-2" style="display: {{ (isset($rangeType) && $rangeType === 'custom_range') ? 'flex' : 'none' }} !important;">
                    <input type="date" name="start_date" id="startDateFilter" class="form-control border-0 text-secondary bg-light rounded-pill px-3" value="{{ $startDate ?? $targetDate }}">
                    <span class="text-muted small">to</span>
                    <input type="date" name="end_date" id="endDateFilter" class="form-control border-0 text-secondary bg-light rounded-pill px-3" value="{{ $endDate ?? $targetDate }}">
                    <button type="submit" class="btn btn-primary rounded-pill px-3">Apply</button>
                </div>
            </form>
            
            <button type="button" class="btn btn-light d-inline-flex align-items-center rounded-pill premium-shadow border-secondary-subtle px-4 py-2 hover-lift text-dark fw-semibold" data-bs-toggle="modal" data-bs-target="#productivityCalendarModal">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2 text-primary" viewBox="0 0 16 16">
                  <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                </svg>
                Calendar
            </button>
            
            <button class="btn btn-light d-inline-flex align-items-center rounded-pill premium-shadow border-secondary-subtle px-4 py-2 hover-lift text-dark fw-semibold" onclick="window.print()">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2 text-primary" viewBox="0 0 16 16">
                    <path d="M5 1a2 2 0 0 0-2 2v1h10V3a2 2 0 0 0-2-2H5zm6 8H5a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-3a1 1 0 0 0-1-1z"/>
                    <path d="M0 7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2h-1v-2a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v2H2a2 2 0 0 1-2-2V7zm2.5 1a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
                </svg>
                Export
            </button>
            
            <button type="button" id="btn-evening" class="btn btn-primary d-inline-flex align-items-center rounded-pill premium-shadow border-0 px-4 py-2 hover-lift fw-semibold" style="background: linear-gradient(135deg, #6366f1, #4f46e5); color: white;" onclick="submitReportForm('evening', 'reportForm', this)">
                <i class="bi bi-moon-stars me-2"></i> Generate Evening Report
            </button>
            
            <form id="reportForm" method="POST" action="{{ route('manager.generate') }}" class="d-none">
                @csrf
                <input type="hidden" name="date" value="{{ $targetDate ?? \Carbon\Carbon::today()->toDateString() }}">
                <input type="hidden" name="type" id="reportType" value="executive">
            </form>
        </div>
    </div>
</div>

<!-- KPI Cards -->
<div class="row g-4 mb-5">
    <!-- Org Performance -->
    <div class="col-md-3">
        <div class="card bg-white premium-shadow border-0 rounded-4 h-100 p-4 hover-lift kpi-card position-relative overflow-hidden">
            <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.05), transparent); pointer-events: none;"></div>
            <div class="d-flex justify-content-between align-items-start mb-3 position-relative z-1">
                <span class="text-uppercase text-secondary fw-bold tracking-wider" style="font-size: 11px;">Org Performance</span>
                <div class="icon-circle bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-activity fs-4"></i>
                </div>
            </div>
            <h2 id="kpi-org-performance" class="font-outfit text-dark fw-bolder mb-1 position-relative z-1" style="font-size: 2.2rem;" data-value="{{ $latestReport ? $latestReport->team_productivity : 0 }}">{{ $latestReport ? $latestReport->team_productivity . '%' : 'N/A' }}</h2>
            <span id="kpi-org-trend" class="badge bg-success bg-opacity-10 text-success border border-success-subtle rounded-pill px-2 py-1 small d-inline-flex align-items-center position-relative z-1">
                <i class="bi bi-arrow-up-right me-1"></i>
                +2.4% vs last week
            </span>
        </div>
    </div>
    <!-- Total Employees -->
    <div class="col-md-3">
        <div class="card bg-white premium-shadow border-0 rounded-4 h-100 p-4 hover-lift kpi-card position-relative overflow-hidden">
            <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.05), transparent); pointer-events: none;"></div>
            <div class="d-flex justify-content-between align-items-start mb-3 position-relative z-1">
                <span class="text-uppercase text-secondary fw-bold tracking-wider" style="font-size: 11px;">Total Employees</span>
                <div class="icon-circle bg-success bg-opacity-10 text-success">
                    <i class="bi bi-people fs-4"></i>
                </div>
            </div>
            <h2 id="kpi-total-employees" class="font-outfit text-dark fw-bolder mb-1 position-relative z-1" style="font-size: 2.2rem;" data-value="{{ $totalMembers }}">{{ $totalMembers }}</h2>
            <span class="text-secondary small mt-1 d-block position-relative z-1">Active registered members</span>
        </div>
    </div>
    <!-- Tasks Completion -->
    <div class="col-md-3">
        <div class="card bg-white premium-shadow border-0 rounded-4 h-100 p-4 hover-lift kpi-card position-relative overflow-hidden">
            <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(135deg, rgba(245, 158, 11, 0.05), transparent); pointer-events: none;"></div>
            <div class="d-flex justify-content-between align-items-start mb-3 position-relative z-1">
                <span id="label-tasks-completed" class="text-uppercase text-secondary fw-bold tracking-wider" style="font-size: 11px;">Tasks Completed ({{ $rangeLabel }})</span>
                <div class="icon-circle bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-check2-all fs-4"></i>
                </div>
            </div>
            <h2 id="kpi-tasks-completed" class="font-outfit text-dark fw-bolder mb-2 position-relative z-1" style="font-size: 2.2rem;">{{ $completedTasksCount }}<span class="fs-5 text-secondary fw-normal"> / {{ $totalTasks }}</span></h2>
            <div class="progress mt-auto position-relative z-1 bg-secondary-subtle rounded-pill" style="height: 6px;">
                @php $taskPct = $totalTasks > 0 ? ($completedTasksCount / $totalTasks) * 100 : 0; @endphp
                <div id="kpi-tasks-progress" class="progress-bar rounded-pill" role="progressbar" style="width: {{ $taskPct }}%; background: linear-gradient(90deg, #f59e0b, #fcd34d); transition: width 1s ease-in-out;"></div>
            </div>
        </div>
    </div>
    <!-- Git Commits -->
    <div class="col-md-3">
        <div class="card bg-white premium-shadow border-0 rounded-4 h-100 p-4 hover-lift kpi-card position-relative overflow-hidden">
            <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(135deg, rgba(139, 92, 246, 0.05), transparent); pointer-events: none;"></div>
            <div class="d-flex justify-content-between align-items-start mb-3 position-relative z-1">
                <span id="label-git-commits" class="text-uppercase text-secondary fw-bold tracking-wider" style="font-size: 11px;">Git Commits ({{ $rangeLabel }})</span>
                <div class="icon-circle bg-purple bg-opacity-10" style="color: #8b5cf6;">
                    <i class="bi bi-git fs-4"></i>
                </div>
            </div>
            <h2 id="kpi-git-commits" class="font-outfit text-dark fw-bolder mb-1 position-relative z-1" style="font-size: 2.2rem;" data-value="{{ $totalCommits }}">{{ $totalCommits }}</h2>
            <span class="text-secondary small mt-1 d-block position-relative z-1">Pushes across all repositories</span>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-5">
    <!-- Trend Chart -->
    <div class="col-lg-8">
        <div class="card bg-white premium-shadow border-0 rounded-4 p-4 h-100">
            <h5 class="font-outfit text-dark fw-bold mb-4">Performance Trend <span class="fw-normal text-secondary fs-6">(Last 7 Days)</span></h5>
            <div style="height: 320px; width: 100%;">
                <canvas id="performanceTrendChart"></canvas>
            </div>
        </div>
    </div>
    <!-- Workload Distribution -->
    <div class="col-lg-4">
        <div class="card bg-white premium-shadow border-0 rounded-4 p-4 h-100">
            <h5 class="font-outfit text-dark fw-bold mb-4">Workload Distribution</h5>
            <div style="height: 260px; width: 100%; display: flex; justify-content: center; position: relative;">
                <canvas id="workloadChart"></canvas>
            </div>
            <div class="mt-4 text-center">
                <span class="small text-secondary fw-semibold bg-light px-3 py-1 rounded-pill">Breakdown of pending tasks by team</span>
            </div>
        </div>
    </div>
</div>

<!-- AI Recommendations Panel -->
<div class="card bg-white premium-shadow border-0 rounded-4 mb-5 position-relative overflow-hidden" style="border-left: 5px solid #6366f1 !important;">
    <div class="card-body p-4 position-relative z-1">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="font-outfit text-dark fw-bold d-flex align-items-center mb-0">
                <div class="icon-circle bg-primary bg-opacity-10 text-primary me-3" style="width: 40px; height: 40px;">
                    <i class="bi bi-stars fs-5"></i>
                </div>
                AI Insights & Action Items
            </h5>
        </div>
        
        <div class="row g-4">
            <!-- Burnout Risks -->
            <div class="col-md-4">
                <div class="p-4 bg-white rounded-4 premium-shadow border border-danger-subtle h-100 d-flex flex-column position-relative overflow-hidden">
                    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(180deg, rgba(239, 68, 68, 0.05), transparent); pointer-events: none;"></div>
                    <div class="d-flex align-items-center mb-3 position-relative z-1">
                        <span class="badge bg-danger rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center shadow-sm">
                            <i class="bi bi-exclamation-octagon me-2"></i> Risks Identified
                        </span>
                    </div>
                    <div class="custom-scroll flex-grow-1 position-relative z-1" style="max-height: 220px; overflow-y: auto; padding-right: 8px;">
                        @if($latestReport && !empty($latestReport->risks))
                            <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                                @foreach($latestReport->risks as $risk)
                                    <li class="bg-light p-2 rounded-3 border border-secondary-subtle text-secondary small d-flex align-items-start">
                                        <i class="bi bi-arrow-right-short text-danger mt-1 me-1"></i>
                                        <span>{{ is_array($risk) ? ($risk['risk'] ?? 'Unknown Risk') : $risk }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="text-center py-4">
                                <i class="bi bi-shield-check text-success fs-1 opacity-50 mb-2"></i>
                                <p class="small text-secondary mb-0 fw-medium">No immediate burnout or workload risks identified.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <!-- Attention Required -->
            <div class="col-md-4">
                <div class="p-4 bg-white rounded-4 premium-shadow border border-warning-subtle h-100 d-flex flex-column position-relative overflow-hidden">
                    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(180deg, rgba(245, 158, 11, 0.05), transparent); pointer-events: none;"></div>
                    <div class="d-flex align-items-center mb-3 position-relative z-1">
                        <span class="badge bg-warning text-dark rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center shadow-sm">
                            <i class="bi bi-exclamation-triangle me-2"></i> Attention Required
                        </span>
                    </div>
                    <div class="custom-scroll flex-grow-1 position-relative z-1" style="max-height: 220px; overflow-y: auto; padding-right: 8px;">
                        @if($latestReport && !empty($latestReport->attention_required))
                            <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                                @foreach($latestReport->attention_required as $item)
                                    <li class="bg-light p-2 rounded-3 border border-secondary-subtle text-secondary small d-flex align-items-start">
                                        <i class="bi bi-arrow-right-short text-warning mt-1 me-1"></i>
                                        <span>{{ is_array($item) ? ($item['name'] ?? 'Unknown') : $item }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="text-center py-4">
                                <i class="bi bi-check-circle text-success fs-1 opacity-50 mb-2"></i>
                                <p class="small text-secondary mb-0 fw-medium">No active project or team delays currently flagged.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <!-- Top Performers -->
            <div class="col-md-4">
                <div class="p-4 bg-white rounded-4 premium-shadow border border-success-subtle h-100 d-flex flex-column position-relative overflow-hidden">
                    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(180deg, rgba(16, 185, 129, 0.05), transparent); pointer-events: none;"></div>
                    <div class="d-flex align-items-center mb-3 position-relative z-1">
                        <span class="badge bg-success rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center shadow-sm">
                            <i class="bi bi-trophy me-2"></i> Top Performers
                        </span>
                    </div>
                    <div class="custom-scroll flex-grow-1 position-relative z-1" style="max-height: 220px; overflow-y: auto; padding-right: 8px;">
                        @if($latestReport && !empty($latestReport->top_performers))
                            <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                                @foreach($latestReport->top_performers as $perf)
                                    <li class="bg-light p-2 rounded-3 border border-secondary-subtle text-secondary small d-flex align-items-center">
                                        <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 24px; height: 24px;">
                                            <i class="bi bi-star-fill" style="font-size: 10px;"></i>
                                        </div>
                                        <span class="fw-medium">{{ is_array($perf) ? ($perf['name'] ?? 'Unknown') : $perf }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="text-center py-4">
                                <i class="bi bi-hourglass text-secondary fs-1 opacity-50 mb-2"></i>
                                <p class="small text-secondary mb-0 fw-medium">No standout performance evaluated for today yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Team Performance Overview Row -->
<div class="row g-4 mb-5">
    <div class="col-12">
        <div class="card bg-white premium-shadow border-0 rounded-4 p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="font-outfit text-dark fw-bold mb-0">Team Performance Overview</h5>
                <a href="{{ route('manager.teams.index') }}" class="btn btn-sm btn-light rounded-pill px-3 premium-shadow text-primary fw-semibold border-secondary-subtle hover-lift">View All Teams</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0" style="border-collapse: separate; border-spacing: 0 8px;">
                    <thead>
                        <tr>
                            <th class="border-0 px-4 text-uppercase text-secondary fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">Team Name</th>
                            <th class="border-0 px-4 text-uppercase text-secondary fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">Lead/Manager</th>
                            <th class="border-0 px-4 text-uppercase text-secondary fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">Avg. Productivity</th>
                            <th class="border-0 px-4 text-uppercase text-secondary fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">Task Completion</th>
                            <th class="border-0 px-4 text-uppercase text-secondary fw-bold text-center" style="font-size: 11px; letter-spacing: 0.5px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(collect($dashboardTeams ?? [])->take(3) as $team)
                            <tr onclick="window.location='{{ route('manager.teams.show', $team->slug) }}'" style="cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='scale(1.01)'; this.style.boxShadow='0 5px 15px rgba(0,0,0,0.05)';" onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='none';">
                                <td class="px-4 py-3 bg-white border border-secondary-subtle border-end-0 rounded-start-4">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                            <i class="bi bi-people fs-5"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-dark fw-bold" style="font-size: 15px;">{{ $team->name }}</h6>
                                            <span class="text-secondary fw-medium" style="font-size: 12px;">{{ $team->teamMembers->count() }} Members</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 bg-white border-top border-bottom border-secondary-subtle text-dark fw-medium" style="font-size: 14px;">{{ $team->leader->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 bg-white border-top border-bottom border-secondary-subtle">
                                    <span class="text-secondary" style="font-size: 14px;">N/A</span>
                                </td>
                                <td class="px-4 py-3 bg-white border-top border-bottom border-secondary-subtle text-dark fw-medium" style="font-size: 14px;">
                                    <span class="text-secondary" style="font-size: 14px;">N/A</span>
                                </td>
                                <td class="px-4 py-3 bg-white border border-secondary-subtle border-start-0 rounded-end-4 text-center">
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-20 rounded-pill px-3 py-2 fw-semibold">Pending</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-secondary">
                                    No teams found. Please add a team to see performance overview.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Attendance & Active Projects Row -->
<div class="row g-4">
    <!-- Attendance Overview -->
    <div class="col-lg-6">
        <div class="card bg-white shadow-sm border-0 rounded-4 p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="font-outfit text-dark mb-0">Attendance Overview ({{ $rangeLabel }})</h5>
                <a href="{{ route('manager.attendance-registry') }}" class="small text-primary text-decoration-none">View All</a>
            </div>
            <div class="d-flex justify-content-around align-items-center mb-3">
                <div class="text-center">
                    <h3 class="text-success mb-0 font-outfit">{{ $presentPct }}%</h3>
                    <span class="small text-secondary">Present</span>
                </div>
                <div class="text-center">
                    <h3 class="text-warning mb-0 font-outfit">{{ $latePct }}%</h3>
                    <span class="small text-secondary">Late</span>
                </div>
                <div class="text-center">
                    <h3 class="text-danger mb-0 font-outfit">{{ $absentPct }}%</h3>
                    <span class="small text-secondary">Absent</span>
                </div>
            </div>
            <div class="progress" style="height: 8px;">
                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $presentPct }}%"></div>
                <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $latePct }}%"></div>
                <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $absentPct }}%"></div>
            </div>
        </div>
    </div>
    
    <!-- Latest Report Narrative -->
    <div class="col-lg-6">
        <div class="card bg-white shadow-sm border-0 rounded-4 p-4 h-100">
            <h5 class="font-outfit text-dark mb-3">Latest AI Performance Report</h5>
            @if($latestReport)
                <p class="text-secondary small mb-0" style="line-height: 1.6; max-height: 120px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 5; -webkit-box-orient: vertical;">
                    {{ strip_tags($latestReport->full_report) }}
                </p>
                <div class="mt-3">
                    <a href="{{ route('manager.report-detail', $latestReport->id) }}" class="btn btn-sm btn-outline-primary">Read Full Report</a>
                </div>
            @else
                <p class="text-secondary small">No AI reports generated yet.</p>
                <form method="POST" action="{{ route('manager.generate') }}">
                    @csrf
                    <input type="hidden" name="date" value="{{ date('Y-m-d') }}">
                    <button type="submit" class="btn btn-sm btn-primary">Generate Report Now</button>
                </form>
            @endif
        </div>
    </div>
</div>

<!-- Productivity Calendar Modal -->
<div class="modal fade" id="productivityCalendarModal" tabindex="-1" aria-labelledby="productivityCalendarModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 rounded-4 shadow-lg">
      <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
        <h5 class="modal-title font-outfit text-dark" id="productivityCalendarModalLabel">Monthly Productivity Calendar</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <h6 class="mb-0 text-secondary font-semibold d-none d-md-block">Viewing Month:</h6>
            <form action="{{ route('manager.dashboard') }}" method="GET" class="d-flex align-items-center">
                @php
                    $prevMonth = \Carbon\Carbon::parse($targetDate)->subMonth()->format('Y-m-d');
                    $nextMonth = \Carbon\Carbon::parse($targetDate)->addMonth()->format('Y-m-d');
                @endphp
                <div class="d-flex align-items-center bg-light rounded-2 px-2 border border-secondary-subtle">
                    <a href="{{ route('manager.dashboard', ['date' => $prevMonth, 'show_calendar' => 1]) }}" class="btn btn-sm btn-link text-secondary text-decoration-none px-2"><i class="bi bi-chevron-left"></i></a>
                    <span class="text-secondary small me-1"><i class="bi bi-calendar-event"></i></span>
                    <input type="month" class="form-control form-control-sm border-0 bg-transparent text-dark shadow-none px-1" value="{{ \Carbon\Carbon::parse($targetDate)->format('Y-m') }}" onchange="document.getElementById('modalDateInput').value = this.value + '-01'; this.form.submit()">
                    <a href="{{ route('manager.dashboard', ['date' => $nextMonth, 'show_calendar' => 1]) }}" class="btn btn-sm btn-link text-secondary text-decoration-none px-2"><i class="bi bi-chevron-right"></i></a>
                </div>
                <input type="hidden" name="date" id="modalDateInput" value="{{ $targetDate }}">
                <input type="hidden" name="show_calendar" value="1">
            </form>
        </div>
        
        <div class="calendar-wrapper">
            <div class="d-grid text-center font-semibold text-secondary small mb-2" style="grid-template-columns: repeat(7, 1fr);">
                <div>Sun</div>
                <div>Mon</div>
                <div>Tue</div>
                <div>Wed</div>
                <div>Thu</div>
                <div>Fri</div>
                <div>Sat</div>
            </div>
            <div class="d-grid" style="grid-template-columns: repeat(7, 1fr); gap: 10px;">
                @php
                    $parsedDate = \Carbon\Carbon::parse($targetDate);
                    $startOfMonth = $parsedDate->copy()->startOfMonth();
                    $endOfMonth = $parsedDate->copy()->endOfMonth();
                    $startDayOfWeek = $startOfMonth->dayOfWeek; // 0 (Sun) to 6 (Sat)
                    $daysInMonth = $endOfMonth->day;
                    
                    // Fill empty slots before start of month
                    for ($i = 0; $i < $startDayOfWeek; $i++) {
                        echo '<div class="p-3 rounded-3" style="background-color: #f8fafc; border: 1px dashed #e2e8f0;"></div>';
                    }
                    
                    for ($day = 1; $day <= $daysInMonth; $day++) {
                        $currentDateStr = $parsedDate->copy()->day($day)->format('Y-m-d');
                        $hasReport = isset($monthReports) && isset($monthReports[$currentDateStr]);
                        $score = $hasReport ? $monthReports[$currentDateStr]->team_productivity : null;
                        
                        $bgColor = '#f8fafc';
                        $textColor = '#64748b';
                        $scoreHtml = '<span class="small opacity-50">-</span>';
                        $borderColor = '#e2e8f0';
                        
                        if ($hasReport) {
                            if ($score >= 80) {
                                $bgColor = '#dcfce7';
                                $textColor = '#166534';
                                $borderColor = '#bbf7d0';
                            } elseif ($score >= 60) {
                                $bgColor = '#fef9c3';
                                $textColor = '#854d0e';
                                $borderColor = '#fef08a';
                            } else {
                                $bgColor = '#fee2e2';
                                $textColor = '#991b1b';
                                $borderColor = '#fecaca';
                            }
                            $scoreHtml = '<span class="font-bold" style="font-size: 1.1rem;">'.$score.'%</span>';
                        }
                        
                        $isTargetDate = ($currentDateStr === $targetDate);
                        $targetStyle = $isTargetDate ? 'border: 2px solid var(--accent-color);' : 'border: 1px solid '.$borderColor.';';
                        
                        echo '<a href="'.route('manager.dashboard', ['date' => $currentDateStr]).'" class="text-decoration-none p-2 rounded-3 text-center position-relative d-flex flex-column justify-content-between hover-card" style="min-height: 80px; background-color: '.$bgColor.'; '.$targetStyle.' transition: all 0.2s;">';
                        echo '<div class="text-end small font-semibold mb-1" style="color: '.$textColor.';">'.$day.'</div>';
                        echo '<div style="color: '.$textColor.';">'.$scoreHtml.'</div>';
                        echo '</a>';
                    }
                    
                    // Fill empty slots after end of month
                    $endDayOfWeek = $endOfMonth->dayOfWeek; // 0 to 6
                    $remainingSlots = 6 - $endDayOfWeek;
                    for ($i = 0; $i < $remainingSlots; $i++) {
                        echo '<div class="p-3 rounded-3" style="background-color: #f8fafc; border: 1px dashed #e2e8f0;"></div>';
                    }
                @endphp
            </div>
        </div>
        
        <div class="mt-4 d-flex justify-content-center gap-4 text-secondary small">
            <div class="d-flex align-items-center"><span class="d-inline-block rounded-circle me-2" style="width: 10px; height: 10px; background-color: #dcfce7; border: 1px solid #bbf7d0;"></span> 80%+ (Excellent)</div>
            <div class="d-flex align-items-center"><span class="d-inline-block rounded-circle me-2" style="width: 10px; height: 10px; background-color: #fef9c3; border: 1px solid #fef08a;"></span> 60-79% (Average)</div>
            <div class="d-flex align-items-center"><span class="d-inline-block rounded-circle me-2" style="width: 10px; height: 10px; background-color: #fee2e2; border: 1px solid #fecaca;"></span> < 60% (Needs Attention)</div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let trendChartInstance = null;
    let workloadChartInstance = null;

    document.addEventListener('DOMContentLoaded', function() {
        // Prepare data for the Trend Chart
        @php
            $dates = [];
            $scores = [];
            // Assuming $reports is ordered by latest first, we reverse it for the chart
            $orderedReports = $reports->reverse()->values();
            foreach($orderedReports as $r) {
                $dates[] = $r->report_date->format('M d');
                $scores[] = $r->team_productivity;
            }
        @endphp

        const trendCtx = document.getElementById('performanceTrendChart').getContext('2d');
        
        // Create Gradient
        let gradient = trendCtx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(99, 102, 241, 0.4)');
        gradient.addColorStop(1, 'rgba(99, 102, 241, 0.0)');

        trendChartInstance = new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($dates) !!},
                datasets: [{
                    label: 'Productivity Score',
                    data: {!! json_encode($scores) !!},
                    borderColor: '#6366f1',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#6366f1',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleColor: '#ffffff',
                        bodyColor: '#e2e8f0',
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        grid: { borderDash: [5, 5], color: '#f1f5f9' },
                        ticks: { color: '#94a3b8', font: { size: 11 } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#94a3b8', font: { size: 11 } }
                    }
                }
            }
        });

        // Prepare data for Doughnut Chart
        const workloadCtx = document.getElementById('workloadChart').getContext('2d');
        workloadChartInstance = new Chart(workloadCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($workloadLabelsInit) !!},
                datasets: [{
                    data: {!! json_encode($workloadDataInit) !!},
                    backgroundColor: ['#6366f1', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#14b8a6', '#f43f5e', '#3b82f6'],
                    borderWidth: 0,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 20, usePointStyle: true, pointStyle: 'circle', color: '#64748b', font: { size: 12 } }
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 12,
                        cornerRadius: 8
                    }
                },
                cutout: '75%'
            }
        });

        // Event listener for filter form submit (specifically for custom range Apply button)
        const filterForm = document.getElementById('filterForm');
        if (filterForm) {
            filterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                fetchDashboardData();
            });
        }
    });

    @if(request('show_calendar'))
    document.addEventListener('DOMContentLoaded', function() {
        var myModal = new bootstrap.Modal(document.getElementById('productivityCalendarModal'), {
            keyboard: true
        });
        myModal.show();
    });
    @endif

    function submitReportForm(type, formId, btnElement = null) {
        const btn = btnElement || document.getElementById(`btn-${type}`);
        let originalText = '';
        if (btn) {
            originalText = btn.innerHTML;
            btn.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Generating...`;
            btn.disabled = true;
        }

        const form = document.getElementById(formId);
        if (document.getElementById('reportType') && formId === 'reportForm') {
            document.getElementById('reportType').value = type;
            
            // Sync date from the active filter so we don't generate report for the old date
            const dateInput = form.querySelector('input[name="date"]');
            const rangeTypeSelect = document.getElementById('rangeType');
            if (dateInput) {
                if (rangeTypeSelect && rangeTypeSelect.value === 'custom_range') {
                    const endDate = document.getElementById('endDateFilter');
                    if (endDate) dateInput.value = endDate.value;
                } else {
                    const quickDate = document.querySelector('#quickDateForm input[name="date"]');
                    if (quickDate) dateInput.value = quickDate.value;
                }
            }
        }
        
        const formData = new FormData(form);
        if (formId === 'bottomReportForm') {
            formData.append('type', type);
        }

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok && response.status !== 500) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (btn) {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
            if(data.success) {
                showToast(data.message || 'Report generated successfully!', 'success');
                const activeFormId = (document.getElementById('rangeType') && document.getElementById('rangeType').value === 'date_wise') ? 'quickDateForm' : 'filterForm';
                fetchDashboardData(activeFormId);
            } else {
                showToast(data.message || 'Error generating report.', 'danger');
            }
        })
        .catch(error => {
            console.error(error);
            if (btn) {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
            showToast('Server error while generating report. Please ensure the backend is running.');
        });
    }

    function toggleCustomDates() {
        const select = document.getElementById('rangeType');
        const customContainer = document.getElementById('customDateContainer');
        if (select.value === 'custom_range') {
            customContainer.style.setProperty('display', 'flex', 'important');
        } else {
            customContainer.style.setProperty('display', 'none', 'important');
            fetchDashboardData('filterForm');
        }
    }

    function fetchDashboardData(formId = 'filterForm') {
        // Show Skeleton Loaders
        document.querySelectorAll('.kpi-card').forEach(el => el.classList.add('skeleton'));
        if (document.getElementById('performanceTrendChart')) {
            document.getElementById('performanceTrendChart').parentElement.classList.add('skeleton');
        }

        const form = document.getElementById(formId);
        const url = new URL(form.action);
        const params = new URLSearchParams(new FormData(form));

        fetch(`${url.pathname}?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            if(data.success) {
                // Update Labels
                const tasksLabel = document.getElementById('label-tasks-completed');
                if(tasksLabel) tasksLabel.innerText = `Tasks Completed (${data.rangeLabel})`;
                
                const gitLabel = document.getElementById('label-git-commits');
                if(gitLabel) gitLabel.innerText = `Git Commits (${data.rangeLabel})`;

                // Animate KPI Numbers
                animateValue('kpi-org-performance', data.orgPerformance, true);
                animateValue('kpi-total-employees', data.totalMembers, false);
                
                const tasksEl = document.getElementById('kpi-tasks-completed');
                if(tasksEl) tasksEl.innerText = `${data.completedTasksCount} / ${data.totalTasks}`;
                
                const progressEl = document.getElementById('kpi-tasks-progress');
                if(progressEl) progressEl.style.width = `${data.taskPct}%`;
                
                animateValue('kpi-git-commits', data.totalCommits, false);

                // Update Chart Data
                if (trendChartInstance && data.chartData) {
                    trendChartInstance.data.labels = data.chartData.labels;
                    trendChartInstance.data.datasets[0].data = data.chartData.scores;
                    trendChartInstance.update();
                }

                if (workloadChartInstance && data.workload) {
                    workloadChartInstance.data.labels = data.workload.labels;
                    workloadChartInstance.data.datasets[0].data = data.workload.data;
                    workloadChartInstance.update();
                }

                if (data.attendance) {
                    animateValue('attendance-present-val', data.attendance.present, true);
                    animateValue('attendance-late-val', data.attendance.late, true);
                    animateValue('attendance-absent-val', data.attendance.absent, true);
                    
                    const progressBars = document.querySelectorAll('.attendance-progress .progress-bar');
                    if (progressBars.length >= 3) {
                        progressBars[0].style.width = data.attendance.present + '%';
                        progressBars[1].style.width = data.attendance.late + '%';
                        progressBars[2].style.width = data.attendance.absent + '%';
                    }
                }
            } else {
                throw new Error(data.message || 'Error parsing data');
            }
        })
        .catch(error => {
            console.error('Error fetching dashboard data:', error);
            showToast('Failed to load dashboard data. Please try again.');
        })
        .finally(() => {
            // Remove Skeleton Loaders
            setTimeout(() => {
                document.querySelectorAll('.kpi-card').forEach(el => el.classList.remove('skeleton'));
                if (document.getElementById('performanceTrendChart')) {
                    document.getElementById('performanceTrendChart').parentElement.classList.remove('skeleton');
                }
            }, 300); // Slight delay for visual smoothness
        });
    }

    function animateValue(id, end, isPercentage) {
        const obj = document.getElementById(id);
        if (!obj) return;
        
        let endStr = String(end).replace('%','');
        let endVal = parseFloat(endStr);
        let startVal = parseFloat(obj.getAttribute('data-value') || 0);

        if (isNaN(endVal)) {
            obj.innerText = end; // Fallback for 'N/A'
            return;
        }

        const duration = 800; // ms
        let startTimestamp = null;
        
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            const current = Math.floor(progress * (endVal - startVal) + startVal);
            
            obj.innerText = current + (isPercentage ? '%' : '');
            
            if (progress < 1) {
                window.requestAnimationFrame(step);
            } else {
                obj.setAttribute('data-value', endVal);
                // Ensure final value is exact (e.g. keeping decimals if present)
                let finalVal = endVal % 1 !== 0 ? endVal.toFixed(1) : endVal;
                obj.innerText = finalVal + (isPercentage ? '%' : '');
            }
        };
        window.requestAnimationFrame(step);
    }

    function showToast(message, type = 'danger') {
        const icon = type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill';
        const toastHtml = `
            <div class="toast align-items-center text-white bg-${type} border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
              <div class="d-flex">
                <div class="toast-body">
                    <i class="bi ${icon} me-2"></i>${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
              </div>
            </div>`;
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            document.body.appendChild(container);
        }
        container.innerHTML = toastHtml;
        setTimeout(() => { container.innerHTML = ''; }, 4000);
    }
    function toggleTeams() {
        const hiddenRows = document.querySelectorAll('.hidden-team');
        const btn = document.getElementById('toggleTeamsBtn');
        
        hiddenRows.forEach(row => {
            if (row.classList.contains('d-none')) {
                row.classList.remove('d-none');
            } else {
                row.classList.add('d-none');
            }
        });
        
        if (btn.innerText.includes('View All Teams')) {
            btn.innerText = 'View Less Teams';
        } else {
            btn.innerText = 'View All Teams';
        }
    }
</script>
@endsection
