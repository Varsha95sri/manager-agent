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
    Route::post('/manager-agent/commit', [\App\Http\Controllers\ManagerAgentController::class, 'storeCommit'])->name('manager.store-commit');
    Route::put('/manager-agent/commit/{id}', [\App\Http\Controllers\ManagerAgentController::class, 'updateCommit'])->name('manager.update-commit');
    Route::delete('/manager-agent/commit/{id}', [\App\Http\Controllers\ManagerAgentController::class, 'destroyCommit'])->name('manager.destroy-commit');
    Route::post('/manager-agent/attendance', [\App\Http\Controllers\ManagerAgentController::class, 'storeAttendance'])->name('manager.store-attendance');
    Route::post('/manager-agent/meeting', [\App\Http\Controllers\ManagerAgentController::class, 'storeMeeting'])->name('manager.store-meeting');
    Route::post('/manager-agent/team-member', [\App\Http\Controllers\ManagerAgentController::class, 'storeTeamMember'])->name('manager.store-team-member');
    Route::put('/manager-agent/team-member/{id}', [\App\Http\Controllers\ManagerAgentController::class, 'updateTeamMember'])->name('manager.update-team-member');
    Route::delete('/manager-agent/team-member/{id}', [\App\Http\Controllers\ManagerAgentController::class, 'destroyTeamMember'])->name('manager.destroy-team-member');
    
    // Reports History & Details Routes
    Route::get('/manager-agent/reports', [\App\Http\Controllers\ManagerAgentController::class, 'reports'])->name('manager.reports');
    Route::get('/manager-agent/reports/{id}', [\App\Http\Controllers\ManagerAgentController::class, 'detail'])->name('manager.report-detail');

    // AI Chatbot Routes
    Route::get('/manager-agent/chatbot', [\App\Http\Controllers\ChatbotController::class, 'index'])->name('manager.chatbot');
    Route::post('/manager-agent/chatbot/ask', [\App\Http\Controllers\ChatbotController::class, 'ask'])->name('manager.chatbot.ask');
    Route::post('/manager-agent/chatbot/clear', [\App\Http\Controllers\ChatbotController::class, 'clear'])->name('manager.chatbot.clear');
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
