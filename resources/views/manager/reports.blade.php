@extends('layouts.manager')
<!-- resources/views/manager/reports.blade.php -->

@section('title', 'Reports History - Manager Agent')
@section('page_title', 'Performance Reports Log')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        
        <div class="row g-4 align-items-center mb-4">
            <div class="col-md-6">
                <h2 class="h3 font-outfit text-white mb-1">Performance Archive</h2>
                <p class="text-secondary small mb-0">Browse and query all historical daily team analytics evaluations.</p>
            </div>
            
            <!-- Search bar form -->
            <div class="col-md-6">
                <form method="GET" action="{{ route('manager.reports') }}" class="d-flex">
                    <div class="input-group">
                        <input
                            type="text"
                            name="search"
                            class="form-control border-slate-700 bg-slate-900 text-white placeholder-secondary rounded-start-3"
                            placeholder="Search by date or keyword..."
                            value="{{ request('search') }}"
                        >
                        <button class="btn btn-primary px-3" type="submit">
                            Search
                        </button>
                        @if(request('search'))
                            <a href="{{ route('manager.reports') }}" class="btn btn-outline-danger px-3 d-flex align-items-center justify-content-center">
                                Clear
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
                                <td class="py-3 font-semibold text-slate-100">{{ $report->report_date->format('F d, Y') }}</td>
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
                                    @if(request('search'))
                                        No performance reports found matching your keyword.
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
