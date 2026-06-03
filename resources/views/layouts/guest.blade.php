<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - Manager Agent</title>

        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Custom Styling for Premium Dark Theme -->
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
                justify-content: center;
                align-items: center;
                margin: 0;
                padding: 1.5rem;
                box-sizing: border-box;
            }

            .auth-card {
                background-color: var(--card-bg);
                border: 1px solid rgba(255, 255, 255, 0.05);
                border-radius: 24px;
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5), 0 10px 10px -5px rgba(0, 0, 0, 0.5);
                width: 100%;
                max-width: 440px;
                padding: 2.5rem 2rem;
                box-sizing: border-box;
                position: relative;
                overflow: hidden;
            }

            .auth-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 4px;
                background: linear-gradient(90deg, #a855f7, #6366f1);
            }

            .logo-container {
                display: flex;
                flex-direction: column;
                align-items: center;
                margin-bottom: 2rem;
            }

            .logo-icon {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 56px;
                height: 56px;
                border-radius: 16px;
                background: linear-gradient(135deg, #a855f7, #6366f1);
                margin-bottom: 1rem;
                box-shadow: 0 8px 16px -2px rgba(168, 85, 247, 0.3);
            }

            .logo-title {
                font-family: 'Outfit', sans-serif;
                font-size: 1.5rem;
                font-weight: 800;
                color: #ffffff;
                margin: 0;
            }

            .logo-subtitle {
                font-size: 0.813rem;
                color: var(--text-muted);
                margin-top: 0.25rem;
            }

            /* Inputs & Forms Override */
            label, .input-label {
                color: var(--text-muted) !important;
                font-weight: 600;
                font-size: 0.75rem !important;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                margin-bottom: 0.5rem;
                display: block;
            }

            input[type="text"],
            input[type="email"],
            input[type="password"] {
                background-color: #0b0f19 !important;
                color: #ffffff !important;
                border: 1px solid var(--border-color) !important;
                border-radius: 12px !important;
                padding: 0.75rem 1rem !important;
                font-size: 0.875rem !important;
                width: 100% !important;
                box-sizing: border-box !important;
                transition: all 0.2s ease-in-out !important;
            }

            input[type="text"]:focus,
            input[type="email"]:focus,
            input[type="password"]:focus {
                border-color: var(--accent-color) !important;
                outline: none !important;
                box-shadow: 0 0 0 3px rgba(168, 85, 247, 0.25) !important;
            }

            /* Primary Button */
            button[type="submit"], .primary-button {
                background: linear-gradient(135deg, var(--accent-color), #6366f1) !important;
                color: white !important;
                border: none !important;
                font-family: 'Outfit', sans-serif !important;
                font-weight: 700 !important;
                font-size: 0.875rem !important;
                letter-spacing: 0.025em !important;
                text-transform: uppercase !important;
                border-radius: 12px !important;
                padding: 0.75rem 1.5rem !important;
                width: 100% !important;
                cursor: pointer !important;
                transition: all 0.2s ease !important;
                box-shadow: 0 4px 6px -1px rgba(168, 85, 247, 0.2) !important;
            }

            button[type="submit"]:hover, .primary-button:hover {
                opacity: 0.95 !important;
                transform: translateY(-1px) !important;
                box-shadow: 0 10px 15px -3px rgba(168, 85, 247, 0.3) !important;
            }

            /* Checkbox */
            input[type="checkbox"] {
                accent-color: var(--accent-color) !important;
                width: 1rem;
                height: 1rem;
                border-radius: 4px;
                cursor: pointer;
            }

            /* Links */
            a {
                color: var(--accent-color) !important;
                text-decoration: none !important;
                font-weight: 500;
                transition: color 0.15s ease;
            }

            a:hover {
                color: var(--accent-hover) !important;
                text-decoration: underline !important;
            }

            /* Utilities */
            .text-danger, .input-error {
                color: #f43f5e !important;
                font-size: 0.75rem !important;
                margin-top: 0.375rem;
            }

            .mb-4 { margin-bottom: 1rem; }
            .mt-4 { margin-top: 1rem; }
        </style>
    </head>
    <body>
        <div class="logo-container">
            <div class="logo-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="text-white" viewBox="0 0 16 16">
                    <path d="M6 12.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5ZM3 8.062C3 6.7 4 5.65 5.5 5.65h5c1.5 0 2.5 1.05 2.5 2.412v3.838a1.5 1.5 0 0 1-1.5 1.5h-8A1.5 1.5 0 0 1 3 11.9V8.062Zm2.5-.912C4.338 7.15 3.5 7.9 3.5 9v1.5a.5.5 0 0 0 1 0v-1.5c0-.276.224-.5.5-.5h5c.276 0 .5.224.5.5v1.5a.5.5 0 0 0 1 0v-1.5c0-1.1-.838-1.85-2-1.85h-5Z"/>
                    <path d="M8 2a3 3 0 0 0-3 3 .5.5 0 0 0 1 0 2 2 0 1 1 4 0 .5.5 0 0 0 1 0 3 3 0 0 0-3-3Z"/>
                </svg>
            </div>
            <h1 class="logo-title">Manager Agent</h1>
            <span class="logo-subtitle">AI-Powered Performance Insights</span>
        </div>

        <div class="auth-card">
            {{ $slot }}
        </div>
    </body>
</html>
