@extends('layouts.manager')

@section('title', 'Departments & Skills - Manager Agent')
@section('page_title', 'Departments & Skills')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h2 class="h3 font-outfit text-dark mb-1">Departments & Skills Master</h2>
                <p class="text-secondary small mb-0">Manage the list of departments and skills available for employees.</p>
            </div>
        </div>

        <div class="row">
            <!-- Departments Column -->
            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                    <div class="card-header bg-white border-bottom border-secondary-subtle py-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 text-dark font-outfit fw-bold">Departments</h6>
                        <button class="btn btn-sm btn-primary rounded-3" data-bs-toggle="modal" data-bs-target="#addDeptModal">Add Department</button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light text-secondary small">
                                    <tr>
                                        <th class="ps-4">Name</th>
                                        <th>Description</th>
                                        <th class="text-end pe-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($departments as $dept)
                                        <tr>
                                            <td class="ps-4 text-dark fw-medium">{{ $dept->name }}</td>
                                            <td class="text-muted small">{{ $dept->description ?: 'N/A' }}</td>
                                            <td class="text-end pe-4">
                                                <button class="btn btn-sm btn-light text-primary py-0 px-2" data-bs-toggle="modal" data-bs-target="#editDeptModal{{ $dept->id }}"><i class="bi bi-pencil"></i></button>
                                                <form action="{{ route('manager.departments.destroy', $dept->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this department?')">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-sm btn-light text-danger py-0 px-2"><i class="bi bi-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>

                                        <!-- Edit Dept Modal -->
                                        <div class="modal fade" id="editDeptModal{{ $dept->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <form action="{{ route('manager.departments.update', $dept->id) }}" method="POST" class="modal-content rounded-4 border-0 shadow">
                                                    @csrf @method('PUT')
                                                    <div class="modal-header border-secondary-subtle">
                                                        <h5 class="modal-title font-outfit">Edit Department</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label small text-secondary">Name</label>
                                                            <input type="text" name="name" class="form-control" value="{{ $dept->name }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label small text-secondary">Description</label>
                                                            <textarea name="description" class="form-control" rows="2">{{ $dept->description }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-secondary-subtle">
                                                        <button type="submit" class="btn btn-primary rounded-3">Save Changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @empty
                                        <tr><td colspan="3" class="text-center text-muted py-4">No departments found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Skills Column -->
            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                    <div class="card-header bg-white border-bottom border-secondary-subtle py-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 text-dark font-outfit fw-bold">Skills</h6>
                        <button class="btn btn-sm btn-primary rounded-3" data-bs-toggle="modal" data-bs-target="#addSkillModal">Add Skill</button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light text-secondary small">
                                    <tr>
                                        <th class="ps-4">Name</th>
                                        <th>Category</th>
                                        <th class="text-end pe-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($skills as $skill)
                                        <tr>
                                            <td class="ps-4 text-dark fw-medium">{{ $skill->name }}</td>
                                            <td><span class="badge bg-light text-secondary border">{{ $skill->category ?: 'General' }}</span></td>
                                            <td class="text-end pe-4">
                                                <button class="btn btn-sm btn-light text-primary py-0 px-2" data-bs-toggle="modal" data-bs-target="#editSkillModal{{ $skill->id }}"><i class="bi bi-pencil"></i></button>
                                                <form action="{{ route('manager.skills.destroy', $skill->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this skill?')">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-sm btn-light text-danger py-0 px-2"><i class="bi bi-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>

                                        <!-- Edit Skill Modal -->
                                        <div class="modal fade" id="editSkillModal{{ $skill->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <form action="{{ route('manager.skills.update', $skill->id) }}" method="POST" class="modal-content rounded-4 border-0 shadow">
                                                    @csrf @method('PUT')
                                                    <div class="modal-header border-secondary-subtle">
                                                        <h5 class="modal-title font-outfit">Edit Skill</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label small text-secondary">Name</label>
                                                            <input type="text" name="name" class="form-control" value="{{ $skill->name }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label small text-secondary">Category</label>
                                                            <input type="text" name="category" class="form-control" value="{{ $skill->category }}">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-secondary-subtle">
                                                        <button type="submit" class="btn btn-primary rounded-3">Save Changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @empty
                                        <tr><td colspan="3" class="text-center text-muted py-4">No skills found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Dept Modal -->
<div class="modal fade" id="addDeptModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('manager.departments.store') }}" method="POST" class="modal-content rounded-4 border-0 shadow">
            @csrf
            <div class="modal-header border-secondary-subtle">
                <h5 class="modal-title font-outfit">Add Department</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small text-secondary">Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small text-secondary">Description</label>
                    <textarea name="description" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer border-secondary-subtle">
                <button type="submit" class="btn btn-primary rounded-3">Add Department</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Skill Modal -->
<div class="modal fade" id="addSkillModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('manager.skills.store') }}" method="POST" class="modal-content rounded-4 border-0 shadow">
            @csrf
            <div class="modal-header border-secondary-subtle">
                <h5 class="modal-title font-outfit">Add Skill</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small text-secondary">Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small text-secondary">Category</label>
                    <input type="text" name="category" class="form-control">
                </div>
            </div>
            <div class="modal-footer border-secondary-subtle">
                <button type="submit" class="btn btn-primary rounded-3">Add Skill</button>
            </div>
        </form>
    </div>
</div>
@endsection
