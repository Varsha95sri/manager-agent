<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return redirect()->route('manager.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Manager Agent Routes
    Route::get('/manager-agent', [\App\Http\Controllers\ManagerAgentController::class, 'index'])->name('manager.dashboard');
    Route::get('/manager-agent/api/members', [\App\Http\Controllers\ManagerAgentController::class, 'apiMembers'])->name('manager.api.members');
    Route::get('/manager-agent/api/tasks', [\App\Http\Controllers\ManagerAgentController::class, 'apiTasks'])->name('manager.api.tasks');
    Route::get('/manager-agent/api/commits', [\App\Http\Controllers\ManagerAgentController::class, 'apiCommits'])->name('manager.api.commits');

    // GitLab Integration Routes
    Route::prefix('manager-agent/gitlab')->name('manager.gitlab.')->group(function () {
        Route::get('/', [App\Http\Controllers\GitLabController::class, 'index'])->name('index');
        Route::post('/save-credentials', [App\Http\Controllers\GitLabController::class, 'saveCredentials'])->name('credentials.save');
        Route::get('/test-connection', [App\Http\Controllers\GitLabController::class, 'testConnection'])->name('credentials.test');
        
        // Projects CRUD
        Route::post('/project', [App\Http\Controllers\GitLabController::class, 'storeProject'])->name('project.store');
        Route::put('/project/{id}/details', [App\Http\Controllers\GitLabController::class, 'updateProjectDetails'])->name('project.update_details');
        Route::delete('/project/{id}', [App\Http\Controllers\GitLabController::class, 'destroyProject'])->name('project.destroy');
        Route::put('/project/{id}', [App\Http\Controllers\GitLabController::class, 'updateProject'])->name('project.update'); // existing mapping update
        Route::post('/project/{id}/sync', [App\Http\Controllers\GitLabController::class, 'syncProjectCommits'])->name('project.sync');

        // Employees CRUD
        Route::post('/employee', [App\Http\Controllers\GitLabController::class, 'storeEmployee'])->name('employee.store');
        Route::put('/employee/{id}/details', [App\Http\Controllers\GitLabController::class, 'updateEmployeeDetails'])->name('employee.update_details');
        Route::delete('/employee/{id}', [App\Http\Controllers\GitLabController::class, 'destroyEmployee'])->name('employee.destroy');
        Route::put('/employee/{id}', [App\Http\Controllers\GitLabController::class, 'updateEmployee'])->name('employee.update'); // existing mapping update

        // Commits CRUD
        Route::post('/commit', [App\Http\Controllers\GitLabController::class, 'storeCommit'])->name('commit.store');
        Route::put('/commit/{id}', [App\Http\Controllers\GitLabController::class, 'updateCommit'])->name('commit.update');
        Route::delete('/commit/{id}', [App\Http\Controllers\GitLabController::class, 'destroyCommit'])->name('commit.destroy');
    });

    Route::get('/manager-agent/commits-list', [\App\Http\Controllers\ManagerAgentController::class, 'commitsList'])->name('manager.commits.index');
    Route::get('/manager-agent/repositories-list', function () { return redirect()->route('manager.gitlab.index', ['tab' => 'projects']); })->name('manager.repositories.index');
    Route::post('/manager-agent/generate', [\App\Http\Controllers\ManagerAgentController::class, 'generate'])->name('manager.generate');
    Route::get('/manager-agent/data-entry', [\App\Http\Controllers\ManagerAgentController::class, 'dataEntry'])->name('manager.data-entry');
    Route::post('/manager-agent/task', [\App\Http\Controllers\ManagerAgentController::class, 'storeTask'])->name('manager.store-task');
    Route::put('/manager-agent/task/{id}', [\App\Http\Controllers\ManagerAgentController::class, 'updateTask'])->name('manager.update-task');
    Route::delete('/manager-agent/task/{id}', [\App\Http\Controllers\ManagerAgentController::class, 'destroyTask'])->name('manager.destroy-task');
    Route::get('/manager-agent/tasks', [\App\Http\Controllers\ManagerAgentController::class, 'taskEntry'])->name('manager.task-entry');
    Route::post('/manager-agent/commit', [\App\Http\Controllers\ManagerAgentController::class, 'storeCommit'])->name('manager.store-commit');
    Route::put('/manager-agent/commit/{id}', [\App\Http\Controllers\ManagerAgentController::class, 'updateCommit'])->name('manager.update-commit');
    Route::delete('/manager-agent/commit/{id}', [\App\Http\Controllers\ManagerAgentController::class, 'destroyCommit'])->name('manager.destroy-commit');

    // Repository CRUD routes
    Route::post('/manager-agent/repository', [\App\Http\Controllers\ManagerAgentController::class, 'storeRepository'])->name('manager.store-repository');
    Route::put('/manager-agent/repository/{id}', [\App\Http\Controllers\ManagerAgentController::class, 'updateRepository'])->name('manager.update-repository');
    Route::delete('/manager-agent/repository/{id}', [\App\Http\Controllers\ManagerAgentController::class, 'destroyRepository'])->name('manager.destroy-repository');

    // Project store route
    Route::post('/manager-agent/project', [\App\Http\Controllers\ManagerAgentController::class, 'storeProject'])->name('manager.store-project');
    Route::get('/manager-agent/attendance', [\App\Http\Controllers\ManagerAgentController::class, 'attendanceRegistry'])->name('manager.attendance-registry');
    Route::post('/manager-agent/attendance', [\App\Http\Controllers\ManagerAgentController::class, 'storeAttendance'])->name('manager.store-attendance');
    Route::put('/manager-agent/attendance/{id}', [\App\Http\Controllers\ManagerAgentController::class, 'updateAttendance'])->name('manager.update-attendance');
    Route::delete('/manager-agent/attendance/{id}', [\App\Http\Controllers\ManagerAgentController::class, 'destroyAttendance'])->name('manager.destroy-attendance');
    Route::get('/manager-agent/meetings', [\App\Http\Controllers\ManagerAgentController::class, 'meetingsList'])->name('manager.meetings.index');
    Route::post('/manager-agent/meeting', [\App\Http\Controllers\ManagerAgentController::class, 'storeMeeting'])->name('manager.store-meeting');
    Route::put('/manager-agent/meeting/{id}', [\App\Http\Controllers\ManagerAgentController::class, 'updateMeeting'])->name('manager.update-meeting');
    Route::delete('/manager-agent/meeting/{id}', [\App\Http\Controllers\ManagerAgentController::class, 'destroyMeeting'])->name('manager.destroy-meeting');
    Route::post('/manager-agent/team-member', [\App\Http\Controllers\ManagerAgentController::class, 'storeTeamMember'])->name('manager.store-team-member');
    Route::put('/manager-agent/team-member/{id}', [\App\Http\Controllers\ManagerAgentController::class, 'updateTeamMember'])->name('manager.update-team-member');
    Route::delete('/manager-agent/team-member/{id}', [\App\Http\Controllers\ManagerAgentController::class, 'destroyTeamMember'])->name('manager.destroy-team-member');
    
    // Reports History & Details Routes
    Route::get('/manager-agent/reports', [\App\Http\Controllers\ManagerAgentController::class, 'reports'])->name('manager.reports');
    Route::get('/manager-agent/reports/{id}', [\App\Http\Controllers\ManagerAgentController::class, 'detail'])->name('manager.report-detail');
    Route::delete('/manager-agent/reports/{id}', [\App\Http\Controllers\ManagerAgentController::class, 'destroyReport'])->name('manager.destroy-report');
    Route::get('/manager-agent/employee-report/{id}', [\App\Http\Controllers\ManagerAgentController::class, 'employeeReport'])->name('manager.employee-report');
    Route::get('/manager-agent/group-report', [\App\Http\Controllers\ManagerAgentController::class, 'groupReport'])->name('manager.group-report');

    // AI Chatbot Routes
    Route::get('/manager-agent/chatbot', [\App\Http\Controllers\ChatbotController::class, 'index'])->name('manager.chatbot');
    Route::post('/manager-agent/chatbot/ask', [\App\Http\Controllers\ChatbotController::class, 'ask'])->name('manager.chatbot.ask');
    Route::post('/manager-agent/chatbot/clear', [\App\Http\Controllers\ChatbotController::class, 'clear'])->name('manager.chatbot.clear');

    // Developer Tools Web Routes
    Route::get('/manager-agent/developer', [\App\Http\Controllers\DeveloperWebController::class, 'index'])->name('developer.index');
    Route::post('/manager-agent/developer/keys', [\App\Http\Controllers\DeveloperWebController::class, 'store'])->name('developer.keys.store');
    Route::delete('/manager-agent/developer/keys/{id}', [\App\Http\Controllers\DeveloperWebController::class, 'destroy'])->name('developer.keys.destroy');
    Route::post('/manager-agent/developer/third-party', [\App\Http\Controllers\DeveloperWebController::class, 'storeThirdParty'])->name('developer.third-party.store');
    Route::put('/manager-agent/developer/third-party/{id}', [\App\Http\Controllers\DeveloperWebController::class, 'updateThirdParty'])->name('developer.third-party.update');
    Route::post('/manager-agent/developer/third-party/{id}/toggle', [\App\Http\Controllers\DeveloperWebController::class, 'toggleThirdParty'])->name('developer.third-party.toggle');
    Route::delete('/manager-agent/developer/third-party/{id}', [\App\Http\Controllers\DeveloperWebController::class, 'destroyThirdParty'])->name('developer.third-party.destroy');

    // Employee CRUD & Import/Export Routes
    Route::get('/manager-agent/employees', [\App\Http\Controllers\EmployeeController::class, 'index'])->name('manager.employees.index');
    Route::post('/manager-agent/employees', [\App\Http\Controllers\EmployeeController::class, 'store'])->name('manager.employees.store');
    Route::put('/manager-agent/employees/{id}', [\App\Http\Controllers\EmployeeController::class, 'update'])->name('manager.employees.update');
    Route::delete('/manager-agent/employees/{id}', [\App\Http\Controllers\EmployeeController::class, 'destroy'])->name('manager.employees.destroy');
    Route::get('/manager-agent/employees-export', [\App\Http\Controllers\EmployeeController::class, 'export'])->name('manager.employees.export');
    Route::post('/manager-agent/employees-import', [\App\Http\Controllers\EmployeeController::class, 'import'])->name('manager.employees.import');

    // Tasks CSV Export/Import Routes
    Route::get('/manager-agent/tasks-export', [\App\Http\Controllers\ManagerAgentController::class, 'exportTasks'])->name('manager.tasks.export');
    Route::post('/manager-agent/tasks-import', [\App\Http\Controllers\ManagerAgentController::class, 'importTasks'])->name('manager.tasks.import');

    // Attendance CSV Export/Import Routes
    Route::get('/manager-agent/attendance-export', [\App\Http\Controllers\ManagerAgentController::class, 'exportAttendance'])->name('manager.attendance.export');
    Route::post('/manager-agent/attendance-import', [\App\Http\Controllers\ManagerAgentController::class, 'importAttendance'])->name('manager.attendance.import');
});

Route::get('/view-logs', function () {
    $path = storage_path('logs/laravel.log');
    if (file_exists($path)) {
        $content = file_get_contents($path);
        // Return last 40000 characters to ensure we capture the exception message
        return '<pre>' . htmlspecialchars(substr($content, -40000)) . '</pre>';
    }
    return 'Log file not found';
});

Route::get('/force-migrate-fresh', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate:fresh', [
            '--seed' => true,
            '--force' => true
        ]);
        return '<h2>Database Rebuilt Successfully!</h2><pre>' . \Illuminate\Support\Facades\Artisan::output() . '</pre>';
    } catch (\Throwable $e) {
        return '<h2>Migration Failed!</h2><pre>' . $e->getMessage() . "\n\n" . $e->getTraceAsString() . '</pre>';
    }
});

require __DIR__.'/auth.php';
