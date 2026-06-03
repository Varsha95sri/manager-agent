<!DOCTYPE html>
<!-- resources/views/layouts/manager.blade.php -->
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Manager Agent')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom Dark Styles -->
    <style>
        :root {
            --bg-color: #0f172a;
            --card-bg: #1e293b;
            --text-color: #ffffff;
            --accent-color: #a855f7;
            --accent-hover: #b55fe6;
            --border-color: #334155;
            --text-muted: #94a3b8;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        h1, h2, h3, h4, h5, h6, .font-outfit {
            font-family: 'Outfit', sans-serif;
        }

        /* Sidebar styling */
        .sidebar {
            background-color: #0b0f19;
            border-right: 1px solid var(--border-color);
            min-height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
            transition: all 0.3s;
        }

        .sidebar-brand {
            padding: 1.5rem 1.25rem;
            border-bottom: 1px solid #1e293b;
            font-weight: 800;
            letter-spacing: -0.025em;
            color: var(--text-color);
        }

        .sidebar-brand-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: linear-gradient(135deg, #a855f7, #6366f1);
            margin-right: 10px;
        }

        .sidebar-nav {
            padding: 1.25rem 0.75rem;
            list-style: none;
            margin: 0;
        }

        .sidebar-nav-item {
            margin-bottom: 0.25rem;
        }

        .sidebar-nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: var(--text-muted);
            font-weight: 500;
            font-size: 0.875rem;
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.2s;
        }

        .sidebar-nav-link:hover {
            color: var(--text-color);
            background-color: rgba(255, 255, 255, 0.04);
        }

        .sidebar-nav-link.active {
            color: var(--text-color);
            background-color: var(--card-bg);
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: inset 0 1px 0 0 rgba(255, 255, 255, 0.05);
        }

        .sidebar-nav-link svg {
            margin-right: 12px;
            flex-shrink: 0;
        }

        /* Main Content wrapper */
        .wrapper {
            margin-left: 250px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            width: calc(100% - 250px);
            transition: all 0.3s;
        }

        /* Header Navbar */
        .header-nav {
            background-color: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border-color);
            height: 70px;
            display: flex;
            align-items: center;
            padding: 0 2rem;
            position: sticky;
            top: 0;
            z-index: 90;
        }

        /* Cards & Containers */
        .glass-card {
            background-color: var(--card-bg);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3), 0 4px 6px -4px rgba(0, 0, 0, 0.3);
            margin-bottom: 1.5rem;
        }

        .accent-btn {
            background: linear-gradient(135deg, var(--accent-color), #6366f1);
            color: white;
            border: none;
            font-weight: 600;
            border-radius: 12px;
            padding: 0.625rem 1.25rem;
            transition: all 0.3s;
            box-shadow: 0 4px 6px -1px rgba(168, 85, 247, 0.2);
        }

        .accent-btn:hover {
            opacity: 0.95;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(168, 85, 247, 0.3);
        }

        .accent-text {
            color: var(--accent-color);
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #0f172a;
        }
        ::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #475569;
        }

        /* Responsive sidebar settings */
        @media (max-width: 991.98px) {
            .sidebar {
                margin-left: -250px;
            }
            .sidebar.active {
                margin-left: 0;
            }
            .wrapper {
                margin-left: 0;
                width: 100%;
            }
            .sidebar-toggle-btn {
                display: block !important;
            }
        }

        /* High Contrast Styling Overrides for Forms and Tables */
        .form-control, .form-select {
            background-color: #0f172a !important;
            color: #ffffff !important;
            border: 1px solid var(--border-color) !important;
        }
        .form-control:focus, .form-select:focus {
            background-color: #0f172a !important;
            color: #ffffff !important;
            border-color: var(--accent-color) !important;
            box-shadow: 0 0 0 0.25rem rgba(168, 85, 247, 0.25) !important;
        }
        .form-control::placeholder {
            color: var(--text-muted) !important;
        }
        .form-select option {
            background-color: #0f172a;
            color: #ffffff;
        }
        .form-label {
            color: var(--text-muted) !important;
            font-weight: 600;
        }
        .table {
            color: var(--text-color) !important;
        }
        .table th {
            color: var(--text-muted) !important;
            border-bottom: 1px solid var(--border-color) !important;
        }
        .table td {
            color: #e2e8f0 !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05) !important;
        }
        
        /* Premium Theme Overrides */
        .bg-primary, .btn-primary, .nav-pills .btn.active {
            background: linear-gradient(135deg, var(--accent-color), #6366f1) !important;
            color: #ffffff !important;
            border: none !important;
        }
        .btn-primary:hover, .nav-pills .btn.active:hover {
            opacity: 0.95 !important;
        }
        .nav-pills .btn {
            border: 1px solid var(--border-color) !important;
            transition: all 0.2s ease;
        }
        .nav-pills .btn:hover:not(.active) {
            background-color: rgba(255, 255, 255, 0.04) !important;
            border-color: var(--text-muted) !important;
        }
        .text-secondary {
            color: #94a3b8 !important;
        }

        /* Slate color utility fallbacks */
        .text-slate-100 { color: #f8fafc !important; }
        .text-slate-200 { color: #e2e8f0 !important; }
        .text-slate-300 { color: #cbd5e1 !important; }
        .text-slate-400 { color: #94a3b8 !important; }
        .bg-slate-900 { background-color: #0b0f19 !important; }
        .bg-slate-950 { background-color: #020617 !important; }
        .border-slate-700 { border-color: #334155 !important; }
        .border-slate-800 { border-color: #1e293b !important; }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Sidebar Navigation -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand d-flex align-items-center">
            <div class="sidebar-brand-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="text-white" viewBox="0 0 16 16">
                    <path d="M6 12.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5ZM3 8.062C3 6.7 4 5.65 5.5 5.65h5c1.5 0 2.5 1.05 2.5 2.412v3.838a1.5 1.5 0 0 1-1.5 1.5h-8A1.5 1.5 0 0 1 3 11.9V8.062Zm2.5-.912C4.338 7.15 3.5 7.9 3.5 9v1.5a.5.5 0 0 0 1 0v-1.5c0-.276.224-.5.5-.5h5c.276 0 .5.224.5.5v1.5a.5.5 0 0 0 1 0v-1.5c0-1.1-.838-1.85-2-1.85h-5Z"/>
                    <path d="M8 2a3 3 0 0 0-3 3 .5.5 0 0 0 1 0 2 2 0 1 1 4 0 .5.5 0 0 0 1 0 3 3 0 0 0-3-3Z"/>
                </svg>
            </div>
            <span>Manager Agent</span>
        </div>

        <ul class="sidebar-nav">
            <li class="sidebar-nav-item">
                <a href="{{ route('manager.dashboard') }}" class="sidebar-nav-link {{ request()->routeIs('manager.dashboard') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                    </svg>
                    Dashboard
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="{{ route('manager.chatbot') }}" class="sidebar-nav-link {{ request()->routeIs('manager.chatbot') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    AI Chatbot
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="{{ route('manager.data-entry') }}" class="sidebar-nav-link {{ request()->routeIs('manager.data-entry') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Data Entry
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="{{ route('manager.reports') }}" class="sidebar-nav-link {{ request()->routeIs('manager.reports') || request()->routeIs('manager.report-detail') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Reports History
                </a>
            </li>
            <li class="sidebar-nav-item mt-4 pt-4 border-top border-slate-800">
                <form method="POST" action="{{ route('logout') }}" id="logout-form" class="d-none">
                    @csrf
                </form>
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="sidebar-nav-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Log Out
                </a>
            </li>
        </ul>
    </div>

    <!-- Wrapper content -->
    <div class="wrapper">
        <!-- Top Navbar -->
        <header class="header-nav d-flex justify-content-between">
            <div class="d-flex align-items-center">
                <button class="btn btn-outline-light d-lg-none me-3 sidebar-toggle-btn" id="sidebarToggle" style="display:none;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
                    </svg>
                </button>
                <h4 class="m-0 font-outfit text-slate-100">@yield('page_title', 'Dashboard')</h4>
            </div>
            
            <div class="d-flex align-items-center space-x-3">
                <div class="text-end me-3 d-none d-sm-block">
                    <div class="text-xs text-muted font-bold text-uppercase">Manager Interface</div>
                    <div class="text-sm font-semibold text-slate-200">{{ Auth::user()?->name ?? 'Varsha Manager' }}</div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="container-fluid p-4 flex-grow-1">
            <!-- Flash Message Alerts -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 rounded-4 p-3 mb-4 text-white" style="background-color: rgba(16, 185, 129, 0.15); border-left: 4px solid #10b981 !important;" role="alert">
                    <div class="d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="text-emerald-400 me-2" viewBox="0 0 16 16">
                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                        </svg>
                        <span class="text-emerald-300">{{ session('success') }}</span>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 rounded-4 p-3 mb-4 text-white" style="background-color: rgba(244, 63, 94, 0.15); border-left: 4px solid #f43f5e !important;" role="alert">
                    <div class="d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="text-rose-400 me-2" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                            <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/>
                        </svg>
                        <span class="text-rose-300">{{ session('error') }}</span>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="py-4 border-top border-slate-800 text-center text-xs text-secondary bg-slate-950/20">
            &copy; {{ date('Y') }} Manager Agent. Powered by Claude Sonnet.
        </footer>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Sidebar Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            if(toggle && sidebar) {
                toggle.addEventListener('click', function() {
                    sidebar.classList.toggle('active');
                });
            }
        });
    </script>
    @yield('scripts')
</body>
</html>
