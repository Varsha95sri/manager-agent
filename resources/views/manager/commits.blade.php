@extends('layouts.manager')

@section('title', 'Git Commits Log - Manager Agent')
@section('page_title', 'Git Commits Log')

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
</style>
@endsection

@section('content')
<div class="card glass-card p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="h5 font-outfit text-white mb-0">Commit History Ledger</h4>
            <p class="text-secondary small mb-0 mt-1">Yajra DataTables — server-side powered commit log</p>
        </div>
        <button class="btn accent-btn d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addCommitModal">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/></svg>
            Log New Commit
        </button>
    </div>

    <div class="table-responsive">
        <table id="commitsTable" class="table table-hover align-middle w-100 text-white"
               style="--bs-table-bg: transparent; --bs-table-border-color: #334155;">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Commit Hash</th>
                    <th>Repository</th>
                    <th>Developer</th>
                    <th>GitLab ID</th>
                    <th>Message</th>
                    <th>Committed At</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

{{-- ==================== ADD COMMIT MODAL ==================== --}}
<div class="modal fade" id="addCommitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content text-white" style="background-color:#0b0f19;border:1px solid #334155;border-radius:20px;">
            <div class="modal-header border-bottom border-slate-800 p-4">
                <h5 class="modal-title font-outfit">Log New Git Commit</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ route('manager.store-commit') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Developer Email</label>
                            <input type="email" name="email" class="form-control" placeholder="developer@manageragent.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Commit Hash (SHA)</label>
                            <input type="text" name="commit_hash" class="form-control" placeholder="abc1234def5678..." required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Repository Name</label>
                            <select name="repository_id" class="form-select" onchange="fillRepoName(this)">
                                <option value="">-- Select Repository --</option>
                                @foreach($repositories as $repo)
                                    <option value="{{ $repo->id }}" data-name="{{ $repo->name }}">{{ $repo->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Repository Name (manual override)</label>
                            <input type="text" name="repository_name" id="repoNameInput" class="form-control" placeholder="org/repo-name" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Commit Message</label>
                            <input type="text" name="message" class="form-control" placeholder="feat: added new feature..." required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Committed At</label>
                            <input type="datetime-local" name="committed_at" class="form-control" required>
                        </div>
                    </div>
                    <div class="mt-4 text-end">
                        <button type="button" class="btn btn-secondary rounded-3 me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn accent-btn">Save Commit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ==================== EDIT COMMIT MODAL ==================== --}}
<div class="modal fade" id="editCommitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content text-white" style="background-color:#0b0f19;border:1px solid #334155;border-radius:20px;">
            <div class="modal-header border-bottom border-slate-800 p-4">
                <h5 class="modal-title font-outfit">Edit Git Commit</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="editCommitForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Developer Email</label>
                            <input type="email" name="email" id="editCommitEmail" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Commit Hash</label>
                            <input type="text" name="commit_hash" id="editCommitHash" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Repository Name</label>
                            <input type="text" name="repository_name" id="editCommitRepo" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Commit Message</label>
                            <input type="text" name="message" id="editCommitMessage" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Committed At</label>
                            <input type="datetime-local" name="committed_at" id="editCommitDate" class="form-control" required>
                        </div>
                    </div>
                    <div class="mt-4 text-end">
                        <button type="button" class="btn btn-secondary rounded-3 me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn accent-btn">Update Commit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ==================== DELETE COMMIT MODAL ==================== --}}
<div class="modal fade" id="deleteCommitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content text-white" style="background-color:#0b0f19;border:1px solid #334155;border-radius:16px;">
            <div class="modal-header border-bottom border-slate-800 p-3">
                <h6 class="modal-title font-outfit">Confirm Delete</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3 text-secondary small">
                Are you sure you want to delete this commit? This action cannot be undone.
            </div>
            <div class="modal-footer border-top border-slate-800 p-3">
                <button type="button" class="btn btn-secondary btn-sm rounded-3" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteCommitForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm rounded-3">Delete</button>
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
    var table = $('#commitsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('manager.commits.index') }}',
        order: [[6, 'desc']],
        pageLength: 15,
        language: {
            processing: '<div class="d-flex align-items-center gap-2 text-slate-400 small"><div class="spinner-border spinner-border-sm text-primary" role="status"></div> Loading commits...</div>',
            emptyTable: '<div class="text-center text-secondary py-4 italic small">No commits found.</div>',
            zeroRecords: '<div class="text-center text-secondary py-4 italic small">No commits match your search.</div>',
        },
        columns: [
            { data: 'id', name: 'id', render: (d, t, r, m) => m.row + 1 + m.settings._iDisplayStart, orderable: false, searchable: false, width: '40px' },
            { data: 'commit_hash', name: 'commit_hash', render: d => `<span class="font-mono text-primary" style="font-size:13px;">${d}</span>` },
            { data: 'repository_name', name: 'repository_name', render: d => `<a href="/manager-agent/gitlab?tab=projects" class="text-slate-300 font-semibold text-decoration-none hover-text-white">${d ?? 'N/A'}</a>` },
            { data: 'developer', name: 'teamMember.name', render: d => `<a href="/manager-agent/gitlab?tab=employees" class="text-slate-300 text-decoration-none hover-text-white">${d}</a>` },
            { data: 'gitlab_id', name: 'teamMember.gitlab_id', render: d => `<span class="font-mono text-purple-400" style="font-size:12px;">${d ?? 'N/A'}</span>` },
            { data: 'message', name: 'message', render: d => `<span class="text-slate-100" style="max-width:300px;display:inline-block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="${d}">${d}</span>` },
            { data: 'committed_at', name: 'committed_at', render: d => `<span class="text-slate-400" style="font-size:12px;">${d}</span>` },
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
                                onclick="openEditCommit(${id}, '${row.commit_hash}', '${row.repository_name}', '${row.message}', '${row.committed_at}', '${row.developer_email}')">
                                Edit
                            </button>
                            <button class="btn btn-sm btn-action" style="background:rgba(244,63,94,0.12);color:#fb7185;border:1px solid rgba(244,63,94,0.3);"
                                onclick="openDeleteCommit(${id})">
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

function fillRepoName(sel) {
    var opt = sel.options[sel.selectedIndex];
    document.getElementById('repoNameInput').value = opt.dataset.name || '';
}

function openEditCommit(id, hash, repo, message, committedAt, email) {
    document.getElementById('editCommitEmail').value = email || '';
    document.getElementById('editCommitHash').value = hash;
    document.getElementById('editCommitRepo').value = repo;
    document.getElementById('editCommitMessage').value = message;
    // Parse the displayed date back to datetime-local format
    var dt = new Date(committedAt);
    if (!isNaN(dt)) {
        var pad = n => String(n).padStart(2, '0');
        document.getElementById('editCommitDate').value =
            `${dt.getFullYear()}-${pad(dt.getMonth()+1)}-${pad(dt.getDate())}T${pad(dt.getHours())}:${pad(dt.getMinutes())}`;
    }
    document.getElementById('editCommitForm').action = `/manager-agent/commit/${id}`;
    new bootstrap.Modal(document.getElementById('editCommitModal')).show();
}

function openDeleteCommit(id) {
    document.getElementById('deleteCommitForm').action = `/manager-agent/commit/${id}`;
    new bootstrap.Modal(document.getElementById('deleteCommitModal')).show();
}
</script>
@endsection
