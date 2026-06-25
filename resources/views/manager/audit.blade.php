@extends('layouts.manager')

@section('title', 'Security & Audit - Manager Agent')
@section('page_title', 'Security & Audit Logs')

@section('content')
<div class="row mb-4 animate-fade-in-up">
    <div class="col-12">
        <div class="card glass-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h4 font-outfit text-dark mb-0 d-flex align-items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="me-2 text-primary" viewBox="0 0 16 16">
                        <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2zM5 8h6a1 1 0 0 1 1 1v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V9a1 1 0 0 1 1-1z"/>
                    </svg>
                    Audit Trail
                </h2>
                <form action="{{ route('manager.audit') }}" method="GET" class="d-flex gap-2">
                    <select name="filter_action" class="form-select form-select-sm bg-white text-dark border-secondary-subtle shadow-sm">
                        <option value="">All Actions</option>
                        <option value="created" {{ request('filter_action') == 'created' ? 'selected' : '' }}>Created</option>
                        <option value="updated" {{ request('filter_action') == 'updated' ? 'selected' : '' }}>Updated</option>
                        <option value="deleted" {{ request('filter_action') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                    </select>
                    <select name="filter_model" class="form-select form-select-sm bg-white text-dark border-secondary-subtle shadow-sm">
                        <option value="">All Models</option>
                        <option value="TeamMember" {{ request('filter_model') == 'TeamMember' ? 'selected' : '' }}>Team Member</option>
                        <option value="Project" {{ request('filter_model') == 'Project' ? 'selected' : '' }}>Project</option>
                        <option value="Task" {{ request('filter_model') == 'Task' ? 'selected' : '' }}>Task</option>
                    </select>
                    <input type="text" name="search" class="form-control form-control-sm bg-white text-dark border-secondary-subtle shadow-sm" placeholder="Search logs..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-sm btn-primary shadow-sm">Filter</button>
                    @if(request()->hasAny(['search', 'filter_action', 'filter_model']))
                        <a href="{{ route('manager.audit') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
                    @endif
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-dark" style="--bs-table-bg: transparent; --bs-table-hover-bg: rgba(255, 255, 255, 0.02); --bs-table-border-color: #334155;">
                    <thead class="text-secondary" style="font-size: 11px;">
                        <tr>
                            <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider">Timestamp</th>
                            <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider">Action</th>
                            <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider">Model</th>
                            <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider">Record ID</th>
                            <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider">Changes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td class="py-3 text-secondary" style="font-size: 13px;">
                                    {{ $log->created_at->format('M d, Y h:i A') }}
                                </td>
                                <td class="py-3">
                                    @if($log->action === 'created')
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">Created</span>
                                    @elseif($log->action === 'updated')
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1">Updated</span>
                                    @elseif($log->action === 'deleted')
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1">Deleted</span>
                                    @endif
                                </td>
                                <td class="py-3 font-semibold text-dark">{{ $log->model_type }}</td>
                                <td class="py-3 text-secondary">#{{ $log->model_id }}</td>
                                <td class="py-3">
                                    <button class="btn btn-xs btn-outline-secondary py-0 px-2 rounded-2" type="button" data-bs-toggle="collapse" data-bs-target="#changes-{{ $log->id }}" aria-expanded="false" style="font-size: 11px;">
                                        View Details
                                    </button>
                                    <div class="collapse mt-2" id="changes-{{ $log->id }}">
                                        <div class="card card-body bg-light border-secondary-subtle text-dark p-2 custom-scroll" style="max-height: 150px; overflow-y: auto; font-family: monospace; font-size: 11px; white-space: pre-wrap;">{{ json_encode($log->changes, JSON_PRETTY_PRINT) }}</div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-secondary italic">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="mb-2 opacity-50" viewBox="0 0 16 16">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                        <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/>
                                    </svg><br>
                                    No audit logs found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4 d-flex justify-content-end">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
