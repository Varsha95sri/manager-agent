<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Manager Agent - AI-Powered Management Assistant</title>

        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Bootstrap 5 CSS (Used for grid structures) -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

        <!-- Custom Styles -->
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
                overflow-x: hidden;
            }

            h1, h2, h3, h4, h5, h6, .font-outfit {
                font-family: 'Outfit', sans-serif;
            }

            /* Header styling */
            .navbar {
                background-color: rgba(15, 23, 42, 0.8) !important;
                backdrop-filter: blur(12px);
                border-bottom: 1px solid var(--border-color);
                padding: 1rem 0;
            }

            .navbar-brand-icon {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 36px;
                height: 36px;
                border-radius: 10px;
                background: linear-gradient(135deg, #a855f7, #6366f1);
                margin-right: 10px;
            }

            .logo-text {
                font-family: 'Outfit', sans-serif;
                font-weight: 800;
                font-size: 1.25rem;
                letter-spacing: -0.02em;
                color: #ffffff;
            }

            /* Hero Section */
            .hero-section {
                padding: 8rem 0 5rem 0;
                background: radial-gradient(circle at 80% 20%, rgba(168, 85, 247, 0.15) 0%, transparent 50%),
                            radial-gradient(circle at 10% 80%, rgba(99, 102, 241, 0.15) 0%, transparent 50%);
            }

            .hero-badge {
                display: inline-flex;
                align-items: center;
                padding: 0.5rem 1rem;
                background-color: rgba(168, 85, 247, 0.1);
                border: 1px solid rgba(168, 85, 247, 0.2);
                border-radius: 99px;
                color: #d8b4fe;
                font-size: 0.875rem;
                font-weight: 600;
                margin-bottom: 1.5rem;
            }

            .hero-title {
                font-size: 3.5rem;
                font-weight: 800;
                line-height: 1.15;
                letter-spacing: -0.025em;
                margin-bottom: 1.5rem;
                background: linear-gradient(to right, #ffffff, #e2e8f0);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }

            .hero-subtitle {
                font-size: 1.125rem;
                color: var(--text-muted);
                line-height: 1.6;
                margin-bottom: 2.5rem;
                max-width: 620px;
            }

            .btn-accent {
                background: linear-gradient(135deg, var(--accent-color), #6366f1);
                color: #ffffff;
                border: none;
                font-weight: 600;
                border-radius: 12px;
                padding: 0.8rem 1.8rem;
                transition: all 0.3s;
                box-shadow: 0 4px 14px -2px rgba(168, 85, 247, 0.4);
            }

            .btn-accent:hover {
                opacity: 0.95;
                color: #ffffff;
                transform: translateY(-2px);
                box-shadow: 0 10px 20px -3px rgba(168, 85, 247, 0.5);
            }

            .btn-secondary-outline {
                background-color: transparent;
                color: #ffffff;
                border: 1px solid var(--border-color);
                font-weight: 600;
                border-radius: 12px;
                padding: 0.8rem 1.8rem;
                transition: all 0.3s;
            }

            .btn-secondary-outline:hover {
                background-color: rgba(255, 255, 255, 0.04);
                border-color: var(--text-muted);
                color: #ffffff;
                transform: translateY(-2px);
            }

            /* Features section */
            .features-section {
                padding: 5rem 0;
            }

            .feature-card {
                background-color: var(--card-bg);
                border: 1px solid rgba(255, 255, 255, 0.05);
                border-radius: 20px;
                padding: 2.5rem 2rem;
                height: 100%;
                transition: all 0.3s;
            }

            .feature-card:hover {
                transform: translateY(-5px);
                border-color: rgba(255, 255, 255, 0.1);
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
            }

            .feature-icon {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 48px;
                height: 48px;
                border-radius: 12px;
                background-color: rgba(168, 85, 247, 0.1);
                color: var(--accent-color);
                margin-bottom: 1.5rem;
            }

            /* Mockup Chat Interface */
            .mockup-container {
                background-color: #0b0f19;
                border: 1px solid var(--border-color);
                border-radius: 24px;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
                overflow: hidden;
            }

            .mockup-header {
                background-color: #0d1321;
                border-bottom: 1px solid var(--border-color);
                padding: 1rem 1.5rem;
                display: flex;
                align-items: center;
            }

            .mockup-dot {
                width: 10px;
                height: 10px;
                border-radius: 50%;
                margin-right: 6px;
            }

            .mockup-body {
                padding: 1.5rem;
                font-size: 0.875rem;
                line-height: 1.5;
            }

            .mock-bubble-user {
                background: linear-gradient(135deg, #a855f7, #6366f1);
                color: #ffffff;
                padding: 0.75rem 1rem;
                border-radius: 16px;
                border-top-right-radius: 4px;
                max-width: 80%;
                margin-left: auto;
                margin-bottom: 1rem;
            }

            .mock-bubble-ai {
                background-color: #1e293b;
                color: #e2e8f0;
                border: 1px solid #334155;
                padding: 0.75rem 1rem;
                border-radius: 16px;
                border-top-left-radius: 4px;
                max-width: 80%;
                margin-right: auto;
                margin-bottom: 1rem;
            }

            .footer-section {
                padding: 3rem 0;
                border-top: 1px solid var(--border-color);
                color: var(--text-muted);
                font-size: 0.875rem;
            }
        </style>
    </head>
    <body>

        <!-- Navigation Bar -->
        <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="/">
                    <div class="navbar-brand-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="text-white" viewBox="0 0 16 16">
                            <path d="M6 12.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5ZM3 8.062C3 6.7 4 5.65 5.5 5.65h5c1.5 0 2.5 1.05 2.5 2.412v3.838a1.5 1.5 0 0 1-1.5 1.5h-8A1.5 1.5 0 0 1 3 11.9V8.062Zm2.5-.912C4.338 7.15 3.5 7.9 3.5 9v1.5a.5.5 0 0 0 1 0v-1.5c0-.276.224-.5.5-.5h5c.276 0 .5.224.5.5v1.5a.5.5 0 0 0 1 0v-1.5c0-1.1-.838-1.85-2-1.85h-5Z"/>
                            <path d="M8 2a3 3 0 0 0-3 3 .5.5 0 0 0 1 0 2 2 0 1 1 4 0 .5.5 0 0 0 1 0 3 3 0 0 0-3-3Z"/>
                        </svg>
                    </div>
                    <span class="logo-text">Manager Agent</span>
                </a>
                
                <div class="ms-auto d-flex align-items-center gap-3">
                    @auth
                        <a href="{{ route('manager.dashboard') }}" class="btn btn-accent px-4">Workspace Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-secondary-outline px-4 btn-sm">Log In</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-accent px-4 btn-sm">Sign Up</a>
                        @endif
                    @endauth
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <header class="hero-section">
            <div class="container">
                <div class="row align-items-center g-5">
                    <div class="col-lg-6">
                        <div class="hero-badge">
                            🚀 Introducing Automated Team Analytics
                        </div>
                        <h2 class="hero-title font-outfit">AI-Powered Performance Insights for Managers</h2>
                        <p class="hero-subtitle">
                            Manager Agent collects tasks logs, GitHub commits, attendance indices, and standup notes, then generates a comprehensive daily productivity audit every evening at 8 PM.
                        </p>
                        
                        <div class="d-flex flex-wrap gap-3">
                            @auth
                                <a href="{{ route('manager.dashboard') }}" class="btn btn-accent">Go to Dashboard</a>
                                <a href="{{ route('manager.chatbot') }}" class="btn btn-secondary-outline">Chat with AI</a>
                            @else
                                <a href="{{ route('register') }}" class="btn btn-accent">Get Started Free</a>
                                <a href="{{ route('login') }}" class="btn btn-secondary-outline">Log in to Workspace</a>
                            @endif
                        </div>
                    </div>
                    
                    <div class="col-lg-6">
                        <div class="mockup-container">
                            <div class="mockup-header">
                                <span class="mockup-dot bg-danger"></span>
                                <span class="mockup-dot bg-warning"></span>
                                <span class="mockup-dot bg-success"></span>
                                <span class="text-secondary small ms-2">Manager Agent AI Assistant</span>
                            </div>
                            <div class="mockup-body">
                                <div class="mock-bubble-user">
                                    Who was absent today, and what tasks were completed?
                                </div>
                                <div class="mock-bubble-ai">
                                    <strong>Live Attendance Log:</strong> Anushka is absent today. Rahul, Arjun, Priya, and Shipra check-in records are present.<br><br>
                                    <strong>Completed Tasks:</strong><br>
                                    - Rahul: Integrated Postgres indexes & API endpoints.<br>
                                    - Arjun: Finished mobile navigation views.<br>
                                    - Priya: Created SVG circular index guides.
                                </div>
                                <div class="mock-bubble-user">
                                    How many commits did Rahul push today?
                                </div>
                                <div class="mock-bubble-ai mb-0">
                                    Rahul pushed <strong>3 commits</strong> today: API schema definitions, postgres query indexing, and redis cache configuration.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Features Section -->
        <section class="features-section">
            <div class="container">
                <div class="text-center mb-5">
                    <h3 class="h2 font-outfit text-white">Full-Suite Automated Workflow</h3>
                    <p class="text-secondary mx-auto" style="max-width: 500px;">Unlock insights about your engineers without reading endless chat channels or repository logs.</p>
                </div>
                
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M11 2a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1V2zM5 5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v9a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V5zm-4 4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v5a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V9z"/>
                                </svg>
                            </div>
                            <h4 class="h5 text-white font-outfit mb-3">Daily Performance Index</h4>
                            <p class="text-secondary small mb-0">Calculates a composite team productivity percentage based on daily commit frequency, completed checklists, and attendance metrics.</p>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M8 15c4.418 0 8-3.134 8-7s-3.582-7-8-7-8 3.134-8 7c0 1.76.743 3.37 1.97 4.6-.097 1.016-.417 2.13-.771 2.966-.079.186.074.394.273.362 2.256-.37 3.597-.938 4.18-1.234A9.06 9.06 0 0 0 8 15Z"/>
                                </svg>
                            </div>
                            <h4 class="h5 text-white font-outfit mb-3">Interactive AI Chatbot</h4>
                            <p class="text-secondary small mb-0">Ask questions using natural language. The AI agent evaluates your active database logs instantly to retrieve accurate status sync summaries.</p>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h13zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13z"/>
                                </svg>
                            </div>
                            <h4 class="h5 text-white font-outfit mb-3">Evening Report Summaries</h4>
                            <p class="text-secondary small mb-0">Every day at 8:00 PM, a daily performance narrative review is automatically generated, identifying high-risk areas, blockers, and star performers.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="footer-section text-center bg-slate-950/20">
            <div class="container">
                <p class="mb-0">&copy; {{ date('Y') }} Manager Agent. All rights reserved. Powered by Claude Sonnet.</p>
            </div>
        </footer>

        <!-- Bootstrap 5 JS Bundle -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
