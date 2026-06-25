@extends('layouts.manager')

@section('title', 'Projects Dashboard - Manager Agent')
@section('page_title', 'Projects Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="font-outfit text-dark mb-1">Project Portfolio</h3>
        <p class="text-secondary mb-0">Manage all your active, planned, and completed projects.</p>
    </div>
    <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#createProjectModal">
        <i class="fas fa-plus me-2"></i> New Project
    </button>
</div>

<div class="card glass-card border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3 text-secondary font-semibold border-bottom-0">Project Name</th>
                        <th class="py-3 text-secondary font-semibold border-bottom-0">Category</th>
                        <th class="py-3 text-secondary font-semibold border-bottom-0">Status</th>
                        <th class="py-3 text-secondary font-semibold border-bottom-0">Progress</th>
                        <th class="py-3 text-secondary font-semibold border-bottom-0">Health</th>
                        <th class="py-3 text-secondary font-semibold border-bottom-0">Deadline</th>
                        <th class="px-4 py-3 text-end text-secondary font-semibold border-bottom-0">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $project)
                        <tr>
                            <td class="px-4 py-3 border-secondary-subtle">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-primary bg-opacity-10 rounded p-2 text-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 text-dark">{{ $project->name }}</h6>
                                        <span class="text-secondary small">{{ Str::limit($project->description, 80) }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 border-secondary-subtle">
                                <span class="badge bg-light text-dark border">{{ $project->category ?? 'Uncategorized' }}</span>
                            </td>
                            <td class="py-3 border-secondary-subtle">
                                @php
                                    $statusColors = [
                                        'planning' => 'secondary',
                                        'active' => 'primary',
                                        'on_hold' => 'warning',
                                        'completed' => 'success',
                                        'archived' => 'dark'
                                    ];
                                    $color = $statusColors[$project->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $color }} bg-opacity-10 text-{{ $color }} border border-{{ $color }} border-opacity-25">
                                    {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                </span>
                            </td>
                            <td class="py-3 border-secondary-subtle" style="width: 15%;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1 bg-light" style="height: 6px;">
                                        <div class="progress-bar bg-{{ $color }}" role="progressbar" style="width: {{ $project->progress_percent }}%;"></div>
                                    </div>
                                    <span class="small font-semibold">{{ $project->progress_percent }}%</span>
                                </div>
                            </td>
                            <td class="py-3 border-secondary-subtle">
                                <div class="d-flex align-items-center gap-2">
                                    @if($project->health_score >= 80)
                                        <span class="text-success"><i class="fas fa-heartbeat"></i></span>
                                        <span class="text-dark font-semibold">{{ $project->health_score }}</span>
                                    @elseif($project->health_score >= 50)
                                        <span class="text-warning"><i class="fas fa-heartbeat"></i></span>
                                        <span class="text-dark font-semibold">{{ $project->health_score }}</span>
                                    @else
                                        <span class="text-danger"><i class="fas fa-heartbeat"></i></span>
                                        <span class="text-dark font-semibold">{{ $project->health_score }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 border-secondary-subtle">
                                @if($project->deadline)
                                    <span class="{{ \Carbon\Carbon::parse($project->deadline)->isPast() && $project->status != 'completed' ? 'text-danger font-semibold' : 'text-dark' }}">
                                        {{ \Carbon\Carbon::parse($project->deadline)->format('M d, Y') }}
                                    </span>
                                @else
                                    <span class="text-secondary small">No Deadline</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-end border-secondary-subtle">
                                <button class="btn btn-sm btn-light text-primary border me-1 hover-card" data-bs-toggle="modal" data-bs-target="#editProjectModal{{ $project->id }}" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('manager.projects.destroy', $project->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this project?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light text-danger border hover-card" title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Edit Project Modal -->
                        <div class="modal fade" id="editProjectModal{{ $project->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow-lg rounded-4">
                                    <div class="modal-header border-bottom-0 pb-0">
                                        <h5 class="modal-title font-outfit font-bold">Edit Project</h5>
                                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('manager.projects.update', $project->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            
                                            <div class="mb-3">
                                                <label class="form-label small">Project Name <span class="text-danger">*</span></label>
                                                <input type="text" name="name" class="form-control" value="{{ $project->name }}" required>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label small">Category</label>
                                                <input type="text" name="category" class="form-control" value="{{ $project->category }}" placeholder="e.g. Internal, Client A, Mobile App">
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label small">Status</label>
                                                    <select name="status" class="form-select">
                                                        <option value="planning" {{ $project->status == 'planning' ? 'selected' : '' }}>Planning</option>
                                                        <option value="active" {{ $project->status == 'active' ? 'selected' : '' }}>Active</option>
                                                        <option value="on_hold" {{ $project->status == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                                                        <option value="completed" {{ $project->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                                        <option value="archived" {{ $project->status == 'archived' ? 'selected' : '' }}>Archived</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small">Progress (%)</label>
                                                    <input type="number" name="progress_percent" class="form-control" min="0" max="100" value="{{ $project->progress_percent }}">
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label small">Deadline</label>
                                                <input type="date" name="deadline" class="form-control" value="{{ $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('Y-m-d') : '' }}">
                                            </div>

                                            <div class="mb-4">
                                                <label class="form-label small">Description</label>
                                                <textarea name="description" class="form-control" rows="3">{{ $project->description }}</textarea>
                                            </div>

                                            <div class="d-flex justify-content-end gap-2 mt-4">
                                                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary rounded-pill px-4">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-secondary">
                                <div class="mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1" class="text-muted opacity-50">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                </div>
                                <h6 class="font-outfit mb-1">No Projects Found</h6>
                                <p class="small mb-0">Create your first project to start tracking.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Project Modal -->
<div class="modal fade" id="createProjectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title font-outfit font-bold">Create New Project</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('manager.projects.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label small">Project Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g. Website Redesign">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small">Category</label>
                        <input type="text" name="category" class="form-control" placeholder="e.g. Internal, Client A, Mobile App">
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label small">Status</label>
                            <select name="status" class="form-select">
                                <option value="planning" selected>Planning</option>
                                <option value="active">Active</option>
                                <option value="on_hold">On Hold</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Deadline</label>
                            <input type="date" name="deadline" class="form-control">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Brief overview of the project goals..."></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Create Project</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
