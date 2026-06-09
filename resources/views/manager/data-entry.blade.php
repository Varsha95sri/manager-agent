@extends('layouts.manager')
<!-- resources/views/manager/data-entry.blade.php -->

@section('title', 'Manual Data Logger - Manager Agent')
@section('page_title', 'Manual Data Logger')

@php
    // Read the active tab from session, default to members
    $activeTab = session('active_tab', 'members');
@endphp

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        
        <div class="mb-4">
            <h2 class="h3 font-outfit text-white mb-1">Manual Data Entry</h2>
            <p class="text-secondary small">Log details to simulate various team activity scenarios and evaluate productivity indices.</p>
        </div>

        <!-- Tabs Navigation -->
        <ul class="nav nav-pills mb-4 gap-2 border-bottom border-slate-800 pb-3" id="entryTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="btn btn-outline-secondary rounded-3 text-white px-4 py-2 border-slate-800 {{ $activeTab === 'members' ? 'active bg-primary border-0' : '' }}" 
                        id="members-tab" data-bs-toggle="pill" data-bs-target="#members" type="button" role="tab" aria-controls="members" aria-selected="{{ $activeTab === 'members' ? 'true' : 'false' }}">
                    Team Members
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="btn btn-outline-secondary rounded-3 text-white px-4 py-2 border-slate-800 {{ $activeTab === 'tasks' ? 'active bg-primary border-0' : '' }}" 
                        id="tasks-tab" data-bs-toggle="pill" data-bs-target="#tasks" type="button" role="tab" aria-controls="tasks" aria-selected="{{ $activeTab === 'tasks' ? 'true' : 'false' }}">
                    Tasks
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="btn btn-outline-secondary rounded-3 text-white px-4 py-2 border-slate-800 {{ $activeTab === 'commits' ? 'active bg-primary border-0' : '' }}" 
                        id="commits-tab" data-bs-toggle="pill" data-bs-target="#commits" type="button" role="tab" aria-controls="commits" aria-selected="{{ $activeTab === 'commits' ? 'true' : 'false' }}">
                    Git Commits
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="btn btn-outline-secondary rounded-3 text-white px-4 py-2 border-slate-800 {{ $activeTab === 'attendance' ? 'active bg-primary border-0' : '' }}" 
                        id="attendance-tab" data-bs-toggle="pill" data-bs-target="#attendance" type="button" role="tab" aria-controls="attendance" aria-selected="{{ $activeTab === 'attendance' ? 'true' : 'false' }}">
                    Attendance Logs
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="btn btn-outline-secondary rounded-3 text-white px-4 py-2 border-slate-800 {{ $activeTab === 'meetings' ? 'active bg-primary border-0' : '' }}" 
                        id="meetings-tab" data-bs-toggle="pill" data-bs-target="#meetings" type="button" role="tab" aria-controls="meetings" aria-selected="{{ $activeTab === 'meetings' ? 'true' : 'false' }}">
                    Meeting Notes
                </button>
            </li>
        </ul>

        <!-- Tab Contents -->
        <div class="tab-content card glass-card p-4 p-md-5 border-light border-opacity-10 position-relative overflow-hidden" id="entryTabsContent">
            <!-- Top visual accent line -->
            <div class="position-absolute top-0 start-0 w-100 bg-gradient" style="height: 4px; background: linear-gradient(90deg, #a855f7, #6366f1) !important;"></div>

            <!-- Tab 1: Team Members -->
            <div class="tab-pane fade {{ $activeTab === 'members' ? 'show active' : '' }}" id="members" role="tabpanel" aria-labelledby="members-tab">
                <form method="POST" action="{{ route('manager.store-team-member') }}" class="needs-validation">
                    @csrf
                    <h3 class="h4 font-outfit text-white mb-4 pb-2 border-bottom border-slate-800">Add Team Member</h3>
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-slate-400 small font-bold text-uppercase tracking-wider">Full Name</label>
                            <input type="text" name="name" class="form-control border-slate-700 bg-slate-900 text-white rounded-3 px-3 py-2.5 @error('name') is-invalid @enderror" placeholder="e.g. Rahul Kumar" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-slate-400 small font-bold text-uppercase tracking-wider">Email Address</label>
                            <input type="email" name="email" class="form-control border-slate-700 bg-slate-900 text-white rounded-3 px-3 py-2.5 @error('email') is-invalid @enderror" placeholder="e.g. rahul@company.com" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-slate-400 small font-bold text-uppercase tracking-wider">Designated Role</label>
                            <input type="text" name="role" class="form-control border-slate-700 bg-slate-900 text-white rounded-3 px-3 py-2.5 @error('role') is-invalid @enderror" placeholder="e.g. Backend Dev, Designer, DevOps" value="{{ old('role') }}" required>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-slate-400 small font-bold text-uppercase tracking-wider">GitHub Username / ID</label>
                            <input type="text" name="github_id" class="form-control border-slate-700 bg-slate-900 text-white rounded-3 px-3 py-2.5 @error('github_id') is-invalid @enderror" placeholder="e.g. rahul-dev" value="{{ old('github_id') }}">
                            @error('github_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn accent-btn px-4 py-2.5">Add Team Member</button>
                    </div>
                </form>
            </div>

            <!-- Tab 2: Tasks -->
            <div class="tab-pane fade {{ $activeTab === 'tasks' ? 'show active' : '' }}" id="tasks" role="tabpanel" aria-labelledby="tasks-tab">
                <form method="POST" action="{{ route('manager.store-task') }}">
                    @csrf
                    <h3 class="h4 font-outfit text-white mb-4 pb-2 border-bottom border-slate-800">Assign New Task</h3>
                    <div class="mb-4">
                        <label class="form-label text-slate-400 small font-bold text-uppercase tracking-wider">Team Member</label>
                        <select name="team_member_id" class="form-select border-slate-700 bg-slate-900 text-white rounded-3 px-3 py-2.5 @error('team_member_id') is-invalid @enderror" required>
                            <option value="">Select a member...</option>
                            @foreach($teamMembers as $m)
                                <option value="{{ $m->id }}" {{ old('team_member_id') == $m->id ? 'selected' : '' }}>{{ $m->name }} ({{ $m->role }})</option>
                            @endforeach
                        </select>
                        @error('team_member_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-slate-400 small font-bold text-uppercase tracking-wider">Task Title / Details</label>
                        <input type="text" name="title" class="form-control border-slate-700 bg-slate-900 text-white rounded-3 px-3 py-2.5 @error('title') is-invalid @enderror" placeholder="e.g. Implement Oauth callback views" value="{{ old('title') }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-slate-400 small font-bold text-uppercase tracking-wider">Status</label>
                            <select name="status" class="form-select border-slate-700 bg-slate-900 text-white rounded-3 px-3 py-2.5 @error('status') is-invalid @enderror" required>
                                <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="in_progress" {{ old('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-slate-400 small font-bold text-uppercase tracking-wider">Due Date</label>
                            <input type="date" name="due_date" class="form-control border-slate-700 bg-slate-900 text-white rounded-3 px-3 py-2.5 @error('due_date') is-invalid @enderror" value="{{ old('due_date') }}" required>
                            @error('due_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn accent-btn px-4 py-2.5">Log Task</button>
                    </div>
                </form>
            </div>

            <!-- Tab 3: Git Commits -->
            <div class="tab-pane fade {{ $activeTab === 'commits' ? 'show active' : '' }}" id="commits" role="tabpanel" aria-labelledby="commits-tab">
                <form method="POST" action="{{ route('manager.store-commit') }}">
                    @csrf
                    <h3 class="h4 font-outfit text-white mb-4 pb-2 border-bottom border-slate-800">Log Git Commit</h3>
                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <label class="form-label text-slate-400 small font-bold text-uppercase tracking-wider">Author Member</label>
                            <select name="team_member_id" class="form-select border-slate-700 bg-slate-900 text-white rounded-3 px-3 py-2.5 @error('team_member_id') is-invalid @enderror" required>
                                <option value="">Select a member...</option>
                                @foreach($teamMembers as $m)
                                    <option value="{{ $m->id }}" {{ old('team_member_id') == $m->id ? 'selected' : '' }}>{{ $m->name }} ({{ $m->role }})</option>
                                @endforeach
                            </select>
                            @error('team_member_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-slate-400 small font-bold text-uppercase tracking-wider">Commit Hash</label>
                            <input type="text" name="commit_hash" class="form-control border-slate-700 bg-slate-900 text-white rounded-3 px-3 py-2.5 @error('commit_hash') is-invalid @enderror" placeholder="e.g. 7f1a20c" value="{{ old('commit_hash') }}" required>
                            @error('commit_hash')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-slate-400 small font-bold text-uppercase tracking-wider">Repository Name</label>
                            <input type="text" name="repository_name" class="form-control border-slate-700 bg-slate-900 text-white rounded-3 px-3 py-2.5 @error('repository_name') is-invalid @enderror" placeholder="e.g. manager-agent" value="{{ old('repository_name', 'manager-agent') }}" required>
                            @error('repository_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-slate-400 small font-bold text-uppercase tracking-wider">Commit Message</label>
                        <input type="text" name="message" class="form-control border-slate-700 bg-slate-900 text-white rounded-3 px-3 py-2.5 @error('message') is-invalid @enderror" placeholder="e.g. feat: core db triggers for report analytics" value="{{ old('message') }}" required>
                        @error('message')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-slate-400 small font-bold text-uppercase tracking-wider">Committed At (DateTime)</label>
                        <input type="datetime-local" name="committed_at" class="form-control border-slate-700 bg-slate-900 text-white rounded-3 px-3 py-2.5 @error('committed_at') is-invalid @enderror" value="{{ old('committed_at', date('Y-m-d\TH:i')) }}" required>
                        @error('committed_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn accent-btn px-4 py-2.5">Log Commit</button>
                    </div>
                </form>
            </div>

            <!-- Tab 4: Attendance Logs -->
            <div class="tab-pane fade {{ $activeTab === 'attendance' ? 'show active' : '' }}" id="attendance" role="tabpanel" aria-labelledby="attendance-tab">
                <form method="POST" action="{{ route('manager.store-attendance') }}">
                    @csrf
                    <h3 class="h4 font-outfit text-white mb-4 pb-2 border-bottom border-slate-800">Log Daily Attendance</h3>
                    <div class="mb-4">
                        <label class="form-label text-slate-400 small font-bold text-uppercase tracking-wider">Team Member</label>
                        <select name="team_member_id" class="form-select border-slate-700 bg-slate-900 text-white rounded-3 px-3 py-2.5 @error('team_member_id') is-invalid @enderror" required>
                            <option value="">Select a member...</option>
                            @foreach($teamMembers as $m)
                                <option value="{{ $m->id }}" {{ old('team_member_id') == $m->id ? 'selected' : '' }}>{{ $m->name }} ({{ $m->role }})</option>
                            @endforeach
                        </select>
                        @error('team_member_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-slate-400 small font-bold text-uppercase tracking-wider">Log Date</label>
                            <input type="date" name="date" class="form-control border-slate-700 bg-slate-900 text-white rounded-3 px-3 py-2.5 @error('date') is-invalid @enderror" value="{{ old('date', date('Y-m-d')) }}" required>
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-slate-400 small font-bold text-uppercase tracking-wider">Status</label>
                            <select name="status" class="form-select border-slate-700 bg-slate-900 text-white rounded-3 px-3 py-2.5 @error('status') is-invalid @enderror" required>
                                <option value="present" {{ old('status') === 'present' ? 'selected' : '' }}>Present</option>
                                <option value="late" {{ old('status') === 'late' ? 'selected' : '' }}>Late</option>
                                <option value="absent" {{ old('status') === 'absent' ? 'selected' : '' }}>Absent</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-slate-400 small font-bold text-uppercase tracking-wider">Check-in Time (Optional)</label>
                        <input type="time" name="check_in" class="form-control border-slate-700 bg-slate-900 text-white rounded-3 px-3 py-2.5 @error('check_in') is-invalid @enderror" value="{{ old('check_in') }}">
                        @error('check_in')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn accent-btn px-4 py-2.5">Log Attendance</button>
                    </div>
                </form>
            </div>

            <!-- Tab 5: Meeting Notes -->
            <div class="tab-pane fade {{ $activeTab === 'meetings' ? 'show active' : '' }}" id="meetings" role="tabpanel" aria-labelledby="meetings-tab">
                <form method="POST" action="{{ route('manager.store-meeting') }}">
                    @csrf
                    <h3 class="h4 font-outfit text-white mb-4 pb-2 border-bottom border-slate-800">Record Meeting Summary</h3>
                    <div class="row g-4 mb-4">
                        <div class="col-md-8">
                            <label class="form-label text-slate-400 small font-bold text-uppercase tracking-wider">Meeting Title</label>
                            <input type="text" name="title" class="form-control border-slate-700 bg-slate-900 text-white rounded-3 px-3 py-2.5 @error('title') is-invalid @enderror" placeholder="e.g. Morning status check sync" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-slate-400 small font-bold text-uppercase tracking-wider">Meeting Date</label>
                            <input type="date" name="meeting_date" class="form-control border-slate-700 bg-slate-900 text-white rounded-3 px-3 py-2.5 @error('meeting_date') is-invalid @enderror" value="{{ old('meeting_date', date('Y-m-d')) }}" required>
                            @error('meeting_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-slate-400 small font-bold text-uppercase tracking-wider">Discussion Notes & Decisions</label>
                        <textarea name="notes" rows="6" class="form-control border-slate-700 bg-slate-900 text-white rounded-3 px-3 py-2.5 @error('notes') is-invalid @enderror" placeholder="Enter key details from the meeting, topics discussed, resolutions..." required>{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn accent-btn px-4 py-2.5">Save Meeting Note</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
