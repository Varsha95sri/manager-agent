<?php

use App\Http\Controllers\DeveloperController;
use App\Http\Controllers\PublicApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/developer/keys', [DeveloperController::class, 'index']);
    Route::post('/developer/keys', [DeveloperController::class, 'store']);
    Route::delete('/developer/keys/{id}', [DeveloperController::class, 'destroy']);
    Route::get('/developer/logs', [DeveloperController::class, 'logs']);
});

Route::prefix('v1')->middleware(['api.key', 'throttle:public-api'])->group(function () {
    Route::post('/generate-report', [PublicApiController::class, 'generateReport']);
    Route::post('/analyze-team', [PublicApiController::class, 'analyzeTeam']);
    Route::post('/chat', [PublicApiController::class, 'chat']);
    
    // New Sandbox Endpoints
    Route::post('/employees', [PublicApiController::class, 'createEmployee']);
    Route::get('/tasks', [PublicApiController::class, 'getTasks']);
    Route::post('/tasks', [PublicApiController::class, 'createTask']);
    Route::get('/attendance', [PublicApiController::class, 'getAttendance']);
    Route::post('/attendance', [PublicApiController::class, 'recordAttendance']);
    Route::get('/metrics', [PublicApiController::class, 'getMetrics']);
    Route::post('/analyze-momentum', [PublicApiController::class, 'analyzeMomentum']);
});
