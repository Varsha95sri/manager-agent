<?php
$file = 'resources/views/manager/report-detail.blade.php';
$content = file_get_contents($file);

$commitsAndAttendanceHtml = <<<HTML
        <!-- Commits for this specific date -->
        <div class="card glass-card p-4 mb-4">
            <h4 class="h5 font-outfit text-dark mb-3 d-flex align-items-center">
                <span class="d-inline-block bg-primary rounded-circle me-2 shadow-lg" style="width: 10px; height: 10px;"></span>
                Git Commits on this Date
            </h4>
            
            <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                <table class="table table-hover align-middle mb-0 text-dark" style="--bs-table-bg: transparent; --bs-table-hover-bg: rgba(255, 255, 255, 0.02); --bs-table-border-color: #334155;">
                    <thead class="text-secondary" style="font-size: 11px;">
                        <tr>
                            <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider">#</th>
                            <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider">Commit Hash</th>
                            <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider">Message</th>
                            <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider">Developer</th>
                            <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider">Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset(\$commits) && \$commits->count() > 0)
                            @foreach(\$commits as \$commit)
                                <tr>
                                    <td class="py-3 text-secondary">{{ \$loop->iteration }}</td>
                                    <td class="py-3 font-monospace text-primary small">{{ substr(\$commit->commit_hash ?? \$commit->commit_sha, 0, 7) }}</td>
                                    <td class="py-3 font-semibold text-dark">{{ \Illuminate\Support\Str::limit(\$commit->message, 50) }}</td>
                                    <td class="py-3 text-secondary">{{ \$commit->teamMember?->name ?? 'Unknown' }}</td>
                                    <td class="py-3 text-secondary small">{{ \$commit->committed_at->format('h:i A') }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="text-center py-4 text-secondary italic small">No commits pushed on this date.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Attendance for this specific date -->
        <div class="card glass-card p-4 mb-4">
            <h4 class="h5 font-outfit text-dark mb-3 d-flex align-items-center">
                <span class="d-inline-block bg-warning rounded-circle me-2 shadow-lg" style="width: 10px; height: 10px;"></span>
                Attendance on this Date
            </h4>
            
            <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                <table class="table table-hover align-middle mb-0 text-dark" style="--bs-table-bg: transparent; --bs-table-hover-bg: rgba(255, 255, 255, 0.02); --bs-table-border-color: #334155;">
                    <thead class="text-secondary" style="font-size: 11px;">
                        <tr>
                            <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider">#</th>
                            <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider">Employee</th>
                            <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider">Check In</th>
                            <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider">Check Out</th>
                            <th scope="col" class="pb-3 border-secondary-subtle uppercase font-semibold tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset(\$attendanceLogs) && \$attendanceLogs->count() > 0)
                            @foreach(\$attendanceLogs as \$log)
                                <tr>
                                    <td class="py-3 text-secondary">{{ \$loop->iteration }}</td>
                                    <td class="py-3 font-semibold text-dark">{{ \$log->teamMember?->name ?? 'Unknown' }}</td>
                                    <td class="py-3 text-secondary">{{ \$log->check_in ? \Carbon\Carbon::parse(\$log->check_in)->format('h:i A') : '--' }}</td>
                                    <td class="py-3 text-secondary">{{ \$log->check_out ? \Carbon\Carbon::parse(\$log->check_out)->format('h:i A') : '--' }}</td>
                                    <td class="py-3">
                                        @if(\$log->status === 'present')
                                            <span class="badge rounded-pill bg-success bg-opacity-10 text-success border border-success border-opacity-20 px-2.5 py-1" style="font-size: 10px;">Present</span>
                                        @elseif(\$log->status === 'late')
                                            <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning border border-warning border-opacity-20 px-2.5 py-1" style="font-size: 10px;">Late</span>
                                        @else
                                            <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger border border-danger border-opacity-20 px-2.5 py-1" style="font-size: 10px;">Absent</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="text-center py-4 text-secondary italic small">No attendance records for this date.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

HTML;

$content = str_replace('<!-- Narrative AI Text Review -->', $commitsAndAttendanceHtml . "\n        <!-- Narrative AI Text Review -->", $content);

file_put_contents($file, $content);
echo "Updated report-detail.blade.php\n";
