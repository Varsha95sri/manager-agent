@extends('layouts.manager')

@section('title', 'My Commits')
@section('page_title', 'My Commits')

@section('content')
<div class="container-fluid">
    <div class="card kpi-card premium-shadow mb-4">
        <div class="card-header bg-transparent border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">My Recent Git Commits</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Commit Message</th>
                            <th>Project</th>
                            <th>Branch</th>
                            <th>Date</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($commits as $commit)
                        <tr>
                            <td>
                                <strong class="text-dark d-block">{{ Str::limit($commit->message, 60) }}</strong>
                                <small class="text-muted font-monospace">{{ substr($commit->commit_sha ?? $commit->commit_id ?? 'N/A', 0, 8) }}</small>
                            </td>
                            <td>
                                @if($commit->project)
                                    <span class="badge bg-primary bg-opacity-10 text-primary">{{ $commit->project->name }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary"><i class="fa-solid fa-code-branch me-1"></i> {{ $commit->branch ?? 'main' }}</span>
                            </td>
                            <td>
                                {{ \Carbon\Carbon::parse($commit->committed_at ?? $commit->created_at)->format('M d, Y h:i A') }}
                            </td>
                            <td class="text-end">
                                @if($commit->commit_url)
                                    <a href="{{ $commit->commit_url }}" target="_blank" class="btn btn-sm btn-outline-primary">View <i class="fa-solid fa-external-link-alt ms-1"></i></a>
                                @else
                                    <button class="btn btn-sm btn-outline-secondary" disabled>No Link</button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No commits recorded yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($commits->hasPages())
            <div class="d-flex justify-content-end mt-4">
                {{ $commits->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
