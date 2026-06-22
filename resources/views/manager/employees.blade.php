@extends('layouts.manager')

@section('title', 'Employee Database - Manager Agent')
@section('page_title', 'Employee Database')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">
        <!-- Header Actions Section -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h2 class="h3 font-outfit text-white mb-1">Employee Registry</h2>
                <p class="text-secondary small mb-0">View, search, update, export, and import complete information of all registered employees.</p>
            </div>
            
            <div class="d-flex flex-wrap gap-2">
                <!-- Add Employee Trigger -->
                <button type="button" class="btn btn-primary d-inline-flex align-items-center rounded-3 px-3 py-2" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2" viewBox="0 0 16 16">
                        <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Zm.5-5v1h1a.5.5 0 0 1 0 1h-1v1a.5.5 0 0 1-1 0v-1h-1a.5.5 0 0 1 0-1h1v-1a.5.5 0 0 1 1 0Zm-2-6a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                        <path d="M2 13c0 1 1 1 1 1h5.256A4.493 4.493 0 0 1 8 12.5a4.49 4.49 0 0 1 1.544-3.393C9.077 9.038 8.564 9 8 9c-5 0-6 3-6 4Z"/>
                    </svg>
                    Add Employee
                </button>

                <!-- Export CSV Button -->
                <a href="{{ route('manager.employees.export') }}" class="btn btn-outline-secondary d-inline-flex align-items-center rounded-3 px-3 py-2 text-white border-slate-700 bg-slate-900/40">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2 text-info" viewBox="0 0 16 16">
                        <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                        <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
                    </svg>
                    Export CSV
                </a>

                <!-- Import CSV Trigger -->
                <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center rounded-3 px-3 py-2 text-white border-slate-700 bg-slate-900/40" data-bs-toggle="modal" data-bs-target="#importCSVModal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2 text-warning" viewBox="0 0 16 16">
                        <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                        <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z"/>
                    </svg>
                    Import CSV
                </button>

                <!-- Download CSV Template -->
                <button type="button" onclick="downloadCsvTemplate()" class="btn btn-outline-secondary d-inline-flex align-items-center rounded-3 px-3 py-2 text-white border-slate-700 bg-slate-900/40">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2 text-success" viewBox="0 0 16 16">
                        <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/>
                        <path fill-rule="evenodd" d="M4.5 12.5A.5.5 0 0 1 5 12h3a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5zm0-2A.5.5 0 0 1 5 10h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5zm0-2A.5.5 0 0 1 5 8h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5z"/>
                    </svg>
                    Download Template
                </button>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="card glass-card p-3 mb-4">
            <form method="GET" action="{{ route('manager.employees.index') }}" class="row g-2 align-items-center">
                <div class="col-md-9">
                    <div class="input-group">
                        <span class="input-group-text bg-slate-900 border-slate-700 text-secondary" style="border-right: none;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                            </svg>
                        </span>
                        <input type="text" name="search" class="form-control border-slate-700 bg-slate-900 text-white shadow-none" style="border-left: none;" placeholder="Search employees by name, email, role, or gitlab username..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 py-2 font-semibold">Filter / Search</button>
                    @if(request('search'))
                        <a href="{{ route('manager.employees.index') }}" class="btn btn-outline-secondary text-white border-slate-700 py-2 d-inline-flex align-items-center">Clear</a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Employees Listing Table -->
        <div class="card glass-card p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-white" style="--bs-table-bg: transparent; --bs-table-hover-bg: rgba(255, 255, 255, 0.02); --bs-table-border-color: #334155;">
                    <thead class="text-secondary" style="font-size: 11px;">
                        <tr>
                            <th scope="col" class="pb-3 border-slate-800 uppercase font-semibold tracking-wider">#</th>
                            <th scope="col" class="pb-3 border-slate-800 uppercase font-semibold tracking-wider">Employee Name & Role</th>
                            <th scope="col" class="pb-3 border-slate-800 uppercase font-semibold tracking-wider">Contact Email</th>
                            <th scope="col" class="pb-3 border-slate-800 uppercase font-semibold tracking-wider">GitLab Link</th>
                            <th scope="col" class="pb-3 border-slate-800 uppercase font-semibold tracking-wider">Attendance & Login</th>
                            <th scope="col" class="pb-3 border-slate-800 uppercase font-semibold tracking-wider">Assigned Task</th>
                            <th scope="col" class="pb-3 border-slate-800 uppercase font-semibold tracking-wider">Latest Commit</th>
                            <th scope="col" class="pb-3 border-slate-800 uppercase font-semibold tracking-wider">Meeting Sync</th>
                            <th scope="col" class="pb-3 border-slate-800 uppercase font-semibold tracking-wider text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $employee)
                            <tr>
                                <td class="py-3 text-secondary" style="font-size: 13px;">{{ ($employees->currentPage() - 1) * $employees->perPage() + $loop->iteration }}</td>
                                <td class="py-3">
                                    <div class="font-semibold text-slate-100" style="font-size: 14px;">{{ $employee->name }}</div>
                                    <div class="text-secondary small mt-0.5" style="font-size: 11px;">{{ $employee->role }}</div>
                                </td>
                                <td class="py-3">
                                    <span class="font-mono text-slate-300" style="font-size: 13px;">{{ $employee->email }}</span>
                                </td>
                                <td class="py-3">
                                    @if($employee->gitlab_id)
                                        <a href="https://gitlab.com/{{ $employee->gitlab_id }}" target="_blank" class="text-decoration-none text-purple-400 font-semibold hover-underline d-inline-flex align-items-center" style="font-size: 13px;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                                                <path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.012 8.012 0 0 0 16 8c0-4.42-3.58-8-8-8z"/>
                                            </svg>
                                            {{ $employee->gitlab_id }}
                                        </a>
                                    @else
                                        <span class="text-secondary italic" style="font-size: 12px;">N/A</span>
                                    @endif
                                </td>
                                <td class="py-3">
                                    @if($employee->attendance)
                                        @php
                                            $att = strtolower($employee->attendance);
                                            $badge = 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-20';
                                            if ($att === 'present') $badge = 'bg-success bg-opacity-10 text-success border border-success border-opacity-20';
                                            elseif ($att === 'late') $badge = 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-20';
                                            elseif ($att === 'absent') $badge = 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-20';
                                        @endphp
                                        <span class="badge rounded-pill {{ $badge }} px-2.5 py-1 uppercase" style="font-size: 9px; font-weight: 700;">{{ $employee->attendance }}</span>
                                    @else
                                        <span class="text-secondary italic" style="font-size: 11px;">Not Logged</span>
                                    @endif
                                    @if($employee->login_timing)
                                        <div class="text-secondary small mt-1 font-mono" style="font-size: 10px;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" fill="currentColor" class="me-1 align-middle text-secondary" viewBox="0 0 16 16">
                                                <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/>
                                                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/>
                                            </svg>
                                            <span class="align-middle">{{ $employee->login_timing }}</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3" style="max-width: 180px;">
                                    @if($employee->task_title)
                                        <div class="text-slate-200 font-semibold text-truncate" style="font-size: 13px;" title="{{ $employee->task_title }}">{{ $employee->task_title }}</div>
                                        <div class="text-secondary small mt-0.5" style="font-size: 10px;">
                                            @if($employee->task_assign_date)
                                                Assigned: <span class="text-slate-400">{{ \Carbon\Carbon::parse($employee->task_assign_date)->format('M d') }}</span>
                                            @endif
                                            @if($employee->due_date)
                                                | Due: <span class="text-rose-400 font-medium">{{ \Carbon\Carbon::parse($employee->due_date)->format('M d') }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-secondary italic" style="font-size: 12px;">No Active Task</span>
                                    @endif
                                </td>
                                <td class="py-3" style="max-width: 140px;">
                                    @if($employee->task_commit)
                                        <div class="text-slate-300 text-truncate font-mono" style="font-size: 12px;" title="{{ $employee->task_commit }}">{{ $employee->task_commit }}</div>
                                    @else
                                        <span class="text-secondary italic" style="font-size: 12px;">No Commit</span>
                                    @endif
                                </td>
                                <td class="py-3" style="max-width: 160px;">
                                    @if($employee->meeting_title)
                                        <div class="text-slate-200 font-semibold text-truncate" style="font-size: 13px;" title="{{ $employee->meeting_title }}">{{ $employee->meeting_title }}</div>
                                        @if($employee->meeting_date)
                                            <div class="text-secondary mt-0.5" style="font-size: 10px;">
                                                Date: <span class="text-slate-400">{{ \Carbon\Carbon::parse($employee->meeting_date)->format('M d, Y') }}</span>
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-secondary italic" style="font-size: 12px;">No Sync Logs</span>
                                    @endif
                                </td>
                                <td class="py-3 text-end">
                                    <div class="d-flex justify-content-end gap-1">
                                        <!-- Edit Action -->
                                        <button type="button" class="btn btn-sm btn-outline-info rounded-3 py-1.5 px-2.5 font-medium" onclick="editEmployee({{ json_encode($employee) }})">
                                            Edit
                                        </button>
                                        
                                        <!-- Delete Action -->
                                        <form action="{{ route('manager.employees.destroy', $employee->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to permanently delete this employee?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-3 py-1.5 px-2.5 font-medium">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-secondary italic small">No employees matching search query registered or found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Links -->
            @if($employees->hasPages())
                <div class="mt-4 border-top border-slate-800 pt-3">
                    {{ $employees->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Add Employee Modal -->
<div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-labelledby="addEmployeeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content text-white" style="background-color: #0b0f19; border: 1px solid #334155; border-radius: 20px;">
            <div class="modal-header border-bottom border-slate-800 p-4">
                <h5 class="modal-title font-outfit text-white" id="addEmployeeModalLabel">Add Employee Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('manager.employees.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <!-- Core Details -->
                    <h6 class="text-primary font-outfit uppercase tracking-wider mb-3" style="font-size: 12px; font-weight: 700;">1. Personal & Role Info</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-slate-400 small font-bold text-uppercase">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Rahul Kumar" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-slate-400 small font-bold text-uppercase">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="e.g. rahul@company.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-slate-400 small font-bold text-uppercase">Designated Role <span class="text-danger">*</span></label>
                            <input type="text" name="role" class="form-control" placeholder="e.g. Backend Dev" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-slate-400 small font-bold text-uppercase">GitLab Username</label>
                            <input type="text" name="gitlab_id" class="form-control" placeholder="e.g. rahul-dev">
                        </div>
                    </div>

                    <!-- Work Details -->
                    <h6 class="text-primary font-outfit uppercase tracking-wider mb-3" style="font-size: 12px; font-weight: 700;">2. Attendance & Login Status</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-slate-400 small font-bold text-uppercase">Attendance Status</label>
                            <select name="attendance" class="form-select text-white">
                                <option value="">Select Status</option>
                                <option value="present">Present</option>
                                <option value="late">Late</option>
                                <option value="absent">Absent</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-slate-400 small font-bold text-uppercase">Login / Check-in Timing</label>
                            <input type="text" name="login_timing" class="form-control" placeholder="e.g. 09:00 AM">
                        </div>
                    </div>

                    <!-- Task details -->
                    <h6 class="text-primary font-outfit uppercase tracking-wider mb-3" style="font-size: 12px; font-weight: 700;">3. Assign Task & Git Progress</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label class="form-label text-slate-400 small font-bold text-uppercase">Task Title</label>
                            <input type="text" name="task_title" class="form-control" placeholder="Describe the current workflow item...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-slate-400 small font-bold text-uppercase">Task Assign Date</label>
                            <input type="date" name="task_assign_date" class="form-control text-white">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-slate-400 small font-bold text-uppercase">Task Due Date</label>
                            <input type="date" name="due_date" class="form-control text-white">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label text-slate-400 small font-bold text-uppercase">Latest Git Commit Info</label>
                            <input type="text" name="task_commit" class="form-control" placeholder="e.g. feat: implement auth controllers">
                        </div>
                    </div>

                    <!-- Meeting note association -->
                    <h6 class="text-primary font-outfit uppercase tracking-wider mb-3" style="font-size: 12px; font-weight: 700;">4. Meeting Sync Detail</h6>
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label text-slate-400 small font-bold text-uppercase">Sync Meeting Title</label>
                            <input type="text" name="meeting_title" class="form-control" placeholder="e.g. Morning Sprint Standup">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-slate-400 small font-bold text-uppercase">Meeting Date</label>
                            <input type="date" name="meeting_date" class="form-control text-white">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top border-slate-800 p-4">
                    <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-3 px-4">Save Employee</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Employee Modal -->
<div class="modal fade" id="editEmployeeModal" tabindex="-1" aria-labelledby="editEmployeeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content text-white" style="background-color: #0b0f19; border: 1px solid #334155; border-radius: 20px;">
            <div class="modal-header border-bottom border-slate-800 p-4">
                <h5 class="modal-title font-outfit text-white" id="editEmployeeModalLabel">Edit Employee Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editEmployeeForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <!-- Core Details -->
                    <h6 class="text-primary font-outfit uppercase tracking-wider mb-3" style="font-size: 12px; font-weight: 700;">1. Personal & Role Info</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-slate-400 small font-bold text-uppercase">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-slate-400 small font-bold text-uppercase">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="edit_email" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-slate-400 small font-bold text-uppercase">Designated Role <span class="text-danger">*</span></label>
                            <input type="text" name="role" id="edit_role" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-slate-400 small font-bold text-uppercase">GitLab Username</label>
                            <input type="text" name="gitlab_id" id="edit_gitlab_id" class="form-control">
                        </div>
                    </div>

                    <!-- Work Details -->
                    <h6 class="text-primary font-outfit uppercase tracking-wider mb-3" style="font-size: 12px; font-weight: 700;">2. Attendance & Login Status</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-slate-400 small font-bold text-uppercase">Attendance Status</label>
                            <select name="attendance" id="edit_attendance" class="form-select text-white">
                                <option value="">Select Status</option>
                                <option value="present">Present</option>
                                <option value="late">Late</option>
                                <option value="absent">Absent</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-slate-400 small font-bold text-uppercase">Login / Check-in Timing</label>
                            <input type="text" name="login_timing" id="edit_login_timing" class="form-control">
                        </div>
                    </div>

                    <!-- Task details -->
                    <h6 class="text-primary font-outfit uppercase tracking-wider mb-3" style="font-size: 12px; font-weight: 700;">3. Assign Task & Git Progress</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label class="form-label text-slate-400 small font-bold text-uppercase">Task Title</label>
                            <input type="text" name="task_title" id="edit_task_title" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-slate-400 small font-bold text-uppercase">Task Assign Date</label>
                            <input type="date" name="task_assign_date" id="edit_task_assign_date" class="form-control text-white">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-slate-400 small font-bold text-uppercase">Task Due Date</label>
                            <input type="date" name="due_date" id="edit_due_date" class="form-control text-white">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label text-slate-400 small font-bold text-uppercase">Latest Git Commit Info</label>
                            <input type="text" name="task_commit" id="edit_task_commit" class="form-control">
                        </div>
                    </div>

                    <!-- Meeting note association -->
                    <h6 class="text-primary font-outfit uppercase tracking-wider mb-3" style="font-size: 12px; font-weight: 700;">4. Meeting Sync Detail</h6>
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label text-slate-400 small font-bold text-uppercase">Sync Meeting Title</label>
                            <input type="text" name="meeting_title" id="edit_meeting_title" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-slate-400 small font-bold text-uppercase">Meeting Date</label>
                            <input type="date" name="meeting_date" id="edit_meeting_date" class="form-control text-white">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top border-slate-800 p-4">
                    <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-3 px-4">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Import CSV Modal -->
<div class="modal fade" id="importCSVModal" tabindex="-1" aria-labelledby="importCSVModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-white" style="background-color: #0b0f19; border: 1px solid #334155; border-radius: 20px;">
            <div class="modal-header border-bottom border-slate-800 p-4">
                <h5 class="modal-title font-outfit text-white" id="importCSVModalLabel">Bulk Import Employees</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('manager.employees.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 text-white rounded-3 small p-3 mb-4" style="background-color: rgba(99, 102, 241, 0.15); border-left: 4px solid #6366f1 !important;">
                        <strong>Note:</strong> Columns in the CSV file must match the following format exactly:
                        <div class="mt-2 font-mono text-slate-300" style="font-size: 10px; word-break: break-all;">
                            Name, Email, Role, GitLab ID, Task Title, Task Commit, Attendance, Meeting Date, Meeting Title, Task Assign Date, Due Date, Login Timing
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

@section('scripts')
<script>
    // Edit Employee JavaScript helper
    function editEmployee(employee) {
        const form = document.getElementById('editEmployeeForm');
        form.action = `{{ url('/manager-agent/employees') }}/${employee.id}`;
        
        document.getElementById('edit_name').value = employee.name || '';
        document.getElementById('edit_email').value = employee.email || '';
        document.getElementById('edit_role').value = employee.role || '';
        document.getElementById('edit_gitlab_id').value = employee.gitlab_id || '';
        document.getElementById('edit_attendance').value = employee.attendance || '';
        document.getElementById('edit_login_timing').value = employee.login_timing || '';
        document.getElementById('edit_task_title').value = employee.task_title || '';
        document.getElementById('edit_task_assign_date').value = employee.task_assign_date || '';
        document.getElementById('edit_due_date').value = employee.due_date || '';
        document.getElementById('edit_task_commit').value = employee.task_commit || '';
        document.getElementById('edit_meeting_title').value = employee.meeting_title || '';
        document.getElementById('edit_meeting_date').value = employee.meeting_date || '';

        const editModal = new bootstrap.Modal(document.getElementById('editEmployeeModal'));
        editModal.show();
    }

    // Download CSV template
    function downloadCsvTemplate() {
        const headers = [
            'Name', 'Email', 'Role', 'GitLab ID', 'Task Title', 
            'Task Commit', 'Attendance', 'Meeting Date', 'Meeting Title', 
            'Task Assign Date', 'Due Date', 'Login Timing'
        ];
        const sampleRow = [
            'Rohan Sharma', 'rohan@company.com', 'Mobile Developer', 'rohan-coder', 'Implement push notifications',
            'feat: push token registrations', 'present', '2026-06-16', 'Daily Mobile Sync', '2026-06-15', '2026-06-20', '09:00 AM'
        ];
        
        // Use standard CSV format with CRLF line breaks and safe quotes
        const csvContent = headers.join(',') + '\r\n' + sampleRow.map(val => {
            // Escape double quotes and wrap in quotes if contains commas
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
        link.setAttribute("download", "employees_import_template.csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>
@endsection
