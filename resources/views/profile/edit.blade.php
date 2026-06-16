@extends('layouts.manager')

@section('title', 'Manage Profile - Manager Agent')
@section('page_title', 'User Profile Settings')

@section('content')
<div class="row justify-content-center animate-fade-in-up">
    <div class="col-lg-10">
        
        <!-- Header area -->
        <div class="row g-4 align-items-center mb-4">
            <div class="col-12">
                <h2 class="h3 font-outfit text-white mb-1">Account Settings</h2>
                <p class="text-secondary small mb-0">Update your account profile information and security settings.</p>
            </div>
        </div>

        @if (session('status') === 'profile-updated')
            <div class="alert alert-success alert-dismissible fade show border-0 rounded-4 p-3 mb-4 text-white" style="background-color: rgba(16, 185, 129, 0.15); border-left: 4px solid #10b981 !important;" role="alert">
                <div class="d-flex align-items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="text-emerald-400 me-2" viewBox="0 0 16 16">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                    </svg>
                    <span class="text-emerald-300">Profile information updated successfully!</span>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('status') === 'password-updated')
            <div class="alert alert-success alert-dismissible fade show border-0 rounded-4 p-3 mb-4 text-white" style="background-color: rgba(16, 185, 129, 0.15); border-left: 4px solid #10b981 !important;" role="alert">
                <div class="d-flex align-items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="text-emerald-400 me-2" viewBox="0 0 16 16">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                    </svg>
                    <span class="text-emerald-300">Password updated successfully!</span>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-4 mb-4">
            <!-- Left Card: Profile Information -->
            <div class="col-md-6">
                <div class="card glass-card p-4 h-100 border border-slate-800">
                    <h4 class="h5 font-outfit text-white mb-2">Profile Information</h4>
                    <p class="text-secondary small mb-4">Update your account's profile name and email address.</p>
                    
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('patch')

                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" name="name" id="name" class="form-control rounded-3" value="{{ old('name', $user->name) }}" required autocomplete="name">
                            @error('name', 'updateProfileInformation')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control rounded-3" value="{{ old('email', $user->email) }}" required autocomplete="username">
                            @error('email', 'updateProfileInformation')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex align-items-center gap-3">
                            <button type="submit" class="btn btn-primary accent-btn px-4 rounded-3">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Card: Update Password -->
            <div class="col-md-6">
                <div class="card glass-card p-4 h-100 border border-slate-800">
                    <h4 class="h5 font-outfit text-white mb-2">Update Password</h4>
                    <p class="text-secondary small mb-4">Ensure your account is using a secure password to stay protected.</p>
                    
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        @method('put')

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" name="current_password" id="current_password" class="form-control rounded-3" required autocomplete="current-password">
                            @error('current_password', 'updatePassword')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" name="password" id="password" class="form-control rounded-3" required autocomplete="new-password">
                            @error('password', 'updatePassword')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control rounded-3" required autocomplete="new-password">
                            @error('password_confirmation', 'updatePassword')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex align-items-center gap-3">
                            <button type="submit" class="btn btn-primary accent-btn px-4 rounded-3">Update Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Danger Zone Card: Delete Account -->
        <div class="card glass-card p-4 border border-rose-950/40 bg-rose-950 bg-opacity-10 rounded-4">
            <h4 class="h5 font-outfit text-white mb-2 d-flex align-items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="text-danger me-2" viewBox="0 0 16 16">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                    <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/>
                </svg>
                Danger Zone
            </h4>
            <p class="text-secondary small mb-4">Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.</p>
            
            <div>
                <button type="button" class="btn btn-danger rounded-3 px-4" data-bs-toggle="modal" data-bs-target="#confirmUserDeletionModal">
                    Delete Account
                </button>
            </div>
        </div>

    </div>
</div>

<!-- Confirm Deletion Modal -->
<div class="modal fade" id="confirmUserDeletionModal" tabindex="-1" aria-labelledby="confirmUserDeletionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-white border border-rose-900 rounded-4" style="background-color: #0f172a;">
            <form method="POST" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')
                
                <div class="modal-header border-bottom border-slate-800 p-4">
                    <h5 class="modal-title font-outfit font-bold text-rose-400" id="confirmUserDeletionModalLabel">Are you sure you want to delete your account?</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body p-4">
                    <p class="text-slate-300 small mb-4">Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.</p>
                    
                    <div class="mb-3">
                        <label for="delete_password" class="form-label">Password</label>
                        <input type="password" name="password" id="delete_password" class="form-control rounded-3" placeholder="Enter your password" required>
                        @error('password', 'userDeletion')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="modal-footer border-top border-slate-800 p-4">
                    <button type="button" class="btn btn-secondary rounded-3 px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-3 px-4">Delete Account</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
