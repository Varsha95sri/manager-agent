@extends('layouts.manager')

@section('title', 'My Tasks')
@section('page_title', 'My Tasks')

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success premium-shadow rounded-3">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="card kpi-card premium-shadow mb-4">
        <div class="card-header bg-transparent border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">Assigned Tasks</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Task</th>
                            <th>Priority</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tasks as $task)
                        <tr>
                            <td>
                                <strong class="text-dark d-block">{{ $task->title }}</strong>
                                <small class="text-muted">{{ Str::limit($task->description, 50) }}</small>
                            </td>
                            <td>
                                @if($task->priority == 'High')
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle">High</span>
                                @elseif($task->priority == 'Medium')
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning-subtle">Medium</span>
                                @else
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info-subtle">Low</span>
                                @endif
                            </td>
                            <td>
                                {{ \Carbon\Carbon::parse($task->due_date)->format('M d, Y') }}
                                @if($task->due_date < now() && $task->status != 'Completed' && $task->status != 'completed')
                                    <br><span class="badge bg-danger text-white" style="font-size: 0.65rem;">Overdue</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusClass = 'secondary';
                                    $statusText = ucfirst(str_replace('_', ' ', $task->status));
                                    if(strtolower($task->status) == 'completed') $statusClass = 'success';
                                    if(strtolower($task->status) == 'in_progress' || strtolower($task->status) == 'in progress') $statusClass = 'primary';
                                    if(strtolower($task->status) == 'pending') $statusClass = 'warning';
                                @endphp
                                <span class="badge bg-{{ $statusClass }} bg-opacity-10 text-{{ $statusClass }} px-2 py-1 border border-{{ $statusClass }}-subtle">
                                    {{ $statusText }}
                                </span>
                            </td>
                            <td class="text-end">
                                <form action="{{ route('user.tasks.update', $task->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                                        <option value="pending" {{ strtolower($task->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="in_progress" {{ strtolower($task->status) == 'in_progress' || strtolower($task->status) == 'in progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="completed" {{ strtolower($task->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No tasks assigned yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
