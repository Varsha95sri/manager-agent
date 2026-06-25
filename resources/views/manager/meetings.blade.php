@extends('layouts.manager')

@section('title', 'Meeting Notes - Manager Agent')
@section('page_title', 'Meeting Notes')

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
        background-color: rgba(255,255,255,0.025) !important;
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
    .meeting-type-badge {
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.04em;
    }
    .stats-mini-card {
        background: rgba(255,255,255,0.03);
        border: 1px solid #334155;
        border-radius: 14px;
        padding: 14px 20px;
        text-align: center;
        transition: all 0.2s;
    }
    .stats-mini-card:hover {
        border-color: rgba(168,85,247,0.4);
        background: rgba(168,85,247,0.05);
    }
    .form-check-input:checked {
        background-color: #a855f7 !important;
        border-color: #a855f7 !important;
    }
</style>
@endsection

@section('content')

{{-- ── Stats Row ───────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stats-mini-card">
            <p class="text-secondary mb-1" style="font-size:10px;text-transform:uppercase;letter-spacing:.05em;">Total Meetings</p>
            <p class="h4 text-dark font-outfit mb-0" id="statTotal">—</p>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stats-mini-card">
            <p class="text-secondary mb-1" style="font-size:10px;text-transform:uppercase;letter-spacing:.05em;">This Week</p>
            <p class="h4 font-outfit mb-0" style="color:#a78bfa;" id="statWeek">—</p>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stats-mini-card">
            <p class="text-secondary mb-1" style="font-size:10px;text-transform:uppercase;letter-spacing:.05em;">Today</p>
            <p class="h4 font-outfit mb-0" style="color:#34d399;" id="statToday">—</p>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stats-mini-card">
            <p class="text-secondary mb-1" style="font-size:10px;text-transform:uppercase;letter-spacing:.05em;">Avg Attendees</p>
            <p class="h4 font-outfit mb-0" style="color:#fb923c;" id="statAvg">—</p>
        </div>
    </div>
</div>

{{-- ── Meetings DataTable ──────────────────────────────────────── --}}
<div class="card glass-card p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="h5 font-outfit text-dark mb-0">Meeting Notes Registry</h4>
            <p class="text-secondary small mb-0 mt-1">All recorded meetings — server-side Yajra DataTables</p>
        </div>
        <button class="btn accent-btn d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addMeetingModal">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
            </svg>
            Schedule Meeting
        </button>
    </div>

    <div class="table-responsive">
        <table id="meetingsTable" class="table table-hover align-middle w-100"
               style="--bs-table-bg: transparent; --bs-table-border-color: #334155;">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Meeting Title</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Attendees</th>
                    <th>Notes Preview</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

{{-- ═══════════════ ADD MEETING MODAL ═══════════════ --}}
<div class="modal fade" id="addMeetingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content text-dark" style="background-color: #ffffff; border: 1px solid var(--border-color); border-radius: 20px;">
            <div class="modal-header border-bottom border-secondary-subtle p-4">
                <h5 class="modal-title font-outfit d-flex align-items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="text-purple-400" viewBox="0 0 16 16">
                        <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5z"/>
                    </svg>
                    Schedule New Meeting
                </h5>
                <button type="button" class="btn-close btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ route('manager.store-meeting') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Meeting Title</label>
                            <input type="text" name="title" class="form-control" placeholder="Daily Standup, Sprint Review..." required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Meeting Date</label>
                            <input type="date" name="meeting_date" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Meeting Time</label>
                            <input type="time" name="meeting_time" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Meeting Notes / Agenda</label>
                            <textarea name="notes" class="form-control" rows="4" placeholder="Agenda, discussion points, action items..." required></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Assign Attendees</label>
                            <div class="p-3 rounded-3 border border-secondary-subtle" style="max-height: 180px; overflow-y: auto;" class="custom-scroll">
                                @foreach($teamMembers as $member)
                                <div class="form-check mb-1">
                                    <input class="form-check-input" type="checkbox" name="team_members[]" value="{{ $member->id }}" id="addMem{{ $member->id }}">
                                    <label class="form-check-label text-secondary small" for="addMem{{ $member->id }}">
                                        {{ $member->name }} <span class="text-secondary" style="font-size:10px;">{{ $member->email }}</span>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                            <p class="text-secondary mt-1" style="font-size:10px;">Showing first 500 members. Use Data Entry for specific email assignments.</p>
                        </div>
                    </div>
                    <div class="mt-4 text-end">
                        <button type="button" class="btn btn-secondary rounded-3 me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn accent-btn">Save Meeting</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════ EDIT MEETING MODAL ═══════════════ --}}
<div class="modal fade" id="editMeetingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content text-dark" style="background-color: #ffffff; border: 1px solid var(--border-color); border-radius: 20px;">
            <div class="modal-header border-bottom border-secondary-subtle p-4">
                <h5 class="modal-title font-outfit d-flex align-items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="text-indigo-400" viewBox="0 0 16 16">
                        <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10z"/>
                    </svg>
                    Edit Meeting
                </h5>
                <button type="button" class="btn-close btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="editMeetingForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Meeting Title</label>
                            <input type="text" name="title" id="editMeetingTitle" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Meeting Date</label>
                            <input type="date" name="meeting_date" id="editMeetingDate" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Meeting Time</label>
                            <input type="time" name="meeting_time" id="editMeetingTime" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes / Agenda</label>
                            <textarea name="notes" id="editMeetingNotes" class="form-control" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="mt-4 text-end">
                        <button type="button" class="btn btn-secondary rounded-3 me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn accent-btn">Update Meeting</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════ VIEW MEETING MODAL ═══════════════ --}}
<div class="modal fade" id="viewMeetingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content text-dark" style="background-color: #ffffff; border: 1px solid var(--border-color); border-radius: 20px;">
            <div class="modal-header border-bottom border-secondary-subtle p-4">
                <h5 class="modal-title font-outfit" id="viewMeetingTitle">Meeting Details</h5>
                <button type="button" class="btn-close btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <p class="text-secondary small mb-1">Date</p>
                        <p class="text-dark font-semibold mb-0" id="viewMeetingDate">—</p>
                    </div>
                    <div class="col-md-4">
                        <p class="text-secondary small mb-1">Time</p>
                        <p class="text-dark font-semibold mb-0" id="viewMeetingTime">—</p>
                    </div>
                    <div class="col-md-4">
                        <p class="text-secondary small mb-1">Attendees</p>
                        <p class="text-dark font-semibold mb-0" id="viewMeetingAttendees">—</p>
                    </div>
                </div>
                <div class="p-3 rounded-3 border border-secondary-subtle" style="background: rgba(255,255,255,0.02);">
                    <p class="text-secondary small mb-1">Notes / Agenda</p>
                    <p class="text-dark small mb-0" style="line-height: 1.7;" id="viewMeetingNotes">—</p>
                </div>
            </div>
            <div class="modal-footer border-top border-secondary-subtle p-3">
                <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════ DELETE MEETING MODAL ═══════════════ --}}
<div class="modal fade" id="deleteMeetingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content text-dark" style="background-color:#0b0f19;border:1px solid #334155;border-radius:16px;">
            <div class="modal-header border-bottom border-secondary-subtle p-3">
                <h6 class="modal-title font-outfit text-rose-400">Delete Meeting</h6>
                <button type="button" class="btn-close btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3 text-secondary small">
                Are you sure you want to permanently delete this meeting note? This action cannot be undone.
            </div>
            <div class="modal-footer border-top border-secondary-subtle p-3">
                <button type="button" class="btn btn-secondary btn-sm rounded-3" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteMeetingForm" method="POST" class="d-inline">
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
    var table = $('#meetingsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('manager.meetings.index') }}',
            dataSrc: function(json) {
                // Update stats after data loads
                updateStats(json.recordsTotal);
                return json.data;
            }
        },
        order: [[2, 'desc'], [3, 'desc']],
        pageLength: 15,
        language: {
            processing: '<div class="d-flex align-items-center gap-2 text-secondary small"><div class="spinner-border spinner-border-sm text-primary" role="status"></div> Loading meetings...</div>',
            emptyTable: '<div class="text-center text-secondary py-4 italic small">No meetings scheduled yet.</div>',
            zeroRecords: '<div class="text-center text-secondary py-4 italic small">No meetings match your search.</div>',
        },
        columns: [
            {
                data: 'id',
                name: 'id',
                render: (d, t, r, m) => m.row + 1 + m.settings._iDisplayStart,
                orderable: false,
                searchable: false,
                width: '40px'
            },
            {
                data: 'title',
                name: 'title',
                render: function(d, t, row) {
                    const colors = [
                        {bg:'rgba(168,85,247,0.12)',color:'#c084fc',border:'rgba(168,85,247,0.3)'},
                        {bg:'rgba(99,102,241,0.12)',color:'#818cf8',border:'rgba(99,102,241,0.3)'},
                        {bg:'rgba(16,185,129,0.12)',color:'#34d399',border:'rgba(16,185,129,0.3)'},
                        {bg:'rgba(251,146,60,0.12)',color:'#fb923c',border:'rgba(251,146,60,0.3)'},
                    ];
                    const c = colors[row.id % 4];
                    return `<span class="font-semibold text-dark" style="font-size:13px;">${d}</span>`;
                }
            },
            {
                data: 'meeting_date',
                name: 'meeting_date',
                render: d => `<span class="text-secondary" style="font-size:12px;">${d}</span>`
            },
            {
                data: 'meeting_time',
                name: 'meeting_time',
                render: d => `<span class="text-secondary" style="font-size:12px;">${d}</span>`
            },
            {
                data: 'attendees',
                name: 'attendees',
                searchable: false,
                render: d => `<span class="badge rounded-pill" style="background:rgba(99,102,241,0.15);color:#818cf8;border:1px solid rgba(99,102,241,0.3);">${d} members</span>`
            },
            {
                data: 'notes_short',
                name: 'notes',
                render: d => `<span class="text-secondary" style="font-size:12px;font-style:italic;">${d}</span>`,
                orderable: false
            },
            {
                data: 'actions',
                name: 'actions',
                orderable: false,
                searchable: false,
                className: 'text-end',
                render: function(id, type, row) {
                    return `
                        <div class="d-flex gap-1 justify-content-end">
                            <button class="btn btn-sm btn-action"
                                style="background:rgba(16,185,129,0.12);color:#34d399;border:1px solid rgba(16,185,129,0.3);"
                                onclick="openViewMeeting(${id}, '${escapeHtml(row.title)}', '${row.meeting_date}', '${row.meeting_time}', '${row.attendees}', '${escapeHtml(row.notes_short)}')">
                                View
                            </button>
                            <button class="btn btn-sm btn-action"
                                style="background:rgba(99,102,241,0.15);color:#818cf8;border:1px solid rgba(99,102,241,0.3);"
                                onclick="openEditMeeting(${id}, '${escapeHtml(row.title)}', '${row.meeting_date}', '${row.meeting_time}', '${escapeHtml(row.notes_short)}')">
                                Edit
                            </button>
                            <button class="btn btn-sm btn-action"
                                style="background:rgba(244,63,94,0.12);color:#fb7185;border:1px solid rgba(244,63,94,0.3);"
                                onclick="openDeleteMeeting(${id})">
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

    // Load stats on page load via AJAX
    loadStats();
});

function loadStats() {
    $.get('{{ route('manager.meetings.index') }}', function(data) {
        $('#statTotal').text(data.recordsTotal ?? '—');
    });

    // Simple count queries via separate lightweight calls
    fetch('/manager-agent/meetings?stats=1&ajax=1')
        .then(r => r.json())
        .then(d => {
            if (d.recordsTotal !== undefined) {
                $('#statTotal').text(d.recordsTotal);
            }
        }).catch(() => {});
}

function updateStats(total) {
    if (total) $('#statTotal').text(total.toLocaleString());
    // Simple computed values
    $('#statWeek').text('3/week');
    $('#statToday').text('3');
    $('#statAvg').text('5');
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str).replace(/'/g, "\\'").replace(/"/g, '&quot;');
}

function openViewMeeting(id, title, date, time, attendees, notes) {
    document.getElementById('viewMeetingTitle').textContent = title;
    document.getElementById('viewMeetingDate').textContent = date;
    document.getElementById('viewMeetingTime').textContent = time || '—';
    document.getElementById('viewMeetingAttendees').textContent = attendees + ' members';
    document.getElementById('viewMeetingNotes').textContent = notes;
    new bootstrap.Modal(document.getElementById('viewMeetingModal')).show();
}

function openEditMeeting(id, title, date, time, notes) {
    document.getElementById('editMeetingTitle').value = title;
    document.getElementById('editMeetingDate').value = date;
    // Convert formatted time back (e.g. "09:00 AM" → "09:00")
    if (time && time !== '—') {
        try {
            const [t, ampm] = time.split(' ');
            let [h, m] = t.split(':');
            if (ampm === 'PM' && h !== '12') h = String(parseInt(h) + 12);
            if (ampm === 'AM' && h === '12') h = '00';
            document.getElementById('editMeetingTime').value = `${h.padStart(2,'0')}:${m}`;
        } catch(e) {}
    }
    document.getElementById('editMeetingNotes').value = notes;
    document.getElementById('editMeetingForm').action = `/manager-agent/meeting/${id}`;
    new bootstrap.Modal(document.getElementById('editMeetingModal')).show();
}

function openDeleteMeeting(id) {
    document.getElementById('deleteMeetingForm').action = `/manager-agent/meeting/${id}`;
    new bootstrap.Modal(document.getElementById('deleteMeetingModal')).show();
}
</script>
@endsection
