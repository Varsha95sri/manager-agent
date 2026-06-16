@extends('layouts.manager')

@section('title', 'Daily Attendance - Manager Agent')
@section('page_title', 'Daily Attendance Management')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        
        <!-- Page Header & Date Selector & CSV Actions -->
        <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-center gap-3 mb-4">
            <div>
                <h2 class="h3 font-outfit text-white mb-1">Daily Attendance Tracker</h2>
                <p class="text-secondary small mb-0">Record and review daily attendance logs to track developer availability and punctuality.</p>
            </div>
            
            <div class="d-flex flex-wrap align-items-center gap-3">
                <div>
                    <form method="GET" action="{{ route('manager.attendance-registry') }}" class="d-flex align-items-center gap-2">
                        <label class="text-slate-400 small font-semibold text-uppercase tracking-wider shrink-0 mb-0">Select Date:</label>
                        <input 
                            type="date" 
                            name="date" 
                            class="form-control border-slate-700 bg-slate-900 text-white rounded-3 py-1.5 px-3" 
                            value="{{ $date }}" 
                            onchange="this.form.submit()"
                            style="color-scheme: dark; width: 160px;"
                        >
                    </form>
                </div>

                <div class="d-flex gap-2">
                    <!-- Export CSV Button -->
                    <a href="{{ route('manager.attendance.export') }}" class="btn btn-outline-secondary d-inline-flex align-items-center rounded-3 px-3 py-2 text-white border-slate-700 bg-slate-900/40">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2 text-info" viewBox="0 0 16 16">
                            <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                            <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
                        </svg>
                        Export CSV
                    </a>

                    <!-- Import CSV Trigger -->
                    <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center rounded-3 px-3 py-2 text-white border-slate-700 bg-slate-900/40" data-bs-toggle="modal" data-bs-target="#importAttendanceCSVModal">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2 text-warning" viewBox="0 0 16 16">
                            <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                            <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3-3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z"/>
                        </svg>
                        Import CSV
                    </button>

                    <!-- Download CSV Template -->
                    <button type="button" onclick="downloadAttendanceCsvTemplate()" class="btn btn-outline-secondary d-inline-flex align-items-center rounded-3 px-3 py-2 text-white border-slate-700 bg-slate-900/40">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2 text-success" viewBox="0 0 16 16">
                            <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/>
                            <path fill-rule="evenodd" d="M4.5 12.5A.5.5 0 0 1 5 12h3a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5zm0-2A.5.5 0 0 1 5 10h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5zm0-2A.5.5 0 0 1 5 8h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5z"/>
                        </svg>
                        Download Template
                    </button>
                </div>
            </div>
        </div>

        <!-- Attendance Stats Counters -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-md-3">
                <div class="card glass-card p-3 border border-slate-800 text-center">
                    <span class="text-secondary small font-bold text-uppercase tracking-wider">Total Members</span>
                    <h3 class="h2 font-outfit text-white mt-1 mb-0">{{ $teamMembers->count() }}</h3>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="card glass-card p-3 border border-slate-800 text-center" style="border-left: 4px solid #10b981 !important;">
                    <span class="text-emerald-400 small font-bold text-uppercase tracking-wider">Present</span>
                    <h3 class="h2 font-outfit text-emerald-400 mt-1 mb-0">{{ $totalPresent }}</h3>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="card glass-card p-3 border border-slate-800 text-center" style="border-left: 4px solid #f59e0b !important;">
                    <span class="text-warning small font-bold text-uppercase tracking-wider">Late</span>
                    <h3 class="h2 font-outfit text-warning mt-1 mb-0">{{ $totalLate }}</h3>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="card glass-card p-3 border border-slate-800 text-center" style="border-left: 4px solid #f43f5e !important;">
                    <span class="text-rose-400 small font-bold text-uppercase tracking-wider">Absent</span>
                    <h3 class="h2 font-outfit text-rose-400 mt-1 mb-0">{{ $totalAbsent }}</h3>
                </div>
            </div>
        </div>

        <!-- Main Workspace -->
        <div class="row g-4">
            
            <!-- Left Column: Add/Log Attendance Form -->
            <div class="col-lg-4 col-12">
                <div class="card glass-card p-4 border border-slate-800">
                    <h4 class="h5 font-outfit text-white mb-3">Log Attendance Log</h4>
                    <form method="POST" action="{{ route('manager.store-attendance') }}">
                        @csrf
                        
                        <!-- Keep selected date context -->
                        <input type="hidden" name="date" value="{{ $date }}">
                        
                        <div class="mb-3">
                            <label class="form-label text-slate-400 small font-bold text-uppercase tracking-wider">Select Employee</label>
                            <select name="team_member_id" class="form-select border-slate-700 bg-slate-900 text-white rounded-3 px-3 py-2.5 @error('team_member_id') is-invalid @enderror" required>
                                <option value="" disabled selected>Choose a developer...</option>
                                @foreach($teamMembers as $m)
                                    <!-- Only select if they do not have a log already logged for this date -->
                                    <option value="{{ $m->id }}" {{ old('team_member_id') == $m->id ? 'selected' : '' }}>
                                        {{ $m->name }} ({{ $m->role }}) {{ $logsMap->has($m->id) ? '✓ Logged' : '⚠ Missing' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('team_member_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-slate-400 small font-bold text-uppercase tracking-wider">Status</label>
                            <select name="status" id="form-status-select" class="form-select border-slate-700 bg-slate-900 text-white rounded-3 px-3 py-2.5 @error('status') is-invalid @enderror" required onchange="toggleFormCheckIn()">
                                <option value="present" {{ old('status', 'present') === 'present' ? 'selected' : '' }}>Present</option>
                                <option value="late" {{ old('status') === 'late' ? 'selected' : '' }}>Late</option>
                                <option value="absent" {{ old('status') === 'absent' ? 'selected' : '' }}>Absent</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4" id="form-check-in-group">
                            <label class="form-label text-slate-400 small font-bold text-uppercase tracking-wider">Check-in Time</label>
                            <input type="time" name="check_in" class="form-control border-slate-700 bg-slate-900 text-white rounded-3 px-3 py-2.5 @error('check_in') is-invalid @enderror" value="{{ old('check_in', '09:00') }}">
                            @error('check_in')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn accent-btn w-100 py-2.5">Record Attendance</button>
                    </form>
                </div>
            </div>

            <!-- Right Column: Employee List & Log Table -->
            <div class="col-lg-8 col-12">
                <div class="card glass-card p-4 border border-slate-800 shadow-2xl">
                    <h4 class="h5 font-outfit text-white mb-3">Logs Registry for {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</h4>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-white" style="--bs-table-bg: transparent; --bs-table-hover-bg: rgba(255, 255, 255, 0.02); --bs-table-border-color: #334155;">
                            <thead class="text-secondary" style="font-size: 11px;">
                                <tr>
                                    <th scope="col" class="pb-3 border-slate-800 uppercase font-semibold tracking-wider">Developer</th>
                                    <th scope="col" class="pb-3 border-slate-800 uppercase font-semibold tracking-wider">Role</th>
                                    <th scope="col" class="pb-3 border-slate-800 uppercase font-semibold tracking-wider text-center">Status</th>
                                    <th scope="col" class="pb-3 border-slate-800 uppercase font-semibold tracking-wider text-center">Check-in</th>
                                    <th scope="col" class="pb-3 border-slate-800 uppercase font-semibold tracking-wider text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($teamMembers as $member)
                                    @php
                                        $log = $logsMap->get($member->id);
                                    @endphp
                                    <tr id="attendance-row-{{ $member->id }}">
                                        <!-- Developer Name -->
                                        <td class="py-3">
                                            <span class="font-semibold text-slate-100">{{ $member->name }}</span>
                                        </td>
                                        
                                        <!-- Role -->
                                        <td class="py-3 text-slate-300">
                                            <span>{{ $member->role }}</span>
                                        </td>
                                        
                                        <!-- Attendance Status -->
                                        <td class="py-3 text-center">
                                            @if($log)
                                                <!-- View Mode -->
                                                <span class="view-mode-{{ $member->id }}">
                                                    @if($log->status === 'present')
                                                        <span class="badge rounded-pill bg-success bg-opacity-10 text-success border border-success border-opacity-20 px-2.5 py-1" style="font-size: 10px;">Present</span>
                                                    @elseif($log->status === 'late')
                                                        <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning border border-warning border-opacity-20 px-2.5 py-1" style="font-size: 10px;">Late</span>
                                                    @else
                                                        <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger border border-danger border-opacity-20 px-2.5 py-1" style="font-size: 10px;">Absent</span>
                                                    @endif
                                                </span>
                                                
                                                <!-- Edit Mode Form Elements -->
                                                <div class="edit-mode-{{ $member->id }} d-none">
                                                    <select name="status" form="update-form-{{ $log->id }}" class="form-select form-select-sm border-slate-700 bg-slate-900 text-white rounded-2" style="font-size: 11px; min-width: 100px;" onchange="toggleEditCheckIn({{ $member->id }}, this.value)">
                                                        <option value="present" {{ $log->status === 'present' ? 'selected' : '' }}>Present</option>
                                                        <option value="late" {{ $log->status === 'late' ? 'selected' : '' }}>Late</option>
                                                        <option value="absent" {{ $log->status === 'absent' ? 'selected' : '' }}>Absent</option>
                                                    </select>
                                                </div>
                                            @else
                                                <!-- View Mode: No Log -->
                                                <span class="view-mode-{{ $member->id }}">
                                                    <span class="badge rounded-pill bg-secondary bg-opacity-10 text-slate-400 border border-slate-700 px-2.5 py-1" style="font-size: 10px;">No Record</span>
                                                </span>
                                                
                                                <!-- Log Mode Form Elements -->
                                                <div class="log-mode-{{ $member->id }} d-none">
                                                    <select name="status" form="store-form-{{ $member->id }}" class="form-select form-select-sm border-slate-700 bg-slate-900 text-white rounded-2" style="font-size: 11px; min-width: 100px;" onchange="toggleLogCheckIn({{ $member->id }}, this.value)">
                                                        <option value="present" selected>Present</option>
                                                        <option value="late">Late</option>
                                                        <option value="absent">Absent</option>
                                                    </select>
                                                </div>
                                            @endif
                                        </td>
                                        
                                        <!-- Check-in Time -->
                                        <td class="py-3 text-center text-slate-400">
                                            @if($log)
                                                <!-- View Mode -->
                                                <span class="view-mode-{{ $member->id }}">
                                                    {{ $log->check_in ? \Carbon\Carbon::parse($log->check_in)->format('h:i A') : '—' }}
                                                </span>
                                                
                                                <!-- Edit Mode Form Elements -->
                                                <div class="edit-mode-{{ $member->id }} d-none" id="edit-check-in-wrapper-{{ $member->id }}">
                                                    <input 
                                                        type="time" 
                                                        name="check_in" 
                                                        form="update-form-{{ $log->id }}" 
                                                        value="{{ $log->check_in ? \Carbon\Carbon::parse($log->check_in)->format('H:i') : '' }}" 
                                                        class="form-control form-control-sm border-slate-700 bg-slate-900 text-white rounded-2 text-center" 
                                                        style="font-size: 11px; max-width: 90px; margin: 0 auto;"
                                                        {{ $log->status === 'absent' ? 'disabled' : '' }}
                                                    >
                                                </div>
                                            @else
                                                <!-- View Mode: No Log -->
                                                <span class="view-mode-{{ $member->id }}">—</span>
                                                
                                                <!-- Log Mode Form Elements -->
                                                <div class="log-mode-{{ $member->id }} d-none" id="log-check-in-wrapper-{{ $member->id }}">
                                                    <input 
                                                        type="time" 
                                                        name="check_in" 
                                                        form="store-form-{{ $member->id }}" 
                                                        value="09:00" 
                                                        class="form-control form-control-sm border-slate-700 bg-slate-900 text-white rounded-2 text-center" 
                                                        style="font-size: 11px; max-width: 90px; margin: 0 auto;"
                                                    >
                                                </div>
                                            @endif
                                        </td>
                                        
                                        <!-- Actions -->
                                        <td class="py-3 text-end">
                                            @if($log)
                                                <!-- Update Forms -->
                                                <form id="update-form-{{ $log->id }}" action="{{ route('manager.update-attendance', $log->id) }}" method="POST" class="d-none">
                                                    @csrf
                                                    @method('PUT')
                                                </form>
                                                
                                                <!-- View Mode Buttons -->
                                                <div class="view-mode-{{ $member->id }}">
                                                    <button type="button" class="btn btn-xs btn-outline-info" onclick="toggleEditMode({{ $member->id }}, true)">Edit</button>
                                                    
                                                    <form action="{{ route('manager.destroy-attendance', $log->id) }}" method="POST" class="d-inline ms-1" onsubmit="return confirm('Are you sure you want to delete this attendance log?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-xs btn-outline-danger">Delete</button>
                                                    </form>
                                                </div>
                                                
                                                <!-- Edit Mode Buttons -->
                                                <div class="edit-mode-{{ $member->id }} d-none">
                                                    <button type="submit" form="update-form-{{ $log->id }}" class="btn btn-xs btn-success">Save</button>
                                                    <button type="button" class="btn btn-xs btn-outline-secondary ms-1" onclick="toggleEditMode({{ $member->id }}, false)">Cancel</button>
                                                </div>
                                            @else
                                                <!-- Store Forms -->
                                                <form id="store-form-{{ $member->id }}" action="{{ route('manager.store-attendance') }}" method="POST" class="d-none">
                                                    @csrf
                                                    <input type="hidden" name="date" value="{{ $date }}">
                                                    <input type="hidden" name="team_member_id" value="{{ $member->id }}">
                                                </form>
                                                
                                                <!-- View Mode Buttons -->
                                                <div class="view-mode-{{ $member->id }}">
                                                    <button type="button" class="btn btn-xs btn-outline-primary" onclick="toggleLogMode({{ $member->id }}, true)">Log Status</button>
                                                </div>
                                                
                                                <!-- Log Mode Buttons -->
                                                <div class="log-mode-{{ $member->id }} d-none">
                                                    <button type="submit" form="store-form-{{ $member->id }}" class="btn btn-xs btn-success">Save</button>
                                                    <button type="button" class="btn btn-xs btn-outline-secondary ms-1" onclick="toggleLogMode({{ $member->id }}, false)">Cancel</button>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-secondary italic small">No registered developers found in system.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
    // Toggle check-in time visibility based on status in standard add form
    function toggleFormCheckIn() {
        const status = document.getElementById('form-status-select').value;
        const group = document.getElementById('form-check-in-group');
        const input = group.querySelector('input[name="check_in"]');
        if (status === 'absent') {
            input.disabled = true;
            group.style.opacity = '0.5';
        } else {
            input.disabled = false;
            group.style.opacity = '1';
        }
    }

    // Toggle inline update edit mode
    function toggleEditMode(memberId, enable) {
        const row = document.getElementById(`attendance-row-${memberId}`);
        if (!row) return;
        
        const views = row.querySelectorAll(`.view-mode-${memberId}`);
        const edits = row.querySelectorAll(`.edit-mode-${memberId}`);
        
        if (enable) {
            views.forEach(el => el.classList.add('d-none'));
            edits.forEach(el => el.classList.remove('d-none'));
        } else {
            views.forEach(el => el.classList.remove('d-none'));
            edits.forEach(el => el.classList.add('d-none'));
        }
    }

    // Toggle inline store log mode
    function toggleLogMode(memberId, enable) {
        const row = document.getElementById(`attendance-row-${memberId}`);
        if (!row) return;
        
        const views = row.querySelectorAll(`.view-mode-${memberId}`);
        const logs = row.querySelectorAll(`.log-mode-${memberId}`);
        
        if (enable) {
            views.forEach(el => el.classList.add('d-none'));
            logs.forEach(el => el.classList.remove('d-none'));
        } else {
            views.forEach(el => el.classList.remove('d-none'));
            logs.forEach(el => el.classList.add('d-none'));
        }
    }

    // Toggle check-in input disabled state during inline edit
    function toggleEditCheckIn(memberId, status) {
        const wrapper = document.getElementById(`edit-check-in-wrapper-${memberId}`);
        if (!wrapper) return;
        const input = wrapper.querySelector('input[name="check_in"]');
        if (input) {
            input.disabled = (status === 'absent');
        }
    }

    // Toggle check-in input disabled state during inline log
    function toggleLogCheckIn(memberId, status) {
        const wrapper = document.getElementById(`log-check-in-wrapper-${memberId}`);
        if (!wrapper) return;
        const input = wrapper.querySelector('input[name="check_in"]');
        if (input) {
            input.disabled = (status === 'absent');
        }
    }

    // Run on startup
    document.addEventListener('DOMContentLoaded', function() {
        toggleFormCheckIn();
    });

    // Download Attendance CSV template
    function downloadAttendanceCsvTemplate() {
        const headers = ['Employee Email', 'Date', 'Status', 'Check-in Time'];
        const sampleRow = ['test@example.com', '2026-06-16', 'present', '09:00:00'];
        
        const csvContent = headers.join(',') + '\r\n' + sampleRow.map(val => {
            let cleanVal = val ? val.toString().replace(/"/g, '""') : '';
            if (cleanVal.search(/("|,|\n)/g) >= 0) {
                cleanVal = `"${cleanVal}"`;
            }
            return cleanVal;
        }).join(',');
        
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const encodedUri = URL.createObjectURL(blob);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "attendance_import_template.csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>

<!-- Import Attendance CSV Modal -->
<div class="modal fade" id="importAttendanceCSVModal" tabindex="-1" aria-labelledby="importAttendanceCSVModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-white" style="background-color: #0b0f19; border: 1px solid #334155; border-radius: 20px;">
            <div class="modal-header border-bottom border-slate-800 p-4">
                <h5 class="modal-title font-outfit text-white" id="importAttendanceCSVModalLabel">Bulk Import Attendance</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('manager.attendance.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 text-white rounded-3 small p-3 mb-4" style="background-color: rgba(99, 102, 241, 0.15); border-left: 4px solid #6366f1 !important;">
                        <strong>Note:</strong> Columns in the CSV file must match the following format exactly:
                        <div class="mt-2 font-mono text-slate-300" style="font-size: 10px; word-break: break-all;">
                            Employee Email, Date, Status, Check-in Time
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-slate-400 small font-bold text-uppercase">Select CSV File <span class="text-danger">*</span></label>
                        <input type="file" name="file" class="form-control" accept=".csv" required>
                    </div>
                </div>
                <div class="modal-footer border-top border-slate-800 p-4">
                    <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning rounded-3 text-dark px-4 font-bold">Upload & Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
