@extends('layouts.manager')

@section('title', 'Leaderboard - Manager Agent')
@section('page_title', 'Performance Leaderboard')

@section('styles')
<style>
    .podium-container {
        display: flex;
        justify-content: center;
        align-items: flex-end;
        height: 350px;
        gap: 15px;
        margin-top: 40px;
        margin-bottom: 40px;
    }
    .podium-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 140px;
        position: relative;
    }
    .podium-rank-1 {
        height: 200px;
        background: linear-gradient(180deg, rgba(234, 179, 8, 0.4) 0%, rgba(234, 179, 8, 0.1) 100%);
        border: 1px solid rgba(234, 179, 8, 0.5);
        border-bottom: none;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
        z-index: 3;
    }
    .podium-rank-2 {
        height: 150px;
        background: linear-gradient(180deg, rgba(148, 163, 184, 0.4) 0%, rgba(148, 163, 184, 0.1) 100%);
        border: 1px solid rgba(148, 163, 184, 0.5);
        border-bottom: none;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
        z-index: 2;
    }
    .podium-rank-3 {
        height: 120px;
        background: linear-gradient(180deg, rgba(180, 83, 9, 0.4) 0%, rgba(180, 83, 9, 0.1) 100%);
        border: 1px solid rgba(180, 83, 9, 0.5);
        border-bottom: none;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
        z-index: 1;
    }
    .podium-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        border: 3px solid #1e293b;
        margin-bottom: 15px;
        object-fit: cover;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.5);
    }
    .podium-rank-1 .podium-avatar {
        width: 100px;
        height: 100px;
        border-color: #eab308;
        box-shadow: 0 0 20px rgba(234, 179, 8, 0.4);
    }
    .podium-rank-2 .podium-avatar {
        border-color: #94a3b8;
    }
    .podium-rank-3 .podium-avatar {
        border-color: #b45309;
    }
    .podium-number {
        font-size: 48px;
        font-weight: 800;
        font-family: 'Outfit', sans-serif;
        opacity: 0.3;
        margin-top: 10px;
    }
    .podium-rank-1 .podium-number { color: #eab308; }
    .podium-rank-2 .podium-number { color: #94a3b8; }
    .podium-rank-3 .podium-number { color: #b45309; }
    
    .podium-score {
        position: absolute;
        top: -15px;
        background: #1e293b;
        padding: 4px 12px;
        border-radius: 20px;
        font-weight: bold;
        font-size: 14px;
        border: 1px solid rgba(255,255,255,0.1);
        box-shadow: 0 4px 6px rgba(0,0,0,0.3);
    }
    .podium-rank-1 .podium-score { color: #eab308; border-color: rgba(234, 179, 8, 0.3); }
    .podium-rank-2 .podium-score { color: #94a3b8; border-color: rgba(148, 163, 184, 0.3); }
    .podium-rank-3 .podium-score { color: #b45309; border-color: rgba(180, 83, 9, 0.3); }
    
    .podium-name {
        text-align: center;
        font-weight: 700;
        margin-bottom: 5px;
        color: #1e293b;
        width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        padding: 0 10px;
    }

    .crown-icon {
        position: absolute;
        top: -125px;
        color: #eab308;
        font-size: 32px;
        filter: drop-shadow(0 0 10px rgba(234, 179, 8, 0.6));
    }
    .stat-card {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        transition: transform 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-5px);
    }
    .nav-pills .nav-link {
        border-radius: 30px;
        padding: 8px 24px;
        color: #475569;
        font-weight: 500;
    }
    .nav-pills .nav-link.active {
        background-color: #3b82f6;
        box-shadow: 0 4px 10px rgba(59, 130, 246, 0.4);
    }
    select[name="filter"] option {
        background-color: #ffffff;
        color: #1e293b;
        font-weight: normal;
    }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <ul class="nav nav-pills" id="leaderboardTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="individual-tab" data-bs-toggle="tab" data-bs-target="#individual" type="button" role="tab" aria-controls="individual" aria-selected="true">Individual</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="team-tab" data-bs-toggle="tab" data-bs-target="#team" type="button" role="tab" aria-controls="team" aria-selected="false">Team</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="org-tab" data-bs-toggle="tab" data-bs-target="#org" type="button" role="tab" aria-controls="org" aria-selected="false">Organization</button>
        </li>
    </ul>

    <form method="GET" action="" class="d-flex align-items-center gap-2 bg-white px-3 py-2 rounded-pill shadow-sm border" id="filterForm">
        <i class="fas fa-calendar text-muted"></i>
        <select name="filter" id="filterSelect" class="form-select form-select-sm border-0 shadow-none bg-transparent fw-bold text-dark" style="cursor: pointer; outline: none; box-shadow: none;" onchange="toggleCustomDates()">
            <option value="all" {{ $filter == 'all' ? 'selected' : '' }}>All Time</option>
            <option value="daily" {{ $filter == 'daily' ? 'selected' : '' }}>Daily</option>
            <option value="weekly" {{ $filter == 'weekly' ? 'selected' : '' }}>Weekly</option>
            <option value="monthly" {{ $filter == 'monthly' ? 'selected' : '' }}>Monthly</option>
            <option value="quarterly" {{ $filter == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
            <option value="yearly" {{ $filter == 'yearly' ? 'selected' : '' }}>Yearly</option>
            <option value="custom" {{ $filter == 'custom' ? 'selected' : '' }}>Custom Date Range</option>
        </select>
        
        <div id="customDateContainer" class="d-flex align-items-center gap-2" style="display: {{ $filter == 'custom' ? 'flex' : 'none' }} !important;">
            <input type="date" name="start_date" class="form-control form-control-sm border-0 text-secondary bg-light rounded-pill px-3" value="{{ $customStart ?? '' }}" placeholder="Start Date">
            <span class="text-muted small">to</span>
            <input type="date" name="end_date" class="form-control form-control-sm border-0 text-secondary bg-light rounded-pill px-3" value="{{ $customEnd ?? '' }}" placeholder="End Date">
            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3"><i class="fas fa-search"></i> Apply</button>
        </div>
    </form>
</div>

<script>
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

<div class="tab-content" id="leaderboardTabsContent">
    <!-- INDIVIDUAL TAB -->
    <div class="tab-pane fade show active" id="individual" role="tabpanel">
        <div class="card glass-card p-4">
            @if($individualLeaderboard->count() >= 3)
                <div class="podium-container">
                    <!-- 2nd Place -->
                    <div class="podium-item">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($individualLeaderboard[1]->name) }}&background=475569&color=fff" class="podium-avatar">
                        <div class="podium-name">{{ explode(' ', $individualLeaderboard[1]->name)[0] }}</div>
                        <div class="w-100 podium-rank-2 d-flex flex-column align-items-center position-relative">
                            <div class="podium-score">{{ number_format($individualLeaderboard[1]->score) }}</div>
                            <div class="podium-number">2</div>
                        </div>
                    </div>
                    <!-- 1st Place -->
                    <div class="podium-item" style="margin-bottom: 20px;">
                        <i class="fas fa-crown crown-icon"></i>
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($individualLeaderboard[0]->name) }}&background=eab308&color=fff" class="podium-avatar">
                        <div class="podium-name" style="font-size: 1.1rem; color: #1e293b;">{{ explode(' ', $individualLeaderboard[0]->name)[0] }}</div>
                        <div class="w-100 podium-rank-1 d-flex flex-column align-items-center position-relative">
                            <div class="podium-score" style="top: -18px; font-size: 16px; padding: 6px 16px;">{{ number_format($individualLeaderboard[0]->score) }}</div>
                            <div class="podium-number">1</div>
                        </div>
                    </div>
                    <!-- 3rd Place -->
                    <div class="podium-item">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($individualLeaderboard[2]->name) }}&background=9a3412&color=fff" class="podium-avatar">
                        <div class="podium-name">{{ explode(' ', $individualLeaderboard[2]->name)[0] }}</div>
                        <div class="w-100 podium-rank-3 d-flex flex-column align-items-center position-relative">
                            <div class="podium-score">{{ number_format($individualLeaderboard[2]->score) }}</div>
                            <div class="podium-number">3</div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="table-responsive mt-4">
                <table class="table table-hover align-middle text-dark mb-0">
                    <thead class="text-secondary fw-bold" style="font-size: 12px; text-transform: uppercase;">
                        <tr>
                            <th class="text-center">Rank</th>
                            <th>Employee</th>
                            <th class="text-center">Task Done</th>
                            <th class="text-center">Task Qlty</th>
                            <th class="text-center">Attendance</th>
                            <th class="text-center">Code Qlty</th>
                            <th class="text-center">GitLab</th>
                            <th class="text-end text-primary">Total Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($individualLeaderboard as $index => $employee)
                            <tr class="hover-light">
                                <td class="text-center py-3">
                                    <span class="text-secondary fw-bold fs-5">{{ $index + 1 }}</span>
                                </td>
                                <td class="py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($employee->name) }}&background=1e293b&color=fff" class="rounded-circle" width="40" height="40">
                                        <div>
                                            <a href="{{ route('manager.employees.show', $employee->id) }}" class="text-decoration-none">
                                                <strong class="text-dark d-block hover-primary">{{ $employee->name }}</strong>
                                            </a>
                                            <span class="text-secondary small">{{ $employee->role ?? 'Employee' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center py-3 text-dark fw-medium">{{ $employee->score_details['task_completion'] }}</td>
                                <td class="text-center py-3 text-dark fw-medium">{{ $employee->score_details['task_quality'] }}</td>
                                <td class="text-center py-3 text-dark fw-medium">{{ $employee->score_details['attendance'] }}</td>
                                <td class="text-center py-3 text-dark fw-medium">{{ $employee->score_details['code_quality'] }}</td>
                                <td class="text-center py-3 text-dark fw-medium">{{ $employee->score_details['gitlab'] }}</td>
                                <td class="text-end py-3">
                                    <span class="fw-bold fs-5 text-primary">{{ number_format($employee->score) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center py-5">No employees found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TEAM TAB -->
    <div class="tab-pane fade" id="team" role="tabpanel">
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-4">
            @forelse($teamLeaderboard as $index => $team)
            <div class="col">
                <div class="stat-card h-100 position-relative overflow-hidden">
                    @if($index === 0)
                        <div class="position-absolute top-0 end-0 bg-warning text-dark px-3 py-1 rounded-bottom-start fw-bold shadow-sm">
                            <i class="fas fa-trophy me-1"></i> #1 Team
                        </div>
                    @endif
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-users fs-4"></i>
                        </div>
                        <div>
                            @if($team->slug)
                                <a href="{{ route('manager.teams.show', $team->slug) }}" class="text-decoration-none hover-primary stretched-link">
                                    <h4 class="mb-0 fw-bold text-dark hover-primary">{{ $team->name }}</h4>
                                </a>
                            @else
                                <h4 class="mb-0 fw-bold text-dark">{{ $team->name }}</h4>
                            @endif
                            <span class="text-secondary small">{{ $team->member_count }} Members</span>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Productivity</span>
                        <span class="fw-bold text-dark">{{ $team->score_details['productivity'] }} pts</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Delivery</span>
                        <span class="fw-bold text-dark">{{ $team->score_details['delivery'] }} pts</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Code Quality</span>
                        <span class="fw-bold text-dark">{{ $team->score_details['code_quality'] }} pts</span>
                    </div>
                    
                    <hr class="my-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-uppercase fw-bold text-secondary" style="font-size: 12px; letter-spacing: 1px;">Total Score</span>
                        <span class="fs-4 fw-bold text-primary">{{ number_format($team->score) }}</span>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12"><div class="alert alert-info">No team data available.</div></div>
            @endforelse
        </div>
    </div>

    <!-- ORGANIZATION TAB -->
    <div class="tab-pane fade" id="org" role="tabpanel">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card glass-card h-100">
                    <div class="card-header bg-transparent border-0 pt-4 pb-0">
                        <h5 class="fw-bold text-dark"><i class="fas fa-star text-warning me-2"></i> Top Employees</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush bg-transparent">
                            @foreach($orgLeaderboard->top_employees as $emp)
                            <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center px-0">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($emp->name) }}&background=1e293b&color=fff" class="rounded-circle" width="30" height="30">
                                    <a href="{{ route('manager.employees.show', $emp->id) }}" class="text-decoration-none">
                                        <span class="fw-medium text-dark hover-primary">{{ $emp->name }}</span>
                                    </a>
                                </div>
                                <span class="badge bg-primary rounded-pill">{{ number_format($emp->score) }} pts</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card glass-card h-100">
                    <div class="card-header bg-transparent border-0 pt-4 pb-0">
                        <h5 class="fw-bold text-dark"><i class="fas fa-users text-primary me-2"></i> Top Teams</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush bg-transparent">
                            @foreach($orgLeaderboard->top_teams as $team)
                            <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center px-0">
                                @if($team->slug)
                                    <a href="{{ route('manager.teams.show', $team->slug) }}" class="text-decoration-none">
                                        <span class="fw-medium text-dark hover-primary">{{ $team->name }}</span>
                                    </a>
                                @else
                                    <span class="fw-medium text-dark">{{ $team->name }}</span>
                                @endif
                                <span class="badge bg-success rounded-pill">{{ number_format($team->score) }} pts</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card glass-card">
                    <div class="card-header bg-transparent border-0 pt-4 pb-0">
                        <h5 class="fw-bold text-dark"><i class="fab fa-gitlab text-danger me-2"></i> Top Contributors (GitLab)</h5>
                    </div>
                    <div class="card-body">
                        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-5 g-3">
                            @foreach($orgLeaderboard->top_contributors as $contributor)
                            <div class="col text-center">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($contributor->name) }}&background=ef4444&color=fff" class="rounded-circle mb-2" width="60" height="60">
                                <a href="{{ route('manager.employees.show', $contributor->id) }}" class="text-decoration-none">
                                    <div class="fw-bold text-dark text-truncate hover-primary">{{ $contributor->name }}</div>
                                </a>
                                <div class="small text-muted">{{ $contributor->commits_count }} Commits</div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script>
document.addEventListener('DOMContentLoaded', function () {
    var triggerTabList = [].slice.call(document.querySelectorAll('#leaderboardTabs button'))
    triggerTabList.forEach(function (triggerEl) {
        var tabTrigger = new bootstrap.Tab(triggerEl)
        triggerEl.addEventListener('click', function (event) {
            event.preventDefault()
            tabTrigger.show()
        })
    })
});
</script>
@endsection
