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
    Route::post('/manager-agent/generate', [\App\Http\Controllers\ManagerAgentController::class, 'generate'])->name('manager.generate');
    Route::get('/manager-agent/data-entry', [\App\Http\Controllers\ManagerAgentController::class, 'dataEntry'])->name('manager.data-entry');
    Route::post('/manager-agent/task', [\App\Http\Controllers\ManagerAgentController::class, 'storeTask'])->name('manager.store-task');
    Route::put('/manager-agent/task/{id}', [\App\Http\Controllers\ManagerAgentController::class, 'updateTask'])->name('manager.update-task');
    Route::delete('/manager-agent/task/{id}', [\App\Http\Controllers\ManagerAgentController::class, 'destroyTask'])->name('manager.destroy-task');
    Route::get('/manager-agent/tasks', [\App\Http\Controllers\ManagerAgentController::class, 'taskEntry'])->name('manager.task-entry');
    Route::post('/manager-agent/commit', [\App\Http\Controllers\ManagerAgentController::class, 'storeCommit'])->name('manager.store-commit');
    Route::put('/manager-agent/commit/{id}', [\App\Http\Controllers\ManagerAgentController::class, 'updateCommit'])->name('manager.update-commit');
    Route::delete('/manager-agent/commit/{id}', [\App\Http\Controllers\ManagerAgentController::class, 'destroyCommit'])->name('manager.destroy-commit');
    Route::get('/manager-agent/attendance', [\App\Http\Controllers\ManagerAgentController::class, 'attendanceRegistry'])->name('manager.attendance-registry');
    Route::post('/manager-agent/attendance', [\App\Http\Controllers\ManagerAgentController::class, 'storeAttendance'])->name('manager.store-attendance');
    Route::put('/manager-agent/attendance/{id}', [\App\Http\Controllers\ManagerAgentController::class, 'updateAttendance'])->name('manager.update-attendance');
    Route::delete('/manager-agent/attendance/{id}', [\App\Http\Controllers\ManagerAgentController::class, 'destroyAttendance'])->name('manager.destroy-attendance');
    Route::post('/manager-agent/meeting', [\App\Http\Controllers\ManagerAgentController::class, 'storeMeeting'])->name('manager.store-meeting');
    Route::post('/manager-agent/team-member', [\App\Http\Controllers\ManagerAgentController::class, 'storeTeamMember'])->name('manager.store-team-member');
    Route::put('/manager-agent/team-member/{id}', [\App\Http\Controllers\ManagerAgentController::class, 'updateTeamMember'])->name('manager.update-team-member');
    Route::delete('/manager-agent/team-member/{id}', [\App\Http\Controllers\ManagerAgentController::class, 'destroyTeamMember'])->name('manager.destroy-team-member');
    
    // Reports History & Details Routes
    Route::get('/manager-agent/reports', [\App\Http\Controllers\ManagerAgentController::class, 'reports'])->name('manager.reports');
    Route::get('/manager-agent/reports/{id}', [\App\Http\Controllers\ManagerAgentController::class, 'detail'])->name('manager.report-detail');
    Route::get('/manager-agent/employee-report/{id}', [\App\Http\Controllers\ManagerAgentController::class, 'employeeReport'])->name('manager.employee-report');

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
