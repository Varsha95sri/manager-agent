<?php

$file = 'resources/views/manager/dashboard.blade.php';
$content = file_get_contents($file);

// 1. Dropdown updates
$dropdownSearch = <<<HTML
                  <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 rounded-3" aria-labelledby="generateReportDropdown">
                      <li><a class="dropdown-item py-2" href="#" onclick="event.preventDefault(); document.getElementById('reportType').value='daily'; document.getElementById('reportForm').submit();"><i class="bi bi-file-earmark-text me-2 text-secondary"></i>Daily Report</a></li>
                      <li><a class="dropdown-item py-2" href="#" onclick="event.preventDefault(); document.getElementById('reportType').value='monthly'; document.getElementById('reportForm').submit();"><i class="bi bi-file-earmark-bar-graph me-2 text-secondary"></i>Monthly Summary</a></li>
                      <li><a class="dropdown-item py-2" href="#" onclick="event.preventDefault(); document.getElementById('reportType').value='executive'; document.getElementById('reportForm').submit();"><i class="bi bi-file-earmark-check me-2 text-secondary"></i>Executive Summary</a></li>
                  </ul>
HTML;

$dropdownReplace = <<<HTML
                  <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 rounded-3" aria-labelledby="generateReportDropdown">
                      <li><a class="dropdown-item py-2" href="#" onclick="event.preventDefault(); submitReportForm('daily', 'reportForm');"><i class="bi bi-file-earmark-text me-2 text-secondary"></i>Daily Report</a></li>
                      <li><a class="dropdown-item py-2" href="#" onclick="event.preventDefault(); submitReportForm('monthly', 'reportForm');"><i class="bi bi-file-earmark-bar-graph me-2 text-secondary"></i>Monthly Summary</a></li>
                      <li><a class="dropdown-item py-2" href="#" onclick="event.preventDefault(); submitReportForm('executive', 'reportForm');"><i class="bi bi-file-earmark-check me-2 text-secondary"></i>Executive Summary</a></li>
                  </ul>
HTML;
$content = str_replace($dropdownSearch, $dropdownReplace, $content);

// 2. Bottom form update
$bottomFormSearch = <<<HTML
                  <form method="POST" action="{{ route('manager.generate') }}">
                      @csrf
                      <input type="hidden" name="date" value="{{ date('Y-m-d') }}">
                      <button type="submit" class="btn btn-sm btn-primary">Generate Report Now</button>
                  </form>
HTML;

$bottomFormReplace = <<<HTML
                  <form method="POST" action="{{ route('manager.generate') }}" id="bottomReportForm" onsubmit="event.preventDefault(); submitReportForm('daily', 'bottomReportForm');">
                      @csrf
                      <input type="hidden" name="date" value="{{ date('Y-m-d') }}">
                      <button type="submit" class="btn btn-sm btn-primary">Generate Report Now</button>
                  </form>
HTML;
$content = str_replace($bottomFormSearch, $bottomFormReplace, $content);

// 3. Add script function
$scriptSearch = <<<JS
    function toggleCustomDates() {
JS;

$scriptReplace = <<<JS
    function submitReportForm(type, formId) {
        // Show overlay
        const overlay = document.createElement('div');
        overlay.id = 'reportLoadingOverlay';
        overlay.innerHTML = `
            <div style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.85); z-index:9999; display:flex; flex-direction:column; align-items:center; justify-content:center; backdrop-filter: blur(5px);">
                <div class="spinner-border text-primary" role="status" style="width: 3.5rem; height: 3.5rem; border-width: 0.35em;"></div>
                <h4 class="mt-4 font-outfit text-dark fw-bold">Analyzing Report...</h4>
                <p class="text-secondary fw-semibold">Please wait, AI is processing the organizational data.</p>
            </div>
        `;
        document.body.appendChild(overlay);

        const form = document.getElementById(formId);
        if (document.getElementById('reportType') && formId === 'reportForm') {
            document.getElementById('reportType').value = type;
        }
        
        const formData = new FormData(form);
        if (formId === 'bottomReportForm') {
            formData.append('type', type);
        }

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success && data.redirect_url) {
                window.location.href = data.redirect_url;
            } else {
                document.body.removeChild(overlay);
                showToast(data.message || 'Error generating report.');
            }
        })
        .catch(error => {
            console.error(error);
            document.body.removeChild(overlay);
            showToast('Server error while generating report. Please ensure the backend is running.');
        });
    }

    function toggleCustomDates() {
JS;

$content = str_replace($scriptSearch, $scriptReplace, $content);

file_put_contents($file, $content);
echo "dashboard.blade.php updated.\n";
