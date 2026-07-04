@extends('layouts.manager')

@section('title', 'My Projects')
@section('page_title', 'My Projects')

@section('content')
<div class="container-fluid">
    <div class="card kpi-card premium-shadow mb-4">
        <div class="card-header bg-transparent border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">Projects I am Assigned To</h5>
        </div>
        <div class="card-body">
            <div class="row g-4 mt-1">
                @forelse($allocations as $alloc)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 hover-card">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="icon-circle bg-primary bg-opacity-10 text-primary" style="width: 48px; height: 48px;">
                                    <i class="fa-solid fa-folder-tree fs-5"></i>
                                </div>
                                <span class="badge bg-{{ strtolower($alloc->project->status) == 'active' ? 'success' : 'secondary' }} bg-opacity-10 text-{{ strtolower($alloc->project->status) == 'active' ? 'success' : 'secondary' }} px-3 py-2 rounded-pill">
                                    {{ $alloc->project->status }}
                                </span>
                            </div>
                            
                            <h5 class="font-outfit text-dark fw-bold mb-2">{{ $alloc->project->name }}</h5>
                            <p class="text-secondary small mb-4" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ $alloc->project->description ?? 'No description provided.' }}
                            </p>
                            
                            <div class="p-3 bg-light rounded-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-secondary small fw-semibold">My Role:</span>
                                    <span class="text-dark fw-bold">{{ $alloc->role }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-secondary small fw-semibold">Allocation:</span>
                                    <span class="badge bg-primary rounded-pill">{{ $alloc->allocation_percentage }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5">
                    <div class="icon-circle bg-light text-muted mx-auto mb-3" style="width: 64px; height: 64px;">
                        <i class="fa-solid fa-folder-open fs-3"></i>
                    </div>
                    <h5 class="text-secondary">No Projects Assigned</h5>
                    <p class="text-muted">You are not currently assigned to any active projects.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
