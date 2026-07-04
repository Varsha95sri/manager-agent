@extends('layouts.manager')

@section('title', 'Employee Dashboard')
@section('page_title', 'My Dashboard')

@section('styles')
<style>
    .premium-shadow {
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
    }
    .kpi-card {
        border-radius: 16px;
        border: 1px solid var(--border-color);
        background: #ffffff;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    .icon-circle {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
    .bg-gradient-primary {
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        color: white;
    }
    .bg-gradient-success {
        background: linear-gradient(135deg, #10b981, #34d399);
        color: white;
    }
    .bg-gradient-warning {
        background: linear-gradient(135deg, #f59e0b, #fbbf24);
        color: white;
    }
    .bg-gradient-info {
        background: linear-gradient(135deg, #0ea5e9, #38bdf8);
        color: white;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    @if(isset($error))
        <div class="alert alert-danger premium-shadow rounded-3">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ $error }}
        </div>
    @else
        <!-- Employee Profile Header -->
        <div class="card kpi-card premium-shadow mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-circle bg-gradient-primary text-white" style="width: 64px; height: 64px; font-size: 32px;">
                        {{ substr($teamMember->name, 0, 1) }}
                    </div>
                    <div>
                        <h4 class="mb-1 fw-bold">{{ $teamMember->name }}</h4>
                        <p class="text-muted mb-0">
                            {{ $teamMember->designation ? $teamMember->designation->name : 'Employee' }} 
                            @if($teamMember->team)
                                | <span class="badge bg-primary bg-opacity-10 text-primary">{{ $teamMember->team->name }}</span>
                            @endif
                            @if($teamMember->gitlab_username)
                                | <span class="text-secondary"><i class="fa-brands fa-gitlab text-warning me-1"></i>{{ $teamMember->gitlab_username }}</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card kpi-card hover-lift h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="text-muted fw-semibold mb-0">Total Tasks</h6>
                            <div class="icon-circle bg-gradient-primary bg-opacity-10 text-primary" style="width: 40px; height: 40px; font-size: 20px;">
                                <i class="bi bi-list-check"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold mb-0">{{ $totalTasks }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card kpi-card hover-lift h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="text-muted fw-semibold mb-0">Completed</h6>
                            <div class="icon-circle bg-gradient-success bg-opacity-10 text-success" style="width: 40px; height: 40px; font-size: 20px;">
                                <i class="bi bi-check-circle"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold mb-0">{{ $completedTasks }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card kpi-card hover-lift h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="text-muted fw-semibold mb-0">Pending/Overdue</h6>
                            <div class="icon-circle bg-gradient-warning bg-opacity-10 text-warning" style="width: 40px; height: 40px; font-size: 20px;">
                                <i class="bi bi-clock-history"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold mb-0">{{ $pendingTasks }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card kpi-card hover-lift h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="text-muted fw-semibold mb-0">Attendance (Month)</h6>
                            <div class="icon-circle bg-gradient-info bg-opacity-10 text-info" style="width: 40px; height: 40px; font-size: 20px;">
                                <i class="bi bi-calendar-check"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold mb-0">{{ $attendancePercentage }}%</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row g-4 mb-4">
            <div class="col-md-8">
                <div class="card kpi-card premium-shadow h-100">
                    <div class="card-header bg-transparent border-0 pt-4 pb-0">
                        <h6 class="fw-bold mb-0">Task Completion Trend (Last 7 Days)</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="taskTrendChart" height="250"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card kpi-card premium-shadow h-100">
                    <div class="card-header bg-transparent border-0 pt-4 pb-0">
                        <h6 class="fw-bold mb-0">Task Status Breakdown</h6>
                    </div>
                    <div class="card-body d-flex justify-content-center align-items-center">
                        <canvas id="taskStatusChart" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <!-- Leave Request Form -->
            <div class="col-md-6">
                <div class="card kpi-card premium-shadow h-100">
                    <div class="card-header bg-transparent border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0">Apply for Leave</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('user.leaves.store') }}" method="POST">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-semibold">Start Date</label>
                                    <input type="date" name="start_date" class="form-control bg-light border-0 shadow-none" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-semibold">End Date</label>
                                    <input type="date" name="end_date" class="form-control bg-light border-0 shadow-none" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label text-muted small fw-semibold">Leave Type</label>
                                    <select name="type" class="form-select bg-light border-0 shadow-none" required>
                                        <option value="Sick">Sick Leave</option>
                                        <option value="Casual">Casual Leave</option>
                                        <option value="Annual">Annual Leave</option>
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label text-muted small fw-semibold">Reason</label>
                                    <textarea name="reason" class="form-control bg-light border-0 shadow-none" rows="3" required></textarea>
                                </div>
                                <div class="col-md-12 text-end mt-4">
                                    <button type="submit" class="btn btn-primary px-4 rounded-pill">Submit Request</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- My Recent Tasks -->
            <div class="col-md-6">
                <div class="card kpi-card premium-shadow h-100">
                    <div class="card-header bg-transparent border-0 pt-4 pb-0">
                        <h6 class="fw-bold mb-0">Recent Tasks</h6>
                    </div>
                    <div class="card-body p-0 mt-3">
                        <div class="list-group list-group-flush">
                            @forelse($allTasks->take(5) as $task)
                                <div class="list-group-item px-4 py-3 border-secondary-subtle">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1 text-dark fw-semibold">{{ $task->title }}</h6>
                                            <small class="text-muted">Due: {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('M d, Y') : 'N/A' }}</small>
                                        </div>
                                        <span class="badge rounded-pill 
                                            {{ $task->status === 'Completed' ? 'bg-success bg-opacity-10 text-success border border-success border-opacity-25' : 
                                               ($task->status === 'In Progress' ? 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25' : 
                                               'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25') }}">
                                            {{ $task->status }}
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <div class="p-4 text-center text-muted">No tasks assigned yet.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- My Team Section -->
        <div id="my-team" class="card kpi-card premium-shadow mb-4">
            <div class="card-header bg-transparent border-0 pt-4 pb-0">
                <h6 class="fw-bold mb-0">My Team @if($teamMember->team) ({{ $teamMember->team->name }}) @endif</h6>
            </div>
            <div class="card-body">
                @if($teamMembers && $teamMembers->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle border-secondary-subtle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0 rounded-start">Member Name</th>
                                    <th class="border-0">Designation</th>
                                    <th class="border-0">GitLab ID</th>
                                    <th class="border-0">Repositories (Projects)</th>
                                    <th class="border-0 text-center rounded-end">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($teamMembers as $member)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="icon-circle bg-primary bg-opacity-10 text-primary" style="width: 40px; height: 40px; font-size: 16px;">
                                                    {{ substr($member->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <a href="{{ route('user.team-members.show', $member->id) }}" class="text-decoration-none">
                                                        <h6 class="mb-0 fw-bold text-dark hover-primary">{{ $member->name }}</h6>
                                                    </a>
                                                    <small class="text-muted">{{ $member->role }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-muted">{{ $member->designation ? $member->designation->name : 'N/A' }}</td>
                                        <td>
                                            @if($member->gitlab_username)
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary"><i class="fa-brands fa-gitlab text-warning me-1"></i>{{ $member->gitlab_username }}</span>
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                @forelse($member->projectAllocations as $alloc)
                                                    @if($alloc->project && strtolower($alloc->project->status) == 'active')
                                                        <span class="badge bg-light text-dark border border-secondary-subtle" title="Role: {{ $alloc->role }}">{{ $alloc->project->name }}</span>
                                                    @endif
                                                @empty
                                                    <span class="text-muted small">-</span>
                                                @endforelse
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill">Active</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-people" style="font-size: 3rem;"></i>
                        <p class="mt-3 mb-0">You are not part of any team yet.</p>
                    </div>
                @endif
            </div>
        </div>

    @endif
</div>

<!-- Scripts for Charts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(isset($teamMember) && $teamMember)
            // Task Trend Chart
            const trendCtx = document.getElementById('taskTrendChart').getContext('2d');
            new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($last7Days) !!},
                    datasets: [{
                        label: 'Completed Tasks',
                        data: {!! json_encode($taskTrendData) !!},
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { borderDash: [2, 4], color: '#e2e8f0' } },
                        x: { grid: { display: false } }
                    }
                }
            });

            // Task Status Chart
            const statusCtx = document.getElementById('taskStatusChart').getContext('2d');
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Completed', 'In Progress', 'Pending', 'Overdue'],
                    datasets: [{
                        data: {!! json_encode($taskStatusData) !!},
                        backgroundColor: ['#10b981', '#4f46e5', '#f59e0b', '#ef4444'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8 } }
                    }
                }
            });
        @endif
    });
</script>
@endsection
