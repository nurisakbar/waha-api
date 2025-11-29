<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();

// Redirect root to login, or home if already authenticated
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('home');
    }
    return redirect()->route('login');
});

// Keep landing page accessible via /landing if needed
Route::get('/landing', [App\Http\Controllers\LandingController::class, 'index'])->name('landing');

// Public Documentation Page
Route::get('/docs', [App\Http\Controllers\DocumentationController::class, 'index'])->name('docs.index');

Route::middleware('auth')->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    
    // WhatsApp Sessions - with rate limiting for creation
    Route::resource('sessions', App\Http\Controllers\SessionController::class);
    Route::post('/sessions', [App\Http\Controllers\SessionController::class, 'store'])
        ->middleware('throttle:5,1') // Max 5 requests per minute
        ->name('sessions.store');
    Route::get('/sessions/{session}/pair', [App\Http\Controllers\SessionController::class, 'pair'])->name('sessions.pair');
    Route::get('/sessions/{session}/check-status', [App\Http\Controllers\SessionController::class, 'checkStatus'])
        ->middleware('throttle:30,1') // Max 30 status checks per minute
        ->name('sessions.check-status');
    Route::post('/sessions/{session}/refresh-qr', [App\Http\Controllers\SessionController::class, 'refreshQrCode'])
        ->middleware('throttle:10,1') // Max 10 QR refreshes per minute
        ->name('sessions.refresh-qr');
    Route::post('/sessions/{session}/stop', [App\Http\Controllers\SessionController::class, 'stop'])->name('sessions.stop');
    
    // Debug routes
    Route::get('/debug/session', [App\Http\Controllers\SessionDebugController::class, 'debug'])->name('debug.session');
    Route::post('/debug/session/restart', [App\Http\Controllers\SessionDebugController::class, 'restart'])->name('debug.session.restart');
    
    // Messages - DataTables route must be before resource route
    Route::get('messages/data', [App\Http\Controllers\MessageController::class, 'index'])->name('messages.data');
    Route::resource('messages', App\Http\Controllers\MessageController::class)->except(['edit', 'update']);
    
    // Webhooks
    Route::resource('webhooks', App\Http\Controllers\WebhookController::class);
    
    // API Keys
    Route::resource('api-keys', App\Http\Controllers\ApiKeyController::class);
    
    // Contacts & Groups
    Route::get('/contacts', [App\Http\Controllers\ContactController::class, 'index'])->name('contacts.index');
    Route::get('/contacts/{session}', [App\Http\Controllers\ContactController::class, 'show'])->name('contacts.show');
    Route::get('/groups', [App\Http\Controllers\ContactController::class, 'groups'])->name('groups.index');
    Route::get('/groups/{session}', [App\Http\Controllers\ContactController::class, 'groupShow'])->name('groups.show');
    
    // Billing
    Route::get('/billing', [App\Http\Controllers\BillingController::class, 'index'])->name('billing.index');
    Route::post('/billing/subscribe', [App\Http\Controllers\BillingController::class, 'subscribe'])->name('billing.subscribe');
    
    // Analytics
    Route::get('/analytics', [App\Http\Controllers\AnalyticsController::class, 'index'])->name('analytics.index');
    
    // API Documentation
    Route::get('/api-docs', [App\Http\Controllers\ApiDocumentationController::class, 'index'])->name('api-docs.index');
});

// Webhook receiver (public endpoint)
Route::post('/webhook/receive/{session}', [App\Http\Controllers\WebhookController::class, 'receive'])->name('webhook.receive');
