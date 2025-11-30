<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public endpoints (no auth required)
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String(),
    ]);
});

// API endpoints (require API key authentication)
Route::middleware(['api.key'])->prefix('v1')->group(function () {
    
    // Devices (formerly Sessions)
    Route::get('/devices', [App\Http\Controllers\Api\SessionApiController::class, 'index']);
    Route::post('/devices', [App\Http\Controllers\Api\SessionApiController::class, 'store']);
    Route::get('/devices/{session}', [App\Http\Controllers\Api\SessionApiController::class, 'show']);
    Route::get('/devices/{session}/status', [App\Http\Controllers\Api\SessionApiController::class, 'status']);
    Route::get('/devices/{session}/pair', [App\Http\Controllers\Api\SessionApiController::class, 'pair']);
    
    // Messages - Standard format (session_id in body)
    Route::post('/messages', [App\Http\Controllers\Api\MessageApiController::class, 'store']);
    Route::get('/messages', [App\Http\Controllers\Api\MessageApiController::class, 'index']);
    Route::get('/messages/{message}', [App\Http\Controllers\Api\MessageApiController::class, 'show']);
    
    // Messages - RESTful format (device in URL)
    Route::post('/devices/{session}/messages', [App\Http\Controllers\Api\MessageApiController::class, 'store']);
    Route::get('/devices/{session}/messages', [App\Http\Controllers\Api\MessageApiController::class, 'index']);
    Route::post('/devices/{session}/messages/sync', [App\Http\Controllers\Api\MessageApiController::class, 'sync']);
    
    // Messages - Legacy format support (for backward compatibility)
    Route::post('/devices/{session}/messages/text', [App\Http\Controllers\Api\MessageApiController::class, 'store']);
    Route::post('/devices/{session}/messages/image', [App\Http\Controllers\Api\MessageApiController::class, 'store']);
    Route::post('/devices/{session}/messages/video', [App\Http\Controllers\Api\MessageApiController::class, 'store']);
    Route::post('/devices/{session}/messages/document', [App\Http\Controllers\Api\MessageApiController::class, 'store']);
    Route::post('/devices/{session}/messages/poll', [App\Http\Controllers\Api\MessageApiController::class, 'store']);
    Route::post('/devices/{session}/messages/button', [App\Http\Controllers\Api\MessageApiController::class, 'store']);
    Route::post('/devices/{session}/messages/list', [App\Http\Controllers\Api\MessageApiController::class, 'store']);
    
    // Account
    Route::get('/account', [App\Http\Controllers\Api\AccountApiController::class, 'show']);
    Route::get('/account/usage', [App\Http\Controllers\Api\AccountApiController::class, 'usage']);
    
    // Templates
    Route::get('/templates', [App\Http\Controllers\Api\TemplateApiController::class, 'index']);
    Route::post('/templates', [App\Http\Controllers\Api\TemplateApiController::class, 'store']);
    Route::get('/templates/{template}', [App\Http\Controllers\Api\TemplateApiController::class, 'show']);
    Route::put('/templates/{template}', [App\Http\Controllers\Api\TemplateApiController::class, 'update']);
    Route::patch('/templates/{template}', [App\Http\Controllers\Api\TemplateApiController::class, 'update']);
    Route::delete('/templates/{template}', [App\Http\Controllers\Api\TemplateApiController::class, 'destroy']);
    Route::post('/templates/{template}/preview', [App\Http\Controllers\Api\TemplateApiController::class, 'preview']);
    
    // OTP
    Route::post('/messages/otp', [App\Http\Controllers\Api\OtpApiController::class, 'send']);
    Route::post('/messages/verify-otp', [App\Http\Controllers\Api\OtpApiController::class, 'verify']);
    Route::get('/messages/otp/{otp}/status', [App\Http\Controllers\Api\OtpApiController::class, 'status']);
});


