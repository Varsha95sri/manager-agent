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
    Route::post('/manager-agent/commit', [\App\Http\Controllers\ManagerAgentController::class, 'storeCommit'])->name('manager.store-commit');
    Route::post('/manager-agent/attendance', [\App\Http\Controllers\ManagerAgentController::class, 'storeAttendance'])->name('manager.store-attendance');
    Route::post('/manager-agent/meeting', [\App\Http\Controllers\ManagerAgentController::class, 'storeMeeting'])->name('manager.store-meeting');
    Route::post('/manager-agent/team-member', [\App\Http\Controllers\ManagerAgentController::class, 'storeTeamMember'])->name('manager.store-team-member');
    
    // Reports History & Details Routes
    Route::get('/manager-agent/reports', [\App\Http\Controllers\ManagerAgentController::class, 'reports'])->name('manager.reports');
    Route::get('/manager-agent/reports/{id}', [\App\Http\Controllers\ManagerAgentController::class, 'detail'])->name('manager.report-detail');

    // AI Chatbot Routes
    Route::get('/manager-agent/chatbot', [\App\Http\Controllers\ChatbotController::class, 'index'])->name('manager.chatbot');
    Route::post('/manager-agent/chatbot/ask', [\App\Http\Controllers\ChatbotController::class, 'ask'])->name('manager.chatbot.ask');
    Route::post('/manager-agent/chatbot/clear', [\App\Http\Controllers\ChatbotController::class, 'clear'])->name('manager.chatbot.clear');
});

require __DIR__.'/auth.php';
