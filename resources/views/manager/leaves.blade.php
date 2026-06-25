@extends('layouts.manager')

@section('title', 'Leave Management - Manager Agent')
@section('page_title', 'Leave Management')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h2 class="h3 font-outfit text-dark mb-1">Leave Management</h2>
                <p class="text-secondary small mb-0">Track and manage employee leave requests.</p>
            </div>
            <button class="btn btn-primary d-inline-flex align-items-center rounded-3 px-3 py-2" data-bs-toggle="modal" data-bs-target="#addLeaveModal">
                <i class="bi bi-plus-lg me-2"></i> Record Leave
            </button>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-secondary small">
                            <tr>
                                <th class="ps-4">Employee</th>
                                <th>Type</th>
                                <th>Duration</th>
                                <th>Days</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leaves as $leave)
                                <tr>
                                    <td class="ps-4">
                                        <div class="font-semibold text-dark">{{ $leave->employee->name }}</div>
                                        <div class="text-secondary small">{{ $leave->employee->role }}</div>
                                    </td>
                                    <td><span class="badge bg-light text-dark border">{{ $leave->leave_type }}</span></td>
                                    <td>
                                        <div class="text-dark small">{{ \Carbon\Carbon::parse($leave->start_date)->format('M d, Y') }}</div>
                                        <div class="text-secondary" style="font-size: 11px;">to {{ \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') }}</div>
                                    </td>
                                    <td><span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $leave->days }} {{ Str::plural('day', $leave->days) }}</span></td>
                                    <td>
                                        <div class="text-secondary small text-truncate" style="max-width: 150px;" title="{{ $leave->reason }}">{{ $leave->reason ?: 'N/A' }}</div>
                                    </td>
                                    <td>
                                        @php
                                            $statusBadge = match($leave->status) {
                                                'approved' => 'bg-success text-success',
                                                'rejected' => 'bg-danger text-danger',
                                                default => 'bg-warning text-warning'
                                            };
                                        @endphp
                                        <span class="badge rounded-pill bg-opacity-10 {{ $statusBadge }} text-capitalize px-2 py-1">{{ $leave->status }}</span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <form action="{{ route('manager.leaves.update', $leave->id) }}" method="POST" class="d-inline">
                                            @csrf @method('PUT')
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="btn btn-sm btn-light text-success py-0 px-2" title="Approve" {{ $leave->status === 'approved' ? 'disabled' : '' }}><i class="bi bi-check-lg"></i></button>
                                        </form>
                                        <form action="{{ route('manager.leaves.update', $leave->id) }}" method="POST" class="d-inline">
                                            @csrf @method('PUT')
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="btn btn-sm btn-light text-danger py-0 px-2" title="Reject" {{ $leave->status === 'rejected' ? 'disabled' : '' }}><i class="bi bi-x-lg"></i></button>
                                        </form>
                                        <form action="{{ route('manager.leaves.destroy', $leave->id) }}" method="POST" class="d-inline ms-1" onsubmit="return confirm('Delete this leave record?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light text-secondary py-0 px-2" title="Delete"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">No leave requests found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($leaves->hasPages())
                <div class="card-footer bg-white border-top border-secondary-subtle">
                    {{ $leaves->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Add Leave Modal -->
<div class="modal fade" id="addLeaveModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('manager.leaves.store') }}" method="POST" class="modal-content rounded-4 border-0 shadow">
            @csrf
            <div class="modal-header border-secondary-subtle">
                <h5 class="modal-title font-outfit">Record Leave</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small text-secondary">Employee</label>
                    <select name="employee_id" class="form-select text-dark" required>
                        <option value="">Select Employee</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->name }} ({{ $emp->role }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small text-secondary">Leave Type</label>
                    <select name="leave_type" class="form-select text-dark" required>
                        <option value="Sick Leave">Sick Leave</option>
                        <option value="Casual Leave">Casual Leave</option>
                        <option value="Annual Leave">Annual Leave</option>
                        <option value="Unpaid Leave">Unpaid Leave</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small text-secondary">Start Date</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" required onchange="calculateDays()">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-secondary">End Date</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" required onchange="calculateDays()">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small text-secondary">Total Days</label>
                    <input type="number" name="days" id="days" class="form-control" value="1" min="1" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small text-secondary">Reason (Optional)</label>
                    <textarea name="reason" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer border-secondary-subtle">
                <button type="submit" class="btn btn-primary rounded-3">Save Leave</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function calculateDays() {
    const start = document.getElementById('start_date').value;
    const end = document.getElementById('end_date').value;
    if(start && end) {
        const diffTime = Math.abs(new Date(end) - new Date(start));
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
        document.getElementById('days').value = diffDays;
    }
}
</script>
@endsection
