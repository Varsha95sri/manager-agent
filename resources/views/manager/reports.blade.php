@extends('layouts.manager')
<!-- resources/views/manager/reports.blade.php -->

@section('title', 'Reports History - Manager Agent')
@section('page_title', 'Performance Reports Log')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        
        <div class="row g-4 align-items-center mb-4">
            <div class="col-lg-4 col-md-12">
                <h2 class="h3 font-outfit text-dark mb-1">Performance Archive</h2>
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
                            class="form-control border-secondary-subtle bg-white shadow-sm text-dark placeholder-secondary rounded-3"
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
                            class="form-control border-secondary-subtle bg-white shadow-sm text-dark placeholder-secondary rounded-3"
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
                            class="form-control border-secondary-subtle bg-white shadow-sm text-dark placeholder-secondary rounded-3"
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

    <!-- Individual Employee AI Audits Card -->
    <div class="card glass-card p-4 mb-4 animate-fade-in-up">
        <h4 class="h5 font-outfit text-dark mb-3 d-flex align-items-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="me-2 text-primary">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
            Individual Employee AI Audits
        </h4>
        <p class="text-secondary small mb-4">Generate and view real-time AI-synthesized daily performance reports for individual team members.</p>
        
        <div class="row g-3">
            @foreach($allMembers as $member)
                <div class="col-md-6 col-lg-4">
                    <div class="p-3 rounded-4 border border-secondary-subtle bg-white shadow-sm/40 d-flex justify-content-between align-items-center hover-card" style="transition: all 0.2s;">
                        <div style="min-width: 0; flex-grow: 1; margin-right: 12px;">
                            <h6 class="text-dark font-outfit font-semibold mb-0.5 text-truncate">{{ $member->name }}</h6>
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

        @if($allMembers->hasPages())
            <div class="mt-4 border-top border-secondary-subtle pt-4 d-flex justify-content-center">
                {!! $allMembers->links() !!}
            </div>
        @endif
    </div>

        <div class="card glass-card p-4 shadow-2xl">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-dark" style="--bs-table-bg: transparent; --bs-table-hover-bg: rgba(255, 255, 255, 0.02); --bs-table-border-color: #334155;">
                    <thead class="text-secondary" style="font-size: 11px;">
                        <tr>
                            <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider">Report Date</th>
                            <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider">Productivity Index</th>
                            <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider d-none d-sm-table-cell">Performers & Risks</th>
                            <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider">Status</th>
                            <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider text-end">Action</th>
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
                                <td class="py-3 font-semibold text-dark">
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
                                    <div class="d-inline-flex gap-2.5 align-items-center">
                                        <a href="{{ route('manager.report-detail', $report->id) }}" class="text-primary font-semibold text-decoration-none small">View Details</a>
                                        <form action="{{ route('manager.destroy-report', $report->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this report?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-link text-danger p-0 m-0 font-semibold text-decoration-none small" style="border: none; background: none; line-height: 1;">Delete</button>
                                        </form>
                                    </div>
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
                <div class="mt-4 border-top border-secondary-subtle pt-4 d-flex justify-content-center">
                    {!! $reports->links() !!}
                </div>
            @endif
        </div>

    </div>
</div>

<!-- Employee AI Report Modal -->
<div class="modal fade" id="employeeReportModal" tabindex="-1" aria-labelledby="employeeReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content text-dark shadow-2xl" style="background-color: #ffffff; border: 1px solid var(--border-color); border-radius: 20px;">
            <div class="modal-header border-bottom border-secondary-subtle p-4">
                <div>
                    <h5 class="modal-title font-outfit text-dark mb-0.5" id="employeeReportModalLabel">Employee Evening AI Audit</h5>
                    <span id="employee-report-meta" class="text-secondary small">Generating report...</span>
                </div>
                <button type="button" class="btn-close btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="background-color: #f8fafc; min-height: 250px;">
                <div id="employee-report-spinner" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="text-secondary mt-3 mb-0">Analyzing today's activity log...</p>
                </div>
                <div id="employee-report-content" class="text-secondary small d-none" style="line-height: 1.625;">
                    <!-- Rendered markdown content goes here -->
                </div>
            </div>
            <div class="modal-footer border-top border-secondary-subtle p-4">
                <button type="button" class="btn btn-secondary rounded-3 px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

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
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script>
    // Show AI report for employee
    function showEmployeeReport(memberId, memberName) {
        const modalEl = document.getElementById('employeeReportModal');
        const modal = new bootstrap.Modal(modalEl);
        
        const metaEl = document.getElementById('employee-report-meta');
        const spinnerEl = document.getElementById('employee-report-spinner');
        const contentEl = document.getElementById('employee-report-content');
        
        metaEl.innerText = `${memberName} — Preparing report...`;
        spinnerEl.classList.remove('d-none');
        contentEl.classList.add('d-none');
        contentEl.innerHTML = '';
        
        modal.show();
        
        const urlParams = new URLSearchParams(window.location.search);
        const filterDate = urlParams.get('filter_date') || '';
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
                <div class="alert alert-danger border-0 p-3 text-dark" style="background-color: rgba(244, 63, 94, 0.15); border-left: 4px solid #f43f5e !important;">
                    <strong>Error:</strong> ${error.message}
                </div>
            `;
        });
    }
</script>
@endsection
