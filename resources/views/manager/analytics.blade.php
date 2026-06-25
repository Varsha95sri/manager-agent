@extends('layouts.manager')

@section('title', 'Predictive Analytics - Manager Agent')
@section('page_title', 'Predictive analytics')

@section('styles')
<style>
    .stat-card {
        border-radius: 12px;
        border: 1px solid var(--border-color);
        background-color: var(--card-bg);
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        padding: 1.25rem;
    }
    .stat-card-title {
        color: var(--text-muted);
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 0.75rem;
    }
    .stat-card-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--text-color);
        font-family: 'Outfit', sans-serif;
    }
    .table-risk th {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
        font-weight: 600;
        border-bottom: 2px solid var(--border-color);
    }
    .table-risk td {
        vertical-align: middle;
        font-size: 0.875rem;
        color: var(--text-color);
        border-bottom: 1px solid var(--border-color);
    }
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }
    .section-title {
        font-family: 'Outfit', sans-serif;
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 1.25rem;
        margin-top: 2rem;
        font-size: 1.25rem;
        border-bottom: 2px solid #e2e8f0;
        padding-bottom: 0.5rem;
    }
</style>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <p class="text-secondary m-0">Forward-looking signals on delivery, workload and burnout risk.</p>
            
            <form method="GET" action="{{ route('manager.analytics') }}" class="d-flex align-items-center gap-2 bg-white px-3 py-2 rounded-pill shadow-sm border" id="filterForm">
                <i class="fas fa-calendar text-muted"></i>
                <select name="time_filter" id="filterSelect" class="form-select form-select-sm border-0 shadow-none bg-transparent fw-bold text-dark" style="cursor: pointer; outline: none; box-shadow: none;" onchange="toggleCustomDates()">
                    <option value="all_time" {{ ($timeFilter ?? 'all_time') == 'all_time' ? 'selected' : '' }}>All Time</option>
                    <option value="today" {{ ($timeFilter ?? 'all_time') == 'today' ? 'selected' : '' }}>Today</option>
                    <option value="week" {{ ($timeFilter ?? 'all_time') == 'week' ? 'selected' : '' }}>Week</option>
                    <option value="month" {{ ($timeFilter ?? 'all_time') == 'month' ? 'selected' : '' }}>Month</option>
                    <option value="quarter" {{ ($timeFilter ?? 'all_time') == 'quarter' ? 'selected' : '' }}>Quarter</option>
                    <option value="custom" {{ ($timeFilter ?? 'all_time') == 'custom' ? 'selected' : '' }}>Custom Date Range</option>
                </select>
                
                <div id="customDateContainer" class="d-flex align-items-center gap-2" style="display: {{ ($timeFilter ?? 'all_time') == 'custom' ? 'flex' : 'none' }} !important;">
                    <input type="date" name="start_date" class="form-control form-control-sm border-0 text-secondary bg-light rounded-pill px-3" value="{{ request('start_date') }}" placeholder="Start Date">
                    <span class="text-muted small">to</span>
                    <input type="date" name="end_date" class="form-control form-control-sm border-0 text-secondary bg-light rounded-pill px-3" value="{{ request('end_date') }}" placeholder="End Date">
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3"><i class="fas fa-search"></i> Apply</button>
                </div>
            </form>
        </div>
    </div>

    <!-- WORKLOAD ANALYSIS -->
    <div class="col-12">
        <h3 class="section-title">Workload Analysis</h3>
    </div>
    
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-card-title">Employee Workload Score</div>
            <div class="stat-card-value">{{ $workloadMetrics['avgWorkloadScore'] }}</div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-card-title">Team Capacity Utilization</div>
            <div class="stat-card-value">{{ $workloadMetrics['capacityUtilization'] }}%</div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-card-title">Overloaded Employee Detection</div>
            <div class="stat-card-value text-danger">{{ $workloadMetrics['overloadedCount'] }} <span class="fs-6">found</span></div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-card-title">Underutilized Employee Detection</div>
            <div class="stat-card-value text-warning">{{ $workloadMetrics['underutilizedCount'] }} <span class="fs-6">found</span></div>
        </div>
    </div>

    <!-- PREDICTIONS -->
    <div class="col-12">
        <h3 class="section-title">Predictions</h3>
    </div>

    <div class="col-lg-6">
        <div class="card glass-card p-4 h-100 d-flex flex-column">
            <h5 class="text-dark mb-3 font-outfit font-semibold">Burnout Risk Prediction</h5>
            <div class="row flex-grow-1">
                <div class="col-sm-7 d-flex flex-column">
                    @if($highFlightRisks->count() > 0)
                        <ul class="list-group list-group-flush mb-auto">
                            @foreach($highFlightRisks as $employee)
                                <li class="list-group-item bg-transparent px-0 text-danger border-secondary-subtle">
                                    <strong>{{ $employee->name }}</strong> - {{ Str::limit($employee->risk_reason, 40) }}
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-success m-0 small mb-auto">No high-risk employees identified for burnout.</p>
                    @endif
                    <!-- Employee Pagination -->
                    <nav aria-label="Employee page navigation" class="mt-3">
                        <ul class="pagination pagination-sm mb-0">
                            <li class="page-item {{ $highFlightRisks->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link text-secondary border-secondary-subtle" href="{{ $highFlightRisks->previousPageUrl() ? $highFlightRisks->previousPageUrl() . '&time_filter=' . ($timeFilter ?? 'all_time') : '#' }}">Prev</a>
                            </li>
                            <li class="page-item active">
                                <span class="page-link text-white" style="background-color: #4f46e5; border-color: #4f46e5;">{{ $highFlightRisks->currentPage() }} / {{ max(1, $highFlightRisks->lastPage()) }}</span>
                            </li>
                            <li class="page-item {{ !$highFlightRisks->hasMorePages() ? 'disabled' : '' }}">
                                <a class="page-link text-secondary border-secondary-subtle" href="{{ $highFlightRisks->nextPageUrl() ? $highFlightRisks->nextPageUrl() . '&time_filter=' . ($timeFilter ?? 'all_time') : '#' }}">Next</a>
                            </li>
                        </ul>
                    </nav>
                </div>
                <div class="col-sm-5 d-flex align-items-center justify-content-center">
                    <div style="position: relative; width: 140px; height: 140px;">
                        <canvas id="flightRiskChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card glass-card p-4 h-100">
            <h5 class="text-dark mb-3 font-outfit font-semibold">Resource Shortage Prediction</h5>
            <ul class="list-group list-group-flush">
                <li class="list-group-item bg-transparent px-0 text-warning border-secondary-subtle">
                    <strong>Engineering Team</strong> - Predicted to hit 95% capacity by next sprint. Risk of bottleneck.
                </li>
                <li class="list-group-item bg-transparent px-0 text-warning border-secondary-subtle">
                    <strong>Design Team</strong> - UI/UX resources running short based on incoming project pipeline.
                </li>
            </ul>
        </div>
    </div>

    <div class="col-12 mt-4">
        <div class="card glass-card border-0 shadow-sm" style="border-radius: 12px; border: 1px solid var(--border-color) !important;">
            <div class="card-header bg-transparent border-bottom">
                <h5 class="text-dark mb-0 font-outfit font-semibold py-2">Project Delay Prediction (Risk Register)</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-risk mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4 py-3 border-0 border-bottom">PROJECT</th>
                            <th class="py-3 border-0 border-bottom">DELAY PROBABILITY</th>
                            <th class="py-3 border-0 border-bottom">PRIMARY DRIVER</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($highRiskProjects))
                            @foreach($highRiskProjects as $project)
                            <tr>
                                <td class="ps-4 py-3 font-medium">{{ $project->name }}</td>
                                <td class="py-3">
                                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2 py-1">
                                        <span class="d-inline-block rounded-circle bg-danger me-1" style="width: 6px; height: 6px;"></span>
                                        High Risk
                                    </span>
                                </td>
                                <td class="py-3 text-secondary">{{ $project->risk_reason }}</td>
                            </tr>
                            @endforeach
                            @if($highRiskProjects->isEmpty())
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">No high-risk projects found.</td>
                                </tr>
                            @endif
                        @else
                            @foreach($projectRisks['high'] ?? [] as $project)
                            <tr>
                                <td class="ps-4 py-3 font-medium">{{ $project->name }}</td>
                                <td class="py-3">
                                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2 py-1">
                                        <span class="d-inline-block rounded-circle bg-danger me-1" style="width: 6px; height: 6px;"></span>
                                        High Risk
                                    </span>
                                </td>
                                <td class="py-3 text-secondary">{{ $project->risk_reason }}</td>
                            </tr>
                            @endforeach
                            @if(count($projectRisks['high'] ?? []) == 0)
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">No high-risk projects found.</td>
                                </tr>
                            @endif
                        @endif
                    </tbody>
                </table>
            </div>
            @if(isset($highRiskProjects))
            <div class="card-footer bg-white border-0 py-3 d-flex justify-content-between align-items-center" style="border-radius: 0 0 12px 12px; border-top: 1px solid var(--border-color) !important;">
                <span class="text-secondary small ms-3">Showing {{ $highRiskProjects->firstItem() ?? 0 }} to {{ $highRiskProjects->lastItem() ?? 0 }} of {{ $highRiskProjects->total() }} entries</span>
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm mb-0 me-3">
                        <li class="page-item {{ $highRiskProjects->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link text-secondary border-secondary-subtle" href="{{ $highRiskProjects->previousPageUrl() ? $highRiskProjects->previousPageUrl() . '&time_filter=' . ($timeFilter ?? 'all_time') : '#' }}">Previous</a>
                        </li>
                        @for ($i = 1; $i <= $highRiskProjects->lastPage(); $i++)
                            <li class="page-item {{ $highRiskProjects->currentPage() == $i ? 'active' : '' }}">
                                <a class="page-link {{ $highRiskProjects->currentPage() == $i ? 'text-white' : 'text-secondary border-secondary-subtle' }}" 
                                   href="{{ $highRiskProjects->url($i) }}&time_filter={{ $timeFilter ?? 'all_time' }}" 
                                   @if($highRiskProjects->currentPage() == $i) style="background-color: #4f46e5; border-color: #4f46e5;" @endif>
                                   {{ $i }}
                                </a>
                            </li>
                        @endfor
                        <li class="page-item {{ !$highRiskProjects->hasMorePages() ? 'disabled' : '' }}">
                            <a class="page-link text-secondary border-secondary-subtle" href="{{ $highRiskProjects->nextPageUrl() ? $highRiskProjects->nextPageUrl() . '&time_filter=' . ($timeFilter ?? 'all_time') : '#' }}">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
            @endif

    <!-- VISUALIZATIONS -->
    <div class="col-12">
        <h3 class="section-title">Visualizations</h3>
    </div>

    <!-- Delivery Forecast Chart -->
    <div class="col-lg-6">
        <div class="card glass-card p-4 h-100">
            <h5 class="text-dark mb-4 font-outfit font-semibold">Delivery Forecast (Trend Charts & Forecast Graphs)</h5>
            <div class="chart-container">
                <canvas id="deliveryChart"></canvas>
            </div>
            <!-- Custom Legend -->
            <div class="d-flex justify-content-center mt-3 gap-4">
                <div class="d-flex align-items-center">
                    <span style="width:12px; height:12px; background-color: #4f46e5; border: 2px solid white; outline: 1px solid #4f46e5; margin-right: 8px;"></span>
                    <span class="text-secondary small font-medium">Actual Trend</span>
                </div>
                <div class="d-flex align-items-center">
                    <span style="width:12px; height:12px; border: 2px dashed #b45309; margin-right: 8px;"></span>
                    <span class="text-secondary small font-medium">Predicted Forecast</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Capacity Planning Chart -->
    <div class="col-lg-6">
        <div class="card glass-card p-4 h-100">
            <h5 class="text-dark mb-4 font-outfit font-semibold">Capacity Planning Charts</h5>
            <div class="chart-container">
                <canvas id="capacityChart"></canvas>
            </div>
            <div class="d-flex justify-content-center mt-3 gap-4">
                <div class="d-flex align-items-center">
                    <span style="width:12px; height:12px; background-color: #4f46e5; border-radius: 2px; margin-right: 8px;"></span>
                    <span class="text-secondary small font-medium">Utilized</span>
                </div>
                <div class="d-flex align-items-center">
                    <span style="width:12px; height:12px; background-color: #e2e8f0; border-radius: 2px; margin-right: 8px;"></span>
                    <span class="text-secondary small font-medium">Available</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js setup -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deliveryLabels = {!! json_encode($deliveryChart['labels']) !!};
        const deliveryActual = {!! json_encode($deliveryChart['actual']) !!};
        const deliveryPredicted = {!! json_encode($deliveryChart['predicted']) !!};
        
        const capacityLabels = {!! json_encode($capacityChart['labels']) !!};
        const capacityUtilized = {!! json_encode($capacityChart['utilized']) !!};
        const capacityAvailable = {!! json_encode($capacityChart['available']) !!};

        // --- Delivery Forecast Chart ---
        const ctxDelivery = document.getElementById('deliveryChart').getContext('2d');
        new Chart(ctxDelivery, {
            type: 'line',
            data: {
                labels: deliveryLabels,
                datasets: [
                    {
                        label: 'Actual',
                        data: deliveryActual,
                        borderColor: '#4f46e5',
                        backgroundColor: '#4f46e5',
                        borderWidth: 2,
                        tension: 0.4,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#4f46e5',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        fill: false
                    },
                    {
                        label: 'Predicted',
                        data: deliveryPredicted,
                        borderColor: '#b45309',
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        tension: 0.4,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#b45309',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        fill: false
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        mode: 'index', intersect: false,
                        backgroundColor: 'rgba(255, 255, 255, 0.9)',
                        titleColor: '#1e293b', bodyColor: '#475569',
                        borderColor: '#e2e8f0', borderWidth: 1,
                        padding: 10, boxPadding: 4
                    }
                },
                scales: {
                    y: { min: 60, max: 90, grid: { color: '#f1f5f9', drawBorder: false }, ticks: { color: '#94a3b8' } },
                    x: { grid: { display: false, drawBorder: false }, ticks: { color: '#94a3b8' } }
                }
            }
        });

        // --- Capacity Planning Chart ---
        const ctxCapacity = document.getElementById('capacityChart').getContext('2d');
        new Chart(ctxCapacity, {
            type: 'bar',
            data: {
                labels: capacityLabels,
                datasets: [
                    {
                        label: 'Utilized',
                        data: capacityUtilized,
                        backgroundColor: '#4f46e5',
                        borderWidth: 0,
                        barPercentage: 0.5,
                        categoryPercentage: 0.7
                    },
                    {
                        label: 'Available',
                        data: capacityAvailable,
                        backgroundColor: '#e2e8f0',
                        borderWidth: 0,
                        barPercentage: 0.5,
                        categoryPercentage: 0.7
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { mode: 'index', intersect: false, backgroundColor: 'rgba(255, 255, 255, 0.9)', titleColor: '#1e293b', bodyColor: '#475569', borderColor: '#e2e8f0', borderWidth: 1 }
                },
                scales: {
                    x: { stacked: true, grid: { display: false, drawBorder: false }, ticks: { color: '#94a3b8' } },
                    y: { stacked: true, min: 0, max: 100, grid: { color: '#f1f5f9', drawBorder: false }, ticks: { color: '#94a3b8' } }
                }
            }
        });

        // --- Flight Risk Pie Chart ---
        const flightRiskData = {!! json_encode($flightRiskChart) !!};
        const ctxFlightRisk = document.getElementById('flightRiskChart').getContext('2d');
        new Chart(ctxFlightRisk, {
            type: 'doughnut',
            data: {
                labels: flightRiskData.labels,
                datasets: [{
                    data: flightRiskData.data,
                    backgroundColor: ['#ef4444', '#f59e0b', '#10b981'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { display: false },
                    tooltip: { backgroundColor: 'rgba(255, 255, 255, 0.9)', titleColor: '#1e293b', bodyColor: '#475569', borderColor: '#e2e8f0', borderWidth: 1 }
                }
            }
        });
    });

    function toggleCustomDates() {
        const select = document.getElementById('filterSelect');
        const customContainer = document.getElementById('customDateContainer');
        if (select.value === 'custom') {
            customContainer.style.setProperty('display', 'flex', 'important');
        } else {
            customContainer.style.setProperty('display', 'none', 'important');
            document.getElementById('filterForm').submit();
        }
    }
</script>
@endsection
