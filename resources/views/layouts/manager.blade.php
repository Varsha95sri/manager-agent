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
    
    <!-- FontAwesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom Dark Styles -->
    <style>
        :root {
            --bg-color: #f8fafc;
            --card-bg: #ffffff;
            --text-color: #1e293b;
            --accent-color: #4f46e5;
            --accent-hover: #4338ca;
            --border-color: #e2e8f0;
            --text-muted: #64748b;
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
            background-color: var(--card-bg);
            border-right: 1px solid var(--border-color);
            height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
            transition: all 0.3s;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }

        .sidebar::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .sidebar-brand {
            padding: 1.5rem 1.25rem;
            border-bottom: 1px solid var(--border-color);
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
            color: var(--accent-color);
            background-color: #f5f3ff;
        }

        .sidebar-nav-link.active {
            color: var(--accent-color);
            background-color: #f5f3ff;
            font-weight: 600;
        }

        .sidebar-nav-link svg {
            margin-right: 12px;
            flex-shrink: 0;
        }

        .sidebar-heading {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 700;
            color: #94a3b8;
            margin-top: 1.25rem;
            margin-bottom: 0.5rem;
            padding: 0 1.25rem;
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
            background-color: rgba(255, 255, 255, 0.9);
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
            border: 1px solid var(--border-color);
            border-radius: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
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
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
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

        /* Light Theme Overrides for Forms and Tables */
        .form-control, .form-select {
            background-color: #ffffff;
            color: #1e293b;
            border: 1px solid var(--border-color);
        }
        .form-control:focus, .form-select:focus {
            background-color: #ffffff;
            color: #1e293b;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.25rem rgba(79, 70, 229, 0.25);
        }
        .form-control::placeholder {
            color: #94a3b8;
        }
        .form-select option {
            background-color: #ffffff;
            color: #1e293b;
        }
        .form-label {
            color: #475569;
            font-weight: 600;
        }
        .table {
            color: var(--text-color);
        }
        .table th {
            color: var(--text-muted);
            border-bottom: 1px solid var(--border-color);
        }
        .table td {
            color: var(--text-color);
            border-bottom: 1px solid var(--border-color);
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
            color: var(--text-color);
        }
        .nav-pills .btn:hover:not(.active) {
            background-color: #f1f5f9 !important;
            border-color: #cbd5e1 !important;
        }
        .text-secondary {
            color: #64748b !important;
        }

        /* Premium Card styling enhancements */
        .hover-card {
            transition: transform 0.22s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.22s cubic-bezier(0.4, 0, 0.2, 1), border-color 0.22s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .hover-card:hover {
            transform: translateY(-4px) !important;
            border-color: rgba(168, 85, 247, 0.4) !important;
            box-shadow: 0 12px 24px -4px rgba(0, 0, 0, 0.1), 0 4px 20px -2px rgba(168, 85, 247, 0.15) !important;
        }

        /* Floating AI Chatbot widget styles */
        .floating-chat-btn {
            position: fixed;
            bottom: 25px;
            right: 25px;
            z-index: 9999;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: linear-gradient(135deg, #a855f7, #6366f1);
            border: none;
            color: #ffffff;
            box-shadow: 0 4px 16px rgba(168, 85, 247, 0.4);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            outline: none !important;
        }
        .floating-chat-btn:hover {
            transform: scale(1.08);
            box-shadow: 0 6px 20px rgba(168, 85, 247, 0.6);
        }
        .floating-chat-window {
            position: fixed;
            bottom: 95px;
            right: 25px;
            z-index: 9999;
            width: 380px;
            max-width: calc(100vw - 50px);
            height: 500px;
            max-height: calc(100vh - 120px);
            background-color: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .floating-chat-header {
            background: linear-gradient(135deg, var(--bg-color), #ffffff);
            padding: 15px 20px;
            border-bottom: 1px solid var(--border-color);
        }
        .floating-chat-body {
            padding: 15px 20px;
            overflow-y: auto;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            background-color: #f8fafc;
        }
        .floating-chat-footer {
            border-top: 1px solid var(--border-color);
            padding: 12px 15px;
            background-color: #ffffff;
        }
        
        /* Message Bubbles */
        .chat-message {
            display: flex;
            flex-direction: column;
            max-width: 80%;
            margin-bottom: 10px;
        }
        .chat-message.user {
            align-self: flex-end;
            align-items: flex-end;
        }
        .chat-message.assistant {
            align-self: flex-start;
            align-items: flex-start;
        }
        .message-bubble {
            padding: 10px 14px;
            border-radius: 12px;
            font-size: 13px;
            line-height: 1.45;
            word-break: break-word;
        }
        .chat-message.user .message-bubble {
            background-color: #a855f7;
            color: #ffffff;
            border-bottom-right-radius: 2px;
        }
        .chat-message.assistant .message-bubble {
            background-color: #ffffff;
            color: #1e293b;
            border-bottom-left-radius: 2px;
            border: 1px solid var(--border-color);
        }
        .message-time {
            font-size: 9px;
            color: #64748b;
            margin-top: 4px;
            padding: 0 4px;
        }
        
        /* Custom Dropdown Styling */
        .dropdown-menu-custom {
            background-color: #ffffff !important;
            border: 1px solid var(--border-color) !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
            border-radius: 12px !important;
            padding: 0 !important;
            overflow: hidden;
            margin-top: 8px !important;
        }
        .dropdown-item-custom {
            color: #475569 !important;
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }
        .dropdown-item-custom:hover, .dropdown-item-custom:focus {
            background-color: #f1f5f9 !important;
            color: #1e293b !important;
        }
        .hover-light:hover {
            background-color: #f8fafc !important;
        }

        /* Custom scrollbar for content panels */
        .custom-scroll {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }
        .custom-scroll::-webkit-scrollbar {
            width: 5px;
        }
        .custom-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .custom-scroll::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Sidebar Navigation -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand d-flex align-items-center">
            <div class="sidebar-brand-icon">
                <span class="text-dark font-bold font-outfit" style="font-size: 12px; font-weight: 800;">PM</span>
            </div>
            <span>AI Manager <span style="color: #c084fc;">✨</span></span>
        </div>
        <ul class="sidebar-nav">
            <li class="sidebar-heading">Overview</li>
            <li class="sidebar-nav-item">
                <a href="{{ route('manager.dashboard') }}" class="sidebar-nav-link {{ request()->routeIs('manager.dashboard') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                    </svg>
                    Dashboard
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="{{ route('manager.analytics') }}" class="sidebar-nav-link {{ request()->routeIs('manager.analytics') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    Predictive Analytics
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="{{ route('manager.leaderboard') }}" class="sidebar-nav-link {{ request()->routeIs('manager.leaderboard') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                    Leaderboard
                </a>
            </li>

            <li class="sidebar-heading">HR & Operations</li>
            <li class="sidebar-nav-item">
                <a href="{{ route('manager.employees.index') }}" class="sidebar-nav-link {{ request()->routeIs('manager.employees.index') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Employees
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="{{ route('manager.departments.index') }}" class="sidebar-nav-link {{ request()->routeIs('manager.departments.index') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    Departments & Skills
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="{{ route('manager.attendance-registry') }}" class="sidebar-nav-link {{ request()->routeIs('manager.attendance-registry') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Daily Attendance
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="{{ route('manager.leaves.index') }}" class="sidebar-nav-link {{ request()->routeIs('manager.leaves.index') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Leave Management
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="{{ route('manager.meetings.index') }}" class="sidebar-nav-link {{ request()->routeIs('manager.meetings.index') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Meetings
                </a>
            </li>

            <li class="sidebar-heading">Projects & Code</li>
            <li class="sidebar-nav-item">
                <a href="{{ route('manager.projects.index') }}" class="sidebar-nav-link {{ request()->routeIs('manager.projects.index') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    Projects Setup
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="{{ route('manager.projects.reports') }}" class="sidebar-nav-link {{ request()->routeIs('manager.projects.reports') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Project Reports
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="{{ route('manager.task-entry') }}" class="sidebar-nav-link {{ request()->routeIs('manager.task-entry') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    Daily Tasks
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="{{ route('manager.gitlab.index') }}" class="sidebar-nav-link {{ request()->routeIs('manager.gitlab.*') || request()->routeIs('manager.repositories.index') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                    </svg>
                    Git Sync
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="{{ route('manager.commits.index') }}" class="sidebar-nav-link {{ request()->routeIs('manager.commits.index') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Git Commits
                </a>
            </li>

            <li class="sidebar-heading">System & Settings</li>
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
            <li class="sidebar-nav-item">
                <a href="{{ route('manager.audit') }}" class="sidebar-nav-link {{ request()->routeIs('manager.audit') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    Security & Audit
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="{{ route('developer.index') }}" class="sidebar-nav-link {{ request()->routeIs('developer.index') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Developer Tools
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="{{ route('profile.edit') }}" class="sidebar-nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Profile
                </a>
            </li>
            <li class="sidebar-nav-item mt-4 pt-4 border-top border-secondary-subtle">
                <form method="POST" action="{{ route('logout') }}" id="logout-form" class="d-none">
                    @csrf
                </form>
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="sidebar-nav-link text-danger">
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
        <!-- Fetch risks dynamically for notifications dropdown -->
        @php
            $latestReportsWithRisks = \App\Models\PerformanceReport::orderBy('report_date', 'desc')->take(5)->get();
            $allRisks = [];
            foreach ($latestReportsWithRisks as $report) {
                if (!empty($report->risks) && is_array($report->risks)) {
                    foreach ($report->risks as $risk) {
                        if (stripos(trim($risk), 'none') === 0 && (strlen(trim($risk)) < 6 || stripos(trim($risk), 'none.') === 0)) {
                            continue;
                        }
                        $allRisks[] = [
                            'report_id' => $report->id,
                            'date' => $report->report_date instanceof \Carbon\Carbon ? $report->report_date->format('M d, Y') : \Carbon\Carbon::parse($report->report_date)->format('M d, Y'),
                            'risk' => $risk
                        ];
                    }
                }
            }
            $riskCount = count($allRisks);
        @endphp

        <!-- Top Navbar -->
        <header class="header-nav d-flex justify-content-between">
            <div class="d-flex align-items-center">
                <button class="btn btn-outline-light d-lg-none me-3 sidebar-toggle-btn" id="sidebarToggle" style="display:none;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
                    </svg>
                </button>
                <h4 class="m-0 font-outfit text-dark">@yield('page_title', 'Dashboard')</h4>
            </div>
            
            <div class="d-flex align-items-center gap-3">
                <!-- Global Search -->
                <div class="d-none d-md-flex align-items-center bg-light rounded-pill px-3 py-1 border border-secondary-subtle me-2" style="width: 260px; transition: all 0.2s;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="text-secondary" viewBox="0 0 16 16">
                      <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                    </svg>
                    <input type="text" class="form-control border-0 bg-transparent shadow-none form-control-sm ms-2" placeholder="Search across modules..." style="font-size: 13px;">
                </div>

                <!-- Notification Bell with count -->
                <div class="dropdown">
                    <div class="position-relative me-2" style="cursor: pointer;" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="text-secondary hover:text-dark" viewBox="0 0 16 16">
                            <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2zM8 1.918l-.797.161A4.002 4.002 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4.002 4.002 0 0 0-3.203-3.92L8 1.917zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5.002 5.002 0 0 1 13 6c0 .88.32 4.2 1.22 6z"/>
                        </svg>
                        @if($riskCount > 0)
                            <span class="position-absolute badge rounded-circle bg-primary" style="font-size: 7px; padding: 2px 4px; top: -5px; right: -5px; background: linear-gradient(135deg, #a855f7, #6366f1) !important;">
                                {{ $riskCount }}
                            </span>
                        @endif
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end p-0 border-secondary-subtle shadow-xl dropdown-menu-custom" aria-labelledby="notificationDropdown" style="background-color: #ffffff; min-width: 320px; border: 1px solid var(--border-color); border-radius: 12px; max-height: 400px; overflow-y: auto;">
                        <li class="p-3 border-bottom border-secondary-subtle d-flex justify-content-between align-items-center">
                            <span class="font-outfit font-semibold text-dark" style="font-size: 14px;">Identified Risks Notification</span>
                            @if($riskCount > 0)
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-20 rounded-pill" style="font-size: 10px;">{{ $riskCount }} Alert{{ $riskCount > 1 ? 's' : '' }}</span>
                            @endif
                        </li>
                        @forelse($allRisks as $riskItem)
                            <li>
                                <a href="{{ route('manager.report-detail', $riskItem['report_id']) }}" class="dropdown-item d-flex align-items-start gap-2.5 p-3 border-bottom border-secondary-subtle bg-transparent text-wrap text-secondary dropdown-item-custom hover-light" style="transition: background-color 0.2s; white-space: normal;">
                                    <div class="bg-warning rounded-circle mt-1.5 shadow-md shadow-warning-500/50" style="width: 8px; height: 8px; flex-shrink: 0;"></div>
                                    <div class="flex-grow-1">
                                        <p class="mb-1 text-dark small" style="line-height: 1.4; font-size: 12px;">{{ $riskItem['risk'] }}</p>
                                        <span class="text-secondary small font-normal" style="font-size: 10px;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" class="text-secondary me-1 align-middle" viewBox="0 0 16 16">
                                                <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/>
                                                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/>
                                            </svg>
                                            {{ $riskItem['date'] }}
                                        </span>
                                    </div>
                                </a>
                            </li>
                        @empty
                            <li class="p-4 text-center text-secondary small italic">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="text-secondary mb-2" viewBox="0 0 16 16">
                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                    <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                                </svg>
                                <div>No roadblocks or risks logged.</div>
                            </li>
                        @endforelse
                    </ul>
                </div>

                <!-- User Profile Dropdown -->
                <div class="dropdown">
                    <div class="d-flex align-items-center gap-3" style="cursor: pointer;" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <!-- Username and sub-role -->
                        <div class="text-end d-none d-sm-block">
                            <div class="text-sm font-semibold text-dark d-flex align-items-center justify-content-end gap-1">
                                <span>{{ Auth::user()?->name ?? 'Varsha Srivastava' }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" class="text-secondary ms-1" viewBox="0 0 16 16" style="margin-top: 2px;">
                                    <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
                                </svg>
                            </div>
                            <div class="text-secondary small mt-0.5" style="font-size: 10px; font-weight: 500;">Project Manager</div>
                        </div>

                        <!-- Circular Avatar -->
                        <div class="position-relative">
                            <img src="{{ asset('avatar.png') }}" alt="Profile" class="rounded-circle border border-primary border-opacity-50" style="width: 38px; height: 38px; object-fit: cover; border-width: 2px !important; box-shadow: 0 0 8px rgba(168, 85, 247, 0.3);">
                        </div>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end p-0 border-secondary-subtle shadow-xl dropdown-menu-custom" aria-labelledby="profileDropdown" style="background-color: #ffffff; min-width: 260px; border: 1px solid var(--border-color); border-radius: 12px;">
                        <li class="p-3 border-bottom border-secondary-subtle">
                            <h6 class="m-0 text-dark font-outfit font-semibold" style="font-size: 14px;">Admin Details</h6>
                            <div class="text-secondary small mt-2">
                                <div class="d-flex justify-content-between mb-1.5">
                                    <span class="text-secondary">Name:</span>
                                    <span class="text-dark font-semibold">{{ Auth::user()?->name ?? 'Varsha Srivastava' }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-secondary">Email:</span>
                                    <span class="text-dark" style="font-size: 11px;">{{ Auth::user()?->email ?? 'admin@manageragent.com' }}</span>
                                </div>
                            </div>
                        </li>
                        <li>
                            <a href="{{ route('profile.edit') }}" class="dropdown-item d-flex align-items-center gap-2 py-2.5 px-3 dropdown-item-custom" style="background: transparent;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="text-secondary">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Edit Profile
                            </a>
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" id="header-logout-form" class="d-none">
                                @csrf
                            </form>
                            <a href="#" onclick="event.preventDefault(); document.getElementById('header-logout-form').submit();" class="dropdown-item d-flex align-items-center gap-2 py-2.5 px-3 text-rose-400 dropdown-item-custom" style="background: transparent;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="text-rose-400">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Log Out
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="container-fluid p-4 flex-grow-1">
            <!-- Flash Message Alerts -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 rounded-4 p-3 mb-4 text-dark" style="background-color: rgba(16, 185, 129, 0.15); border-left: 4px solid #10b981 !important;" role="alert">
                    <div class="d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="text-emerald-400 me-2" viewBox="0 0 16 16">
                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                        </svg>
                        <span class="text-emerald-300">{{ session('success') }}</span>
                    </div>
                    <button type="button" class="btn-close btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 rounded-4 p-3 mb-4 text-dark" style="background-color: rgba(244, 63, 94, 0.15); border-left: 4px solid #f43f5e !important;" role="alert">
                    <div class="d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="text-rose-400 me-2" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                            <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/>
                        </svg>
                        <span class="text-rose-300">{{ session('error') }}</span>
                    </div>
                    <button type="button" class="btn-close btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="py-4 border-top border-secondary-subtle text-center text-xs text-secondary bg-light/20">
            &copy; {{ date('Y') }} Manager Agent. Powered by AI.
        </footer>
    </div>

    <!-- jQuery (required for DataTables) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

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

    <!-- Floating AI Chatbot Action Button (Circle Widget) -->
    <button id="floatingChatBtn" class="floating-chat-btn" onclick="toggleFloatingChat()" title="Ask AI Chatbot">
        <!-- Chat Bubble Icon -->
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8 15c4.418 0 8-3.134 8-7s-3.582-7-8-7-8 3.134-8 7c0 1.76.743 3.37 1.97 4.6-.097 1.016-.417 2.13-.771 2.966-.079.186.074.394.273.362 2.256-.37 3.597-.938 4.18-1.234A9.06 9.06 0 0 0 8 15z"/>
        </svg>
    </button>

    <!-- Floating Chat Window -->
    <div id="floatingChatWindow" class="floating-chat-window d-none">
        <div class="floating-chat-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="m-0 text-dark font-outfit font-bold" style="font-size: 15px;">AI Chat Assistant</h5>
                <span class="text-success font-semibold uppercase tracking-wider" style="font-size: 9px; letter-spacing: 0.05em; display: flex; align-items: center; gap: 4px; margin-top: 2px;">
                    <span class="d-inline-block bg-success rounded-circle" style="width: 6px; height: 6px;"></span>
                    Online & Active
                </span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn p-0 text-secondary hover:text-red-500 bg-transparent border-0 shadow-none" onclick="clearFloatingChatHistory()" title="Clear History">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                        <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                    </svg>
                </button>
                <button type="button" class="btn p-0 text-secondary hover:text-dark bg-transparent border-0 shadow-none" onclick="toggleFloatingChat()" title="Close chat">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Chat Body -->
        <div class="floating-chat-body" id="floatingChatBody">
            <div class="chat-message assistant mb-3">
                <div class="message-bubble">
                    Hello! I am your AI Manager Agent assistant. Ask me anything about employee records, daily attendance logs, productivity analytics, or task details!
                </div>
                <div class="message-time">Now</div>
            </div>

            @foreach(session('chat_history', []) as $msg)
                <div class="chat-message {{ $msg['role'] === 'user' ? 'user' : 'assistant' }} mb-3">
                    <div class="message-bubble">
                        {{ $msg['text'] }}
                    </div>
                    <div class="message-time">{{ $msg['time'] ?? 'Now' }}</div>
                </div>
            @endforeach
        </div>

        <!-- Input Footer form -->
        <div class="floating-chat-footer">
            <form id="floatingChatForm" onsubmit="submitFloatingChat(event)" class="d-flex gap-2 align-items-center">
                <input type="text" id="floatingChatInput" class="form-control form-control-sm text-dark border-secondary-subtle px-3 py-2 rounded-3" style="font-size: 13px; background-color: #ffffff !important;" placeholder="Ask manager agent..." required autocomplete="off">
                <button type="submit" class="btn btn-primary btn-sm rounded-3 py-2 px-3 shadow" style="background: linear-gradient(135deg, #a855f7, #6366f1); border: none;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M15.964.686a.5.5 0 0 0-.65-.65L.767 5.855H.766l-.452.18a.5.5 0 0 0-.082.887l.41.26.001.002 4.995 3.178 3.178 4.995.002.002.26.41a.5.5 0 0 0 .886-.083l6-15Zm-1.833 1.89L6.637 10.07l-.215-.338a.5.5 0 0 0-.154-.154l-.338-.215 7.494-7.494 1.178-.471-.47 1.178Z"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>

    <!-- Floating Chatbot Script -->
    <script>
        // Toggle widget open/close
        function toggleFloatingChat() {
            const chatWindow = document.getElementById('floatingChatWindow');
            if (chatWindow.classList.contains('d-none')) {
                chatWindow.classList.remove('d-none');
                scrollChatToBottom();
                document.getElementById('floatingChatInput').focus();
            } else {
                chatWindow.classList.add('d-none');
            }
        }

        // Scroll chat to bottom
        function scrollChatToBottom() {
            const chatBody = document.getElementById('floatingChatBody');
            if (chatBody) {
                chatBody.scrollTop = chatBody.scrollHeight;
            }
        }

        // Submit chat prompt via Fetch AJAX
        function submitFloatingChat(event) {
            event.preventDefault();
            const inputField = document.getElementById('floatingChatInput');
            const question = inputField.value.trim();
            if (!question) return;

            // Clear input
            inputField.value = '';

            // Append user message to body
            const chatBody = document.getElementById('floatingChatBody');
            const nowTime = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

            const userMsgDiv = document.createElement('div');
            userMsgDiv.className = 'chat-message user mb-3';
            userMsgDiv.innerHTML = `
                <div class="message-bubble">${escapeHtml(question)}</div>
                <div class="message-time">${nowTime}</div>
            `;
            chatBody.appendChild(userMsgDiv);
            scrollChatToBottom();

            // Append loading indicator
            const loadingDiv = document.createElement('div');
            loadingDiv.className = 'chat-message assistant mb-3';
            loadingDiv.id = 'chat-loading-indicator';
            loadingDiv.innerHTML = `
                <div class="message-bubble italic text-secondary">
                    <span class="spinner-grow spinner-grow-sm text-primary align-middle me-1" role="status" style="width: 8px; height: 8px;"></span>
                    Typing response...
                </div>
            `;
            chatBody.appendChild(loadingDiv);
            scrollChatToBottom();

            // Send network request
            fetch("{{ route('manager.chatbot.ask') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ question: question })
            })
            .then(res => res.json())
            .then(data => {
                // Remove loading indicator
                const loader = document.getElementById('chat-loading-indicator');
                if (loader) loader.remove();

                if (data.success) {
                    // Append assistant message
                    const assistantMsgDiv = document.createElement('div');
                    assistantMsgDiv.className = 'chat-message assistant mb-3';
                    assistantMsgDiv.innerHTML = `
                        <div class="message-bubble">${data.assistant.text}</div>
                        <div class="message-time">${data.assistant.time}</div>
                    `;
                    chatBody.appendChild(assistantMsgDiv);
                } else {
                    showErrorBubble('Oops, failed to fetch AI response.');
                }
                scrollChatToBottom();
            })
            .catch(err => {
                const loader = document.getElementById('chat-loading-indicator');
                if (loader) loader.remove();
                showErrorBubble('Fetch Exception: Unable to reach chatbot service.');
                scrollChatToBottom();
            });
        }

        // Show error message
        function showErrorBubble(msg) {
            const chatBody = document.getElementById('floatingChatBody');
            const errDiv = document.createElement('div');
            errDiv.className = 'chat-message assistant mb-3';
            errDiv.innerHTML = `
                <div class="message-bubble text-danger font-semibold">${msg}</div>
                <div class="message-time">Now</div>
            `;
            chatBody.appendChild(errDiv);
        }

        // Escape helper
        function escapeHtml(text) {
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        // Clear history via AJAX
        function clearFloatingChatHistory() {
            if (!confirm('Are you sure you want to clear the conversation logs?')) return;

            fetch("{{ route('manager.chatbot.clear') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Reset body to default message
                    const chatBody = document.getElementById('floatingChatBody');
                    chatBody.innerHTML = `
                        <div class="chat-message assistant mb-3">
                            <div class="message-bubble">
                                Hello! I am your AI Manager Agent assistant. Ask me anything about employee records, daily attendance logs, productivity analytics, or task details!
                            </div>
                            <div class="message-time">Now</div>
                        </div>
                    `;
                }
            });
        }

        // Auto-scroll on load
        document.addEventListener('DOMContentLoaded', () => {
            scrollChatToBottom();
        });

        // Global checkable developer list selection toggle helper
        function toggleDeveloperSelect(event, checkboxId) {
            if (event.target.tagName.toLowerCase() === 'input') {
                return;
            }
            const checkbox = document.getElementById(checkboxId);
            if (checkbox) {
                checkbox.checked = !checkbox.checked;
                checkbox.dispatchEvent(new Event('change'));
            }
        }
    </script>
    @yield('scripts')
</body>
</html>
