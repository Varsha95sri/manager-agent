@extends('layouts.manager')

@section('title', $team->name . ' Details')
@section('page_title', 'Team Details')

@section('content')
<div class="mb-4 d-flex align-items-center">
    <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm me-3 d-flex align-items-center rounded-circle p-2" style="width: 32px; height: 32px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/></svg>
    </a>
    <h4 class="mb-0 font-outfit text-dark">{{ $team->name }}</h4>
</div>

<div class="row g-4 mb-4">
    <!-- Left Column: Overview & Members -->
    <div class="col-lg-8">
        <!-- Overview Card -->
        <div class="card bg-white shadow-sm border-0 rounded-4 p-4 mb-4">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="d-flex align-items-center">
                    <div class="bg-{{ $team->icon_bg }} bg-opacity-10 text-{{ $team->icon_bg }} rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                        @if($team->slug == 'frontend')
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" viewBox="0 0 16 16"><path d="M2 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2H2zm0 1h12a1 1 0 0 1 1 1v1H1V3a1 1 0 0 1 1-1zM1 6h14v7a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6zm2 2v2h2V8H3zm4 0v2h2V8H7z"/></svg>
                        @elseif($team->slug == 'backend')
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" viewBox="0 0 16 16"><path d="M4 11a1 1 0 1 1 2 0v1a1 1 0 1 1-2 0v-1zm5-4a1 1 0 1 1 2 0v5a1 1 0 1 1-2 0V7zM4 3a1 1 0 1 1 2 0v3a1 1 0 1 1-2 0V3z"/><path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z"/></svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" viewBox="0 0 16 16"><path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/><path d="M6.854 4.646a.5.5 0 0 1 0 .708L5.207 7l1.647 1.646a.5.5 0 0 1-.708.708l-2-2a.5.5 0 0 1 0-.708l2-2a.5.5 0 0 1 .708 0zM9.146 4.646a.5.5 0 0 0 0 .708L10.793 7l-1.647 1.646a.5.5 0 0 0 .708.708l2-2a.5.5 0 0 0 0-.708l-2-2a.5.5 0 0 0-.708 0z"/></svg>
                        @endif
                    </div>
                    <div>
                        <h5 class="mb-0 text-dark font-outfit">{{ $team->name }} Overview</h5>
                        <span class="text-secondary small">Lead: {{ $team->lead_id ? 'Assigned' : 'Unassigned' }} | {{ count($team->teamMembers) }} Members</span>
                    </div>
                </div>
                <span class="badge bg-{{ $team->status_color }} bg-opacity-10 text-{{ $team->status_color }} border border-{{ $team->status_color }} border-opacity-20 rounded-pill px-4 py-2" style="font-size: 14px;">{{ $team->status }}</span>
            </div>
            
            <p class="text-secondary text-sm" style="line-height: 1.6;">{{ $team->description }}</p>
            
            <div class="row mt-4 pt-4 border-top border-secondary-subtle">
                <div class="col-md-6 mb-3 mb-md-0">
                    <span class="text-secondary small d-block mb-2">Team Productivity Rating</span>
                    <div class="d-flex align-items-center">
                        <h3 class="font-outfit text-dark mb-0 me-3">{{ $productivityRating }}%</h3>
                        <div class="progress flex-grow-1" style="height: 8px;">
                            <div class="progress-bar bg-{{ $team->status_color }}" role="progressbar" style="width: {{ $productivityRating }}%"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 border-start border-secondary-subtle ps-md-4">
                    <span class="text-secondary small d-block mb-2">Tasks Completed vs Assigned</span>
                    <div class="d-flex align-items-center">
                        <h3 class="font-outfit text-dark mb-0 me-3">{{ $totalCompletedTasks }} / {{ $totalAssignedTasks }}</h3>
                        <span class="text-secondary font-semibold"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Members Table -->
        <div class="card bg-white shadow-sm border-0 rounded-4 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="font-outfit text-dark mb-0">Team Members</h5>
                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="me-1" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/></svg>
                    Add Member
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 rounded-start-2 py-3 px-4 text-secondary font-semibold" style="font-size: 13px;">Name</th>
                            <th class="border-0 py-3 px-4 text-secondary font-semibold" style="font-size: 13px;">Role</th>
                            <th class="border-0 py-3 px-4 text-secondary font-semibold" style="font-size: 13px;">Project Role</th>
                            <th class="border-0 py-3 px-4 text-secondary font-semibold" style="font-size: 13px;">Work Completed</th>
                            <th class="border-0 rounded-end-2 py-3 px-4 text-secondary font-semibold text-center" style="font-size: 13px;">Action</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @foreach($team->teamMembers as $member)
                        <tr>
                            <td class="px-4 py-3 border-secondary-subtle">
                                <a href="{{ route('manager.employees.show', $member->id) }}" class="text-decoration-none">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center me-3 font-semibold" style="width: 36px; height: 36px;">
                                            {{ substr($member->name, 0, 1) }}
                                        </div>
                                        <span class="text-dark font-medium hover-primary">{{ $member->name }}</span>
                                    </div>
                                </a>
                            </td>
                            <td class="px-4 py-3 border-secondary-subtle text-secondary small">{{ $member->role }}</td>
                            <td class="px-4 py-3 border-secondary-subtle text-secondary small">
                                @if($member->projectAllocations->count() > 0)
                                    @foreach($member->projectAllocations as $alloc)
                                        <div class="mb-1 text-truncate" style="max-width: 150px;" title="{{ $alloc->project->name ?? 'Project' }}: {{ $alloc->role_on_project ?: 'Member' }}">
                                            <strong>{{ $alloc->project->name ?? 'Project' }}:</strong> {{ $alloc->role_on_project ?: 'Member' }}
                                        </div>
                                    @endforeach
                                @else
                                    <span class="text-muted fst-italic">No active project</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 border-secondary-subtle text-secondary small">
                                <div class="mb-1"><i class="fas fa-check-circle text-success me-1"></i>{{ $member->tasks_count ?? 0 }} Tasks</div>
                                <div><i class="fab fa-gitlab text-warning me-1"></i>{{ $member->commits_count ?? 0 }} Commits</div>
                            </td>
                            <td class="px-4 py-3 border-secondary-subtle text-center">
                                <a href="{{ route('manager.employees.show', $member->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">View Profile</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column: Recent Activity & Alerts -->
    <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="card bg-white shadow-sm border-0 rounded-4 p-4 mb-4">
            <h6 class="font-outfit text-dark mb-3">Quick Actions</h6>
            <div class="d-grid gap-2">
                <button type="button" class="btn btn-outline-primary rounded-3 text-start d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#assignTaskModal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/></svg>
                    Assign New Task
                </button>
                <button type="button" class="btn btn-outline-secondary rounded-3 text-start d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#scheduleMeetingModal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2" viewBox="0 0 16 16"><path d="M8 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm0 1c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                    Schedule Meeting
                </button>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="card bg-white shadow-sm border-0 rounded-4 p-4 h-100">
            <h6 class="font-outfit text-dark mb-4">Recent Activity</h6>
            <div class="position-relative border-start border-2 border-primary border-opacity-25 ms-2 ps-4">
                
                <div class="mb-4 position-relative">
                    <div class="position-absolute bg-primary rounded-circle" style="width: 12px; height: 12px; left: -29px; top: 4px; border: 2px solid white;"></div>
                    <p class="mb-1 text-dark small font-semibold">Sprint Planning Completed</p>
                    <p class="text-secondary small mb-0" style="font-size: 11px;">Today, 10:30 AM</p>
                </div>
                
                <div class="mb-4 position-relative">
                    <div class="position-absolute bg-success rounded-circle" style="width: 12px; height: 12px; left: -29px; top: 4px; border: 2px solid white;"></div>
                    <p class="mb-1 text-dark small font-semibold">15 Tasks Resolved</p>
                    <p class="text-secondary small mb-0" style="font-size: 11px;">Yesterday, 05:45 PM</p>
                </div>

                <div class="position-relative">
                    <div class="position-absolute bg-warning rounded-circle" style="width: 12px; height: 12px; left: -29px; top: 4px; border: 2px solid white;"></div>
                    <p class="mb-1 text-dark small font-semibold">Code Review Meeting</p>
                    <p class="text-secondary small mb-0" style="font-size: 11px;">Yesterday, 02:00 PM</p>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Assign Task Modal -->
<div class="modal fade" id="assignTaskModal" tabindex="-1" aria-labelledby="assignTaskModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 rounded-4 shadow">
      <div class="modal-header border-bottom-0 pb-0">
        <h5 class="modal-title font-outfit" id="assignTaskModalLabel">Assign New Task</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form>
            <div class="mb-3">
                <label class="form-label text-secondary small">Task Title</label>
                <input type="text" class="form-control rounded-3" placeholder="Enter task title">
            </div>
            <div class="mb-3">
                <label class="form-label text-secondary small">Assign To</label>
                <select class="form-select rounded-3">
                    <option selected disabled>Select team member...</option>
                    @foreach($team->teamMembers as $member)
                        <option value="{{ $member->name }}">{{ $member->name }} ({{ $member->role }})</option>
                    @endforeach
                </select>
            </div>
            <div class="row mb-3">
                <div class="col-6">
                    <label class="form-label text-secondary small">Due Date</label>
                    <input type="date" class="form-control rounded-3">
                </div>
                <div class="col-6">
                    <label class="form-label text-secondary small">Priority</label>
                    <select class="form-select rounded-3">
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label text-secondary small">Description</label>
                <textarea class="form-control rounded-3" rows="3" placeholder="Task details..."></textarea>
            </div>
        </form>
      </div>
      <div class="modal-footer border-top-0 pt-0">
        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary rounded-pill px-4">Assign Task</button>
      </div>
    </div>
  </div>
</div>

<!-- Schedule Meeting Modal -->
<div class="modal fade" id="scheduleMeetingModal" tabindex="-1" aria-labelledby="scheduleMeetingModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 rounded-4 shadow">
      <div class="modal-header border-bottom-0 pb-0">
        <h5 class="modal-title font-outfit" id="scheduleMeetingModalLabel">Schedule Meeting</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form>
            <div class="mb-3">
                <label class="form-label text-secondary small">Meeting Title</label>
                <input type="text" class="form-control rounded-3" placeholder="e.g. Sprint Review">
            </div>
            <div class="row mb-3">
                <div class="col-6">
                    <label class="form-label text-secondary small">Date</label>
                    <input type="date" class="form-control rounded-3">
                </div>
                <div class="col-6">
                    <label class="form-label text-secondary small">Time</label>
                    <input type="time" class="form-control rounded-3">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label text-secondary small">Platform / Location</label>
                <select class="form-select rounded-3">
                    <option value="google_meet">Google Meet</option>
                    <option value="zoom">Zoom</option>
                    <option value="teams">Microsoft Teams</option>
                    <option value="office">In-Office (Conference Room)</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label text-secondary small">Agenda</label>
                <textarea class="form-control rounded-3" rows="2" placeholder="Brief agenda..."></textarea>
            </div>
        </form>
      </div>
      <div class="modal-footer border-top-0 pt-0">
        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-secondary rounded-pill px-4">Schedule</button>
      </div>
    </div>
  </div>
</div>

<!-- View Profile Modals -->
@foreach($team->teamMembers as $index => $member)
<div class="modal fade" id="viewProfileModal{{ $index }}" tabindex="-1" aria-labelledby="viewProfileModalLabel{{ $index }}" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content border-0 rounded-4 shadow text-center p-4">
      <div class="d-flex justify-content-end mb-2">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center mx-auto mb-3 font-semibold" style="width: 80px; height: 80px; font-size: 32px;">
          {{ substr($member->name, 0, 1) }}
      </div>
      <h5 class="font-outfit text-dark mb-1">{{ $member->name }}</h5>
      <p class="text-secondary small mb-3">{{ $member->role }}</p>
      
      <div class="d-grid gap-2">
          <button type="button" class="btn btn-outline-primary rounded-pill btn-sm">Send Message</button>
          <button type="button" class="btn btn-light rounded-pill btn-sm">View Full Profile</button>
      </div>
    </div>
  </div>
</div>
@endforeach

<!-- Add Member Modal -->
<div class="modal fade" id="addMemberModal" tabindex="-1" aria-labelledby="addMemberModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 rounded-4 shadow">
      <div class="modal-header border-bottom-0 pb-0">
        <h5 class="modal-title font-outfit" id="addMemberModalLabel">Add Team Member</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('manager.teams.add-member', $team->slug) }}" method="POST">
        @csrf
        <div class="modal-body">
            <div class="mb-3">
                <label class="form-label text-secondary small">Name</label>
                <input type="text" name="name" class="form-control rounded-3" placeholder="e.g. John Doe" required>
            </div>
            <div class="mb-3">
                <label class="form-label text-secondary small">Email</label>
                <input type="email" name="email" class="form-control rounded-3" placeholder="john@example.com" required>
            </div>
            <div class="mb-3">
                <label class="form-label text-secondary small">Role</label>
                <input type="text" name="role" class="form-control rounded-3" placeholder="e.g. Senior Developer" required>
            </div>
        </div>
        <div class="modal-footer border-top-0 pt-0">
            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary rounded-pill px-4">Add Member</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection
