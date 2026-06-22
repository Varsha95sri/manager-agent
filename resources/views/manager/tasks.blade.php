@extends('layouts.manager')

@section('title', 'Daily Tasks - Manager Agent')
@section('page_title', 'Daily Tasks Management')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        
        <!-- Header Actions Section -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h2 class="h3 font-outfit text-white mb-1">Daily Task Logger</h2>
                <p class="text-secondary small mb-0">Add, edit, or remove daily tasks to evaluate performance statistics and manage workloads.</p>
            </div>
            
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <!-- Add Daily Task Button -->
                <button type="button" class="btn accent-btn d-inline-flex align-items-center rounded-3 px-3 py-2 text-white" data-bs-toggle="modal" data-bs-target="#addDailyTaskModal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2 text-white" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                    </svg>
                    Add Daily Task
                </button>

                <!-- Export CSV Button -->
                <a href="{{ route('manager.tasks.export') }}" class="btn btn-outline-secondary d-inline-flex align-items-center rounded-3 px-3 py-2 text-white border-slate-700 bg-slate-900/40">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2 text-info" viewBox="0 0 16 16">
                        <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                        <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
                    </svg>
                    Export CSV
                </a>

                <!-- Import CSV Trigger -->
                <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center rounded-3 px-3 py-2 text-white border-slate-700 bg-slate-900/40" data-bs-toggle="modal" data-bs-target="#importTasksCSVModal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2 text-warning" viewBox="0 0 16 16">
                        <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                        <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3-3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z"/>
                    </svg>
                    Import CSV
                </button>

                <!-- Download CSV Template -->
                <button type="button" onclick="downloadTasksCsvTemplate()" class="btn btn-outline-secondary d-inline-flex align-items-center rounded-3 px-3 py-2 text-white border-slate-700 bg-slate-900/40">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2 text-success" viewBox="0 0 16 16">
                        <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/>
                        <path fill-rule="evenodd" d="M4.5 12.5A.5.5 0 0 1 5 12h3a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5zm0-2A.5.5 0 0 1 5 10h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5zm0-2A.5.5 0 0 1 5 8h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5z"/>
                    </svg>
                    Download Template
                </button>
            </div>
        </div>

        <div class="row g-4">
            <!-- Tasks Table -->
            <div class="col-12">
                <div class="card glass-card p-4 border border-slate-800 shadow-2xl">
                    <h4 class="h5 font-outfit text-white mb-3">Allocated Tasks Registry</h4>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-white" style="--bs-table-bg: transparent; --bs-table-hover-bg: rgba(255, 255, 255, 0.02); --bs-table-border-color: #334155;">
                            <thead class="text-secondary" style="font-size: 11px;">
                                <tr>
                                    <th scope="col" class="pb-3 border-slate-800 uppercase font-semibold tracking-wider">#</th>
                                    <th scope="col" class="pb-3 border-slate-800 uppercase font-semibold tracking-wider">Task Title</th>
                                    <th scope="col" class="pb-3 border-slate-800 uppercase font-semibold tracking-wider">Assigned Developer</th>
                                    <th scope="col" class="pb-3 border-slate-800 uppercase font-semibold tracking-wider">Status</th>
                                    <th scope="col" class="pb-3 border-slate-800 uppercase font-semibold tracking-wider">Due Date</th>
                                    <th scope="col" class="pb-3 border-slate-800 uppercase font-semibold tracking-wider text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tasks as $task)
                                    <tr id="task-row-{{ $task->id }}">
                                        <td class="py-3 text-secondary">{{ $loop->iteration + ($tasks->currentPage() - 1) * $tasks->perPage() }}</td>
                                        <td class="py-3">
                                            <span class="view-mode font-semibold text-slate-100">{{ $task->title }}</span>
                                            <input type="text" name="title" form="edit-task-form-{{ $task->id }}" value="{{ $task->title }}" class="form-control form-control-sm edit-mode d-none" required>
                                        </td>
                                        <td class="py-3 text-slate-300">
                                            <div class="view-mode d-flex flex-wrap gap-1">
                                                @if($task->teamMembers->isNotEmpty())
                                                    @foreach($task->teamMembers as $m)
                                                        <span class="badge bg-slate-800 text-info border border-slate-700 px-2 py-1 font-outfit" style="font-size: 10px;">
                                                            {{ $m->name }}
                                                        </span>
                                                    @endforeach
                                                @else
                                                    <span class="text-secondary italic">{{ $task->teamMember?->name ?? 'Unassigned' }}</span>
                                                @endif
                                            </div>
                                            <div class="edit-mode d-none" style="min-width: 140px;">
                                                @php
                                                    $emailsList = $task->teamMembers->pluck('email')->join(', ') ?: ($task->teamMember?->email ?? '');
                                                @endphp
                                                <input type="text" name="email" form="edit-task-form-{{ $task->id }}" value="{{ $emailsList }}" class="form-control form-control-sm border-slate-700 bg-slate-900 text-white" placeholder="email1, email2..." required>
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            <span class="view-mode">
                                                @if($task->status === 'completed')
                                                    <span class="badge rounded-pill bg-success bg-opacity-10 text-success border border-success border-opacity-20 px-2.5 py-1" style="font-size: 10px;">Completed</span>
                                                @elseif($task->status === 'in_progress')
                                                    <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning border border-warning border-opacity-20 px-2.5 py-1" style="font-size: 10px;">In Progress</span>
                                                @else
                                                    <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger border border-danger border-opacity-20 px-2.5 py-1" style="font-size: 10px;">Pending</span>
                                                @endif
                                            </span>
                                            <select name="status" form="edit-task-form-{{ $task->id }}" class="form-select form-select-sm edit-mode d-none" required>
                                                <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                            </select>
                                        </td>
                                        <td class="py-3 text-slate-400">
                                            <span class="view-mode">{{ \Carbon\Carbon::parse($task->due_date)->format('M d, Y') }}</span>
                                            <input type="date" name="due_date" form="edit-task-form-{{ $task->id }}" value="{{ \Carbon\Carbon::parse($task->due_date)->format('Y-m-d') }}" class="form-control form-control-sm edit-mode d-none" required>
                                        </td>
                                        <td class="py-3 text-end">
                                            <form id="edit-task-form-{{ $task->id }}" action="{{ route('manager.update-task', $task->id) }}" method="POST" class="d-none">
                                                @csrf
                                                @method('PUT')
                                            </form>
                                            <div class="d-inline-flex justify-content-end gap-1">
                                                <button type="button" class="btn btn-xs btn-outline-info view-mode" onclick="toggleEditMode({{ $task->id }}, 'task')">Edit</button>
                                                
                                                <form action="{{ route('manager.destroy-task', $task->id) }}" method="POST" class="d-inline view-mode" onsubmit="return confirm('Are you sure you want to delete this task?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-xs btn-outline-danger">Delete</button>
                                                </form>
                                                
                                                <button type="submit" form="edit-task-form-{{ $task->id }}" class="btn btn-xs btn-success edit-mode d-none">Save</button>
                                                <button type="button" class="btn btn-xs btn-outline-secondary edit-mode d-none" onclick="toggleEditMode({{ $task->id }}, 'task')">Cancel</button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-secondary italic small">No tasks logged in system.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination Links -->
                    @if($tasks->hasPages())
                        <div class="mt-4 border-top border-slate-800 pt-4 d-flex justify-content-center">
                            {!! $tasks->links() !!}
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Import Tasks CSV Modal -->
<div class="modal fade" id="importTasksCSVModal" tabindex="-1" aria-labelledby="importTasksCSVModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-white" style="background-color: #0b0f19; border: 1px solid #334155; border-radius: 20px;">
            <div class="modal-header border-bottom border-slate-800 p-4">
                <h5 class="modal-title font-outfit text-white" id="importTasksCSVModalLabel">Bulk Import Tasks</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('manager.tasks.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 text-white rounded-3 small p-3 mb-4" style="background-color: rgba(99, 102, 241, 0.15); border-left: 4px solid #6366f1 !important;">
                        <strong>Note:</strong> Columns in the CSV file must match the following format exactly:
                        <div class="mt-2 font-mono text-slate-300" style="font-size: 10px; word-break: break-all;">
                            Employee Email, Task Title, Status, Due Date
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

<!-- Add Daily Task Modal -->
<div class="modal fade" id="addDailyTaskModal" tabindex="-1" aria-labelledby="addDailyTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-white" style="background-color: #0b0f19; border: 1px solid #334155; border-radius: 20px;">
            <div class="modal-header border-bottom border-slate-800 p-4">
                <h5 class="modal-title font-outfit text-white" id="addDailyTaskModalLabel">Add Daily Task</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('manager.store-task') }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-slate-400 small font-bold text-uppercase tracking-wider">Assignee Developer(s)</label>
                        <div class="dropdown">
                            <button class="btn border-slate-700 bg-slate-900 text-white w-100 text-start d-flex justify-content-between align-items-center rounded-3 px-3 py-2.5" type="button" id="assigneeDropdown" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                                <span>Select Developer(s)...</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
                                </svg>
                            </button>
                            <ul class="dropdown-menu w-100 bg-slate-900 border-slate-700 shadow-xl custom-scroll" aria-labelledby="assigneeDropdown" style="max-height: 220px; overflow-y: auto;">
                                @foreach($allTeamMembers as $member)
                                    <li class="px-3 py-1.5 border-bottom border-slate-800 hover-slate-800">
                                        <div class="form-check m-0">
                                            <input class="form-check-input" type="checkbox" name="team_member_ids[]" value="{{ $member->id }}" id="chk-dropdown-{{ $member->id }}" {{ is_array(old('team_member_ids')) && in_array($member->id, old('team_member_ids')) ? 'checked' : '' }}>
                                            <label class="form-check-label text-white small ms-2 w-100" for="chk-dropdown-{{ $member->id }}" style="cursor: pointer;">
                                                <strong>{{ $member->name }}</strong> <span class="text-secondary">({{ $member->role }})</span>
                                            </label>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        @error('team_member_ids')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        @error('team_member_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-slate-400 small font-bold text-uppercase tracking-wider">Task Title / Activity</label>
                        <input type="text" name="title" class="form-control border-slate-700 bg-slate-900 text-white rounded-3 px-3 py-2.5 @error('title') is-invalid @enderror" placeholder="e.g. Debug Oauth callback views" value="{{ old('title') }}" required>
                        @error('title')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-slate-400 small font-bold text-uppercase tracking-wider">Status</label>
                        <select name="status" class="form-select border-slate-700 bg-slate-900 text-white rounded-3 px-3 py-2.5 @error('status') is-invalid @enderror" required>
                            <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in_progress" {{ old('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-slate-400 small font-bold text-uppercase tracking-wider">Due Date</label>
                        <input type="date" name="due_date" class="form-control border-slate-700 bg-slate-900 text-white rounded-3 px-3 py-2.5 @error('due_date') is-invalid @enderror" value="{{ old('due_date', date('Y-m-d')) }}" required>
                        @error('due_date')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer border-top border-slate-800 p-4">
                    <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn accent-btn rounded-3 px-4 font-bold">Log Task Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Automatically open Add Daily Task Modal if there are validation errors
    document.addEventListener('DOMContentLoaded', function () {
        @if($errors->has('team_member_ids') || $errors->has('team_member_id') || $errors->has('title') || $errors->has('status') || $errors->has('due_date'))
            const addModal = new bootstrap.Modal(document.getElementById('addDailyTaskModal'));
            addModal.show();
        @endif
    });

    // Toggle view/edit mode for inline table fields
    function toggleEditMode(rowId, type) {
        const row = document.getElementById(`${type}-row-${rowId}`);
        if (!row) return;
        row.querySelectorAll('.view-mode').forEach(el => el.classList.toggle('d-none'));
        row.querySelectorAll('.edit-mode').forEach(el => el.classList.toggle('d-none'));
    }

    // Download Tasks CSV template
    function downloadTasksCsvTemplate() {
        const headers = ['Employee Email', 'Task Title', 'Status', 'Due Date'];
        const sampleRow = ['test@example.com', 'Build layout templates', 'in_progress', '2026-06-20'];
        
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
        link.setAttribute("download", "tasks_import_template.csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>
@endsection

@section('styles')
<style>
    .developer-select-item {
        border: 1px solid #1e293b !important;
        transition: all 0.2s;
    }
    .developer-select-item:hover {
        border-color: rgba(99, 102, 241, 0.4) !important;
        background-color: rgba(99, 102, 241, 0.04) !important;
    }
    .developer-select-item:has(input:checked) {
        border-color: #6366f1 !important;
        background-color: rgba(99, 102, 241, 0.1) !important;
        box-shadow: 0 0 8px rgba(99, 102, 241, 0.15);
    }
    .hover-slate-800:hover {
        background-color: rgba(255, 255, 255, 0.06);
    }
</style>
@endsection
