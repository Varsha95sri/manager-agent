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
    .current-user-row {
        background-color: rgba(59, 130, 246, 0.1) !important;
        border-left: 4px solid #3b82f6;
    }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    
    @if(isset($currentUserRank) && $currentUserData)
    <div class="alert alert-primary mb-0 d-flex align-items-center gap-3 w-100 shadow-sm border-0" style="background: linear-gradient(135deg, #eff6ff, #dbeafe);">
        <i class="fas fa-medal text-primary fs-3"></i>
        <div>
            <h5 class="mb-1 text-primary fw-bold">Your Current Rank: #{{ $currentUserRank }}</h5>
            <p class="mb-0 text-secondary small">Total Score: {{ number_format($currentUserData->score) }} Pts</p>
        </div>
    </div>
    @endif

    <form method="GET" action="" class="d-flex align-items-center gap-2 bg-white px-3 py-2 rounded-pill shadow-sm border mt-3 w-100" id="filterForm">
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
        
        <div id="customDateContainer" class="d-flex align-items-center gap-2 ms-auto" style="display: {{ $filter == 'custom' ? 'flex' : 'none' }} !important;">
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
                    <th class="text-center">Attendance</th>
                    <th class="text-center">GitLab</th>
                    <th class="text-end text-primary">Total Score</th>
                </tr>
            </thead>
            <tbody>
                @forelse($individualLeaderboard as $index => $employee)
                    <tr class="hover-light {{ auth()->check() && auth()->user()->email === $employee->email ? 'current-user-row' : '' }}">
                        <td class="text-center py-3">
                            @if($index === 0)
                                <i class="fas fa-medal fs-4 text-warning"></i>
                            @elseif($index === 1)
                                <i class="fas fa-medal fs-4 text-secondary"></i>
                            @elseif($index === 2)
                                <i class="fas fa-medal fs-4" style="color: #b45309;"></i>
                            @else
                                <span class="text-secondary fw-bold fs-5">{{ $index + 1 }}</span>
                            @endif
                        </td>
                        <td class="py-3">
                            <div class="d-flex align-items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($employee->name) }}&background=1e293b&color=fff" class="rounded-circle" width="40" height="40">
                                <div>
                                    <strong class="text-dark d-block {{ auth()->check() && auth()->user()->email === $employee->email ? 'text-primary' : '' }}">{{ $employee->name }} {{ auth()->check() && auth()->user()->email === $employee->email ? '(You)' : '' }}</strong>
                                    <span class="text-secondary small">{{ $employee->designation->name ?? 'Employee' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="text-center py-3 text-dark fw-medium">{{ $employee->tasks_count }}</td>
                        <td class="text-center py-3 text-dark fw-medium">{{ $employee->attendance_logs_count }}</td>
                        <td class="text-center py-3 text-dark fw-medium">{{ $employee->commits_count }}</td>
                        <td class="text-end py-3">
                            <span class="fw-bold fs-5 text-primary">{{ number_format($employee->score) }}</span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-5">No employees found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection
