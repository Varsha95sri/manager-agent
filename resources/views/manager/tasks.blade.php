@extends('layouts.manager')

@section('title', 'Task Management - Manager Agent')
@section('page_title', 'Task Management Module')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-12">
        
        <!-- Header Actions Section -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h2 class="h3 font-outfit text-dark mb-1">Task Management Dashboard</h2>
                <p class="text-secondary small mb-0">Track task lifecycles, monitor effort, and view team productivity.</p>
            </div>
            
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <!-- Date Picker UI -->
                <div class="card bg-white shadow-sm border-secondary-subtle p-1 rounded-3">
                    <form action="{{ route('manager.task-entry') }}" method="GET" class="d-flex gap-2 align-items-center m-0" id="filterForm">
                        <div class="d-flex align-items-center bg-light rounded-2 px-2 border border-secondary-subtle">
                            <span class="text-secondary small me-2"><i class="bi bi-calendar-range"></i></span>
                            <input type="date" name="start_date" id="startDate" class="form-control form-control-sm border-0 bg-transparent text-dark shadow-none px-1" value="{{ $startDate ?? \Carbon\Carbon::today()->startOfMonth()->toDateString() }}" required style="width: 120px;">
                            <span class="text-secondary mx-1">-</span>
                            <input type="date" name="end_date" id="endDate" class="form-control form-control-sm border-0 bg-transparent text-dark shadow-none px-1" value="{{ $endDate ?? \Carbon\Carbon::today()->endOfMonth()->toDateString() }}" required style="width: 120px;">
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

                <!-- Import CSV Button -->
                <button type="button" class="btn btn-outline-info d-inline-flex align-items-center rounded-3 px-3 py-2" data-bs-toggle="modal" data-bs-target="#importTasksModal">
                    <i class="bi bi-upload me-2"></i> Import CSV
                </button>
                <!-- Export CSV Button -->
                <a href="{{ route('manager.tasks.export') }}" class="btn btn-outline-success d-inline-flex align-items-center rounded-3 px-3 py-2">
                    <i class="bi bi-download me-2"></i> Export CSV
                </a>
                <!-- Add Task Button -->
                <button type="button" class="btn accent-btn d-inline-flex align-items-center rounded-3 px-3 py-2 text-dark shadow-lg" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2 text-dark" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                    </svg>
                    Create New Task
                </button>
            </div>
        </div>

        <!-- KPI Metrics Row -->
        <div class="row g-3 mb-4">
            <div class="col-md-2 col-sm-4">
                <div class="card bg-white shadow-sm border-secondary-subtle shadow-sm h-100 p-3">
                    <div class="text-secondary small font-semibold text-uppercase tracking-wider mb-1">Total Assigned</div>
                    <div class="h3 text-dark mb-0">{{ $totalTasks ?? 0 }}</div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4">
                <div class="card bg-white shadow-sm border-secondary-subtle shadow-sm h-100 p-3">
                    <div class="text-secondary small font-semibold text-uppercase tracking-wider mb-1">Completed</div>
                    <div class="h3 text-success mb-0">{{ $completedTasks ?? 0 }}</div>
                </div>
            </div>
            <div class="col-md-2 col-sm-4">
                <div class="card bg-white shadow-sm border-secondary-subtle shadow-sm h-100 p-3">
                    <div class="text-secondary small font-semibold text-uppercase tracking-wider mb-1">Delayed/Overdue</div>
                    <div class="h3 text-danger mb-0">{{ $delayedTasks ?? 0 }}</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card bg-white shadow-sm border-secondary-subtle shadow-sm h-100 p-3">
                    <div class="text-secondary small font-semibold text-uppercase tracking-wider mb-1">Avg Completion</div>
                    <div class="h3 text-info mb-0">{{ $avgCompletionHours ?? 0 }} <span class="fs-6 text-secondary font-normal">hrs</span></div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card bg-white shadow-sm border-secondary-subtle shadow-sm h-100 p-3" style="border-left: 4px solid #6366f1 !important;">
                    <div class="text-secondary small font-semibold text-uppercase tracking-wider mb-1">Productivity Score</div>
                    <div class="d-flex align-items-end">
                        <div class="h3 text-warning mb-0">{{ $productivityScore ?? 0 }}</div>
                        <span class="fs-6 text-secondary ms-1 mb-1 font-normal">/ 100</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Tasks Table -->
            <div class="col-12">
                <div class="card glass-card p-4 border border-secondary-subtle shadow-2xl">
                    <h4 class="h5 font-outfit text-dark mb-3">Allocated Tasks Registry</h4>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-dark" style="--bs-table-bg: transparent; --bs-table-hover-bg: rgba(255, 255, 255, 0.02); --bs-table-border-color: #334155;">
                            <thead class="text-secondary" style="font-size: 11px;">
                                <tr>
                                    <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider">#</th>
                                    <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider">Task Details</th>
                                    <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider">Assigned Developer</th>
                                    <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider">Status & Priority</th>
                                    <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider">Timeline & Effort</th>
                                    <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tasks as $task)
                                    <tr id="task-row-{{ $task->id }}" class="{{ $task->is_overdue ? 'bg-danger bg-opacity-10' : '' }}">
                                        <td class="py-3 text-secondary">{{ $loop->iteration + ($tasks->currentPage() - 1) * $tasks->perPage() }}</td>
                                        <td class="py-3" style="max-width: 250px;">
                                            <div class="view-mode font-semibold text-dark text-truncate" title="{{ $task->title }}">
                                                {{ $task->title }}
                                                @if($task->dependency_id)
                                                    <div class="text-info small" style="font-size: 10px;"><i class="bi bi-link-45deg"></i> Dep: #{{ $task->dependency_id }}</div>
                                                @endif
                                            </div>
                                            <div class="edit-mode d-none">
                                                <input type="text" name="title" form="edit-task-form-{{ $task->id }}" value="{{ $task->title }}" class="form-control form-control-sm border-secondary-subtle bg-white shadow-sm text-dark mb-1" required>
                                                <input type="number" name="dependency_id" form="edit-task-form-{{ $task->id }}" value="{{ $task->dependency_id }}" class="form-control form-control-sm border-secondary-subtle bg-white shadow-sm text-dark" placeholder="Dependency ID (optional)">
                                            </div>
                                        </td>
                                        <td class="py-3 text-secondary">
                                            <div class="view-mode d-flex flex-wrap gap-1">
                                                @if($task->teamMembers->isNotEmpty())
                                                    @foreach($task->teamMembers as $m)
                                                        <span class="badge bg-primary text-white border border-primary rounded-pill px-2 py-1 shadow-sm font-outfit" style="font-size: 11px;">
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
                                                <input type="text" name="email" form="edit-task-form-{{ $task->id }}" value="{{ $emailsList }}" class="form-control form-control-sm border-secondary-subtle bg-white shadow-sm text-dark" placeholder="email1, email2..." required>
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            <div class="view-mode">
                                                @if($task->status === 'completed')
                                                    <span class="badge rounded-pill bg-success bg-opacity-10 text-success border border-success border-opacity-20 px-2.5 py-1 mb-1 d-inline-block" style="font-size: 10px;">Completed</span>
                                                @elseif($task->status === 'in_progress')
                                                    <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning border border-warning border-opacity-20 px-2.5 py-1 mb-1 d-inline-block" style="font-size: 10px;">In Progress</span>
                                                @else
                                                    <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger border border-danger border-opacity-20 px-2.5 py-1 mb-1 d-inline-block" style="font-size: 10px;">Pending</span>
                                                @endif
                                                <br>
                                                <span class="badge rounded bg-light border border-secondary-subtle text-secondary" style="font-size: 9px;">Priority: {{ ucfirst($task->priority ?? 'Medium') }}</span>
                                            </div>
                                            <div class="edit-mode d-none">
                                                <select name="status" form="edit-task-form-{{ $task->id }}" class="form-select form-select-sm border-secondary-subtle bg-white shadow-sm text-dark mb-1" required>
                                                    <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                    <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                                </select>
                                                <select name="priority" form="edit-task-form-{{ $task->id }}" class="form-select form-select-sm border-secondary-subtle bg-white shadow-sm text-dark">
                                                    <option value="low" {{ $task->priority == 'low' ? 'selected' : '' }}>Low</option>
                                                    <option value="medium" {{ $task->priority == 'medium' ? 'selected' : '' }}>Medium</option>
                                                    <option value="high" {{ $task->priority == 'high' ? 'selected' : '' }}>High</option>
                                                    <option value="critical" {{ $task->priority == 'critical' ? 'selected' : '' }}>Critical</option>
                                                </select>
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            <div class="view-mode">
                                                <div class="text-dark small">
                                                    Due: <span class="{{ $task->is_overdue ? 'text-danger font-bold' : '' }}">{{ \Carbon\Carbon::parse($task->due_date)->format('M d, Y') }}</span>
                                                    @if($task->is_overdue)
                                                        <i class="bi bi-exclamation-circle text-danger ms-1" title="Overdue!"></i>
                                                    @endif
                                                </div>
                                                <div class="text-secondary" style="font-size: 10px;">
                                                    Effort: {{ $task->effort_estimation ?? 0 }}h | Actual: {{ $task->actual_time ?? 0 }}h
                                                </div>
                                            </div>
                                            <div class="edit-mode d-none">
                                                <input type="date" name="due_date" form="edit-task-form-{{ $task->id }}" value="{{ \Carbon\Carbon::parse($task->due_date)->format('Y-m-d') }}" class="form-control form-control-sm border-secondary-subtle bg-white shadow-sm text-dark mb-1" required>
                                                <div class="d-flex gap-1">
                                                    <input type="number" step="0.5" name="effort_estimation" form="edit-task-form-{{ $task->id }}" value="{{ $task->effort_estimation }}" class="form-control form-control-sm border-secondary-subtle bg-white shadow-sm text-dark" placeholder="Est (h)">
                                                    <input type="number" step="0.5" name="actual_time" form="edit-task-form-{{ $task->id }}" value="{{ $task->actual_time }}" class="form-control form-control-sm border-secondary-subtle bg-white shadow-sm text-dark" placeholder="Act (h)">
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3 text-end">
                                            <form id="edit-task-form-{{ $task->id }}" action="{{ route('manager.update-task', $task->id) }}" method="POST" class="d-none">
                                                @csrf
                                                @method('PUT')
                                            </form>
                                            <div class="d-inline-flex flex-column align-items-end gap-2">
                                                <div class="d-flex justify-content-end gap-1">
                                                    @if($task->status !== 'completed')
                                                        <!-- Close Task Button -->
                                                        <button type="button" class="btn btn-xs btn-success view-mode" onclick="closeTask({{ $task->id }})" title="Quick Close">Close</button>
                                                    @endif
                                                    <button type="button" class="btn btn-xs btn-outline-info view-mode" onclick="toggleEditMode({{ $task->id }}, 'task')">Edit</button>
                                                    
                                                    <form action="{{ route('manager.destroy-task', $task->id) }}" method="POST" class="d-inline view-mode" onsubmit="return confirm('Are you sure you want to delete this task?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-xs btn-outline-danger">Delete</button>
                                                    </form>
                                                    
                                                    <button type="submit" form="edit-task-form-{{ $task->id }}" class="btn btn-xs btn-success edit-mode d-none">Save</button>
                                                    <button type="button" class="btn btn-xs btn-outline-secondary edit-mode d-none" onclick="toggleEditMode({{ $task->id }}, 'task')">Cancel</button>
                                                </div>
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
                        <div class="mt-4 border-top border-secondary-subtle pt-4 d-flex justify-content-center">
                            {!! $tasks->links() !!}
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Add Task Modal -->
<div class="modal fade" id="addTaskModal" tabindex="-1" aria-labelledby="addTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content text-dark" style="background-color: #ffffff; border: 1px solid var(--border-color); border-radius: 20px;">
            <div class="modal-header border-bottom border-secondary-subtle p-4">
                <h5 class="modal-title font-outfit text-dark" id="addTaskModalLabel">Create New Task</h5>
                <button type="button" class="btn-close btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('manager.store-task') }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label text-secondary small font-bold text-uppercase tracking-wider">Task Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control border-secondary-subtle bg-white shadow-sm text-dark rounded-3 px-3 py-2.5 @error('title') is-invalid @enderror" placeholder="e.g. Implement Task Overdue Logic" value="{{ old('title') }}" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label text-secondary small font-bold text-uppercase tracking-wider">Assignee Developer(s) <span class="text-danger">*</span></label>
                            <div class="dropdown">
                                <button class="btn border-secondary-subtle bg-white shadow-sm text-dark w-100 text-start d-flex justify-content-between align-items-center rounded-3 px-3 py-2.5" type="button" id="assigneeDropdown" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                                    <span>Select Developer(s)...</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
                                    </svg>
                                </button>
                                <ul class="dropdown-menu w-100 bg-white shadow-sm border-secondary-subtle shadow-xl custom-scroll" aria-labelledby="assigneeDropdown" style="max-height: 220px; overflow-y: auto;">
                                    @foreach($allTeamMembers as $member)
                                        <li class="px-3 py-1.5 border-bottom border-secondary-subtle hover-light">
                                            <div class="form-check m-0">
                                                <input class="form-check-input" type="checkbox" name="team_member_ids[]" value="{{ $member->id }}" id="chk-dropdown-{{ $member->id }}" {{ is_array(old('team_member_ids')) && in_array($member->id, old('team_member_ids')) ? 'checked' : '' }}>
                                                <label class="form-check-label text-dark small ms-2 w-100" for="chk-dropdown-{{ $member->id }}" style="cursor: pointer;">
                                                    <strong>{{ $member->name }}</strong> <span class="text-secondary">({{ $member->role }})</span>
                                                </label>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-secondary small font-bold text-uppercase tracking-wider">Due Date <span class="text-danger">*</span></label>
                            <input type="date" name="due_date" class="form-control border-secondary-subtle bg-white shadow-sm text-dark rounded-3 px-3 py-2.5 @error('due_date') is-invalid @enderror" value="{{ old('due_date', date('Y-m-d')) }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label text-secondary small font-bold text-uppercase tracking-wider">Status</label>
                            <select name="status" class="form-select border-secondary-subtle bg-white shadow-sm text-dark rounded-3 px-3 py-2.5" required>
                                <option value="pending" selected>Pending</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label text-secondary small font-bold text-uppercase tracking-wider">Priority</label>
                            <select name="priority" class="form-select border-secondary-subtle bg-white shadow-sm text-dark rounded-3 px-3 py-2.5">
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label text-secondary small font-bold text-uppercase tracking-wider">Effort (Hours)</label>
                            <input type="number" step="0.5" min="0" name="effort_estimation" class="form-control border-secondary-subtle bg-white shadow-sm text-dark rounded-3 px-3 py-2.5" placeholder="e.g. 5.5">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label text-secondary small font-bold text-uppercase tracking-wider">Dependency ID (Optional)</label>
                            <input type="number" name="dependency_id" class="form-control border-secondary-subtle bg-white shadow-sm text-dark rounded-3 px-3 py-2.5" placeholder="Enter Task # if dependent on another task">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top border-secondary-subtle p-4">
                    <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn accent-btn rounded-3 px-4 font-bold">Create Task</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Import Tasks Modal -->
<div class="modal fade" id="importTasksModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-white shadow-sm border-secondary-subtle">
            <div class="modal-header border-secondary-subtle">
                <h5 class="modal-title font-outfit text-dark">Import Tasks from CSV</h5>
                <button type="button" class="btn-close btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('manager.tasks.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body text-secondary">
                    <p class="small text-secondary mb-4">
                        Upload a CSV file containing your tasks. Required columns: <code>Employee Email</code>, <code>Task Title</code>, <code>Status</code>, <code>Due Date</code>.
                    </p>
                    <div class="mb-3">
                        <label for="csv_file" class="form-label">CSV File</label>
                        <input class="form-control bg-light border-secondary-subtle text-dark" type="file" id="csv_file" name="file" accept=".csv" required>
                    </div>
                </div>
                <div class="modal-footer border-secondary-subtle">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn accent-btn text-white">Import Tasks</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Automatically open Add Task Modal if there are validation errors
    document.addEventListener('DOMContentLoaded', function () {
        @if($errors->any())
            const addModal = new bootstrap.Modal(document.getElementById('addTaskModal'));
            addModal.show();
        @endif
    });

    // Handle Quick Date Selection
    function setQuickDate(start, end) {
        document.getElementById('startDate').value = start;
        document.getElementById('endDate').value = end;
        document.getElementById('filterForm').submit();
    }

    // Toggle view/edit mode for inline table fields
    function toggleEditMode(rowId, type) {
        const row = document.getElementById(`${type}-row-${rowId}`);
        if (!row) return;
        row.querySelectorAll('.view-mode').forEach(el => el.classList.toggle('d-none'));
        row.querySelectorAll('.edit-mode').forEach(el => el.classList.toggle('d-none'));
    }

    // Quick Close Task function
    function closeTask(taskId) {
        if (!confirm('Are you sure you want to close this task?')) return;
        
        let form = document.getElementById(`edit-task-form-${taskId}`);
        
        // Find the status select in edit mode and set to completed
        let statusSelect = form.parentElement.parentElement.querySelector(`select[name="status"][form="edit-task-form-${taskId}"]`);
        if (statusSelect) {
            statusSelect.value = 'completed';
        }
        
        // Submit form
        form.submit();
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
    .hover-light:hover {
        background-color: rgba(255, 255, 255, 0.06);
    }
</style>
@endsection
