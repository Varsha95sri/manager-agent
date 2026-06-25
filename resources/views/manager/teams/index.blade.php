@extends('layouts.manager')

@section('title', 'All Teams Overview')
@section('page_title', 'All Teams Overview')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-white shadow-sm border-0 rounded-4 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="font-outfit text-dark mb-1">Organization Teams</h5>
                    <p class="text-secondary small mb-0">Overview of all functional teams and their performance metrics.</p>
                </div>
                <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#createTeamModal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/></svg>
                    Create New Team
                </button>
            </div>

            <div class="row g-4">
                @foreach($teams as $team)
                <div class="col-md-4">
                    <a href="{{ route('manager.teams.show', $team->slug) }}" class="text-decoration-none">
                        <div class="card bg-light border-secondary-subtle rounded-4 h-100 p-4 hover-card" style="transition: all 0.3s; border: 1px solid #e2e8f0;">
                            <div class="d-flex align-items-start mb-3">
                                <div class="bg-{{ $team->icon_bg }} bg-opacity-10 text-{{ $team->icon_bg }} rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                                    @if($team->slug == 'frontend')
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16"><path d="M2 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2H2zm0 1h12a1 1 0 0 1 1 1v1H1V3a1 1 0 0 1 1-1zM1 6h14v7a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6zm2 2v2h2V8H3zm4 0v2h2V8H7z"/></svg>
                                    @elseif($team->slug == 'backend')
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16"><path d="M4 11a1 1 0 1 1 2 0v1a1 1 0 1 1-2 0v-1zm5-4a1 1 0 1 1 2 0v5a1 1 0 1 1-2 0V7zM4 3a1 1 0 1 1 2 0v3a1 1 0 1 1-2 0V3z"/><path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z"/></svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16"><path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/><path d="M6.854 4.646a.5.5 0 0 1 0 .708L5.207 7l1.647 1.646a.5.5 0 0 1-.708.708l-2-2a.5.5 0 0 1 0-.708l2-2a.5.5 0 0 1 .708 0zM9.146 4.646a.5.5 0 0 0 0 .708L10.793 7l-1.647 1.646a.5.5 0 0 0 .708.708l2-2a.5.5 0 0 0 0-.708l-2-2a.5.5 0 0 0-.708 0z"/></svg>
                                    @endif
                                </div>
                                <div>
                                    <h5 class="mb-1 text-dark font-outfit">{{ $team->name }}</h5>
                                    <span class="text-secondary small">{{ $team->team_members_count }} Members</span>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <span class="text-dark small font-semibold">Lead:</span>
                                <span class="text-secondary small">{{ $team->lead_id ? 'Assigned' : 'Unassigned' }}</span>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-secondary small">Productivity</span>
                                    <span class="text-dark font-semibold small">N/A</span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-{{ $team->status_color }}" role="progressbar" style="width: 0%"></div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top border-secondary-subtle">
                                <span class="text-secondary small">Tasks: <span class="text-dark font-semibold">N/A</span></span>
                                <span class="badge bg-{{ $team->status_color }} bg-opacity-10 text-{{ $team->status_color }} border border-{{ $team->status_color }} border-opacity-20 rounded-pill px-3">{{ $team->status }}</span>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Create Team Modal -->
<div class="modal fade" id="createTeamModal" tabindex="-1" aria-labelledby="createTeamModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 rounded-4 shadow">
      <div class="modal-header border-bottom-0 pb-0">
        <h5 class="modal-title font-outfit" id="createTeamModalLabel">Create New Team</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('manager.teams.store') }}" method="POST">
        @csrf
        <div class="modal-body">
            <div class="mb-3">
                <label class="form-label text-secondary small">Team Name</label>
                <input type="text" name="name" class="form-control rounded-3" placeholder="e.g. Design Team" required>
            </div>
            <div class="mb-3">
                <label class="form-label text-secondary small">Description</label>
                <textarea name="description" class="form-control rounded-3" rows="3" placeholder="What does this team do?"></textarea>
            </div>
        </div>
        <div class="modal-footer border-top-0 pt-0">
            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary rounded-pill px-4">Create Team</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection
