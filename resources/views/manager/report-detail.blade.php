@extends('layouts.manager')
<!-- resources/views/manager/report-detail.blade.php -->

@section('title', 'Performance Evaluation Detail - Manager Agent')
@section('page_title', 'Performance Evaluation Details')

@section('styles')
<style>
    @media print {
        /* Hide UI elements not meant for print */
        header, .sidebar, .btn, .d-md-end {
            display: none !important;
        }
        
        /* Reset background to white for printing */
        body, .main-content, .card, .glass-card {
            background: #ffffff !important;
            color: #000000 !important;
            border: none !important;
            box-shadow: none !important;
        }
        
        /* Ensure narrative text is clear */
        #narrative-content {
            color: #000000 !important;
            font-size: 12pt;
        }
        
        /* Adjust text colors that might be too light */
        .text-dark, .text-dark, .text-secondary, .text-secondary {
            color: #000000 !important;
        }
        
        /* Expand scrollable areas */
        .custom-scroll, .table-responsive {
            max-height: none !important;
            overflow: visible !important;
        }
        
        /* Fix page breaks */
        .card {
            page-break-inside: avoid;
            margin-bottom: 20px !important;
        }
    }
</style>
@endsection

@section('content')
@php
    $pct = $report->team_productivity;
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

<div class="row justify-content-center animate-fade-in-up">
    <div class="col-lg-10">
        
        <div class="row g-4 align-items-center mb-4">
            <div class="col-md-6">
                <h2 class="h3 font-outfit text-dark mb-1">Evaluation Details</h2>
                <p class="text-secondary small mb-0">Record generated on {{ $report->report_date->format('F d, Y') }} at {{ $report->created_at->format('h:i:s A') }}</p>
            </div>
            <div class="col-md-6 text-md-end d-flex flex-wrap gap-2 justify-content-md-end align-items-center">
                @if($prevReport)
                    <a href="{{ route('manager.report-detail', $prevReport->id) }}" class="btn btn-sm btn-outline-primary rounded-3 px-3 d-inline-flex align-items-center" style="border: 1px solid var(--border-color) !important;">
                        &larr; Prev Date ({{ $prevReport->report_date->format('M d') }})
                    </a>
                @endif
                @if($nextReport)
                    <a href="{{ route('manager.report-detail', $nextReport->id) }}" class="btn btn-sm btn-outline-primary rounded-3 px-3 d-inline-flex align-items-center" style="border: 1px solid var(--border-color) !important;">
                        Next Date ({{ $nextReport->report_date->format('M d') }}) &rarr;
                    </a>
                @endif
                <a href="{{ route('manager.reports') }}" class="btn btn-sm btn-outline-secondary rounded-3 px-3 d-print-none">
                    Back to History
                </a>
                <button type="button" class="btn btn-sm btn-success rounded-3 px-3 d-print-none" onclick="window.print()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                        <path d="M5 1a2 2 0 0 0-2 2v1h10V3a2 2 0 0 0-2-2H5zm6 8H5a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-3a1 1 0 0 0-1-1z"/>
                        <path d="M0 7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2h-1v-2a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v2H2a2 2 0 0 1-2-2V7zm2.5 1a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
                    </svg>
                    Print / Save PDF
                </button>
                <form action="{{ route('manager.destroy-report', $report->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this report?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger rounded-3 px-3">
                        Delete
                    </button>
                </form>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <!-- Productivity Ring Card -->
            <div class="col-md-5 col-lg-4">
                <div class="card glass-card p-4 h-100 text-center d-flex flex-column align-items-center justify-content-center">
                    <span class="text-uppercase text-secondary small font-weight-bold mb-4">Productivity Index</span>
                    
                    <div class="position-relative d-inline-flex align-items-center justify-content-center" style="width: 150px; height: 150px;">
                        <svg width="150" height="150" class="position-absolute" style="transform: rotate(-90deg);">
                            <!-- Background Circle -->
                            <circle cx="75" cy="75" r="55" stroke="#334155" stroke-width="8" fill="transparent" />
                            <!-- Value Circle -->
                            <circle cx="75" cy="75" r="55" class="{{ $colorStroke }}" stroke-width="8" 
                                    stroke-dasharray="345.58" stroke-dashoffset="{{ 345.58 - ($pct / 100) * 345.58 }}" 
                                    stroke-linecap="round" fill="transparent" />
                        </svg>
                        <div class="d-flex flex-column align-items-center justify-content-center">
                            <span class="h2 font-outfit text-dark mb-0 font-weight-bold">{{ $pct }}%</span>
                            <span class="text-uppercase text-secondary" style="font-size: 8px; tracking-wider">Score</span>
                        </div>
                    </div>
                    
                    <span class="text-secondary small mt-4 font-weight-medium">{{ $statusLabel }}</span>
                </div>
            </div>

            <!-- Performing and Warnings Lists -->
            <div class="col-md-7 col-lg-8">
                <div class="card glass-card p-4 h-100">
                    <div class="row g-4">
                        <div class="col-sm-6">
                            <h4 class="h6 font-outfit text-dark mb-3 flex-shrink-0 d-flex align-items-center justify-content-between">
                                <span class="d-flex align-items-center">
                                    <span class="d-inline-block bg-success rounded-circle me-2 shadow-lg" style="width: 8px; height: 8px;"></span>
                                    Top Performers
                                </span>
                                @if(!empty($report->top_performers))
                                    <span class="badge rounded-pill" style="background:rgba(16,185,129,0.12);color:#34d399;border:1px solid rgba(16,185,129,0.25);font-size:8px;">{{ count($report->top_performers) }}</span>
                                @endif
                            </h4>
                            
                            <div style="max-height: 240px; overflow-y: auto; padding-right: 4px;" class="custom-scroll">
                            @if(!empty($report->top_performers) && is_array($report->top_performers))
                                <ul class="list-unstyled mb-0">
                                    @foreach($report->top_performers as $performer)
                                        <li class="d-flex align-items-center mb-2.5 text-secondary small">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="text-success me-2" viewBox="0 0 16 16" style="flex-shrink:0;">
                                                <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/>
                                            </svg>
                                            <span class="font-weight-medium">{{ $performer }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-secondary small italic mb-0">None identified.</p>
                            @endif
                            </div>
                        </div>

                        <!-- Attention Required -->
                        <div class="col-sm-6">
                            <h4 class="h6 font-outfit text-dark mb-3 flex-shrink-0 d-flex align-items-center justify-content-between">
                                <span class="d-flex align-items-center">
                                    <span class="d-inline-block bg-danger rounded-circle me-2 shadow-lg" style="width: 8px; height: 8px;"></span>
                                    Attention Required
                                </span>
                                @if(!empty($report->attention_required))
                                    <span class="badge rounded-pill" style="background:rgba(244,63,94,0.12);color:#fb7185;border:1px solid rgba(244,63,94,0.25);font-size:8px;">{{ count($report->attention_required) }}</span>
                                @endif
                            </h4>
                            
                            <div style="max-height: 240px; overflow-y: auto; padding-right: 4px;" class="custom-scroll">
                            @if(!empty($report->attention_required) && is_array($report->attention_required))
                                <ul class="list-unstyled mb-0">
                                    @foreach($report->attention_required as $needsAttention)
                                        <li class="d-flex align-items-start mb-2.5 text-secondary small">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="text-danger me-2 mt-0.5" viewBox="0 0 16 16" style="flex-shrink:0;">
                                                <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/>
                                            </svg>
                                            <span class="font-weight-medium">{{ $needsAttention }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-secondary small italic mb-0">None identified.</p>
                            @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Workload Analysis & Recommendations -->
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="card glass-card p-4 h-100">
                    <h4 class="h5 font-outfit text-dark mb-3 d-flex align-items-center">
                        <span class="d-inline-block bg-primary rounded-circle me-2 shadow-lg" style="width: 10px; height: 10px;"></span>
                        Workload Analysis
                    </h4>
                    @if(!empty($report->workload_analysis) && is_array($report->workload_analysis))
                        <div class="mb-3">
                            <strong class="text-secondary small d-block mb-1">Imbalanced Members:</strong>
                            @if(!empty($report->workload_analysis['imbalanced_members']))
                                <ul class="list-unstyled mb-0">
                                    @foreach($report->workload_analysis['imbalanced_members'] as $member)
                                        <li class="text-secondary small d-flex align-items-start mb-1">
                                            <span class="me-2">-</span> {{ $member }}
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-secondary small italic">Workload is balanced.</span>
                            @endif
                        </div>
                        <div>
                            <strong class="text-secondary small d-block mb-1">Resource Allocation Advice:</strong>
                            <p class="text-secondary small mb-0">{{ $report->workload_analysis['resource_allocation_recommendation'] ?? 'N/A' }}</p>
                        </div>
                    @else
                        <p class="text-secondary small italic mb-0">No workload data available.</p>
                    @endif
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card glass-card p-4 h-100 border-primary" style="border-left: 4px solid var(--accent-color) !important;">
                    <h4 class="h5 font-outfit text-dark mb-3 d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="text-primary me-2" viewBox="0 0 16 16">
                            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                        </svg>
                        AI Recommendations
                    </h4>
                    @if(!empty($report->recommendations) && is_array($report->recommendations))
                        <div class="custom-scroll" style="max-height: 250px; overflow-y: auto;">
                            @foreach(['promotion_recommendations' => 'Promotions', 'reward_recommendations' => 'Rewards', 'training_recommendations' => 'Training Needs'] as $key => $title)
                                @if(!empty($report->recommendations[$key]))
                                    <strong class="text-secondary small d-block mt-2 mb-1">{{ $title }}:</strong>
                                    <ul class="list-unstyled mb-2">
                                        @foreach($report->recommendations[$key] as $rec)
                                            <li class="text-secondary small d-flex align-items-start mb-1">
                                                <span class="me-2 text-primary">&bull;</span> {{ $rec }}
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            @endforeach
                            
                            @if(!empty($report->recommendations['hiring_recommendations']))
                                <strong class="text-secondary small d-block mt-2 mb-1">Hiring Recommendations:</strong>
                                <p class="text-secondary small mb-0">{{ $report->recommendations['hiring_recommendations'] }}</p>
                            @endif
                        </div>
                    @else
                        <p class="text-secondary small italic mb-0">No recommendations available.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Risks Section -->
        <div class="card glass-card p-4 mb-4">
            <h4 class="h5 font-outfit text-dark mb-3 d-flex align-items-center">
                <span class="d-inline-block bg-warning rounded-circle me-2 shadow-lg" style="width: 10px; height: 10px;"></span>
                Identified Risks & Roadblocks
            </h4>
            
            @if(!empty($report->risks) && is_array($report->risks))
                <ul class="list-unstyled mb-0">
                    @foreach($report->risks as $risk)
                        <li class="d-flex align-items-start mb-2.5 text-secondary small">
                            <div class="bg-warning rounded-circle me-2.5 mt-1.5 shadow-md shadow-warning-500/50" style="width: 8px; height: 8px; flex-shrink: 0;"></div>
                            <span class="font-weight-medium">{{ $risk }}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-secondary small italic mb-0">No risks logged.</p>
            @endif
        </div>

        <!-- Tasks for this specific date -->
        <div class="card glass-card p-4 mb-4">
            <h4 class="h5 font-outfit text-dark mb-3 d-flex align-items-center">
                <span class="d-inline-block bg-info rounded-circle me-2 shadow-lg" style="width: 10px; height: 10px; background-color: #38bdf8 !important;"></span>
                Tasks Allocated on this Date
            </h4>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-dark" style="--bs-table-bg: transparent; --bs-table-hover-bg: rgba(255, 255, 255, 0.02); --bs-table-border-color: #334155;">
                    <thead class="text-secondary" style="font-size: 11px;">
                        <tr>
                            <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider">#</th>
                            <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider">Task Title</th>
                            <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider">Assigned Developer</th>
                            <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tasks as $task)
                            <tr>
                                <td class="py-3 text-secondary">{{ $loop->iteration }}</td>
                                <td class="py-3 font-semibold text-dark">{{ $task->title }}</td>
                                <td class="py-3 text-secondary">{{ $task->teamMember?->name ?? 'Unassigned' }}</td>
                                <td class="py-3">
                                    @if($task->status === 'completed')
                                        <span class="badge rounded-pill bg-success bg-opacity-10 text-success border border-success border-opacity-20 px-2.5 py-1" style="font-size: 10px;">Completed</span>
                                    @elseif($task->status === 'in_progress')
                                        <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning border border-warning border-opacity-20 px-2.5 py-1" style="font-size: 10px;">In Progress</span>
                                    @else
                                        <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger border border-danger border-opacity-20 px-2.5 py-1" style="font-size: 10px;">Pending</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-secondary italic small">No tasks allocated for this date.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

                <!-- Commits for this specific date -->
        <div class="card glass-card p-4 mb-4">
            <h4 class="h5 font-outfit text-dark mb-3 d-flex align-items-center">
                <span class="d-inline-block bg-primary rounded-circle me-2 shadow-lg" style="width: 10px; height: 10px;"></span>
                Git Commits on this Date
            </h4>
            
            <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                <table class="table table-hover align-middle mb-0 text-dark" style="--bs-table-bg: transparent; --bs-table-hover-bg: rgba(255, 255, 255, 0.02); --bs-table-border-color: #334155;">
                    <thead class="text-secondary" style="font-size: 11px;">
                        <tr>
                            <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider">#</th>
                            <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider">Commit Hash</th>
                            <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider">Message</th>
                            <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider">Developer</th>
                            <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider">Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($commits) && $commits->count() > 0)
                            @foreach($commits as $commit)
                                <tr>
                                    <td class="py-3 text-secondary">{{ $loop->iteration }}</td>
                                    <td class="py-3 font-monospace text-primary small">{{ substr($commit->commit_hash ?? $commit->commit_sha, 0, 7) }}</td>
                                    <td class="py-3 font-semibold text-dark">{{ \Illuminate\Support\Str::limit($commit->message, 50) }}</td>
                                    <td class="py-3 text-secondary">{{ $commit->teamMember?->name ?? 'Unknown' }}</td>
                                    <td class="py-3 text-secondary small">{{ $commit->committed_at->format('h:i A') }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="text-center py-4 text-secondary italic small">No commits pushed on this date.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Attendance for this specific date -->
        <div class="card glass-card p-4 mb-4">
            <h4 class="h5 font-outfit text-dark mb-3 d-flex align-items-center">
                <span class="d-inline-block bg-warning rounded-circle me-2 shadow-lg" style="width: 10px; height: 10px;"></span>
                Attendance on this Date
            </h4>
            
            <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                <table class="table table-hover align-middle mb-0 text-dark" style="--bs-table-bg: transparent; --bs-table-hover-bg: rgba(255, 255, 255, 0.02); --bs-table-border-color: #334155;">
                    <thead class="text-secondary" style="font-size: 11px;">
                        <tr>
                            <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider">#</th>
                            <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider">Employee</th>
                            <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider">Check In</th>
                            <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider">Check Out</th>
                            <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($attendanceLogs) && $attendanceLogs->count() > 0)
                            @foreach($attendanceLogs as $log)
                                <tr>
                                    <td class="py-3 text-secondary">{{ $loop->iteration }}</td>
                                    <td class="py-3 font-semibold text-dark">{{ $log->teamMember?->name ?? 'Unknown' }}</td>
                                    <td class="py-3 text-secondary">{{ $log->check_in ? \Carbon\Carbon::parse($log->check_in)->format('h:i A') : '--' }}</td>
                                    <td class="py-3 text-secondary">{{ $log->check_out ? \Carbon\Carbon::parse($log->check_out)->format('h:i A') : '--' }}</td>
                                    <td class="py-3">
                                        @if($log->status === 'present')
                                            <span class="badge rounded-pill bg-success bg-opacity-10 text-success border border-success border-opacity-20 px-2.5 py-1" style="font-size: 10px;">Present</span>
                                        @elseif($log->status === 'late')
                                            <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning border border-warning border-opacity-20 px-2.5 py-1" style="font-size: 10px;">Late</span>
                                        @else
                                            <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger border border-danger border-opacity-20 px-2.5 py-1" style="font-size: 10px;">Absent</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="text-center py-4 text-secondary italic small">No attendance records for this date.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Narrative AI Text Review -->
        <div class="card glass-card p-4">
            <h4 class="h5 font-outfit text-dark mb-3 pb-3 border-bottom border-secondary-subtle d-flex align-items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2 text-primary" viewBox="0 0 16 16">
                    <path d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h13zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13z"/>
                </svg>
                Complete AI Narrative Review
            </h4>
            
            <div id="narrative-content" class="text-secondary small mt-3" style="line-height: 1.625;" data-raw-text="{{ $report->full_report }}">
                {!! nl2br(e($report->full_report)) !!}
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const narrativeContent = document.getElementById('narrative-content');
        if (narrativeContent && typeof marked !== 'undefined') {
            narrativeContent.innerHTML = marked.parse(narrativeContent.getAttribute('data-raw-text'));
        }
    });
</script>
@endsection
