@extends('layouts.manager')
<!-- resources/views/manager/report-detail.blade.php -->

@section('title', 'Performance Evaluation Detail - Manager Agent')
@section('page_title', 'Performance Evaluation Details')

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
            <div class="col-sm-8">
                <h2 class="h3 font-outfit text-white mb-1">Evaluation Details</h2>
                <p class="text-secondary small mb-0">Record generated on {{ $report->report_date->format('F d, Y') }}</p>
            </div>
            <div class="col-sm-4 text-sm-end">
                <a href="{{ route('manager.reports') }}" class="btn btn-sm btn-outline-secondary rounded-3 px-3">
                    Back to History
                </a>
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
                            <span class="h2 font-outfit text-white mb-0 font-weight-bold">{{ $pct }}%</span>
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
                        <!-- Top Performers -->
                        <div class="col-sm-6">
                            <h4 class="h6 font-outfit text-white mb-3 flex-shrink-0 d-flex align-items-center">
                                <span class="d-inline-block bg-success rounded-circle me-2 shadow-lg" style="width: 8px; height: 8px;"></span>
                                Top Performers
                            </h4>
                            
                            @if(!empty($report->top_performers) && is_array($report->top_performers))
                                <ul class="list-unstyled mb-0">
                                    @foreach($report->top_performers as $performer)
                                        <li class="d-flex align-items-center mb-2.5 text-slate-300 small">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="text-success me-2" viewBox="0 0 16 16">
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

                        <!-- Attention Required -->
                        <div class="col-sm-6">
                            <h4 class="h6 font-outfit text-white mb-3 flex-shrink-0 d-flex align-items-center">
                                <span class="d-inline-block bg-danger rounded-circle me-2 shadow-lg" style="width: 8px; height: 8px;"></span>
                                Attention Required
                            </h4>
                            
                            @if(!empty($report->attention_required) && is_array($report->attention_required))
                                <ul class="list-unstyled mb-0">
                                    @foreach($report->attention_required as $needsAttention)
                                        <li class="d-flex align-items-start mb-2.5 text-slate-300 small">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="text-danger me-2 mt-0.5" viewBox="0 0 16 16">
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

        <!-- Risks Section -->
        <div class="card glass-card p-4 mb-4">
            <h4 class="h5 font-outfit text-white mb-3 d-flex align-items-center">
                <span class="d-inline-block bg-warning rounded-circle me-2 shadow-lg" style="width: 10px; height: 10px;"></span>
                Identified Risks & Roadblocks
            </h4>
            
            @if(!empty($report->risks) && is_array($report->risks))
                <ul class="list-unstyled mb-0">
                    @foreach($report->risks as $risk)
                        <li class="d-flex align-items-start mb-2.5 text-slate-300 small">
                            <div class="bg-warning rounded-circle me-2.5 mt-1.5 shadow-md shadow-warning-500/50" style="width: 8px; height: 8px; flex-shrink: 0;"></div>
                            <span class="font-weight-medium">{{ $risk }}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-secondary small italic mb-0">No risks logged.</p>
            @endif
        </div>

        <!-- Narrative AI Text Review -->
        <div class="card glass-card p-4">
            <h4 class="h5 font-outfit text-white mb-3 pb-3 border-bottom border-slate-800 d-flex align-items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2 text-primary" viewBox="0 0 16 16">
                    <path d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h13zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13z"/>
                </svg>
                Complete AI Narrative Review
            </h4>
            
            <div class="text-slate-300 small whitespace-pre-wrap mt-3" style="line-height: 1.625;">
                {!! nl2br(e($report->full_report)) !!}
            </div>
        </div>

    </div>
</div>
@endsection
