@extends('layouts.manager')

@section('title', 'My Attendance')
@section('page_title', 'My Attendance')

@section('content')
<div class="container-fluid">
    <div class="card kpi-card premium-shadow mb-4">
        <div class="card-header bg-transparent border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">Attendance Logs</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td>
                                <strong class="text-dark">{{ \Carbon\Carbon::parse($log->date)->format('l, M d, Y') }}</strong>
                            </td>
                            <td>
                                @if(strtolower($log->status) == 'present')
                                    <span class="badge bg-success bg-opacity-10 text-success px-2 py-1 border border-success-subtle"><i class="fa-solid fa-check-circle me-1"></i> Present</span>
                                @elseif(strtolower($log->status) == 'absent')
                                    <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1 border border-danger-subtle"><i class="fa-solid fa-times-circle me-1"></i> Absent</span>
                                @elseif(strtolower($log->status) == 'half day' || strtolower($log->status) == 'half_day')
                                    <span class="badge bg-warning bg-opacity-10 text-warning px-2 py-1 border border-warning-subtle"><i class="fa-solid fa-adjust me-1"></i> Half Day</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary px-2 py-1 border border-secondary-subtle">{{ $log->status }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-secondary"><i class="fa-regular fa-clock me-1"></i> {{ $log->check_in ? \Carbon\Carbon::parse($log->check_in)->format('h:i A') : '--' }}</span>
                            </td>
                            <td>
                                <span class="text-secondary"><i class="fa-regular fa-clock me-1"></i> {{ $log->check_out ? \Carbon\Carbon::parse($log->check_out)->format('h:i A') : '--' }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">No attendance logs found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($logs->hasPages())
            <div class="d-flex justify-content-end mt-4">
                {{ $logs->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
