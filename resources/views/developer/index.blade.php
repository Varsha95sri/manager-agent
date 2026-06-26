@extends('layouts.manager')

@section('title', 'Developer Tools & API Sandbox - Manager Agent')
@section('page_title', 'Developer Tools')

@section('content')
<div class="row g-4">
    <!-- Header Subtitle & System Status row -->
    <div class="col-12 mb-2 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
            <h2 class="h3 font-outfit text-slate-900 mb-1">Developer Tools & API Sandbox</h2>
            <p class="text-secondary small mb-0">Simulate Workdesk sync, manage developer keys, and sandbox API data in real-time.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="d-inline-block bg-success rounded-circle" style="width: 8px; height: 8px; animation: pulse 2s infinite;"></span>
            <span class="text-success small font-bold uppercase tracking-wider" style="font-size: 10px;">All Systems Operational</span>
        </div>
    </div>

    <!-- Left Column: Token manager and Sandbox runner (col-lg-7) -->
    <div class="col-12 col-lg-7">
        
        <!-- Flash Alert for Generated API Key -->
        @if(session('new_api_key'))
            <div class="alert alert-success border-0 rounded-4 p-4 mb-4 text-white" style="background-color: rgba(16, 185, 129, 0.15); border-left: 4px solid #10b981 !important;" role="alert">
                <div class="d-flex align-items-start gap-3">
                    <div class="bg-success/20 p-2 rounded-3 text-emerald-400">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                            <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/>
                        </svg>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="text-emerald-400 font-bold mb-1">API Key Generated Successfully!</h5>
                        <p class="text-emerald-300 text-sm mb-3">Copy this key now. For security purposes, you will not be able to view it again.</p>
                        
                        <div class="input-group">
                            <input type="text" class="form-control bg-dark border-secondary text-emerald-400 font-monospace select-all py-2 rounded-start-3" id="newKeyVal" value="{{ session('new_api_key') }}" readonly>
                            <button class="btn btn-success" type="button" onclick="copyNewKey()" style="background-color: #10b981; border: none; font-weight: 600;">
                                Copy
                            </button>
                        </div>
                        <span class="text-emerald-400 text-xs mt-1 d-block" id="copySuccessMsg" style="display: none !important;">Copied to clipboard!</span>
                    </div>
                </div>
            </div>
        @endif

        <!-- Access Tokens Card -->
        <div class="overflow-hidden border border-slate-300 rounded-4 shadow-lg mb-4 p-4" style="background: linear-gradient(135deg, #7c3aed, #4f46e5);">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
                <div>
                    <h4 class="m-0 text-slate-900 font-outfit font-bold">Access Tokens</h4>
                    <p class="m-0 text-purple-100 text-sm opacity-90 d-flex align-items-center gap-1 mt-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                            <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                        </svg>
                        Create & manage your api keys
                    </p>
                </div>
                <button class="btn btn-emerald font-semibold px-4 py-2 rounded-3 text-white shrink-0" style="background-color: #10b981; border: none; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.2);" data-bs-toggle="modal" data-bs-target="#generateKeyModal">
                    Generate API key
                </button>
            </div>
            
            <div class="bg-white rounded-4 p-3 shadow-inner">
                @if($keys->isEmpty())
                    <div class="text-center py-4 text-slate-600">
                        <p class="mb-0 text-sm font-semibold">No API keys active. Generate an API key above to start using the Public API endpoints.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table align-middle m-0" style="--bs-table-bg: transparent; --bs-table-border-color: #f1f5f9; color: #1e293b !important;">
                            <thead class="text-slate-600" style="font-size: 11px;">
                                <tr>
                                    <th class="ps-0 border-0 uppercase font-semibold">Token</th>
                                    <th class="border-0 uppercase font-semibold">Created On</th>
                                    <th class="text-end pe-0 border-0 uppercase font-semibold">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($keys as $key)
                                    <tr>
                                        <td class="ps-0 border-0">
                                            <div class="d-flex align-items-center gap-2">
                                                <code class="px-2.5 py-1 rounded text-slate-600 font-monospace border-0" style="font-size: 13px; background-color: #f8fafc; font-family: monospace; letter-spacing: 0.05em;" id="token-display-{{ $key->id }}" data-prefix="{{ $key->key_prefix }}" data-id="{{ $key->id }}">
                                                    ****************************************
                                                </code>
                                                <!-- Visibility Toggle (Eye) -->
                                                <button type="button" class="btn p-0 text-slate-600 hover:text-slate-600 border-0 bg-transparent shadow-none" onclick="toggleTokenVisibility({{ $key->id }})" title="Toggle Visibility">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" id="eye-icon-{{ $key->id }}" viewBox="0 0 16 16">
                                                        <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 4 8 4c2.02 0 3.78.668 5.167 1.957A13.142 13.142 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12 8 12c-2.02 0-3.78-.668-5.167-1.958A13.145 13.145 0 0 1 1.172 8z"/>
                                                        <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                                                    </svg>
                                                </button>
                                                <!-- Copy Button -->
                                                <button type="button" class="btn p-0 text-slate-600 hover:text-slate-600 border-0 bg-transparent shadow-none" onclick="copyToken({{ $key->id }}, '{{ $key->key_prefix }}')" title="Copy to clipboard">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                                        <path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z"/>
                                                        <path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h3zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="border-0">
                                            <span class="badge font-semibold px-2.5 py-1 text-slate-600 font-mono" style="font-size: 11.5px; background-color: #f1f5f9; border-radius: 6px;">
                                                {{ $key->created_at->format('d M y H:i') }}
                                            </span>
                                        </td>
                                        <td class="text-end pe-0 border-0">
                                            <!-- Revoke Form -->
                                            <form action="{{ route('developer.keys.destroy', $key->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this API Key? This will immediately revoke access.')" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn p-0 text-slate-600 hover:text-red-600 bg-transparent border-0 shadow-none" title="Revoke Token">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                                        <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <!-- Tab Switcher Navigation -->
        <div class="d-flex border-bottom border-slate-300 mb-4 pb-0" id="sandboxTabs" role="tablist" style="margin-top: 1.5rem;">
            <button class="flat-tab active" id="runner-tab" data-bs-toggle="tab" data-bs-target="#runner" type="button" role="tab" aria-selected="true">
                API TESTER / RUNNER
            </button>
            <button class="flat-tab" id="sandbox-tab" data-bs-toggle="tab" data-bs-target="#sandbox" type="button" role="tab" aria-selected="false">
                API SANDBOX / MYSQL WRITER
            </button>
        </div>

        <!-- Tabs content block -->
        <div class="tab-content">
            <!-- TAB 1: API Request Builder / Tester -->
            <div class="tab-pane fade show active" id="runner" role="tabpanel" aria-labelledby="runner-tab">
                <div class="card glass-card p-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="bg-success text-white d-flex align-items-center justify-content-center rounded-3 font-mono font-bold" style="width: 28px; height: 28px; background-color: #10b981 !important;">
                            &gt;
                        </div>
                        <h4 class="font-outfit font-bold text-slate-900 mb-0">API Request Builder</h4>
                    </div>
                    <p class="text-secondary text-sm mb-4">Configure and execute HTTP requests against simulated endpoints.</p>

                    <form id="apiRunnerForm" onsubmit="executeRequest(event)">
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label text-slate-600 small font-bold text-uppercase">HTTP Method</label>
                                <select id="requestMethod" class="form-select text-white border-slate-300 bg-slate-50" style="background-color: #0f172a !important;">
                                    <option value="GET">GET</option>
                                    <option value="POST">POST</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label text-slate-600 small font-bold text-uppercase">Endpoint Route</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-slate-300 text-slate-600 font-mono text-sm" style="border-right: none;">
                                        {{ url('/') }}
                                    </span>
                                    <input type="text" id="requestPath" class="form-control border-slate-300 bg-slate-50 text-white font-mono text-sm shadow-none" style="border-left: none;" placeholder="/api/v1/tasks" value="/api/v1/tasks" required>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label text-slate-600 small font-bold text-uppercase">HTTP Request Headers (JSON)</label>
                                <textarea id="requestHeaders" rows="5" class="form-control text-white border-slate-300 bg-white font-mono text-xs" style="background-color: #020617 !important; border-color: #1e293b !important; line-height: 1.5;" placeholder="JSON headers...">{
  "Accept": "application/json",
  "Content-Type": "application/json",
  "x-api-key": "mgr_live_your_key_here"
}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-slate-600 small font-bold text-uppercase">Request Payload / Body (JSON)</label>
                                <textarea id="requestBody" rows="5" class="form-control text-white border-slate-300 bg-white font-mono text-xs" style="background-color: #020617 !important; border-color: #1e293b !important; line-height: 1.5;" placeholder="JSON payload (only for POST requests)...">{}</textarea>
                            </div>
                        </div>

                        <div class="text-end mb-4">
                            <button type="submit" id="runBtn" class="btn btn-success px-4 py-2.5 rounded-3 font-semibold shadow" style="background: linear-gradient(135deg, #10b981, #059669); border: none;">
                                Execute Request
                            </button>
                        </div>
                    </form>

                    <!-- Response Output Container -->
                    <div id="responseContainer" class="d-none mt-2">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="text-slate-800 font-bold mb-0">Response Console</h5>
                            <div class="d-flex gap-2">
                                <span class="badge text-xs bg-white border border-slate-300 text-slate-700 px-2 py-1 font-mono" id="respStatus">Status: -</span>
                                <span class="badge text-xs bg-white border border-slate-300 text-slate-700 px-2 py-1 font-mono" id="respTime">Latency: -</span>
                            </div>
                        </div>
                        <div class="bg-white rounded-4 border border-slate-300 position-relative p-3">
                            <pre class="m-0 text-slate-700 font-monospace text-xs overflow-x-auto" id="respBody" style="max-height: 350px; white-space: pre-wrap; font-family: monospace;">{}</pre>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: MySQL Sandbox Details -->
            <div class="tab-pane fade" id="sandbox" role="tabpanel" aria-labelledby="sandbox-tab">
                <div class="card glass-card p-4">
                    <div class="alert alert-warning border-0 text-slate-800 rounded-3 small p-3 mb-4" style="background-color: rgba(245, 158, 11, 0.15); border-left: 4px solid #f59e0b !important;">
                        <strong>Warning: API Sandbox / MySQL Writer:</strong> All database write operations from the API Request Builder are **live** and will modify/persist data in the local database. Ensure you use unique/mock credentials during testing.
                    </div>
                    
                    <h5 class="text-slate-900 font-outfit font-bold mb-3">Database Reference (Active Team IDs)</h5>
                    <p class="text-secondary text-sm mb-4">Select and copy valid Team Member IDs below to populate task or attendance POST requests.</p>
                    
                    <div class="table-responsive">
                        <table class="table align-middle text-slate-900 mb-0" style="--bs-table-bg: transparent; --bs-table-border-color: #334155;">
                            <thead class="text-secondary" style="font-size: 11px;">
                                <tr>
                                    <th class="ps-0">ID</th>
                                    <th>Employee Name</th>
                                    <th>Designated Role</th>
                                    <th class="text-end pe-0">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($teamMembers as $member)
                                    <tr>
                                        <td class="ps-0 font-mono text-purple-400 font-bold" style="font-size: 13px;">{{ $member->id }}</td>
                                        <td class="font-semibold">{{ $member->name }}</td>
                                        <td class="text-slate-600">{{ $member->role }}</td>
                                        <td class="text-end pe-0">
                                            <button type="button" class="btn btn-xs btn-outline-info" onclick="copyIdToClipboard({{ $member->id }})">Copy ID</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-3 text-secondary italic">No team members registered. Please add them in the Employees registry first.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Interactive documentation and API Catalog (col-lg-5) -->
    <div class="col-12 col-lg-5">
        <div class="glass-card border border-slate-300 rounded-4 shadow-lg p-4 sticky-top" style="top: 90px; max-height: calc(100vh - 120px); overflow-y: auto;">
            <!-- Language Selector tabs -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="font-outfit font-bold text-slate-900 mb-0">API Documentation</h5>
            </div>
            
            <nav class="nav nav-pills gap-1 mb-4 d-flex" id="api-tabs" role="tablist" style="overflow-x: auto; white-space: nowrap; flex-wrap: nowrap; padding-bottom: 5px;">
                <button class="btn btn-outline-secondary btn-sm active py-1 px-3 text-xs" id="tab-curl" onclick="switchDocLang('curl')">cURL</button>
                <button class="btn btn-outline-secondary btn-sm py-1 px-3 text-xs" id="tab-php" onclick="switchDocLang('php')">PHP</button>
                <button class="btn btn-outline-secondary btn-sm py-1 px-3 text-xs" id="tab-node" onclick="switchDocLang('node')">NodeJS</button>
                <button class="btn btn-outline-secondary btn-sm py-1 px-3 text-xs" id="tab-python" onclick="switchDocLang('python')">Python</button>
                <button class="btn btn-outline-secondary btn-sm py-1 px-3 text-xs" id="tab-java" onclick="switchDocLang('java')">Java</button>
                <button class="btn btn-outline-secondary btn-sm py-1 px-3 text-xs" id="tab-ruby" onclick="switchDocLang('ruby')">Ruby</button>
            </nav>

            <!-- Interactive Route Buttons -->
            <h6 class="text-slate-600 small font-bold text-uppercase tracking-wider mb-3">API Routes Catalog</h6>
            <div class="d-flex flex-column gap-3 mb-4">
                <!-- Group 1: Employee -->
                <div>
                    <span class="text-secondary font-bold" style="font-size: 10px; letter-spacing: 0.05em; display: block; margin-bottom: 6px;">EMPLOYEE</span>
                    <button class="w-100 btn text-start d-flex align-items-center p-2.5 rounded-3 bg-white border-0 hover-card shadow-sm" onclick="selectRoute('register-employee')">
                        <span class="badge font-mono text-xs text-white me-3" style="background-color: #3b82f6; min-width: 50px; text-align: center; padding: 5px 8px;">POST</span>
                        <span class="font-bold text-slate-800" style="font-size: 13px; font-weight: 700;">Register Employee</span>
                    </button>
                </div>

                <!-- Group 2: Tasks -->
                <div>
                    <span class="text-secondary font-bold" style="font-size: 10px; letter-spacing: 0.05em; display: block; margin-bottom: 6px;">TASKS</span>
                    <div class="d-flex flex-column gap-2">
                        <button class="w-100 btn text-start d-flex align-items-center p-2.5 rounded-3 bg-white border-0 hover-card shadow-sm" onclick="selectRoute('get-tasks')">
                            <span class="badge font-mono text-xs text-white me-3" style="background-color: #10b981; min-width: 50px; text-align: center; padding: 5px 8px;">GET</span>
                            <span class="font-bold text-slate-800" style="font-size: 13px; font-weight: 700;">Get Tasks & Sprints</span>
                        </button>
                        <button class="w-100 btn text-start d-flex align-items-center p-2.5 rounded-3 bg-white border-0 hover-card shadow-sm" onclick="selectRoute('create-task')">
                            <span class="badge font-mono text-xs text-white me-3" style="background-color: #3b82f6; min-width: 50px; text-align: center; padding: 5px 8px;">POST</span>
                            <span class="font-bold text-slate-800" style="font-size: 13px; font-weight: 700;">Create Developer Task</span>
                        </button>
                    </div>
                </div>

                <!-- Group 3: Working Hours -->
                <div>
                    <span class="text-secondary font-bold" style="font-size: 10px; letter-spacing: 0.05em; display: block; margin-bottom: 6px;">WORKING HOURS</span>
                    <div class="d-flex flex-column gap-2">
                        <button class="w-100 btn text-start d-flex align-items-center p-2.5 rounded-3 bg-white border-0 hover-card shadow-sm" onclick="selectRoute('get-working-hours')">
                            <span class="badge font-mono text-xs text-white me-3" style="background-color: #10b981; min-width: 50px; text-align: center; padding: 5px 8px;">GET</span>
                            <span class="font-bold text-slate-800" style="font-size: 13px; font-weight: 700;">Get Working Hours Log</span>
                        </button>
                        <button class="w-100 btn text-start d-flex align-items-center p-2.5 rounded-3 bg-white border-0 hover-card shadow-sm" onclick="selectRoute('log-worked-hours')">
                            <span class="badge font-mono text-xs text-white me-3" style="background-color: #3b82f6; min-width: 50px; text-align: center; padding: 5px 8px;">POST</span>
                            <span class="font-bold text-slate-800" style="font-size: 13px; font-weight: 700;">Log Worked Hours</span>
                        </button>
                    </div>
                </div>

                <!-- Group 4: Attendance -->
                <div>
                    <span class="text-secondary font-bold" style="font-size: 10px; letter-spacing: 0.05em; display: block; margin-bottom: 6px;">ATTENDANCE</span>
                    <div class="d-flex flex-column gap-2">
                        <button class="w-100 btn text-start d-flex align-items-center p-2.5 rounded-3 bg-white border-0 hover-card shadow-sm" onclick="selectRoute('get-attendance')">
                            <span class="badge font-mono text-xs text-white me-3" style="background-color: #10b981; min-width: 50px; text-align: center; padding: 5px 8px;">GET</span>
                            <span class="font-bold text-slate-800" style="font-size: 13px; font-weight: 700;">Get Attendance Logs</span>
                        </button>
                        <button class="w-100 btn text-start d-flex align-items-center p-2.5 rounded-3 bg-white border-0 hover-card shadow-sm" onclick="selectRoute('record-attendance')">
                            <span class="badge font-mono text-xs text-white me-3" style="background-color: #3b82f6; min-width: 50px; text-align: center; padding: 5px 8px;">POST</span>
                            <span class="font-bold text-slate-800" style="font-size: 13px; font-weight: 700;">Record Daily Status</span>
                        </button>
                    </div>
                </div>

                <!-- Group 5: Metrics & AI -->
                <div>
                    <span class="text-secondary font-bold" style="font-size: 10px; letter-spacing: 0.05em; display: block; margin-bottom: 6px;">METRICS & AI</span>
                    <div class="d-flex flex-column gap-2">
                        <button class="w-100 btn text-start d-flex align-items-center p-2.5 rounded-3 bg-white border-0 hover-card shadow-sm" onclick="selectRoute('get-metrics')">
                            <span class="badge font-mono text-xs text-white me-3" style="background-color: #10b981; min-width: 50px; text-align: center; padding: 5px 8px;">GET</span>
                            <span class="font-bold text-slate-800" style="font-size: 13px; font-weight: 700;">Get Productivity Metrics</span>
                        </button>
                        <button class="w-100 btn text-start d-flex align-items-center p-2.5 rounded-3 bg-white border-0 hover-card shadow-sm" onclick="selectRoute('analyze-momentum')">
                            <span class="badge font-mono text-xs text-white me-3" style="background-color: #3b82f6; min-width: 50px; text-align: center; padding: 5px 8px;">POST</span>
                            <span class="font-bold text-slate-800" style="font-size: 13px; font-weight: 700;">Analyze Team Momentum</span>
                        </button>
                        <button class="w-100 btn text-start d-flex align-items-center p-2.5 rounded-3 bg-white border-0 hover-card shadow-sm" onclick="selectRoute('generate-report')">
                            <span class="badge font-mono text-xs text-white me-3" style="background-color: #3b82f6; min-width: 50px; text-align: center; padding: 5px 8px;">POST</span>
                            <span class="font-bold text-slate-800" style="font-size: 13px; font-weight: 700;">Generate Evening Report</span>
                        </button>
                        <button class="w-100 btn text-start d-flex align-items-center p-2.5 rounded-3 bg-white border-0 hover-card shadow-sm" onclick="selectRoute('analyze-team')">
                            <span class="badge font-mono text-xs text-white me-3" style="background-color: #3b82f6; min-width: 50px; text-align: center; padding: 5px 8px;">POST</span>
                            <span class="font-bold text-slate-800" style="font-size: 13px; font-weight: 700;">Analyze Team Performance</span>
                        </button>
                        <button class="w-100 btn text-start d-flex align-items-center p-2.5 rounded-3 bg-white border-0 hover-card shadow-sm" onclick="selectRoute('ai-chat')">
                            <span class="badge font-mono text-xs text-white me-3" style="background-color: #3b82f6; min-width: 50px; text-align: center; padding: 5px 8px;">POST</span>
                            <span class="font-bold text-slate-800" style="font-size: 13px; font-weight: 700;">AI Chat Conversation</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Dynamic Snippet Viewer -->
            <div id="snippetWrapper" class="mt-4 border-top border-slate-300 pt-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-slate-800 font-bold mb-0">Code Snippet (<sup id="snippetLangLabel">cURL</sup>)</h6>
                    <button class="btn btn-xs btn-outline-secondary" onclick="copySnippet()">Copy Snippet</button>
                </div>
                <div class="bg-white rounded-4 border border-slate-300 position-relative p-3">
                    <pre class="m-0 text-slate-700 font-monospace text-xs overflow-x-auto" id="snippetPre" style="max-height: 250px; white-space: pre; font-family: monospace;"></pre>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Generate Internal Token Modal -->
<div class="modal fade" id="generateKeyModal" tabindex="-1" aria-labelledby="generateKeyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-white border border-slate-300 rounded-4" style="background-color: #0b0f19;">
            <div class="modal-header border-bottom border-slate-300 p-4">
                <h5 class="modal-title font-outfit font-bold" id="generateKeyModalLabel">Generate New API Key</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('developer.keys.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="label" class="form-label text-slate-600 small font-bold text-uppercase">Key Description / Label</label>
                        <input type="text" class="form-control rounded-3" id="label" name="label" placeholder="e.g. CI/CD Pipeline Agent" required>
                        <div class="form-text text-slate-500 text-xs mt-1">Specify a recognizable description to recall this key's purpose.</div>
                    </div>
                </div>
                <div class="modal-footer border-top border-slate-300 p-4">
                    <button type="button" class="btn btn-secondary rounded-3 px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary accent-btn rounded-3 px-4">Generate</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Local cache for generated tokens in this session
    let generatedKeys = JSON.parse(localStorage.getItem('generated_api_keys') || '{}');

    // Register a newly generated key from PHP session
    @if(session('new_api_key'))
        (function() {
            const rawKey = "{{ session('new_api_key') }}";
            const prefix = "{{ substr(session('new_api_key'), 0, 17) }}";
            generatedKeys[prefix] = rawKey;
            localStorage.setItem('generated_api_keys', JSON.stringify(generatedKeys));
        })();
    @endif

    // Copy the newly generated key from success alert
    function copyNewKey() {
        const copyText = document.getElementById("newKeyVal");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyText.value);
        
        const successMsg = document.getElementById("copySuccessMsg");
        if(successMsg) {
            successMsg.style.setProperty("display", "block", "important");
            setTimeout(() => {
                successMsg.style.setProperty("display", "none", "important");
            }, 3000);
        }
    }

    // Toggle mask/unmask visibility
    function toggleTokenVisibility(keyId) {
        const displayCode = document.getElementById(`token-display-${keyId}`);
        if (!displayCode) return;

        const prefix = displayCode.getAttribute('data-prefix');
        const eyeIcon = document.getElementById(`eye-icon-${keyId}`);

        // Check if currently masked
        const isMasked = displayCode.innerText.includes('****');

        if (isMasked) {
            // Unmask
            if (generatedKeys[prefix]) {
                // We have the full key cached!
                displayCode.innerText = generatedKeys[prefix];
            } else {
                // We only have the prefix
                displayCode.innerText = prefix + '************************';
            }
            // Switch to eye-slash icon
            eyeIcon.innerHTML = `<path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a18.883 18.883 0 0 0-2.838.224l-1.2-1.2A19.861 19.861 0 0 1 8 2c5 0 8 5.5 8 5.5a11.59 11.59 0 0 1-2.778 3.843l-1.862-1.862zm-2.584-2.584a3.5 3.5 0 0 0-4.95-4.95l4.95 4.95z"/>
                                 <path d="M15.79 14.808a.5.5 0 0 1-.77.13L1.253 1.253a.5.5 0 0 1 .708-.708l13.829 13.829a.5.5 0 0 1-.031.708z"/>
                                 <path d="M1.175 4.762C.566 5.867 0 8 0 8s3 5.5 8 5.5a18.883 18.883 0 0 0 2.838-.224l1.2 1.2A19.861 19.861 0 0 1 8 14c-5 0-8-5.5-8-5.5a11.59 11.59 0 0 1 2.778-3.843l1.862 1.862zm2.584 2.584a3.5 3.5 0 0 0 4.95 4.95l-4.95-4.95z"/>`;
        } else {
            // Mask back
            displayCode.innerText = '****************************************';
            // Switch back to regular eye icon
            eyeIcon.innerHTML = `<path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 4 8 4c2.02 0 3.78.668 5.167 1.957A13.142 13.142 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12 8 12c-2.02 0-3.78-.668-5.167-1.958A13.145 13.145 0 0 1 1.172 8z"/>
                                 <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>`;
        }
    }

    // Copy token helper & set in builder
    function copyToken(keyId, prefix) {
        let fullKey = '';
        if (generatedKeys[prefix]) {
            fullKey = generatedKeys[prefix];
            navigator.clipboard.writeText(fullKey);
            alert('Full API Key copied to clipboard and loaded into API Request Builder!');
        } else {
            fullKey = prefix + '************************';
            navigator.clipboard.writeText(fullKey);
            alert('Copied key prefix: ' + prefix + '. Loaded into API Request Builder.');
        }
        
        activeToken = fullKey;
        updateHeadersTextarea();
    }

    // Copy mysql IDs helper
    function copyIdToClipboard(id) {
        navigator.clipboard.writeText(id);
        alert('Copied ID ' + id + ' to clipboard!');
    }

    // Global runner credentials
    let activeToken = 'YOUR_API_KEY_HERE';
    let currentSelectedLang = 'curl';
    let currentSelectedRoute = 'register-employee';

    // Helper to refresh header text field
    function updateHeadersTextarea() {
        const headersArea = document.getElementById('requestHeaders');
        headersArea.value = JSON.stringify({
            "Accept": "application/json",
            "Content-Type": "application/json",
            "x-api-key": activeToken
        }, null, 2);
        
        // Regenerate snippet
        refreshSnippet();
    }

    // Route config
    const routeTemplates = {
        'register-employee': {
            method: 'POST',
            path: '/api/v1/employees',
            body: {
                name: "Amit Dev",
                email: "amit@company.com",
                role: "iOS Developer",
                gitlab_id: "amit-coder",
                login_timing: "09:30 AM",
                attendance: "present"
            }
        },
        'get-tasks': {
            method: 'GET',
            path: '/api/v1/tasks',
            body: null
        },
        'create-task': {
            method: 'POST',
            path: '/api/v1/tasks',
            body: {
                team_member_id: @json($teamMembers->first()?->id ?? 1),
                title: "Implement push notifications",
                status: "pending",
                due_date: "{{ date('Y-m-d') }}"
            }
        },
        'get-working-hours': {
            method: 'GET',
            path: '/api/v1/attendance',
            body: null
        },
        'log-worked-hours': {
            method: 'POST',
            path: '/api/v1/attendance',
            body: {
                team_member_id: @json($teamMembers->first()?->id ?? 1),
                date: "{{ date('Y-m-d') }}",
                status: "present",
                check_in: "09:00:00"
            }
        },
        'get-attendance': {
            method: 'GET',
            path: '/api/v1/attendance',
            body: null
        },
        'record-attendance': {
            method: 'POST',
            path: '/api/v1/attendance',
            body: {
                team_member_id: @json($teamMembers->first()?->id ?? 1),
                date: "{{ date('Y-m-d') }}",
                status: "present",
                check_in: "09:00:00"
            }
        },
        'get-metrics': {
            method: 'GET',
            path: '/api/v1/metrics',
            body: null
        },
        'analyze-momentum': {
            method: 'POST',
            path: '/api/v1/analyze-momentum',
            body: {}
        },
        'generate-report': {
            method: 'POST',
            path: '/api/v1/generate-report',
            body: {
                team_data: {
                    "Rahul": "Pushed 3 commits, fixed responsive layout",
                    "Arjun": "Set up Redis cache layers"
                },
                report_type: "Daily Performance Synthesis"
            }
        },
        'analyze-team': {
            method: 'POST',
            path: '/api/v1/analyze-team',
            body: {
                team_members: [
                    { name: "Rahul", role: "Senior Frontend Developer" }
                ],
                metrics: {
                    "Rahul": { commits: 5, completed_tasks: 2 }
                }
            }
        },
        'ai-chat': {
            method: 'POST',
            path: '/api/v1/chat',
            body: {
                prompt: "Are there any bottlenecks or overdue tasks logged today?",
                system_message: "You are an expert Engineering Director."
            }
        }
    };

    // Selecting a route updates the builder
    function selectRoute(routeKey) {
        currentSelectedRoute = routeKey;
        const config = routeTemplates[routeKey];
        if (!config) return;

        document.getElementById('requestMethod').value = config.method;
        document.getElementById('requestPath').value = config.path;
        document.getElementById('requestBody').value = config.body ? JSON.stringify(config.body, null, 2) : '{}';

        // Scroll runner tab into view if not active
        const runnerTab = new bootstrap.Tab(document.getElementById('runner-tab'));
        runnerTab.show();

        // Refresh dynamic code snippet
        refreshSnippet();
    }

    // Switch between cURL, PHP, etc.
    function switchDocLang(lang) {
        currentSelectedLang = lang;
        
        // Remove active class from all tab buttons
        document.querySelectorAll('#api-tabs button').forEach(btn => btn.classList.remove('active'));
        // Add to active
        document.getElementById(`tab-${lang}`).classList.add('active');

        // Update Label
        document.getElementById('snippetLangLabel').innerText = lang.toUpperCase();

        refreshSnippet();
    }

    // Generate language snippet based on active state
    function refreshSnippet() {
        const config = routeTemplates[currentSelectedRoute];
        if (!config) return;

        const path = config.path;
        const method = config.method;
        const bodyStr = config.body ? JSON.stringify(config.body, null, 2) : null;
        const baseUrl = "{{ url('/') }}";
        const fullUrl = baseUrl + path;

        let code = '';

        if (currentSelectedLang === 'curl') {
            code = `curl -X ${method} "${fullUrl}" \\\n  -H "x-api-key: ${activeToken}" \\\n  -H "Content-Type: application/json" \\\n  -H "Accept: application/json"`;
            if (method === 'POST' && bodyStr) {
                // escape payload for single quotes in bash
                const escapedBody = bodyStr.replace(/'/g, "'\\''");
                code += ` \\\n  -d '${escapedBody}'`;
            }
        } 
        else if (currentSelectedLang === 'php') {
            if (method === 'GET') {
                code = `use Illuminate\\Support\\Facades\\Http;\n\n$response = Http::withHeaders([\n    'x-api-key' => '${activeToken}',\n    'Accept' => 'application/json'\n])->get('${fullUrl}');\n\nprint_r($response->json());`;
            } else {
                code = `use Illuminate\\Support\\Facades\\Http;\n\n$response = Http::withHeaders([\n    'x-api-key' => '${activeToken}',\n    'Accept' => 'application/json'\n])->post('${fullUrl}', ${bodyStr ? bodyStr.replace(/\n/g, '\n    ') : '[]'});\n\nprint_r($response->json());`;
            }
        } 
        else if (currentSelectedLang === 'node') {
            code = `fetch('${fullUrl}', {\n  method: '${method}',\n  headers: {\n    'x-api-key': '${activeToken}',\n    'Content-Type': 'application/json',\n    'Accept': 'application/json'\n  }${method === 'POST' && bodyStr ? ',\n  body: JSON.stringify(' + bodyStr.replace(/\n/g, '\n  ') + ')' : ''}\n})\n.then(res => res.json())\n.then(data => console.log(data));`;
        } 
        else if (currentSelectedLang === 'python') {
            code = `import requests\n\nheaders = {\n    'x-api-key': '${activeToken}',\n    'Content-Type': 'application/json',\n    'Accept': 'application/json'\n}\n`;
            if (method === 'GET') {
                code += `\nr = requests.get('${fullUrl}', headers=headers)\nprint(r.json())`;
            } else {
                code += `\npayload = ${bodyStr ? bodyStr.replace(/\n/g, '\n    ') : '{}'}\n\nr = requests.post('${fullUrl}', json=payload, headers=headers)\nprint(r.json())`;
            }
        } 
        else if (currentSelectedLang === 'java') {
            code = `import java.net.URI;\nimport java.net.http.HttpClient;\nimport java.net.http.HttpRequest;\nimport java.net.http.HttpResponse;\n\nvar client = HttpClient.newHttpClient();\nvar request = HttpRequest.newBuilder()\n    .uri(URI.create("${fullUrl}"))\n    .header("x-api-key", "${activeToken}")\n    .header("Content-Type", "application/json")\n    .header("Accept", "application/json")\n`;
            if (method === 'POST') {
                const compactBody = bodyStr ? bodyStr.replace(/"/g, '\\"').replace(/\n/g, '') : '{}';
                code += `    .POST(HttpRequest.BodyPublishers.ofString("${compactBody}"))\n`;
            } else {
                code += `    .GET()\n`;
            }
            code += `    .build();\n\nvar response = client.send(request, HttpResponse.BodyHandlers.ofString());\nSystem.out.println(response.body());`;
        } 
        else if (currentSelectedLang === 'ruby') {
            code = `require 'net/http'\nrequire 'uri'\nrequire 'json'\n\nuri = URI.parse('${fullUrl}')\nrequest = Net::HTTP::Post.new(uri)\n`;
            if (method === 'GET') {
                code = `require 'net/http'\nrequire 'uri'\nrequire 'json'\n\nuri = URI.parse('${fullUrl}')\nrequest = Net::HTTP::Get.new(uri)\n`;
            }
            code += `request.content_type = 'application/json'\nrequest['x-api-key'] = '${activeToken}'\nrequest['Accept'] = 'application/json'\n`;
            if (method === 'POST' && bodyStr) {
                code += `request.body = JSON.dump(${bodyStr.replace(/\n/g, '\n  ')})\n`;
            }
            code += `\nresponse = Net::HTTP.start(uri.hostname, uri.port) do |http|\n  http.request(request)\nend\nputs JSON.parse(response.body)`;
        }

        document.getElementById('snippetPre').innerText = code;
    }

    // Copy snippet to clipboard
    function copySnippet() {
        const codeText = document.getElementById('snippetPre').innerText;
        navigator.clipboard.writeText(codeText);
        alert('Code snippet copied to clipboard!');
    }

    // Execute Request via Fetch
    function executeRequest(event) {
        event.preventDefault();
        
        const runBtn = document.getElementById('runBtn');
        const respContainer = document.getElementById('responseContainer');
        const respStatus = document.getElementById('respStatus');
        const respTime = document.getElementById('respTime');
        const respBody = document.getElementById('respBody');

        runBtn.disabled = true;
        runBtn.innerText = 'Executing...';
        respContainer.classList.remove('d-none');
        respBody.innerText = 'Sending network request...';
        respStatus.innerText = 'Status: -';
        respTime.innerText = 'Latency: -';

        const method = document.getElementById('requestMethod').value;
        const path = document.getElementById('requestPath').value;
        const headersRaw = document.getElementById('requestHeaders').value;
        const bodyRaw = document.getElementById('requestBody').value;

        // Parse custom headers
        let headersObj = {};
        try {
            headersObj = JSON.parse(headersRaw);
        } catch (e) {
            respBody.innerText = 'JSON Error: Invalid HTTP headers format. Must be a valid JSON object.';
            runBtn.disabled = false;
            runBtn.innerText = 'Execute Request';
            return;
        }

        // Parse body
        let options = {
            method: method,
            headers: headersObj
        };

        if (method === 'POST') {
            try {
                if (bodyRaw && bodyRaw.trim() !== '{}' && bodyRaw.trim() !== '') {
                    options.body = JSON.stringify(JSON.parse(bodyRaw));
                }
            } catch (e) {
                respBody.innerText = 'JSON Error: Invalid Request Payload format. Must be a valid JSON object.';
                runBtn.disabled = false;
                runBtn.innerText = 'Execute Request';
                return;
            }
        }

        const start = performance.now();
        const requestUrl = "{{ url('/') }}" + path;

        fetch(requestUrl, options)
            .then(res => {
                const duration = Math.round(performance.now() - start);
                respStatus.innerText = `Status: ${res.status} ${res.statusText}`;
                respTime.innerText = `Latency: ${duration} ms`;
                
                return res.text();
            })
            .then(text => {
                try {
                    const json = JSON.parse(text);
                    respBody.innerText = JSON.stringify(json, null, 2);
                } catch (e) {
                    respBody.innerText = text;
                }
            })
            .catch(err => {
                const duration = Math.round(performance.now() - start);
                respStatus.innerText = 'Status: Network Fail';
                respTime.innerText = `Latency: ${duration} ms`;
                respBody.innerText = 'Fetch Exception: ' + err.message;
            })
            .finally(() => {
                runBtn.disabled = false;
                runBtn.innerText = 'Execute Request';
            });
    }

    // Initialize Page
    document.addEventListener('DOMContentLoaded', () => {
        // If a new key has just been generated, auto-fill it in activeToken
        const newKeyVal = document.getElementById('newKeyVal');
        if (newKeyVal && newKeyVal.value) {
            activeToken = newKeyVal.value;
            updateHeadersTextarea();
        }
        
        // Trigger default snippet render
        selectRoute('register-employee');
    });
</script>

<style>
    @keyframes pulse {
        0% { transform: scale(0.9); opacity: 0.7; }
        50% { transform: scale(1.15); opacity: 1; }
        100% { transform: scale(0.9); opacity: 0.7; }
    }
    .hover-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }
    .hover-card:hover {
        transform: translateY(-1px);
        background-color: rgba(255, 255, 255, 0.08) !important;
        border-color: rgba(168, 85, 247, 0.5) !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
    }
    .btn-xs {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        border-radius: 0.25rem;
    }
    /* Set dark mode form elements high-visibility styles */
    .form-control, .form-select {
        border-color: #334155 !important;
    }
    .form-control:focus, .form-select:focus {
        border-color: #a855f7 !important;
    }

    /* Flat Tabs Styling */
    .flat-tab {
        background: transparent !important;
        border: none !important;
        color: #64748b !important; /* slate-500 */
        font-weight: 700;
        font-size: 12.5px;
        letter-spacing: 0.05em;
        padding: 0.5rem 0;
        margin-right: 2rem;
        position: relative;
        border-radius: 0 !important;
        transition: color 0.2s ease;
        outline: none !important;
    }
    .flat-tab:hover {
        color: #0f172a !important; /* slate-900 */
    }
    .flat-tab.active {
        color: #4f46e5 !important; /* indigo */
    }
    .flat-tab.active::after {
        content: '';
        position: absolute;
        bottom: -2px; /* align with border-bottom of nav container */
        left: 0;
        width: 100%;
        height: 2px;
        background-color: #4f46e5;
    }
</style>
@endsection
