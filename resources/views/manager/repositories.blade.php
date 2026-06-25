@extends('layouts.manager')

@section('title', 'Projects & Repositories - Manager Agent')
@section('page_title', 'Projects & Repositories')

@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<style>
    .dataTables_wrapper .dataTables_filter input {
        background-color: #0f172a !important;
        color: #fff !important;
        border: 1px solid #334155 !important;
        border-radius: 8px;
        padding: 6px 12px;
    }
    .dataTables_wrapper .dataTables_length select {
        background-color: #0f172a !important;
        color: #fff !important;
        border: 1px solid #334155 !important;
        border-radius: 8px;
        padding: 4px 8px;
    }
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        color: #94a3b8 !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: linear-gradient(135deg, #a855f7, #6366f1) !important;
        border-color: transparent !important;
        color: #fff !important;
        border-radius: 6px;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: rgba(255,255,255,0.06) !important;
        border-color: transparent !important;
        color: #fff !important;
        border-radius: 6px;
    }
    table.dataTable thead th {
        background-color: transparent !important;
        border-bottom: 1px solid #334155 !important;
        color: #94a3b8 !important;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 600;
    }
    table.dataTable tbody tr {
        background-color: transparent !important;
        border-bottom: 1px solid rgba(255,255,255,0.04) !important;
    }
    table.dataTable tbody tr:hover {
        background-color: rgba(255,255,255,0.02) !important;
    }
    table.dataTable tbody td {
        color: #e2e8f0 !important;
        vertical-align: middle;
    }
    .btn-action {
        padding: 4px 10px;
        font-size: 11px;
        border-radius: 6px;
        font-weight: 600;
    }
    .tab-pill {
        border: 1px solid #334155;
        background: transparent;
        color: #94a3b8;
        border-radius: 8px;
        padding: 7px 18px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    .tab-pill.active {
        background: linear-gradient(135deg, #a855f7, #6366f1);
        border-color: transparent;
        color: #fff;
    }
</style>
@endsection

@section('content')

{{-- Tab Navigation --}}
<div class="d-flex gap-2 mb-4">
    <button class="tab-pill active" id="tab-repos" onclick="switchTab('repos')">Repositories</button>
    <button class="tab-pill" id="tab-projects" onclick="switchTab('projects')">Projects</button>
</div>

{{-- ==================== REPOSITORIES TABLE ==================== --}}
<div id="panel-repos">
    <div class="card glass-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h4 class="h5 font-outfit text-dark mb-0">Project & Repository Registry</h4>
                <p class="text-secondary small mb-0 mt-1">Yajra DataTables — server-side powered repository list</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-sm" style="background:rgba(99,102,241,0.15);color:#818cf8;border:1px solid rgba(99,102,241,0.3);" data-bs-toggle="modal" data-bs-target="#addProjectModal">
                    + Add Project
                </button>
                <button class="btn accent-btn d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addRepoModal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/></svg>
                    Add Repository
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table id="reposTable" class="table table-hover align-middle w-100 text-dark"
                   style="--bs-table-bg: transparent; --bs-table-border-color: #334155;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Repository Name</th>
                        <th>Project Association</th>
                        <th>GitHub Web URL</th>
                        <th>Created At</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

{{-- ==================== PROJECTS PANEL (Simple List) ==================== --}}
<div id="panel-projects" style="display:none;">
    <div class="card glass-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="h5 font-outfit text-dark mb-0">Projects Directory</h4>
                <p class="text-secondary small mb-0 mt-1">All registered projects in the system</p>
            </div>
            <button class="btn accent-btn d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addProjectModal">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/></svg>
                New Project
            </button>
        </div>
        <div class="row g-3">
            @foreach($projects as $project)
            <div class="col-md-6 col-lg-4">
                <div class="p-3 rounded-4 border border-secondary-subtle bg-white shadow-sm/40 hover-card" style="transition: all 0.2s;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-dark font-outfit font-semibold mb-1">{{ $project->name }}</h6>
                            <p class="text-secondary small mb-0" style="font-size:11px;">{{ $project->description ?? 'No description' }}</p>
                        </div>
                        <span class="badge rounded-pill" style="background:rgba(168,85,247,0.15);color:#c084fc;border:1px solid rgba(168,85,247,0.3);font-size:9px;">
                            ID #{{ $project->id }}
                        </span>
                    </div>
                </div>
            </div>
            @endforeach
            @if($projects->isEmpty())
                <div class="col-12 text-center text-secondary py-4 italic small">No projects registered yet.</div>
            @endif
        </div>
    </div>
</div>

{{-- ==================== ADD REPOSITORY MODAL ==================== --}}
<div class="modal fade" id="addRepoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-dark" style="background-color: #ffffff; border: 1px solid var(--border-color); border-radius: 20px;">
            <div class="modal-header border-bottom border-secondary-subtle p-4">
                <h5 class="modal-title font-outfit">Add New Repository</h5>
                <button type="button" class="btn-close btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ route('manager.store-repository') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Project</label>
                        <select name="project_id" class="form-select" required>
                            <option value="">-- Select Project --</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Repository Name</label>
                        <input type="text" name="name" class="form-control" placeholder="org/repo-name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">GitHub URL (optional)</label>
                        <input type="url" name="url" class="form-control" placeholder="https://github.com/org/repo">
                    </div>
                    <div class="mt-4 text-end">
                        <button type="button" class="btn btn-secondary rounded-3 me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn accent-btn">Save Repository</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ==================== EDIT REPOSITORY MODAL ==================== --}}
<div class="modal fade" id="editRepoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-dark" style="background-color: #ffffff; border: 1px solid var(--border-color); border-radius: 20px;">
            <div class="modal-header border-bottom border-secondary-subtle p-4">
                <h5 class="modal-title font-outfit">Edit Repository</h5>
                <button type="button" class="btn-close btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="editRepoForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Project</label>
                        <select name="project_id" id="editRepoProject" class="form-select" required>
                            <option value="">-- Select Project --</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Repository Name</label>
                        <input type="text" name="name" id="editRepoName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">GitHub URL</label>
                        <input type="url" name="url" id="editRepoUrl" class="form-control">
                    </div>
                    <div class="mt-4 text-end">
                        <button type="button" class="btn btn-secondary rounded-3 me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn accent-btn">Update Repository</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ==================== DELETE REPOSITORY MODAL ==================== --}}
<div class="modal fade" id="deleteRepoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content text-dark" style="background-color:#0b0f19;border:1px solid #334155;border-radius:16px;">
            <div class="modal-header border-bottom border-secondary-subtle p-3">
                <h6 class="modal-title font-outfit">Confirm Delete</h6>
                <button type="button" class="btn-close btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3 text-secondary small">
                Are you sure you want to delete this repository? All associated commits will also be removed.
            </div>
            <div class="modal-footer border-top border-secondary-subtle p-3">
                <button type="button" class="btn btn-secondary btn-sm rounded-3" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteRepoForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm rounded-3">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ==================== ADD PROJECT MODAL ==================== --}}
<div class="modal fade" id="addProjectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-dark" style="background-color: #ffffff; border: 1px solid var(--border-color); border-radius: 20px;">
            <div class="modal-header border-bottom border-secondary-subtle p-4">
                <h5 class="modal-title font-outfit">Create New Project</h5>
                <button type="button" class="btn-close btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ route('manager.store-project') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Project Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Project Alpha..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description (optional)</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Brief project description..."></textarea>
                    </div>
                    <div class="mt-4 text-end">
                        <button type="button" class="btn btn-secondary rounded-3 me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn accent-btn">Create Project</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {
    $('#reposTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('manager.repositories.index') }}',
        order: [[1, 'asc']],
        pageLength: 15,
        language: {
            processing: '<div class="d-flex align-items-center gap-2 text-secondary small"><div class="spinner-border spinner-border-sm text-primary" role="status"></div> Loading repositories...</div>',
            emptyTable: '<div class="text-center text-secondary py-4 italic small">No repositories found.</div>',
            zeroRecords: '<div class="text-center text-secondary py-4 italic small">No repositories match your search.</div>',
        },
        columns: [
            { data: 'id', name: 'id', render: (d, t, r, m) => m.row + 1 + m.settings._iDisplayStart, orderable: false, searchable: false, width: '40px' },
            { data: 'name', name: 'name', render: d => `<span class="font-semibold text-dark">${d}</span>` },
            {
                data: 'project_name', name: 'project.name',
                render: (d, t, row) => `
                    <div><span class="text-secondary">${d}</span></div>
                    <div class="text-secondary" style="font-size:10px;">${row.project_desc ?? ''}</div>`
            },
            {
                data: 'url', name: 'url',
                render: d => d ? `<a href="${d}" target="_blank" class="text-purple-400 text-decoration-none font-mono" style="font-size:12px;">${d}</a>` : '<span class="text-secondary">—</span>'
            },
            { data: 'created_at', name: 'created_at', render: d => `<span class="text-secondary" style="font-size:12px;">${d}</span>` },
            {
                data: 'actions',
                name: 'actions',
                orderable: false,
                searchable: false,
                className: 'text-end',
                render: function(id, type, row) {
                    return `
                        <div class="d-flex gap-1 justify-content-end">
                            <button class="btn btn-sm btn-action" style="background:rgba(99,102,241,0.15);color:#818cf8;border:1px solid rgba(99,102,241,0.3);"
                                onclick="openEditRepo(${id}, '${row.name}', '${row.url ?? ''}', ${row.project_id ?? 'null'})">
                                Edit
                            </button>
                            <button class="btn btn-sm btn-action" style="background:rgba(244,63,94,0.12);color:#fb7185;border:1px solid rgba(244,63,94,0.3);"
                                onclick="openDeleteRepo(${id})">
                                Delete
                            </button>
                        </div>`;
                }
            }
        ],
        dom: "<'row mb-3'<'col-sm-6'l><'col-sm-6'f>>" +
             "<'row'<'col-12'tr>>" +
             "<'row mt-3'<'col-sm-5'i><'col-sm-7'p>>",
    });
});

function switchTab(tab) {
    document.getElementById('panel-repos').style.display = tab === 'repos' ? '' : 'none';
    document.getElementById('panel-projects').style.display = tab === 'projects' ? '' : 'none';
    document.getElementById('tab-repos').classList.toggle('active', tab === 'repos');
    document.getElementById('tab-projects').classList.toggle('active', tab === 'projects');
}

function openEditRepo(id, name, url, projectId) {
    document.getElementById('editRepoName').value = name;
    document.getElementById('editRepoUrl').value = url;
    if (projectId) document.getElementById('editRepoProject').value = projectId;
    document.getElementById('editRepoForm').action = `/manager-agent/repository/${id}`;
    new bootstrap.Modal(document.getElementById('editRepoModal')).show();
}

function openDeleteRepo(id) {
    document.getElementById('deleteRepoForm').action = `/manager-agent/repository/${id}`;
    new bootstrap.Modal(document.getElementById('deleteRepoModal')).show();
}
</script>
@endsection
