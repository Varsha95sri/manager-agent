<!-- resources/views/developer/partials/doc-section.blade.php -->
<div class="d-flex flex-column gap-4">
    
    <!-- 1. Generate Report Endpoint -->
    <div>
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h5 class="text-slate-200 font-bold mb-0">Generate Report</h5>
            <span class="badge bg-primary text-uppercase font-monospace text-xs" style="font-size: 10px;">POST</span>
        </div>
        <div class="mb-2">
            <code class="text-purple-400 font-monospace text-xs" style="word-break: break-all;">/api/v1/generate-report</code>
        </div>
        <p class="text-secondary text-sm mb-3">Sends team raw activity data and a report type to compile a structured performance synthesis report using the active LLM service.</p>
        
        <div class="bg-slate-950 rounded-4 border border-slate-800 position-relative mb-2">
            <pre class="m-0 p-3 text-slate-300 font-monospace text-xs overflow-x-auto" style="white-space: pre-wrap; font-family: monospace;">{{ $generateReportCode }}</pre>
            <button class="btn btn-sm btn-outline-secondary position-absolute top-0 end-0 m-2 font-semibold text-xs" onclick="copyCode(this)" style="padding: 2px 8px;">Copy</button>
        </div>

        <button class="btn btn-link btn-sm text-decoration-none text-purple-400 p-0 font-semibold text-xs" type="button" data-bs-toggle="collapse" data-bs-target="#response-report-{{ $lang }}">
            View Response Schema (200 OK)
        </button>
        <div class="collapse mt-2" id="response-report-{{ $lang }}">
            <div class="bg-slate-950 p-3 rounded-4 border border-slate-800 position-relative">
                <pre class="m-0 text-slate-400 font-monospace text-xs overflow-x-auto" style="white-space: pre-wrap; font-family: monospace;">{
  "team_productivity": 85,
  "top_performers": ["Alice", "Bob"],
  "attention_required": ["Charlie (Absent)"],
  "risks": ["Milestone delays on UI-14 due to missing task assets."],
  "full_report": "# Daily Performance Synthesis\n..."
}</pre>
            </div>
        </div>
    </div>

    <hr class="border-slate-800 my-1">

    <!-- 2. Analyze Team Endpoint -->
    <div>
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h5 class="text-slate-200 font-bold mb-0">Analyze Team</h5>
            <span class="badge bg-primary text-uppercase font-monospace text-xs" style="font-size: 10px;">POST</span>
        </div>
        <div class="mb-2">
            <code class="text-purple-400 font-monospace text-xs" style="word-break: break-all;">/api/v1/analyze-team</code>
        </div>
        <p class="text-secondary text-sm mb-3">Evaluates team member roles and specific numeric/action metrics for momentum and risk analysis.</p>
        
        <div class="bg-slate-950 rounded-4 border border-slate-800 position-relative mb-2">
            <pre class="m-0 p-3 text-slate-300 font-monospace text-xs overflow-x-auto" style="white-space: pre-wrap; font-family: monospace;">{{ $analyzeTeamCode }}</pre>
            <button class="btn btn-sm btn-outline-secondary position-absolute top-0 end-0 m-2 font-semibold text-xs" onclick="copyCode(this)" style="padding: 2px 8px;">Copy</button>
        </div>

        <button class="btn btn-link btn-sm text-decoration-none text-purple-400 p-0 font-semibold text-xs" type="button" data-bs-toggle="collapse" data-bs-target="#response-analyze-{{ $lang }}">
            View Response Schema (200 OK)
        </button>
        <div class="collapse mt-2" id="response-analyze-{{ $lang }}">
            <div class="bg-slate-950 p-3 rounded-4 border border-slate-800 position-relative">
                <pre class="m-0 text-slate-400 font-monospace text-xs overflow-x-auto" style="white-space: pre-wrap; font-family: monospace;">{
  "analysis": "The team exhibits strong development momentum. Alice leads with 12 commits..."
}</pre>
            </div>
        </div>
    </div>

    <hr class="border-slate-800 my-1">

    <!-- 3. AI Chat Endpoint -->
    <div>
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h5 class="text-slate-200 font-bold mb-0">AI Chat</h5>
            <span class="badge bg-primary text-uppercase font-monospace text-xs" style="font-size: 10px;">POST</span>
        </div>
        <div class="mb-2">
            <code class="text-purple-400 font-monospace text-xs" style="word-break: break-all;">/api/v1/chat</code>
        </div>
        <p class="text-secondary text-sm mb-3">Sends a stateless user prompt and an optional system guiding message directly to the backend LLM service.</p>
        
        <div class="bg-slate-950 rounded-4 border border-slate-800 position-relative mb-2">
            <pre class="m-0 p-3 text-slate-300 font-monospace text-xs overflow-x-auto" style="white-space: pre-wrap; font-family: monospace;">{{ $chatCode }}</pre>
            <button class="btn btn-sm btn-outline-secondary position-absolute top-0 end-0 m-2 font-semibold text-xs" onclick="copyCode(this)" style="padding: 2px 8px;">Copy</button>
        </div>

        <button class="btn btn-link btn-sm text-decoration-none text-purple-400 p-0 font-semibold text-xs" type="button" data-bs-toggle="collapse" data-bs-target="#response-chat-{{ $lang }}">
            View Response Schema (200 OK)
        </button>
        <div class="collapse mt-2" id="response-chat-{{ $lang }}">
            <div class="bg-slate-950 p-3 rounded-4 border border-slate-800 position-relative">
                <pre class="m-0 text-slate-400 font-monospace text-xs overflow-x-auto" style="white-space: pre-wrap; font-family: monospace;">{
  "reply": "Evaluating Alice's productivity: pushing 12 commits indicates high code-level momentum..."
}</pre>
            </div>
        </div>
    </div>

</div>
