@extends('layouts.manager')

@section('title', 'Attendance Tracking - Manager Agent')
@section('page_title', 'Attendance Management')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-12">
        
        <!-- Header Actions Section -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h2 class="h3 font-outfit text-dark mb-1">Attendance Dashboard</h2>
                <p class="text-secondary small mb-0">Monitor team attendance trends and manage individual daily logs.</p>
            </div>
            
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <!-- Cleaner Date Picker UI -->
                <div class="card bg-white shadow-sm border-secondary-subtle p-1 rounded-3">
                    <form action="{{ route('manager.attendance-registry') }}" method="GET" class="d-flex gap-2 align-items-center m-0" id="filterForm">
                        <div class="d-flex align-items-center bg-light rounded-2 px-2 border border-secondary-subtle">
                            <span class="text-secondary small me-2"><i class="bi bi-calendar-range"></i></span>
                            <input type="date" name="start_date" id="startDate" class="form-control form-control-sm border-0 bg-transparent text-dark shadow-none px-1" value="{{ $startDate }}" required style="width: 120px;">
                            <span class="text-secondary mx-1">-</span>
                            <input type="date" name="end_date" id="endDate" class="form-control form-control-sm border-0 bg-transparent text-dark shadow-none px-1" value="{{ $endDate }}" required style="width: 120px;">
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary rounded-2 px-3">Filter</button>
                        
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle rounded-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Quick Select
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end bg-white shadow-sm border-secondary-subtle shadow">
                                <li><h6 class="dropdown-header text-secondary text-uppercase" style="font-size: 10px;">Daily</h6></li>
                                <li><a class="dropdown-item text-dark hover-light" href="#" onclick="setQuickDate('{{ now()->toDateString() }}', '{{ now()->toDateString() }}')">Today</a></li>
                                <li><a class="dropdown-item text-dark hover-light" href="#" onclick="setQuickDate('{{ now()->subDay()->toDateString() }}', '{{ now()->subDay()->toDateString() }}')">Yesterday</a></li>
                                <li><hr class="dropdown-divider border-secondary-subtle"></li>
                                <li><h6 class="dropdown-header text-secondary text-uppercase" style="font-size: 10px;">Monthly / Yearly</h6></li>
                                <li><a class="dropdown-item text-dark hover-light" href="#" onclick="setQuickDate('{{ now()->startOfMonth()->toDateString() }}', '{{ now()->endOfMonth()->toDateString() }}')">This Month</a></li>
                                <li><a class="dropdown-item text-dark hover-light" href="#" onclick="setQuickDate('{{ now()->subMonth()->startOfMonth()->toDateString() }}', '{{ now()->subMonth()->endOfMonth()->toDateString() }}')">Last Month</a></li>
                                <li><a class="dropdown-item text-dark hover-light" href="#" onclick="setQuickDate('{{ now()->startOfYear()->toDateString() }}', '{{ now()->endOfYear()->toDateString() }}')">This Year</a></li>
                            </ul>
                        </div>
                    </form>
                </div>

                <button type="button" class="btn accent-btn btn-sm d-inline-flex align-items-center rounded-3 px-3 py-2 text-dark shadow-lg ms-2" data-bs-toggle="modal" data-bs-target="#logAttendanceModal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2 text-dark" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                    </svg>
                    Log Attendance
                </button>
            </div>
        </div>

        <!-- Team Trends KPI Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-2 col-sm-4">
                <div class="card bg-white shadow-sm border-secondary-subtle shadow-sm h-100 p-3">
                    <div class="text-secondary small font-semibold text-uppercase tracking-wider mb-1">Total Logs</div>
                    <div class="h3 text-dark mb-0">{{ $teamTrends['total_logs'] ?? 0 }}</div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4">
                <div class="card bg-white shadow-sm border-secondary-subtle shadow-sm h-100 p-3">
                    <div class="text-secondary small font-semibold text-uppercase tracking-wider mb-1">Present</div>
                    <div class="h3 text-success mb-0">{{ $teamTrends['present'] ?? 0 }}</div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4">
                <div class="card bg-white shadow-sm border-secondary-subtle shadow-sm h-100 p-3">
                    <div class="text-secondary small font-semibold text-uppercase tracking-wider mb-1">Absent</div>
                    <div class="h3 text-danger mb-0">{{ $teamTrends['absent'] ?? 0 }}</div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4">
                <div class="card bg-white shadow-sm border-secondary-subtle shadow-sm h-100 p-3">
                    <div class="text-secondary small font-semibold text-uppercase tracking-wider mb-1">Late</div>
                    <div class="h3 text-warning mb-0">{{ $teamTrends['late'] ?? 0 }}</div>
                </div>
            </div>
            <div class="col-md-4 col-sm-8">
                <div class="card bg-white shadow-sm border-secondary-subtle shadow-sm h-100 p-3" style="border-left: 4px solid #6366f1 !important;">
                    <div class="text-secondary small font-semibold text-uppercase tracking-wider mb-1">Avg Attendance %</div>
                    <div class="d-flex align-items-end">
                        <div class="h3 text-info mb-0">{{ $teamTrends['average_percentage'] ?? 0 }}</div>
                        <span class="fs-6 text-secondary ms-1 mb-1 font-normal">%</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <!-- Aggregated Analytics Table -->
            <div class="col-12">
                <div class="card glass-card p-4 border border-secondary-subtle shadow-2xl">
                    <h4 class="h5 font-outfit text-dark mb-3">Attendance Analytics Summary ({{ \Carbon\Carbon::parse($startDate)->format('M d') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }})</h4>
                    
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-dark" style="--bs-table-bg: transparent; --bs-table-hover-bg: rgba(255, 255, 255, 0.02); --bs-table-border-color: #334155;">
                            <thead class="text-secondary" style="font-size: 11px;">
                                <tr>
                                    <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider">Member</th>
                                    <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider text-center">Percentage</th>
                                    <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider text-center">Score</th>
                                    <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider text-center">Present</th>
                                    <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider text-center">Late</th>
                                    <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider text-center">Absent</th>
                                    <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider text-center">Leave</th>
                                    <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider">Raw Logs (Recent)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($teamMembers as $member)
                                    @php
                                        $stats = $monthlyAnalytics[$member->id] ?? null;
                                        $memberLogsMap = $logsMap[$member->id] ?? [];
                                        
                                        // Sort member's logs by date descending
                                        krsort($memberLogsMap);
                                        // Get only top 3 most recent for the summary column
                                        $recentLogs = array_slice($memberLogsMap, 0, 3);
                                    @endphp
                                    <tr>
                                        <td class="py-3">
                                            <div class="font-semibold text-dark">{{ $member->name }}</div>
                                            <div class="text-secondary small">{{ $member->email }}</div>
                                        </td>
                                        
                                        @if($stats && $stats['total_days'] > 0)
                                            <td class="py-3 text-center">
                                                <div class="progress" style="height: 6px; background-color: rgba(255,255,255,0.1);">
                                                    <div class="progress-bar {{ $stats['attendance_percentage'] >= 80 ? 'bg-success' : ($stats['attendance_percentage'] >= 60 ? 'bg-warning' : 'bg-danger') }}" role="progressbar" style="width: {{ $stats['attendance_percentage'] }}%" aria-valuenow="{{ $stats['attendance_percentage'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <div class="small mt-1 {{ $stats['attendance_percentage'] >= 80 ? 'text-success' : ($stats['attendance_percentage'] >= 60 ? 'text-warning' : 'text-danger') }}">{{ $stats['attendance_percentage'] }}%</div>
                                            </td>
                                            <td class="py-3 text-center">
                                                <span class="badge bg-light border border-secondary-subtle text-info">{{ $stats['attendance_score'] }}</span>
                                            </td>
                                            <td class="py-3 text-center text-secondary">{{ $stats['present'] }}</td>
                                            <td class="py-3 text-center text-secondary">{{ $stats['late'] }}</td>
                                            <td class="py-3 text-center text-secondary">{{ $stats['absent'] }}</td>
                                            <td class="py-3 text-center text-secondary">{{ $stats['leave'] }}</td>
                                        @else
                                            <td colspan="6" class="py-3 text-center text-secondary small italic">No attendance recorded in this date range.</td>
                                        @endif
                                        
                                        <td class="py-3">
                                            <div class="d-flex flex-wrap gap-1" style="max-width: 200px;">
                                                @forelse($recentLogs as $date => $log)
                                                    @php
                                                        $badgeClass = 'bg-secondary';
                                                        $statusLabel = 'Unknown';
                                                        if($log->status === 'present') { $badgeClass = 'bg-success text-success'; $statusLabel = 'P'; }
                                                        elseif($log->status === 'absent') { $badgeClass = 'bg-danger text-danger'; $statusLabel = 'A'; }
                                                        elseif($log->status === 'late') { $badgeClass = 'bg-warning text-warning'; $statusLabel = 'L'; }
                                                        elseif($log->status === 'leave') { $badgeClass = 'bg-info text-info'; $statusLabel = 'V'; }
                                                    @endphp
                                                    <span class="badge rounded-pill {{ $badgeClass }} bg-opacity-10 border border-opacity-20 px-2 py-1" style="font-size: 9px;" title="{{ $date }}: {{ ucfirst($log->status) }}">
                                                        {{ \Carbon\Carbon::parse($date)->format('M d') }} - {{ $statusLabel }}
                                                    </span>
                                                @empty
                                                    <span class="text-secondary small italic">-</span>
                                                @endforelse
                                                
                                                @if(count($memberLogsMap) > 3)
                                                    <span class="badge rounded-pill bg-light text-secondary border border-secondary-subtle px-2 py-1" style="font-size: 9px;">
                                                        +{{ count($memberLogsMap) - 3 }} more
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if($teamMembers->hasPages())
                        <div class="mt-4 border-top border-secondary-subtle pt-4 d-flex justify-content-center">
                            {!! $teamMembers->links() !!}
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Daily Detailed Logs Table (CRUD) -->
        <div class="row g-4">
            <div class="col-12">
                <div class="card glass-card p-4 border border-secondary-subtle shadow-2xl">
                    <h4 class="h5 font-outfit text-dark mb-3">Daily Logs Details (Manage Records)</h4>
                    
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-dark" style="--bs-table-bg: transparent; --bs-table-hover-bg: rgba(255, 255, 255, 0.02); --bs-table-border-color: #334155;">
                            <thead class="text-secondary" style="font-size: 11px;">
                                <tr>
                                    <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider">Date</th>
                                    <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider">Team Member</th>
                                    <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider">Status</th>
                                    <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider">Check In/Out</th>
                                    <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendanceLogs as $log)
                                    <tr id="log-row-{{ $log->id }}">
                                        <td class="py-3 text-secondary font-semibold">
                                            {{ \Carbon\Carbon::parse($log->date)->format('M d, Y') }}
                                        </td>
                                        <td class="py-3">
                                            {{ $log->teamMember?->name ?? 'Unknown' }}
                                        </td>
                                        <td class="py-3">
                                            <div class="view-mode-{{ $log->id }}">
                                                @if($log->status === 'present')
                                                    <span class="badge rounded-pill bg-success bg-opacity-10 text-success border border-success border-opacity-20 px-2 py-1">Present</span>
                                                @elseif($log->status === 'absent')
                                                    <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger border border-danger border-opacity-20 px-2 py-1">Absent</span>
                                                @elseif($log->status === 'late')
                                                    <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning border border-warning border-opacity-20 px-2 py-1">Late</span>
                                                @elseif($log->status === 'leave')
                                                    <span class="badge rounded-pill bg-info bg-opacity-10 text-info border border-info border-opacity-20 px-2 py-1">Leave</span>
                                                    <div class="text-secondary small mt-1">{{ $log->leave_type }}</div>
                                                @endif
                                            </div>
                                            <div class="edit-mode-{{ $log->id }} d-none">
                                                <select name="status" form="edit-log-form-{{ $log->id }}" class="form-select form-select-sm border-secondary-subtle bg-white shadow-sm text-dark mb-1" onchange="toggleInlineLeaveType({{ $log->id }})">
                                                    <option value="present" {{ $log->status == 'present' ? 'selected' : '' }}>Present</option>
                                                    <option value="absent" {{ $log->status == 'absent' ? 'selected' : '' }}>Absent</option>
                                                    <option value="late" {{ $log->status == 'late' ? 'selected' : '' }}>Late</option>
                                                    <option value="leave" {{ $log->status == 'leave' ? 'selected' : '' }}>Leave</option>
                                                </select>
                                                <input type="text" id="inline-leave-type-{{ $log->id }}" name="leave_type" form="edit-log-form-{{ $log->id }}" value="{{ $log->leave_type }}" class="form-control form-control-sm border-secondary-subtle bg-white shadow-sm text-dark {{ $log->status == 'leave' ? '' : 'd-none' }}" placeholder="Leave Type">
                                            </div>
                                        </td>
                                        <td class="py-3 text-secondary">
                                            <div class="view-mode-{{ $log->id }}">
                                                @if($log->check_in || $log->check_out)
                                                    {{ $log->check_in ?? '--:--' }} to {{ $log->check_out ?? '--:--' }}
                                                @else
                                                    -
                                                @endif
                                            </div>
                                            <div class="edit-mode-{{ $log->id }} d-none">
                                                <div class="d-flex gap-1">
                                                    <input type="time" name="check_in" form="edit-log-form-{{ $log->id }}" value="{{ $log->check_in }}" class="form-control form-control-sm border-secondary-subtle bg-white shadow-sm text-dark" title="Check In">
                                                    <input type="time" name="check_out" form="edit-log-form-{{ $log->id }}" value="{{ $log->check_out }}" class="form-control form-control-sm border-secondary-subtle bg-white shadow-sm text-dark" title="Check Out">
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3 text-end">
                                            <form id="edit-log-form-{{ $log->id }}" action="{{ route('manager.update-attendance', $log->id) }}" method="POST" class="d-none">
                                                @csrf
                                                @method('PUT')
                                            </form>
                                            
                                            <div class="view-mode-{{ $log->id }} d-flex justify-content-end gap-2">
                                                <button type="button" class="btn btn-xs btn-outline-info" onclick="toggleLogEdit({{ $log->id }})">Edit</button>
                                                <form action="{{ route('manager.destroy-attendance', $log->id) }}" method="POST" onsubmit="return confirm('Delete this attendance log permanently?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-xs btn-outline-danger">Delete</button>
                                                </form>
                                            </div>
                                            
                                            <div class="edit-mode-{{ $log->id }} d-none d-flex justify-content-end gap-2">
                                                <button type="submit" form="edit-log-form-{{ $log->id }}" class="btn btn-xs btn-success">Save</button>
                                                <button type="button" class="btn btn-xs btn-outline-secondary" onclick="toggleLogEdit({{ $log->id }})">Cancel</button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-secondary italic small">No daily logs found for this date range.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if($attendanceLogs->hasPages())
                        <div class="mt-4 border-top border-secondary-subtle pt-4 d-flex justify-content-center">
                            {!! $attendanceLogs->links() !!}
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Log Attendance Modal -->
<div class="modal fade" id="logAttendanceModal" tabindex="-1" aria-labelledby="logAttendanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-dark" style="background-color: #ffffff; border: 1px solid var(--border-color); border-radius: 20px;">
            <div class="modal-header border-bottom border-secondary-subtle p-4">
                <h5 class="modal-title font-outfit text-dark" id="logAttendanceModalLabel">Log Member Attendance</h5>
                <button type="button" class="btn-close btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('manager.store-attendance') }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-secondary small font-bold text-uppercase tracking-wider">Team Member Email <span class="text-danger">*</span></label>
                        <div class="position-relative w-100" id="employeeSearchContainer">
                            <div class="input-group">
                                <input type="text" id="employeeSearchInput" class="form-control border-secondary-subtle bg-white shadow-sm text-dark px-3 py-2.5 @error('email') is-invalid @enderror" placeholder="Type to search or click for list..." autocomplete="off" oninput="debounceFilterEmployees()" onclick="openDropdown(); filterEmployees()" value="{{ old('email') }}" style="border-top-left-radius: 0.5rem; border-bottom-left-radius: 0.5rem; border-right: none;">
                                <span class="input-group-text bg-white border-secondary-subtle text-secondary" style="border-top-right-radius: 0.5rem; border-bottom-right-radius: 0.5rem; cursor: pointer;" onclick="toggleDropdown()">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-down" viewBox="0 0 16 16">
                                      <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
                                    </svg>
                                </span>
                            </div>
                            <input type="hidden" name="email" id="employeeEmailHidden" value="{{ old('email') }}" required>
                            
                            <ul class="dropdown-menu w-100 bg-white shadow-sm border-secondary-subtle shadow-xl custom-scroll mt-1" style="max-height: 250px; overflow-y: auto; position: absolute; top: 100%; left: 0; z-index: 1050;" id="employeeDropdownList">
                                <li id="searchingItem" class="px-3 py-2 text-secondary small text-center d-none">
                                    Searching...
                                </li>
                                <div id="employeeListResults">
                                    <li class="px-3 py-2 text-secondary small text-center">Click or type to load...</li>
                                </div>
                            </ul>
                        </div>
                        <div class="form-text text-secondary small mt-1">Type any alphabet/word to search dynamically, or enter email manually.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-secondary small font-bold text-uppercase tracking-wider">Date <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control border-secondary-subtle bg-white shadow-sm text-dark rounded-3 px-3 py-2.5 @error('date') is-invalid @enderror" value="{{ old('date', date('Y-m-d')) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-secondary small font-bold text-uppercase tracking-wider">Status <span class="text-danger">*</span></label>
                        <select name="status" id="statusSelect" class="form-select border-secondary-subtle bg-white shadow-sm text-dark rounded-3 px-3 py-2.5" required onchange="toggleLeaveType()">
                            <option value="present" selected>Present</option>
                            <option value="absent">Absent</option>
                            <option value="late">Late Arrival</option>
                            <option value="leave">On Leave</option>
                        </select>
                    </div>
                    <div class="mb-3 d-none" id="leaveTypeContainer">
                        <label class="form-label text-secondary small font-bold text-uppercase tracking-wider">Leave Type</label>
                        <input type="text" name="leave_type" class="form-control border-secondary-subtle bg-white shadow-sm text-dark rounded-3 px-3 py-2.5" placeholder="e.g. Sick Leave, Vacation">
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label text-secondary small font-bold text-uppercase tracking-wider">Check In (Optional)</label>
                            <input type="time" name="check_in" class="form-control border-secondary-subtle bg-white shadow-sm text-dark rounded-3 px-3 py-2.5">
                        </div>
                        <div class="col-6">
                            <label class="form-label text-secondary small font-bold text-uppercase tracking-wider">Check Out (Optional)</label>
                            <input type="time" name="check_out" class="form-control border-secondary-subtle bg-white shadow-sm text-dark rounded-3 px-3 py-2.5">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top border-secondary-subtle p-4">
                    <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn accent-btn rounded-3 px-4 font-bold">Save Log</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        @if($errors->any())
            const logModal = new bootstrap.Modal(document.getElementById('logAttendanceModal'));
            logModal.show();
        @endif
        
        toggleLeaveType();
    });
    
    function toggleLeaveType() {
        const select = document.getElementById('statusSelect');
        const leaveContainer = document.getElementById('leaveTypeContainer');
        if (select.value === 'leave') {
            leaveContainer.classList.remove('d-none');
            leaveContainer.querySelector('input').required = true;
        } else {
            leaveContainer.classList.add('d-none');
            leaveContainer.querySelector('input').required = false;
        }
    }
    
    function setQuickDate(start, end) {
        document.getElementById('startDate').value = start;
        document.getElementById('endDate').value = end;
        document.getElementById('filterForm').submit();
    }

    function toggleLogEdit(id) {
        document.querySelectorAll(`.view-mode-${id}`).forEach(el => el.classList.toggle('d-none'));
        document.querySelectorAll(`.edit-mode-${id}`).forEach(el => el.classList.toggle('d-none'));
    }

    function toggleInlineLeaveType(id) {
        const select = document.querySelector(`select[name="status"][form="edit-log-form-${id}"]`);
        const input = document.getElementById(`inline-leave-type-${id}`);
        if (select && input) {
            if (select.value === 'leave') {
                input.classList.remove('d-none');
                input.required = true;
            } else {
                input.classList.add('d-none');
                input.required = false;
            }
        }
    }

    let searchTimeout = null;
    let currentSearchQuery = "";
    let currentPage = 1;
    let isLoading = false;
    let hasMorePages = true;

    function openDropdown() {
        document.getElementById('employeeDropdownList').classList.add('show');
    }

    function closeDropdown() {
        document.getElementById('employeeDropdownList').classList.remove('show');
    }

    function toggleDropdown() {
        const list = document.getElementById('employeeDropdownList');
        if (list.classList.contains('show')) {
            closeDropdown();
        } else {
            openDropdown();
            document.getElementById('employeeSearchInput').focus();
            filterEmployees(false);
        }
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const container = document.getElementById('employeeSearchContainer');
        if (container && !container.contains(event.target)) {
            closeDropdown();
        }
    });

    // Add scroll event listener for infinite scrolling
    document.addEventListener('DOMContentLoaded', function () {
        const dropdownList = document.getElementById('employeeDropdownList');
        if (dropdownList) {
            dropdownList.addEventListener('scroll', function() {
                if (dropdownList.scrollTop + dropdownList.clientHeight >= dropdownList.scrollHeight - 50) {
                    if (hasMorePages && !isLoading) {
                        filterEmployees(true); // true = append next page
                    }
                }
            });
        }
    });

    function debounceFilterEmployees() {
        const inputField = document.getElementById("employeeSearchInput");
        const hiddenField = document.getElementById("employeeEmailHidden");
        hiddenField.value = inputField.value; // Allow raw email submission
        
        openDropdown(); // Ensure it opens when user types

        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            filterEmployees(false); // false = new search, reset pages
        }, 250);
    }

    async function filterEmployees(isAppend = false) {
        const inputField = document.getElementById("employeeSearchInput");
        const filterVal = inputField.value.trim();
        
        if (!isAppend) {
            currentSearchQuery = filterVal;
            currentPage = 1;
            hasMorePages = true;
        }

        if (!hasMorePages || isLoading) return;

        isLoading = true;
        const resultsContainer = document.getElementById("employeeListResults");
        const searchingItem = document.getElementById("searchingItem");
        
        searchingItem.classList.remove('d-none');
        if (!isAppend) {
            resultsContainer.innerHTML = '';
        }
        
        try {
            const response = await fetch(`/manager-agent/api/members?search=${encodeURIComponent(currentSearchQuery)}&page=${currentPage}`);
            const data = await response.json();
            
            searchingItem.classList.add('d-none');
            
            if (!isAppend && data.data.length === 0) {
                resultsContainer.innerHTML = `<li class="px-3 py-2 text-secondary small text-center">No matches found.</li>`;
                hasMorePages = false;
                isLoading = false;
                return;
            }
            
            data.data.forEach(member => {
                const li = document.createElement('li');
                li.className = "px-3 py-2 border-bottom border-secondary-subtle hover-light";
                li.style.cursor = "pointer";
                li.style.transition = "background 0.2s";
                
                li.innerHTML = `
                    <div class="font-bold text-dark employee-name">${escapeHtml(member.name)}</div>
                    <div class="small text-secondary employee-email">${escapeHtml(member.email)}</div>
                `;
                li.onclick = () => selectEmployee(member.email, member.name);
                resultsContainer.appendChild(li);
            });
            
            if (data.current_page >= data.last_page) {
                hasMorePages = false;
            } else {
                currentPage++;
            }
            
        } catch (error) {
            console.error('Error fetching employees:', error);
            searchingItem.classList.add('d-none');
            if (!isAppend) {
                resultsContainer.innerHTML = `<li class="px-3 py-2 text-danger small text-center">Error loading results.</li>`;
            }
        } finally {
            isLoading = false;
        }
    }

    function escapeHtml(unsafe) {
        return (unsafe || '').toString()
             .replace(/&/g, "&amp;")
             .replace(/</g, "&lt;")
             .replace(/>/g, "&gt;")
             .replace(/"/g, "&quot;")
             .replace(/'/g, "&#039;");
    }

    function selectEmployee(email, name) {
        document.getElementById("employeeSearchInput").value = name + ' (' + email + ')';
        document.getElementById("employeeEmailHidden").value = email;
        closeDropdown();
    }
</script>
@endsection
