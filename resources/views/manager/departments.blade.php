
@extends('layouts.manager')

@section('title', 'Master Data - Manager Agent')
@section('page_title', 'Master Data (Depts, Skills, Designations)')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h2 class="h3 font-outfit text-slate-900 mb-1">Master Data Management</h2>
                <p class="text-secondary small mb-0">Manage departments, skills, and designations.</p>
            </div>
        </div>

        <div class="row">
            <!-- Departments Column -->
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                    <div class="card-header bg-white border-bottom border-secondary-subtle py-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 text-slate-900 font-outfit fw-bold">Departments</h6>
                        <button class="btn btn-sm btn-primary rounded-3" data-bs-toggle="modal" data-bs-target="#addDeptModal">Add</button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light text-secondary small">
                                    <tr>
                                        <th class="ps-4">Name</th>
                                        <th class="text-end pe-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($departments as $dept)
                                        <tr>
                                            <td class="ps-4 text-slate-900 fw-medium">{{ $dept->name }}</td>
                                            <td class="text-end pe-4">
                                                <button class="btn btn-sm btn-light text-primary py-0 px-2" data-bs-toggle="modal" data-bs-target="#editDeptModal{{ $dept->id }}"><i class="fa-solid fa-pen"></i></button>
                                                <form action="{{ route('manager.departments.destroy', $dept->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this department?')">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-sm btn-light text-danger py-0 px-2"><i class="fa-solid fa-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="2" class="text-center text-muted py-4">No departments.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Skills Column -->
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                    <div class="card-header bg-white border-bottom border-secondary-subtle py-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 text-slate-900 font-outfit fw-bold">Skills</h6>
                        <button class="btn btn-sm btn-primary rounded-3" data-bs-toggle="modal" data-bs-target="#addSkillModal">Add</button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light text-secondary small">
                                    <tr>
                                        <th class="ps-4">Name</th>
                                        <th class="text-end pe-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($skills as $skill)
                                        <tr>
                                            <td class="ps-4 text-slate-900 fw-medium">{{ $skill->name }}</td>
                                            <td class="text-end pe-4">
                                                <button class="btn btn-sm btn-light text-primary py-0 px-2" data-bs-toggle="modal" data-bs-target="#editSkillModal{{ $skill->id }}"><i class="fa-solid fa-pen"></i></button>
                                                <form action="{{ route('manager.skills.destroy', $skill->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this skill?')">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-sm btn-light text-danger py-0 px-2"><i class="fa-solid fa-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="2" class="text-center text-muted py-4">No skills.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Designations Column -->
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                    <div class="card-header bg-white border-bottom border-secondary-subtle py-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 text-slate-900 font-outfit fw-bold">Designations</h6>
                        <button class="btn btn-sm btn-primary rounded-3" data-bs-toggle="modal" data-bs-target="#addDesigModal">Add</button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light text-secondary small">
                                    <tr>
                                        <th class="ps-4">Name (Level)</th>
                                        <th class="text-end pe-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($designations as $desig)
                                        <tr>
                                            <td class="ps-4 text-slate-900 fw-medium">{{ $desig->name }} <span class="badge bg-light text-secondary">{{ $desig->level }}</span></td>
                                            <td class="text-end pe-4">
                                                <button class="btn btn-sm btn-light text-primary py-0 px-2" data-bs-toggle="modal" data-bs-target="#editDesigModal{{ $desig->id }}"><i class="fa-solid fa-pen"></i></button>
                                                <form action="{{ route('manager.designations.destroy', $desig->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this designation?')">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-sm btn-light text-danger py-0 px-2"><i class="fa-solid fa-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="2" class="text-center text-muted py-4">No designations.</td></tr>
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
            <div class="modal-header">
                <h5 class="modal-title font-outfit">Add Department</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer"><button type="submit" class="btn btn-primary">Save</button></div>
        </form>
    </div>
</div>

<!-- Add Skill Modal -->
<div class="modal fade" id="addSkillModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('manager.skills.store') }}" method="POST" class="modal-content rounded-4 border-0 shadow">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title font-outfit">Add Skill</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <input type="text" name="category" class="form-control">
                </div>
            </div>
            <div class="modal-footer"><button type="submit" class="btn btn-primary">Save</button></div>
        </form>
    </div>
</div>

<!-- Add Desig Modal -->
<div class="modal fade" id="addDesigModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('manager.designations.store') }}" method="POST" class="modal-content rounded-4 border-0 shadow">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title font-outfit">Add Designation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Level (Optional)</label>
                    <input type="text" name="level" class="form-control">
                </div>
            </div>
            <div class="modal-footer"><button type="submit" class="btn btn-primary">Save</button></div>
        </form>
    </div>
</div>

@foreach($departments as $dept)
<!-- Edit Dept Modal -->
<div class="modal fade" id="editDeptModal{{ $dept->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('manager.departments.update', $dept->id) }}" method="POST" class="modal-content rounded-4 border-0 shadow">
            @csrf @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title font-outfit">Edit Department</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3"><label class="form-label">Name</label><input type="text" name="name" class="form-control" value="{{ $dept->name }}" required></div>
            </div>
            <div class="modal-footer"><button type="submit" class="btn btn-primary">Save Changes</button></div>
        </form>
    </div>
</div>
@endforeach

@foreach($skills as $skill)
<!-- Edit Skill Modal -->
<div class="modal fade" id="editSkillModal{{ $skill->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('manager.skills.update', $skill->id) }}" method="POST" class="modal-content rounded-4 border-0 shadow">
            @csrf @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title font-outfit">Edit Skill</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3"><label class="form-label">Name</label><input type="text" name="name" class="form-control" value="{{ $skill->name }}" required></div>
                <div class="mb-3"><label class="form-label">Category</label><input type="text" name="category" class="form-control" value="{{ $skill->category }}"></div>
            </div>
            <div class="modal-footer"><button type="submit" class="btn btn-primary">Save Changes</button></div>
        </form>
    </div>
</div>
@endforeach

@foreach($designations as $desig)
<!-- Edit Desig Modal -->
<div class="modal fade" id="editDesigModal{{ $desig->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('manager.designations.update', $desig->id) }}" method="POST" class="modal-content rounded-4 border-0 shadow">
            @csrf @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title font-outfit">Edit Designation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3"><label class="form-label">Name</label><input type="text" name="name" class="form-control" value="{{ $desig->name }}" required></div>
                <div class="mb-3"><label class="form-label">Level</label><input type="text" name="level" class="form-control" value="{{ $desig->level }}"></div>
            </div>
            <div class="modal-footer"><button type="submit" class="btn btn-primary">Save Changes</button></div>
        </form>
    </div>
</div>
@endforeach

@endsection
