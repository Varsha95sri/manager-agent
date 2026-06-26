<?php
$file = 'resources/views/manager/dashboard.blade.php';
$content = file_get_contents($file);

// Fix Search Date form
$searchSearch = <<<HTML
            <!-- Quick Search Date -->
              <form action="{{ route('manager.dashboard') }}" method="GET" class="d-flex align-items-center gap-2 bg-white px-3 py-2 rounded-pill shadow-sm border border-secondary-subtle m-0 flex-shrink-0">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="text-primary" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/></svg>
                  <span class="text-secondary small fw-semibold">Search Date:</span>
                  <input type="hidden" name="range_type" value="custom_range">
                  <input type="date" name="start_date" class="form-control form-control-sm border-0 text-dark bg-transparent p-0 shadow-none" style="outline: none;" onchange="document.getElementById('quickEnd').value = this.value; this.form.submit()">
                  <input type="hidden" name="end_date" id="quickEnd">
              </form>
HTML;

$searchReplace = <<<HTML
            <!-- Quick Search Date -->
              <form action="{{ route('manager.dashboard') }}" method="GET" class="d-flex align-items-center gap-2 bg-white px-3 py-2 rounded-pill shadow-sm border border-secondary-subtle m-0 flex-shrink-0">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="text-primary" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/></svg>
                  <span class="text-secondary small fw-semibold">Search Date:</span>
                  <input type="hidden" name="range_type" value="date_wise">
                  <input type="date" name="date" class="form-control form-control-sm border-0 text-dark bg-transparent p-0 shadow-none" style="outline: none;" onchange="this.form.submit()">
              </form>
HTML;
$content = str_replace($searchSearch, $searchReplace, $content);

// Remove the calendar button
$calBtnSearch = <<<HTML
              <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center rounded-pill bg-white text-nowrap shadow-sm border-secondary-subtle px-3 py-2 flex-shrink-0" data-bs-toggle="modal" data-bs-target="#productivityCalendarModal">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2" viewBox="0 0 16 16">
                    <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                  </svg>
                  Calendar
              </button>
HTML;
$content = str_replace($calBtnSearch, '', $content);

file_put_contents($file, $content);
echo "dashboard.blade.php updated.\n";
