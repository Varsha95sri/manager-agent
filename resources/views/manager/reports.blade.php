@extends('layouts.manager')
<!-- resources/views/manager/reports.blade.php -->

@section('title', 'Reports History - Manager Agent')
@section('page_title', 'Performance Reports Log')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        
        <div class="row g-4 align-items-center mb-4">
            <div class="col-lg-4 col-md-12">
                <h2 class="h3 font-outfit text-white mb-1">Performance Archive</h2>
                <p class="text-secondary small mb-0">Browse and query all historical daily team analytics evaluations.</p>
            </div>
            
            <!-- Search & Filter form -->
            <div class="col-lg-8 col-md-12">
                <form method="GET" action="{{ route('manager.reports') }}" class="row g-2 align-items-end justify-content-lg-end">
                    <!-- Keyword search -->
                    <div class="col-sm-4">
                        <label class="text-secondary small mb-1 d-block" style="font-size: 11px;">Search Keyword</label>
                        <input
                            type="text"
                            name="search"
                            class="form-control border-slate-700 bg-slate-900 text-white placeholder-secondary rounded-3"
                            placeholder="Keyword or date..."
                            value="{{ request('search') }}"
                        >
                    </div>
                    <!-- Calendar Date -->
                    <div class="col-sm-3">
                        <label class="text-secondary small mb-1 d-block" style="font-size: 11px;">Report Date</label>
                        <input
                            type="date"
                            name="filter_date"
                            class="form-control border-slate-700 bg-slate-900 text-white placeholder-secondary rounded-3"
                            value="{{ request('filter_date') }}"
                            style="color-scheme: dark;"
                        >
                    </div>
                    <!-- Calendar DateTime-local -->
                    <div class="col-sm-3">
                        <label class="text-secondary small mb-1 d-block" style="font-size: 11px;">Exact Date & Time</label>
                        <input
                            type="datetime-local"
                            name="filter_datetime"
                            class="form-control border-slate-700 bg-slate-900 text-white placeholder-secondary rounded-3"
                            value="{{ request('filter_datetime') }}"
                            style="color-scheme: dark;"
                        >
                    </div>
                    <!-- Actions -->
                    <div class="col-sm-2 d-flex gap-2 justify-content-end">
                        <button class="btn btn-primary px-3 rounded-3 flex-grow-1" type="submit">
                            Filter
                        </button>
                        @if(request('search') || request('filter_date') || request('filter_datetime'))
                            <a href="{{ route('manager.reports') }}" class="btn btn-outline-danger px-2 rounded-3 d-flex align-items-center justify-content-center" title="Clear Filters">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                </svg>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="card glass-card p-4 shadow-2xl">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-white" style="--bs-table-bg: transparent; --bs-table-hover-bg: rgba(255, 255, 255, 0.02); --bs-table-border-color: #334155;">
                    <thead class="text-secondary" style="font-size: 11px;">
                        <tr>
                            <th scope="col" class="pb-3 border-slate-800 uppercase font-semibold tracking-wider">Report Date</th>
                            <th scope="col" class="pb-3 border-slate-800 uppercase font-semibold tracking-wider">Productivity Index</th>
                            <th scope="col" class="pb-3 border-slate-800 uppercase font-semibold tracking-wider d-none d-sm-table-cell">Performers & Risks</th>
                            <th scope="col" class="pb-3 border-slate-800 uppercase font-semibold tracking-wider">Status</th>
                            <th scope="col" class="pb-3 border-slate-800 uppercase font-semibold tracking-wider text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $report)
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
                                    <div class="d-flex align-items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="text-primary me-2" viewBox="0 0 16 16">
                                            <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                                        </svg>
                                        <span>{{ $report->report_date->format('F d, Y') }}</span>
                                    </div>
                                    <div class="text-secondary small font-normal mt-1 ps-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="text-secondary me-1 align-middle" viewBox="0 0 16 16">
                                            <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/>
                                            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/>
                                        </svg>
                                        <span class="align-middle">Generated: {{ $report->created_at->format('h:i:s A') }}</span>
                                    </div>
                                </td>
                                <td class="py-3 font-bold {{ $textClass }}">
                                    {{ $histPct }}%
                                    <div class="progress d-none d-md-inline-flex ms-2 align-self-center" style="width: 60px; height: 5px; background-color: #334155;">
                                        <div class="progress-bar {{ $histPct >= 80 ? 'bg-success' : ($histPct >= 60 ? 'bg-warning' : 'bg-danger') }}" role="progressbar" style="width: {{ $histPct }}%"></div>
                                    </div>
                                </td>
                                <td class="py-3 d-none d-sm-table-cell text-secondary small" style="max-width: 260px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <strong>Top:</strong> {{ implode(', ', $report->top_performers) }}
                                    <span class="mx-1">|</span>
                                    <strong>Risks:</strong> {{ count($report->risks) }} found
                                </td>
                                <td class="py-3">
                                    <span class="badge rounded-pill {{ $badgeClass }} px-2.5 py-1" style="font-size: 10px;">{{ $label }}</span>
                                </td>
                                <td class="py-3 text-end">
                                    <a href="{{ route('manager.report-detail', $report->id) }}" class="text-primary font-semibold text-decoration-none small">View Details</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-secondary italic small">
                                    @if(request('search') || request('filter_date') || request('filter_datetime'))
                                        No performance reports found matching your criteria.
                                    @else
                                        No historical performance reports logged yet.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Links -->
            @if($reports->hasPages())
                <div class="mt-4 border-top border-slate-800 pt-4 d-flex justify-content-center">
                    {!! $reports->links() !!}
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
