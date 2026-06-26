<?php

$file = 'resources/views/manager/dashboard.blade.php';
$content = file_get_contents($file);

// 1. Add Search Date Option at the top
$searchDateHtml = <<<HTML
              <!-- Quick Search Date -->
              <form action="{{ route('manager.dashboard') }}" method="GET" class="d-flex align-items-center gap-2 bg-white px-3 py-2 rounded-pill shadow-sm border border-secondary-subtle m-0 flex-shrink-0">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="text-primary" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/></svg>
                  <span class="text-secondary small fw-semibold">Search Date:</span>
                  <input type="hidden" name="range_type" value="custom_range">
                  <input type="date" name="start_date" class="form-control form-control-sm border-0 text-dark bg-transparent p-0 shadow-none" style="outline: none;" onchange="document.getElementById('quickEnd').value = this.value; this.form.submit()">
                  <input type="hidden" name="end_date" id="quickEnd">
              </form>
              
              <!-- Date Picker UI (Leaderboard Style) -->
HTML;

$content = str_replace('<!-- Date Picker UI (Leaderboard Style) -->', $searchDateHtml, $content);

// 2. Add IDs to Attendance DOM elements to update them via JS
$attendanceHtmlSearch = <<<HTML
              <div class="d-flex justify-content-around align-items-center mb-3">
                  <div class="text-center">
                      <h3 class="text-success mb-0 font-outfit">{{ \$presentPct }}%</h3>
                      <span class="small text-secondary">Present</span>
                  </div>
                  <div class="text-center">
                      <h3 class="text-warning mb-0 font-outfit">{{ \$latePct }}%</h3>
                      <span class="small text-secondary">Late</span>
                  </div>
                  <div class="text-center">
                      <h3 class="text-danger mb-0 font-outfit">{{ \$absentPct }}%</h3>
                      <span class="small text-secondary">Absent</span>
                  </div>
              </div>
              <div class="progress" style="height: 8px;">
                  <div class="progress-bar bg-success" role="progressbar" style="width: {{ \$presentPct }}%"></div>
                  <div class="progress-bar bg-warning" role="progressbar" style="width: {{ \$latePct }}%"></div>
                  <div class="progress-bar bg-danger" role="progressbar" style="width: {{ \$absentPct }}%"></div>
              </div>
HTML;

$attendanceHtmlReplace = <<<HTML
              <div class="d-flex justify-content-around align-items-center mb-3">
                  <div class="text-center">
                      <h3 class="text-success mb-0 font-outfit" id="attendance-present-val">{{ \$presentPct }}%</h3>
                      <span class="small text-secondary">Present</span>
                  </div>
                  <div class="text-center">
                      <h3 class="text-warning mb-0 font-outfit" id="attendance-late-val">{{ \$latePct }}%</h3>
                      <span class="small text-secondary">Late</span>
                  </div>
                  <div class="text-center">
                      <h3 class="text-danger mb-0 font-outfit" id="attendance-absent-val">{{ \$absentPct }}%</h3>
                      <span class="small text-secondary">Absent</span>
                  </div>
              </div>
              <div class="progress attendance-progress" style="height: 8px;">
                  <div class="progress-bar bg-success" role="progressbar" style="width: {{ \$presentPct }}%"></div>
                  <div class="progress-bar bg-warning" role="progressbar" style="width: {{ \$latePct }}%"></div>
                  <div class="progress-bar bg-danger" role="progressbar" style="width: {{ \$absentPct }}%"></div>
              </div>
HTML;

$content = str_replace($attendanceHtmlSearch, $attendanceHtmlReplace, $content);

// 3. Update the Javascript fetch success block
$jsSearch = <<<JS
                if (workloadChartInstance && data.workload) {
                    workloadChartInstance.data.labels = data.workload.labels;
                    workloadChartInstance.data.datasets[0].data = data.workload.data;
                    workloadChartInstance.update();
                }
            } else {
JS;

$jsReplace = <<<JS
                if (workloadChartInstance && data.workload) {
                    workloadChartInstance.data.labels = data.workload.labels;
                    workloadChartInstance.data.datasets[0].data = data.workload.data;
                    workloadChartInstance.update();
                }

                if (data.attendance) {
                    animateValue('attendance-present-val', data.attendance.present, true);
                    animateValue('attendance-late-val', data.attendance.late, true);
                    animateValue('attendance-absent-val', data.attendance.absent, true);
                    
                    const progressBars = document.querySelectorAll('.attendance-progress .progress-bar');
                    if (progressBars.length >= 3) {
                        progressBars[0].style.width = data.attendance.present + '%';
                        progressBars[1].style.width = data.attendance.late + '%';
                        progressBars[2].style.width = data.attendance.absent + '%';
                    }
                }
            } else {
JS;

$content = str_replace($jsSearch, $jsReplace, $content);

file_put_contents($file, $content);
echo "dashboard.blade.php updated.\n";
