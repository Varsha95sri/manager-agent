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
<div class="row mb-4">
    <div class="col-12 d-flex flex-column flex-xl-row justify-content-between align-items-start align-items-xl-center gap-3">
        <div>
            <h2 class="h3 font-outfit text-dark mb-1">Executive Summary Dashboard</h2>
            <p class="text-secondary small mb-0">High-level KPIs, organization performance, and AI-driven insights.</p>
        </div>
        <div class="d-flex flex-nowrap align-items-center gap-2 overflow-auto w-100 w-xl-auto" style="padding-bottom: 4px; scrollbar-width: none; -ms-overflow-style: none;">
            <style>
                .d-flex.overflow-auto::-webkit-scrollbar { display: none; }
            </style>
            <!-- Date Picker UI (Leaderboard Style) -->
            <form action="{{ route('manager.dashboard') }}" method="GET" class="d-flex align-items-center gap-2 bg-white px-3 py-2 rounded-pill shadow-sm border border-secondary-subtle m-0 flex-shrink-0" id="filterForm">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="text-secondary" viewBox="0 0 16 16"><path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/></svg>
                <select name="range_type" id="rangeType" class="form-select form-select-sm border-0 shadow-none bg-transparent fw-semibold text-dark p-0 pe-3" style="cursor: pointer; outline: none; box-shadow: none; min-width: 90px;" onchange="toggleCustomDates()">
                    <option value="all_time" {{ (isset($rangeType) && $rangeType === 'all_time') ? 'selected' : '' }}>All Time</option>
                    <option value="date_wise" {{ (isset($rangeType) && $rangeType === 'date_wise') ? 'selected' : '' }}>Daily</option>
                    <option value="week_wise" {{ (isset($rangeType) && $rangeType === 'week_wise') ? 'selected' : '' }}>Weekly</option>
                    <option value="month_wise" {{ (isset($rangeType) && $rangeType === 'month_wise') ? 'selected' : '' }}>Monthly</option>
                    <option value="year_wise" {{ (isset($rangeType) && $rangeType === 'year_wise') ? 'selected' : '' }}>Yearly</option>
                    <option value="custom_range" {{ (isset($rangeType) && $rangeType === 'custom_range') ? 'selected' : '' }}>Custom Range</option>
                </select>

                <div id="customDateContainer" class="d-flex align-items-center gap-2" style="display: {{ (isset($rangeType) && $rangeType === 'custom_range') ? 'flex' : 'none' }} !important;">
                    <input type="date" name="start_date" id="startDateFilter" class="form-control form-control-sm border-0 text-secondary bg-light rounded-pill px-3" value="{{ $startDate ?? $targetDate }}">
                    <span class="text-muted small">to</span>
                    <input type="date" name="end_date" id="endDateFilter" class="form-control form-control-sm border-0 text-secondary bg-light rounded-pill px-3" value="{{ $endDate ?? $targetDate }}">
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3">Apply</button>
                </div>
            </form>
            
            <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center rounded-pill bg-white text-nowrap shadow-sm border-secondary-subtle px-3 py-2 flex-shrink-0" data-bs-toggle="modal" data-bs-target="#productivityCalendarModal">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2" viewBox="0 0 16 16">
                  <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                </svg>
                Calendar
            </button>
            
            <button class="btn btn-outline-primary d-inline-flex align-items-center rounded-pill bg-white text-nowrap shadow-sm px-3 py-2 flex-shrink-0" onclick="window.print()">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2" viewBox="0 0 16 16">
                    <path d="M5 1a2 2 0 0 0-2 2v1h10V3a2 2 0 0 0-2-2H5zm6 8H5a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-3a1 1 0 0 0-1-1z"/>
                    <path d="M0 7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2h-1v-2a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v2H2a2 2 0 0 1-2-2V7zm2.5 1a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
                </svg>
                Export
            </button>
            
            <div class="dropdown flex-shrink-0">
                <button class="btn btn-primary d-inline-flex align-items-center rounded-pill text-nowrap shadow-sm px-3 py-2 dropdown-toggle" type="button" id="generateReportDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="background: linear-gradient(135deg, var(--accent-color), #6366f1); border: none;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2" viewBox="0 0 16 16">
                      <path fill-rule="evenodd" d="M14.5 3h-13a.5.5 0 0 0-.5.5v9a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13z"/>
                      <path d="M3 8.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5zm0-5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5z"/>
                    </svg>
                    Generate Report
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 rounded-3" aria-labelledby="generateReportDropdown">
                    <li><a class="dropdown-item py-2" href="#" onclick="event.preventDefault(); document.getElementById('reportType').value='daily'; document.getElementById('reportForm').submit();"><i class="bi bi-file-earmark-text me-2 text-secondary"></i>Daily Report</a></li>
                    <li><a class="dropdown-item py-2" href="#" onclick="event.preventDefault(); document.getElementById('reportType').value='monthly'; document.getElementById('reportForm').submit();"><i class="bi bi-file-earmark-bar-graph me-2 text-secondary"></i>Monthly Summary</a></li>
                    <li><a class="dropdown-item py-2" href="#" onclick="event.preventDefault(); document.getElementById('reportType').value='executive'; document.getElementById('reportForm').submit();"><i class="bi bi-file-earmark-check me-2 text-secondary"></i>Executive Summary</a></li>
                </ul>
            </div>
            
            <form id="reportForm" method="POST" action="{{ route('manager.generate') }}" class="d-none">
                @csrf
                <input type="hidden" name="date" value="{{ $targetDate ?? \Carbon\Carbon::today()->toDateString() }}">
                <input type="hidden" name="type" id="reportType" value="executive">
            </form>
        </div>
    </div>
</div>

<!-- KPI Cards -->
<div class="row g-4 mb-4">
    <!-- Org Performance -->
    <div class="col-md-3">
        <div class="card bg-white shadow-sm border-0 rounded-4 h-100 p-3 hover-lift kpi-card">
            <span class="text-uppercase text-secondary font-semibold" style="font-size: 11px;">Org Performance</span>
            <h3 id="kpi-org-performance" class="font-outfit text-dark mt-2 mb-0" data-value="{{ $latestReport ? $latestReport->team_productivity : 0 }}">{{ $latestReport ? $latestReport->team_productivity . '%' : 'N/A' }}</h3>
            <span id="kpi-org-trend" class="text-success small d-flex align-items-center mt-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="me-1" viewBox="0 0 16 16"><path d="M8 12a.5.5 0 0 0 .5-.5V5.707l2.146 2.147a.5.5 0 0 0 .708-.708l-3-3a.5.5 0 0 0-.708 0l-3 3a.5.5 0 1 0 .708.708L7.5 5.707V11.5a.5.5 0 0 0 .5.5z"/></svg>
                +2.4% vs last week
            </span>
        </div>
    </div>
    <!-- Total Employees -->
    <div class="col-md-3">
        <div class="card bg-white shadow-sm border-0 rounded-4 h-100 p-3 hover-lift kpi-card">
            <span class="text-uppercase text-secondary font-semibold" style="font-size: 11px;">Total Employees</span>
            <h3 id="kpi-total-employees" class="font-outfit text-dark mt-2 mb-0" data-value="{{ $totalMembers }}">{{ $totalMembers }}</h3>
            <span class="text-secondary small mt-1 d-block">Active registered members</span>
        </div>
    </div>
    <!-- Tasks Completion -->
    <div class="col-md-3">
        <div class="card bg-white shadow-sm border-0 rounded-4 h-100 p-3 hover-lift kpi-card">
            <span id="label-tasks-completed" class="text-uppercase text-secondary font-semibold" style="font-size: 11px;">Tasks Completed ({{ $rangeLabel }})</span>
            <h3 id="kpi-tasks-completed" class="font-outfit text-dark mt-2 mb-0">{{ $completedTasksCount }} / {{ $totalTasks }}</h3>
            <div class="progress mt-2" style="height: 4px;">
                @php $taskPct = $totalTasks > 0 ? ($completedTasksCount / $totalTasks) * 100 : 0; @endphp
                <div id="kpi-tasks-progress" class="progress-bar bg-primary" role="progressbar" style="width: {{ $taskPct }}%; transition: width 1s ease-in-out;"></div>
            </div>
        </div>
    </div>
    <!-- Git Commits -->
    <div class="col-md-3">
        <div class="card bg-white shadow-sm border-0 rounded-4 h-100 p-3 hover-lift kpi-card">
            <span id="label-git-commits" class="text-uppercase text-secondary font-semibold" style="font-size: 11px;">Git Commits ({{ $rangeLabel }})</span>
            <h3 id="kpi-git-commits" class="font-outfit text-dark mt-2 mb-0" data-value="{{ $totalCommits }}">{{ $totalCommits }}</h3>
            <span class="text-secondary small mt-1 d-block">Pushes across all repositories</span>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <!-- Trend Chart -->
    <div class="col-lg-8">
        <div class="card bg-white shadow-sm border-0 rounded-4 p-4 h-100">
            <h5 class="font-outfit text-dark mb-4">Performance Trend (Last 7 Days)</h5>
            <div style="height: 300px; width: 100%;">
                <canvas id="performanceTrendChart"></canvas>
            </div>
        </div>
    </div>
    <!-- Workload Distribution -->
    <div class="col-lg-4">
        <div class="card bg-white shadow-sm border-0 rounded-4 p-4 h-100">
            <h5 class="font-outfit text-dark mb-4">Workload Distribution</h5>
            <div style="height: 250px; width: 100%; display: flex; justify-content: center;">
                <canvas id="workloadChart"></canvas>
            </div>
            <div class="mt-4 text-center">
                <span class="small text-secondary">Breakdown of pending tasks by team.</span>
            </div>
        </div>
    </div>
</div>

<!-- AI Recommendations Panel (Placeholder for Phase 2) -->
<div class="card bg-white shadow-sm border-0 rounded-4 mb-4" style="border-left: 4px solid var(--accent-color) !important;">
    <div class="card-body p-4">
        <h5 class="font-outfit text-dark d-flex align-items-center mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="text-primary me-2" viewBox="0 0 16 16">
                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
            </svg>
            AI Recommendations & Alerts
        </h5>
        <div class="row g-3">
            <!-- Burnout Risks -->
            <div class="col-md-4">
                <div class="p-3 bg-light rounded-3 border border-secondary-subtle h-100 d-flex flex-column">
                    <div><span class="badge bg-danger mb-2">Identified Risks</span></div>
                    <div class="custom-scroll flex-grow-1 pe-2" style="max-height: 200px; overflow-y: auto;">
                        @if($latestReport && !empty($latestReport->risks))
                            <ul class="small text-secondary mb-0 ps-3">
                                @foreach($latestReport->risks as $risk)
                                    <li>{{ is_array($risk) ? ($risk['risk'] ?? 'Unknown Risk') : $risk }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="small text-secondary mb-0">No immediate burnout or workload risks identified.</p>
                        @endif
                    </div>
                </div>
            </div>
            <!-- Attention Required -->
            <div class="col-md-4">
                <div class="p-3 bg-light rounded-3 border border-secondary-subtle h-100 d-flex flex-column">
                    <div><span class="badge bg-warning mb-2 text-dark">Attention Required</span></div>
                    <div class="custom-scroll flex-grow-1 pe-2" style="max-height: 200px; overflow-y: auto;">
                        @if($latestReport && !empty($latestReport->attention_required))
                            <ul class="small text-secondary mb-0 ps-3">
                                @foreach($latestReport->attention_required as $item)
                                    <li>{{ is_array($item) ? ($item['name'] ?? 'Unknown') : $item }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="small text-secondary mb-0">No active project or team delays currently flagged.</p>
                        @endif
                    </div>
                </div>
            </div>
            <!-- Top Performers -->
            <div class="col-md-4">
                <div class="p-3 bg-light rounded-3 border border-secondary-subtle h-100 d-flex flex-column">
                    <div><span class="badge bg-success mb-2">Top Performers</span></div>
                    <div class="custom-scroll flex-grow-1 pe-2" style="max-height: 200px; overflow-y: auto;">
                        @if($latestReport && !empty($latestReport->top_performers))
                            <ul class="small text-secondary mb-0 ps-3">
                                @foreach($latestReport->top_performers as $perf)
                                    <li>{{ is_array($perf) ? ($perf['name'] ?? 'Unknown') : $perf }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="small text-secondary mb-0">No standout performance evaluated for today yet.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Team Performance Overview Row -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card bg-white shadow-sm border-0 rounded-4 p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="font-outfit text-dark mb-0">Team Performance Overview</h5>
                <a href="{{ route('manager.teams.index') }}" class="small text-primary text-decoration-none">View All Teams</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 rounded-start-2 py-3 px-4 font-semibold text-secondary" style="font-size: 13px;">Team Name</th>
                            <th class="border-0 py-3 px-4 font-semibold text-secondary" style="font-size: 13px;">Lead/Manager</th>
                            <th class="border-0 py-3 px-4 font-semibold text-secondary" style="font-size: 13px;">Avg. Productivity</th>
                            <th class="border-0 py-3 px-4 font-semibold text-secondary" style="font-size: 13px;">Task Completion</th>
                            <th class="border-0 rounded-end-2 py-3 px-4 font-semibold text-secondary text-center" style="font-size: 13px;">Status</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        <tr onclick="window.location='{{ route('manager.teams.show', 'frontend') }}'" style="cursor: pointer;" class="hover-light">
                            <td class="px-4 py-3 border-secondary-subtle">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path d="M2 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2H2zm0 1h12a1 1 0 0 1 1 1v1H1V3a1 1 0 0 1 1-1zM1 6h14v7a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6zm2 2v2h2V8H3zm4 0v2h2V8H7z"/></svg>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 text-dark" style="font-size: 14px;">Frontend Team</h6>
                                        <span class="text-secondary" style="font-size: 12px;">5 Members</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 border-secondary-subtle text-dark" style="font-size: 14px;">Rahul Sharma</td>
                            <td class="px-4 py-3 border-secondary-subtle">
                                <div class="d-flex align-items-center">
                                    <span class="text-dark font-semibold me-2" style="font-size: 14px;">88%</span>
                                    <div class="progress flex-grow-1" style="height: 6px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 88%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 border-secondary-subtle text-dark" style="font-size: 14px;">92% <span class="text-secondary small">(115/125)</span></td>
                            <td class="px-4 py-3 border-secondary-subtle text-center">
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 rounded-pill px-3 py-1">Excellent</span>
                            </td>
                        </tr>
                        <tr onclick="window.location='{{ route('manager.teams.show', 'backend') }}'" style="cursor: pointer;" class="hover-light">
                            <td class="px-4 py-3 border-secondary-subtle">
                                <div class="d-flex align-items-center">
                                    <div class="bg-warning bg-opacity-10 text-warning rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path d="M4 11a1 1 0 1 1 2 0v1a1 1 0 1 1-2 0v-1zm5-4a1 1 0 1 1 2 0v5a1 1 0 1 1-2 0V7zM4 3a1 1 0 1 1 2 0v3a1 1 0 1 1-2 0V3z"/><path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z"/></svg>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 text-dark" style="font-size: 14px;">Backend Team</h6>
                                        <span class="text-secondary" style="font-size: 12px;">7 Members</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 border-secondary-subtle text-dark" style="font-size: 14px;">Priya Desai</td>
                            <td class="px-4 py-3 border-secondary-subtle">
                                <div class="d-flex align-items-center">
                                    <span class="text-dark font-semibold me-2" style="font-size: 14px;">76%</span>
                                    <div class="progress flex-grow-1" style="height: 6px;">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: 76%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 border-secondary-subtle text-dark" style="font-size: 14px;">78% <span class="text-secondary small">(85/109)</span></td>
                            <td class="px-4 py-3 border-secondary-subtle text-center">
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-20 rounded-pill px-3 py-1">Good</span>
                            </td>
                        </tr>
                        <tr onclick="window.location='{{ route('manager.teams.show', 'qa') }}'" style="cursor: pointer;" class="hover-light">
                            <td class="px-4 py-3 border-bottom-0">
                                <div class="d-flex align-items-center">
                                    <div class="bg-danger bg-opacity-10 text-danger rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/><path d="M6.854 4.646a.5.5 0 0 1 0 .708L5.207 7l1.647 1.646a.5.5 0 0 1-.708.708l-2-2a.5.5 0 0 1 0-.708l2-2a.5.5 0 0 1 .708 0zM9.146 4.646a.5.5 0 0 0 0 .708L10.793 7l-1.647 1.646a.5.5 0 0 0 .708.708l2-2a.5.5 0 0 0 0-.708l-2-2a.5.5 0 0 0-.708 0z"/></svg>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 text-dark" style="font-size: 14px;">QA & Testing</h6>
                                        <span class="text-secondary" style="font-size: 12px;">4 Members</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 border-bottom-0 text-dark" style="font-size: 14px;">Amit Kumar</td>
                            <td class="px-4 py-3 border-bottom-0">
                                <div class="d-flex align-items-center">
                                    <span class="text-dark font-semibold me-2" style="font-size: 14px;">94%</span>
                                    <div class="progress flex-grow-1" style="height: 6px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 94%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 border-bottom-0 text-dark" style="font-size: 14px;">98% <span class="text-secondary small">(210/215)</span></td>
                            <td class="px-4 py-3 border-bottom-0 text-center">
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 rounded-pill px-3 py-1">Exceptional</span>
                            </td>
                        </tr>
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
        trendChartInstance = new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($dates) !!},
                datasets: [{
                    label: 'Productivity Score',
                    data: {!! json_encode($scores) !!},
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#3b82f6',
                    pointBorderWidth: 2,
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        grid: { borderDash: [5, 5], color: '#e2e8f0' },
                        ticks: { color: '#64748b' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#64748b' }
                    }
                }
            }
        });

        // Prepare data for Doughnut Chart (Mocked data for now, ideally fetched from DB)
        const workloadCtx = document.getElementById('workloadChart').getContext('2d');
        workloadChartInstance = new Chart(workloadCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($workloadLabelsInit) !!},
                datasets: [{
                    data: {!! json_encode($workloadDataInit) !!},
                    backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#14b8a6', '#f43f5e', '#8b5cf6'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 20, usePointStyle: true, color: '#64748b' }
                    }
                },
                cutout: '70%'
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

    function toggleCustomDates() {
        const select = document.getElementById('rangeType');
        const customContainer = document.getElementById('customDateContainer');
        if (select.value === 'custom_range') {
            customContainer.style.setProperty('display', 'flex', 'important');
        } else {
            customContainer.style.setProperty('display', 'none', 'important');
            fetchDashboardData();
        }
    }

    function fetchDashboardData() {
        // Show Skeleton Loaders
        document.querySelectorAll('.kpi-card').forEach(el => el.classList.add('skeleton'));
        if (document.getElementById('performanceTrendChart')) {
            document.getElementById('performanceTrendChart').parentElement.classList.add('skeleton');
        }

        const form = document.getElementById('filterForm');
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

    function showToast(message) {
        const toastHtml = `
            <div class="toast align-items-center text-white bg-danger border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
              <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>${message}
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
</script>
@endsection
