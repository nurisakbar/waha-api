@extends('layouts.base')

@section('title', 'API Documentation')

@push('styles')
<style>
    .api-section-title {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 0;
    }
    .api-badge {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: .08em;
    }
    .api-code {
        background-color: #111827;
        color: #e5e7eb;
        border-radius: .35rem;
        padding: .75rem 1rem;
        font-size: 0.85rem;
        font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        overflow-x: auto;
        margin-bottom: 1rem;
    }
    .api-code code {
        color: inherit;
        background: transparent;
        padding: 0;
        border: 0;
        white-space: pre;
        display: block;
    }
    .api-inline-code {
        font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        background-color: #f3f4f6;
        padding: 0.1rem 0.35rem;
        border-radius: 0.25rem;
        font-size: 0.8rem;
    }
    .api-endpoint {
        font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        font-size: 0.9rem;
    }
    .api-endpoint-method {
        font-weight: 700;
        margin-right: .5rem;
    }
    .api-endpoint-url {
        color: #22c55e;
        word-break: break-all;
    }
    .api-list {
        padding-left: 1.1rem;
    }
    .api-list li {
        margin-bottom: .3rem;
    }
    .endpoint-group {
        margin-bottom: 2rem;
    }
    .endpoint-group-header {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 0.5rem 0.5rem 0 0;
        font-weight: 700;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .endpoint-group-header i {
        font-size: 1.25rem;
    }
    .endpoint-group-body {
        border: 1px solid #e5e7eb;
        border-top: none;
        border-radius: 0 0 0.5rem 0.5rem;
        padding: 0;
    }
    .endpoint-item {
        border-bottom: 1px solid #e5e7eb;
        padding: 1.5rem;
    }
    .endpoint-item:last-child {
        border-bottom: none;
    }
    .endpoint-method-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 0.25rem;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        margin-right: 0.75rem;
    }
    .badge-get {
        background-color: #3b82f6;
        color: white;
    }
    .badge-post {
        background-color: #10b981;
        color: white;
    }
    .badge-put {
        background-color: #f59e0b;
        color: white;
    }
    .badge-patch {
        background-color: #f59e0b;
        color: white;
    }
    .badge-delete {
        background-color: #ef4444;
        color: white;
    }
    .endpoint-description {
        color: #6b7280;
        margin-top: 0.5rem;
        font-size: 0.9rem;
    }
    .code-tabs {
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        overflow: hidden;
        margin-top: 1rem;
    }
    .code-tabs-header {
        display: flex;
        background: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
    }
    .code-tab {
        padding: 0.75rem 1.5rem;
        cursor: pointer;
        border: none;
        background: transparent;
        font-size: 0.875rem;
        font-weight: 500;
        color: #6b7280;
        transition: all 0.2s;
        border-bottom: 2px solid transparent;
    }
    .code-tab:hover {
        background: #f3f4f6;
        color: #374151;
    }
    .code-tab.active {
        color: #10b981;
        border-bottom-color: #10b981;
        background: white;
    }
    .code-tab-content {
        display: none;
    }
    .code-tab-content.active {
        display: block;
    }
    .code-example-title {
        font-size: 0.875rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="api-section-title">{{ __('Overview') }}</span>
                    <span class="badge bg-primary api-badge">API</span>
                </div>
                <div class="card-body">
                    <p>
                        {{ __('Gunakan endpoint di bawah ini untuk mengirim pesan WhatsApp menggunakan WAHA melalui API.') }}
                    </p>
                    <p class="mb-1"><strong>{{ __('Base URL') }}</strong></p>
                    <div class="api-code mb-3"><code>{{ $baseUrl }}/api/v1</code></div>

                    <p class="mb-1"><strong>{{ __('Autentikasi') }}</strong></p>
                    <p class="mb-1">
                        {{ __('Setiap request memerlukan API Key yang dapat Anda buat di menu "API Keys".') }}
                    </p>
                    <p class="mb-2"><strong>{{ __('Format API Key:') }}</strong></p>
                    <ul class="api-list mb-3">
                        <li>{{ __('API Key dimulai dengan') }} <code>waha_</code></li>
                        <li>{{ __('Panjang total sekitar 53 karakter') }}</li>
                        <li>{{ __('Contoh:') }} <code>waha_abc123def456ghi789jkl012mno345pqr678stu901vwx234</code></li>
                    </ul>
                    <p class="mb-1"><strong>{{ __('Cara Menggunakan:') }}</strong></p>
                    <p class="mb-1">{{ __('Kirim API Key di header berikut:') }}</p>
                    <div class="api-code mb-3"><code>X-Api-Key: waha_your_full_api_key_here</code></div>
                    <div class="alert alert-warning mb-0">
                        <small>
                            <i class="fas fa-exclamation-triangle"></i> 
                            {{ __('Pastikan Anda menggunakan plain API key (dimulai dengan "waha_"), bukan hash atau key prefix.') }}
                        </small>
                    </div>

                    @if($apiKeys->count())
                        <p class="mb-1"><strong>{{ __('API Key aktif Anda') }}</strong></p>
                        <div class="alert alert-info mb-3">
                            <p class="mb-2"><strong>{{ __('Penting:') }}</strong></p>
                            <ul class="mb-0">
                                <li>{{ __('API Key hanya dapat dilihat sekali saat pertama dibuat.') }}</li>
                                <li>{{ __('Format API Key: waha_xxxxxxxxxxxxx (dimulai dengan "waha_")') }}</li>
                                <li>{{ __('Jika Anda tidak menyimpan API Key saat dibuat, Anda perlu membuat API Key baru.') }}</li>
                            </ul>
                        </div>
                        <p class="text-muted mb-0">
                            <small>{{ __('Anda memiliki') }} {{ $apiKeys->count() }} {{ __('API Key aktif. Lihat di menu "API Keys" untuk detail.') }}</small>
                        </p>
                    @else
                        <div class="alert alert-warning mb-0">
                            {{ __('Anda belum memiliki API Key aktif. Silakan buat di menu "API Keys".') }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sessions Endpoints Group -->
            <div class="endpoint-group">
                <div class="endpoint-group-header">
                    <i class="fas fa-mobile-alt"></i>
                    <span>{{ __('Sessions / Devices') }}</span>
                </div>
                <div class="endpoint-group-body">
                    <div class="endpoint-item">
                        <div class="d-flex align-items-center mb-2">
                            <span class="endpoint-method-badge badge-get">GET</span>
                            <span class="api-endpoint-url">{{ $baseUrl }}/api/v1/sessions</span>
                        </div>
                        <p class="endpoint-description mb-3">{{ __('Mendapatkan daftar semua device WhatsApp yang terhubung.') }}</p>
                        
                        <!-- Code Examples -->
                        <div class="code-tabs">
                            <div class="code-tabs-header">
                                <button class="code-tab active" onclick="switchCodeTab(this, 'sessions-curl')">cURL</button>
                                <button class="code-tab" onclick="switchCodeTab(this, 'sessions-php')">PHP</button>
                                <button class="code-tab" onclick="switchCodeTab(this, 'sessions-python')">Python</button>
                            </div>
                            <div id="sessions-curl" class="code-tab-content active">
                                <div class="api-code mb-0"><code>curl -X GET "{{ $baseUrl }}/api/v1/sessions" \
  -H "X-Api-Key: YOUR_API_KEY" \
  -H "Content-Type: application/json"</code></div>
                            </div>
                            <div id="sessions-php" class="code-tab-content">
                                <div class="api-code mb-0"><code>&lt;?php
$apiKey = 'YOUR_API_KEY';
$url = '{{ $baseUrl }}/api/v1/sessions';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-Api-Key: ' . $apiKey,
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$data = json_decode($response, true);
if ($httpCode === 200 && $data['success']) {
    foreach ($data['data'] as $session) {
        echo "Session: " . $session['name'] . "\n";
    }
}</code></div>
                            </div>
                            <div id="sessions-python" class="code-tab-content">
                                <div class="api-code mb-0"><code>import requests

api_key = 'YOUR_API_KEY'
url = '{{ $baseUrl }}/api/v1/sessions'

headers = {
    'X-Api-Key': api_key,
    'Content-Type': 'application/json'
}

response = requests.get(url, headers=headers)

if response.status_code == 200:
    data = response.json()
    if data['success']:
        for session in data['data']:
            print(f"Session: {session['name']}")</code></div>
                            </div>
                        </div>
                        
                        <p class="mt-3 mb-2"><strong>{{ __('Response Example') }}</strong></p>
                        <div class="api-code mb-0"><code>{
  "success": true,
  "data": [
    {
      "id": "session_123",
      "name": "My Session",
      "status": "connected",
      "created_at": "2025-11-28T12:00:00Z"
    }
  ]
}</code></div>
                    </div>
                    <div class="endpoint-item">
                        <div class="d-flex align-items-center mb-2">
                            <span class="endpoint-method-badge badge-get">GET</span>
                            <span class="api-endpoint-url">{{ $baseUrl }}/api/v1/sessions/{session_id}</span>
                        </div>
                        <p class="endpoint-description mb-3">{{ __('Mendapatkan detail device WhatsApp berdasarkan ID.') }}</p>
                    </div>
                    <div class="endpoint-item">
                        <div class="d-flex align-items-center mb-2">
                            <span class="endpoint-method-badge badge-get">GET</span>
                            <span class="api-endpoint-url">{{ $baseUrl }}/api/v1/sessions/{session_id}/status</span>
                        </div>
                        <p class="endpoint-description mb-3">{{ __('Mendapatkan status koneksi device WhatsApp.') }}</p>
                        
                        <!-- Code Examples -->
                        <div class="code-tabs">
                            <div class="code-tabs-header">
                                <button class="code-tab active" onclick="switchCodeTab(this, 'status-curl')">cURL</button>
                                <button class="code-tab" onclick="switchCodeTab(this, 'status-php')">PHP</button>
                                <button class="code-tab" onclick="switchCodeTab(this, 'status-python')">Python</button>
                            </div>
                            <div id="status-curl" class="code-tab-content active">
                                <div class="api-code mb-0"><code>curl -X GET "{{ $baseUrl }}/api/v1/sessions/YOUR_SESSION_ID/status" \
  -H "X-Api-Key: YOUR_API_KEY" \
  -H "Content-Type: application/json"</code></div>
                            </div>
                            <div id="status-php" class="code-tab-content">
                                <div class="api-code mb-0"><code>&lt;?php
$apiKey = 'YOUR_API_KEY';
$sessionId = 'YOUR_SESSION_ID';
$url = '{{ $baseUrl }}/api/v1/sessions/' . $sessionId . '/status';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-Api-Key: ' . $apiKey,
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
if ($data['success']) {
    echo "Status: " . $data['data']['status'];
    echo "Connected: " . ($data['data']['is_connected'] ? 'Yes' : 'No');
}</code></div>
                            </div>
                            <div id="status-python" class="code-tab-content">
                                <div class="api-code mb-0"><code>import requests

api_key = 'YOUR_API_KEY'
session_id = 'YOUR_SESSION_ID'
url = f'{{ $baseUrl }}/api/v1/sessions/{session_id}/status'

headers = {
    'X-Api-Key': api_key,
    'Content-Type': 'application/json'
}

response = requests.get(url, headers=headers)

if response.status_code == 200:
    data = response.json()
    if data['success']:
        print(f"Status: {data['data']['status']}")
        print(f"Connected: {data['data']['is_connected']}")</code></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Messages Endpoints Group -->
            <div class="endpoint-group">
                <div class="endpoint-group-header">
                    <i class="fas fa-paper-plane"></i>
                    <span>{{ __('Messages') }}</span>
                </div>
                <div class="endpoint-group-body">
                    <div class="endpoint-item">
                        <div class="d-flex align-items-center mb-2">
                            <span class="endpoint-method-badge badge-post">POST</span>
                        <span class="api-endpoint-url">{{ $baseUrl }}/api/v1/messages</span>
                        </div>
                        <p class="endpoint-description mb-3">{{ __('Mengirim pesan WhatsApp. Mendukung berbagai jenis pesan.') }}</p>

                        <!-- Code Examples for Send Message -->
                        <p class="mb-2"><strong>{{ __('Contoh Kode - Kirim Pesan Teks') }}</strong></p>
                        <div class="code-tabs">
                            <div class="code-tabs-header">
                                <button class="code-tab active" onclick="switchCodeTab(this, 'send-msg-curl')">cURL</button>
                                <button class="code-tab" onclick="switchCodeTab(this, 'send-msg-php')">PHP</button>
                                <button class="code-tab" onclick="switchCodeTab(this, 'send-msg-python')">Python</button>
                            </div>
                            <div id="send-msg-curl" class="code-tab-content active">
                                <div class="api-code mb-0"><code>curl -X POST "{{ $baseUrl }}/api/v1/messages" \
  -H "X-Api-Key: YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "device_id": "YOUR_DEVICE_ID",
    "message_type": "text",
    "to": "6281234567890",
    "message": "Halo, ini pesan dari API"
  }'</code></div>
                            </div>
                            <div id="send-msg-php" class="code-tab-content">
                                <div class="api-code mb-0"><code>&lt;?php
$apiKey = 'YOUR_API_KEY';
$url = '{{ $baseUrl }}/api/v1/messages';

$data = [
    'device_id' => 'YOUR_DEVICE_ID',
    'message_type' => 'text',
    'to' => '6281234567890',
    'message' => 'Halo, ini pesan dari API'
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-Api-Key: ' . $apiKey,
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$result = json_decode($response, true);
if ($httpCode === 200 && $result['success']) {
    echo "Pesan terkirim! ID: " . $result['data']['message_id'];
}</code></div>
                            </div>
                            <div id="send-msg-python" class="code-tab-content">
                                <div class="api-code mb-0"><code>import requests
import json

api_key = 'YOUR_API_KEY'
url = '{{ $baseUrl }}/api/v1/messages'

headers = {
    'X-Api-Key': api_key,
    'Content-Type': 'application/json'
}

data = {
    'device_id': 'YOUR_DEVICE_ID',
    'message_type': 'text',
    'to': '6281234567890',
    'message': 'Halo, ini pesan dari API'
}

response = requests.post(url, headers=headers, json=data)

if response.status_code == 200:
    result = response.json()
    if result['success']:
        print(f"Pesan terkirim! ID: {result['data']['message_id']}")</code></div>
                            </div>
                        </div>

                        <p class="mb-3 mt-3"><strong>{{ __('Jenis Pesan yang Didukung') }}</strong></p>
                    <ul class="api-list mb-4">
                        <li><span class="api-inline-code">text</span> – {{ __('Pesan teks') }}</li>
                        <li><span class="api-inline-code">image</span> – {{ __('Pesan gambar') }}</li>
                        <li><span class="api-inline-code">video</span> – {{ __('Pesan video') }}</li>
                        <li><span class="api-inline-code">document</span> – {{ __('Pesan dokumen/file') }}</li>
                        <li><span class="api-inline-code">poll</span> – {{ __('Pesan polling/survey') }}</li>
                        <li><span class="api-inline-code">button</span> – {{ __('Pesan dengan tombol interaktif') }}</li>
                        <li><span class="api-inline-code">list</span> – {{ __('Pesan dengan list interaktif') }}</li>
                    </ul>

                    <p class="mb-2"><strong>{{ __('1. Kirim Pesan Teks') }}</strong></p>
                    <p><strong>{{ __('Body (JSON)') }}</strong></p>
                    <div class="api-code mb-3"><code>{
  "device_id": "YOUR_DEVICE_ID",
  "message_type": "text",
  "to": "6281234567890",
  "message": "Halo, ini pesan dari API"
}</code></div>

                    <p class="mb-2"><strong>{{ __('2. Kirim Pesan Gambar') }}</strong></p>
                    
                    <!-- Code Examples for Send Image -->
                    <div class="code-tabs">
                        <div class="code-tabs-header">
                            <button class="code-tab active" onclick="switchCodeTab(this, 'send-img-curl')">cURL</button>
                            <button class="code-tab" onclick="switchCodeTab(this, 'send-img-php')">PHP</button>
                            <button class="code-tab" onclick="switchCodeTab(this, 'send-img-python')">Python</button>
                        </div>
                        <div id="send-img-curl" class="code-tab-content active">
                            <div class="api-code mb-0"><code>curl -X POST "{{ $baseUrl }}/api/v1/messages" \
  -H "X-Api-Key: YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "device_id": "YOUR_DEVICE_ID",
    "message_type": "image",
    "to": "6281234567890",
    "image_url": "https://example.com/image.jpg",
    "caption": "Halo, ini gambar dari API"
  }'</code></div>
                        </div>
                        <div id="send-img-php" class="code-tab-content">
                            <div class="api-code mb-0"><code>&lt;?php
$apiKey = 'YOUR_API_KEY';
$url = '{{ $baseUrl }}/api/v1/messages';

$data = [
    'device_id' => 'YOUR_DEVICE_ID',
    'message_type' => 'image',
    'to' => '6281234567890',
    'image_url' => 'https://example.com/image.jpg',
    'caption' => 'Halo, ini gambar dari API'
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-Api-Key: ' . $apiKey,
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);
if ($result['success']) {
    echo "Gambar terkirim!";
}</code></div>
                        </div>
                        <div id="send-img-python" class="code-tab-content">
                            <div class="api-code mb-0"><code>import requests

api_key = 'YOUR_API_KEY'
url = '{{ $baseUrl }}/api/v1/messages'

headers = {
    'X-Api-Key': api_key,
    'Content-Type': 'application/json'
}

data = {
    'device_id': 'YOUR_DEVICE_ID',
    'message_type': 'image',
    'to': '6281234567890',
    'image_url': 'https://example.com/image.jpg',
    'caption': 'Halo, ini gambar dari API'
}

response = requests.post(url, headers=headers, json=data)

if response.status_code == 200:
    result = response.json()
    if result['success']:
        print("Gambar terkirim!")</code></div>
                        </div>
                    </div>
                    
                    <p class="mt-3 mb-2"><strong>{{ __('Body (JSON)') }}</strong></p>
                    <div class="api-code mb-3"><code>{
  "device_id": "YOUR_DEVICE_ID",
  "message_type": "image",
  "to": "6281234567890",
  "image_url": "https://example.com/image.jpg",
  "caption": "Halo, ini gambar dari API"
}</code></div>

                    <p class="mb-2"><strong>{{ __('3. Kirim Pesan Video') }}</strong></p>
                    <p><strong>{{ __('Body (JSON)') }}</strong></p>
                    <div class="api-code mb-3"><code>{
  "device_id": "YOUR_DEVICE_ID",
  "message_type": "video",
  "to": "6281234567890",
  "video_url": "https://example.com/video.mp4",
  "caption": "Tonton video ini!",
  "as_note": false,
  "convert": false
}</code></div>
                    <p class="mb-2"><small class="text-muted">{{ __('Catatan:') }} <span class="api-inline-code">as_note</span> {{ __('untuk mengirim sebagai video note (rounded video),') }} <span class="api-inline-code">convert</span> {{ __('untuk konversi format video jika diperlukan.') }}</small></p>

                    <p class="mb-2"><strong>{{ __('4. Kirim Pesan Dokumen') }}</strong></p>
                    <p><strong>{{ __('Body (JSON)') }}</strong></p>
                    <div class="api-code mb-3"><code>{
  "device_id": "YOUR_DEVICE_ID",
  "message_type": "document",
  "to": "6281234567890",
  "document_url": "https://example.com/invoice.pdf",
  "filename": "invoice.pdf",
  "caption": "Ini adalah dokumen"
}</code></div>

                    <p class="mb-2"><strong>{{ __('5. Kirim Pesan Poll') }}</strong></p>
                    <p><strong>{{ __('Body (JSON)') }}</strong></p>
                    <div class="api-code mb-3"><code>{
  "device_id": "YOUR_DEVICE_ID",
  "message_type": "poll",
  "to": "6281234567890",
  "poll_name": "How are you?",
  "poll_options": [
    "Awesome!",
    "Good!",
    "Not bad!"
  ],
  "multiple_answers": false,
  "fallback_to_text": false
}</code></div>
                    <div class="alert alert-warning mb-3">
                        <small>
                            <i class="fas fa-exclamation-triangle"></i> 
                            <strong>{{ __('Catatan Penting:') }}</strong> {{ __('Fitur poll tidak didukung oleh WAHA WEBJS engine. Jika Anda menggunakan WAHA Plus dengan engine WEBJS, fitur ini akan mengembalikan error.') }}
                        </small>
                    </div>
                    <div class="alert alert-info mb-3">
                        <small>
                            <i class="fas fa-info-circle"></i> 
                            <strong>{{ __('Solusi Alternatif:') }}</strong> {{ __('Anda dapat mengaktifkan') }} <span class="api-inline-code">fallback_to_text: true</span> {{ __('untuk mengirim poll sebagai pesan teks yang diformat dengan baik jika engine tidak mendukung poll.') }}
                        </small>
                    </div>
                    <p class="mb-2"><small class="text-muted">{{ __('Catatan:') }} <span class="api-inline-code">poll_options</span> {{ __('harus array dengan minimal 2 opsi dan maksimal 12 opsi.') }} <span class="api-inline-code">multiple_answers</span> {{ __('untuk mengizinkan pilihan ganda (default: false).') }} <span class="api-inline-code">fallback_to_text</span> {{ __('untuk mengirim sebagai teks jika poll tidak didukung (default: false).') }}</small></p>

                    <p class="mb-2"><strong>{{ __('6. Kirim Pesan Button') }}</strong></p>
                    <p><strong>{{ __('Body (JSON)') }}</strong></p>
                    <div class="api-code mb-3"><code>{
  "device_id": "YOUR_DEVICE_ID",
  "message_type": "button",
  "to": "6281234567890",
  "body": "Tell us how are you please 🙏",
  "header": "How are you?",
  "footer": "If you have any questions, please send it in the chat",
  "header_image": {
    "mimetype": "image/jpeg",
    "filename": "filename.jpg",
    "url": "https://github.com/devlikeapro/waha/raw/core/examples/waha.jpg"
  },
  "buttons": [
    {
      "type": "reply",
      "text": "I am good!"
    },
    {
      "type": "call",
      "text": "Call us",
      "phoneNumber": "+1234567890"
    },
    {
      "type": "copy",
      "text": "Copy code",
      "copyCode": "4321"
    },
    {
      "type": "url",
      "text": "How did you do that?",
      "url": "https://waha.devlike.pro"
    }
  ],
  "fallback_to_text": false
}</code></div>
                    <div class="alert alert-warning mb-3">
                        <small>
                            <i class="fas fa-exclamation-triangle"></i> 
                            <strong>{{ __('Peringatan Penting:') }}</strong> {{ __('Fitur button message adalah DEPRECATED di WAHA dan mungkin tidak bekerja dengan baik. Pesan mungkin tidak terkirim meskipun API mengembalikan status sukses.') }}
                        </small>
                    </div>
                    <div class="alert alert-info mb-3">
                        <small>
                            <i class="fas fa-info-circle"></i> 
                            <strong>{{ __('Solusi Alternatif:') }}</strong> {{ __('Anda dapat mengaktifkan') }} <span class="api-inline-code">fallback_to_text: true</span> {{ __('untuk mengirim button sebagai pesan teks yang diformat dengan baik jika button tidak berfungsi. Atau gunakan') }} <span class="api-inline-code">poll</span> {{ __('sebagai alternatif yang lebih stabil.') }}
                        </small>
                    </div>
                    <p class="mb-2"><small class="text-muted">{{ __('Catatan:') }} <span class="api-inline-code">body</span> {{ __('adalah teks utama pesan (required, maks 1024 karakter).') }} <span class="api-inline-code">buttons</span> {{ __('harus array dengan minimal 1 button dan maksimal 3 buttons.') }} <span class="api-inline-code">header</span> {{ __('dan') }} <span class="api-inline-code">footer</span> {{ __('bersifat opsional (maks 60 karakter).') }} <span class="api-inline-code">header_image</span> {{ __('bersifat opsional untuk menampilkan gambar di header.') }} <span class="api-inline-code">fallback_to_text</span> {{ __('untuk mengirim sebagai teks jika button tidak berfungsi (default: false).') }}</small></p>
                    <p class="mb-2"><small class="text-muted"><strong>{{ __('Jenis Button:') }}</strong></p>
                    <ul class="api-list mb-2">
                        <li><span class="api-inline-code">reply</span> – {{ __('Tombol balasan (hanya memerlukan text)') }}</li>
                        <li><span class="api-inline-code">call</span> – {{ __('Tombol panggilan (memerlukan text dan phoneNumber)') }}</li>
                        <li><span class="api-inline-code">copy</span> – {{ __('Tombol salin kode (memerlukan text dan copyCode)') }}</li>
                        <li><span class="api-inline-code">url</span> – {{ __('Tombol link URL (memerlukan text dan url)') }}</li>
                    </ul>
                    <p class="mb-2"><small class="text-muted">{{ __('Setiap button text maksimal 20 karakter.') }}</small></p>

                    <p class="mb-2"><strong>{{ __('7. Kirim Pesan List') }}</strong></p>
                    <p><strong>{{ __('Body (JSON)') }}</strong></p>
                    <div class="api-code mb-3"><code>{
  "device_id": "YOUR_DEVICE_ID",
  "message_type": "list",
  "to": "6281234567890",
  "message": {
    "title": "Simple Menu",
    "description": "Please choose an option",
    "footer": "Thank you!",
    "button": "Choose",
    "sections": [
      {
        "title": "Main",
        "rows": [
          {
            "title": "Option 1",
            "rowId": "option1",
            "description": null
          },
          {
            "title": "Option 2",
            "rowId": "option2",
            "description": null
          },
          {
            "title": "Option 3",
            "rowId": "option3",
            "description": null
          }
        ]
      }
    ]
  },
  "reply_to": null
}</code></div>
                    <div class="alert alert-info mb-3">
                        <small>
                            <i class="fas fa-info-circle"></i> 
                            <strong>{{ __('Catatan:') }}</strong> {{ __('Fitur list message hanya didukung oleh WAHA Plus dengan engine WEBJS dan GOWS. Engine NOWEB tidak mendukung fitur ini.') }}
                        </small>
                    </div>
                    <p class="mb-2"><small class="text-muted">{{ __('Catatan:') }} <span class="api-inline-code">message.title</span> {{ __('adalah judul list (required, maks 60 karakter).') }} <span class="api-inline-code">message.description</span> {{ __('adalah deskripsi list (opsional, maks 72 karakter).') }} <span class="api-inline-code">message.footer</span> {{ __('adalah footer list (opsional, maks 60 karakter).') }} <span class="api-inline-code">message.button</span> {{ __('adalah teks tombol untuk membuka list (required, maks 20 karakter).') }} <span class="api-inline-code">message.sections</span> {{ __('adalah array section (required, minimal 1 section).') }} <span class="api-inline-code">message.sections[].title</span> {{ __('adalah judul section (required, maks 24 karakter).') }} <span class="api-inline-code">message.sections[].rows</span> {{ __('adalah array row dalam section (required, minimal 1 row, maksimal 10 rows per section).') }} <span class="api-inline-code">message.sections[].rows[].title</span> {{ __('adalah judul row (required, maks 24 karakter).') }} <span class="api-inline-code">message.sections[].rows[].rowId</span> {{ __('adalah ID unik untuk row (required, maks 200 karakter).') }} <span class="api-inline-code">message.sections[].rows[].description</span> {{ __('adalah deskripsi row (opsional, maks 72 karakter).') }} <span class="api-inline-code">reply_to</span> {{ __('adalah ID pesan yang akan dibalas (opsional).') }}</small></p>

                    <p class="mb-1"><strong>{{ __('Keterangan Field') }}</strong></p>
                    <ul class="api-list">
                        <li><span class="api-inline-code">device_id</span> – {{ __('ID device WhatsApp (required, string, max 255 karakter). Dapat dilihat di menu Devices.') }}</li>
                        <li><span class="api-inline-code">message_type</span> – {{ __('Jenis pesan (required): "text", "image", "video", "document", "poll", "button", atau "list".') }}</li>
                        <li><span class="api-inline-code">to</span> – {{ __('Nomor tujuan (required, string, max 20 karakter). Format: 08xxxxxxxxxx atau 628xxxxxxxxxx (akan dinormalisasi otomatis).') }}</li>
                        <li><span class="api-inline-code">message</span> – {{ __('Isi pesan teks (required jika message_type="text", string, maks 4096 karakter).') }}</li>
                        <li><span class="api-inline-code">image_url</span> – {{ __('URL gambar (required jika message_type="image", valid URL, maks 500 karakter).') }}</li>
                        <li><span class="api-inline-code">video_url</span> – {{ __('URL video (required jika message_type="video", valid URL, maks 500 karakter).') }}</li>
                        <li><span class="api-inline-code">document_url</span> – {{ __('URL file (required jika message_type="document", valid URL, maks 500 karakter).') }}</li>
                        <li><span class="api-inline-code">filename</span> – {{ __('Nama file (opsional, string, max 255 karakter, untuk document).') }}</li>
                        <li><span class="api-inline-code">caption</span> – {{ __('Caption/teks tambahan (opsional, string, maks 1024 karakter, untuk image, video, dan document).') }}</li>
                        <li><span class="api-inline-code">as_note</span> – {{ __('Kirim sebagai video note/rounded video (opsional, boolean, default: false, hanya untuk video).') }}</li>
                        <li><span class="api-inline-code">convert</span> – {{ __('Konversi format video jika diperlukan (opsional, boolean, default: false, hanya untuk video).') }}</li>
                        <li><span class="api-inline-code">poll_name</span> – {{ __('Nama/pertanyaan poll (required jika message_type="poll", string, maks 255 karakter).') }}</li>
                        <li><span class="api-inline-code">poll_options</span> – {{ __('Array opsi poll (required jika message_type="poll", minimal 2 opsi, maksimal 12 opsi).') }}</li>
                        <li><span class="api-inline-code">multiple_answers</span> – {{ __('Izinkan pilihan ganda (opsional, boolean, default: false, hanya untuk poll).') }}</li>
                        <li><span class="api-inline-code">fallback_to_text</span> – {{ __('Kirim sebagai teks jika poll tidak didukung oleh engine (opsional, boolean, default: false, hanya untuk poll).') }}</li>
                        <li><span class="api-inline-code">body</span> – {{ __('Teks utama pesan button (required jika message_type="button", string, maks 1024 karakter).') }}</li>
                        <li><span class="api-inline-code">buttons</span> – {{ __('Array tombol interaktif (required jika message_type="button", minimal 1 button, maksimal 3 buttons).') }}</li>
                        <li><span class="api-inline-code">buttons[].type</span> – {{ __('Jenis button (required): "reply", "call", "copy", atau "url".') }}</li>
                        <li><span class="api-inline-code">buttons[].text</span> – {{ __('Teks pada button (required, string, maks 20 karakter).') }}</li>
                        <li><span class="api-inline-code">buttons[].phoneNumber</span> – {{ __('Nomor telepon (required jika type="call", string, maks 20 karakter).') }}</li>
                        <li><span class="api-inline-code">buttons[].copyCode</span> – {{ __('Kode untuk disalin (required jika type="copy", string, maks 20 karakter).') }}</li>
                        <li><span class="api-inline-code">buttons[].url</span> – {{ __('URL link (required jika type="url", valid URL, maks 500 karakter).') }}</li>
                        <li><span class="api-inline-code">header</span> – {{ __('Header pesan button (opsional, string, maks 60 karakter, hanya untuk button).') }}</li>
                        <li><span class="api-inline-code">footer</span> – {{ __('Footer pesan button (opsional, string, maks 60 karakter, hanya untuk button).') }}</li>
                        <li><span class="api-inline-code">header_image</span> – {{ __('Gambar header (opsional, object dengan mimetype, filename, dan url, hanya untuk button).') }}</li>
                        <li><span class="api-inline-code">fallback_to_text</span> – {{ __('Kirim sebagai teks jika button tidak berfungsi atau status PENDING (opsional, boolean, default: false, hanya untuk button).') }}</li>
                    </ul>

                    <p class="mt-4 mb-2"><strong>{{ __('Format File yang Didukung') }}</strong></p>
                    <div class="alert alert-info mb-3">
                        <p class="mb-2"><strong>{{ __('Gambar (Image):') }}</strong></p>
                        <ul class="mb-0 api-list">
                            <li>JPEG (<span class="api-inline-code">image/jpeg</span>)</li>
                            <li>PNG (<span class="api-inline-code">image/png</span>)</li>
                            <li>GIF (<span class="api-inline-code">image/gif</span>)</li>
                            <li>WebP (<span class="api-inline-code">image/webp</span>)</li>
                        </ul>
                    </div>
                    <div class="alert alert-info mb-3">
                        <p class="mb-2"><strong>{{ __('Video:') }}</strong></p>
                        <ul class="mb-0 api-list">
                            <li>MP4 (<span class="api-inline-code">video/mp4</span>)</li>
                            <li>WebM (<span class="api-inline-code">video/webm</span>)</li>
                            <li>OGG (<span class="api-inline-code">video/ogg</span>)</li>
                            <li>QuickTime (<span class="api-inline-code">video/quicktime</span>)</li>
                            <li>AVI (<span class="api-inline-code">video/x-msvideo</span>)</li>
                        </ul>
                    </div>
                    <div class="alert alert-info mb-3">
                        <p class="mb-2"><strong>{{ __('Dokumen (Document):') }}</strong></p>
                        <ul class="mb-0 api-list">
                            <li>PDF (<span class="api-inline-code">application/pdf</span>)</li>
                            <li>Word DOC/DOCX (<span class="api-inline-code">application/msword</span>, <span class="api-inline-code">application/vnd.openxmlformats-officedocument.wordprocessingml.document</span>)</li>
                            <li>Excel XLS/XLSX (<span class="api-inline-code">application/vnd.ms-excel</span>, <span class="api-inline-code">application/vnd.openxmlformats-officedocument.spreadsheetml.sheet</span>)</li>
                            <li>PowerPoint PPT/PPTX (<span class="api-inline-code">application/vnd.ms-powerpoint</span>, <span class="api-inline-code">application/vnd.openxmlformats-officedocument.presentationml.presentation</span>)</li>
                            <li>ZIP (<span class="api-inline-code">application/zip</span>)</li>
                            <li>TXT (<span class="api-inline-code">text/plain</span>)</li>
                            <li>CSV (<span class="api-inline-code">text/csv</span>)</li>
                            <li>JSON (<span class="api-inline-code">application/json</span>)</li>
                        </ul>
                    </div>

                    <p class="mt-3 mb-1"><strong>{{ __('Response Error (422 - Validation Failed)') }}</strong></p>
                        <div class="api-code mb-0"><code>{
  "success": false,
  "error": "Validation failed",
  "errors": {
    "device_id": ["Device ID is required."],
    "message_type": ["Message type must be one of: text, image, video, document, poll."],
    "to": ["Recipient number is required."]
  }
}</code></div>
                </div>
                    <div class="endpoint-item">
                        <div class="d-flex align-items-center mb-2">
                            <span class="endpoint-method-badge badge-get">GET</span>
                            <span class="api-endpoint-url">{{ $baseUrl }}/api/v1/messages</span>
            </div>
                        <p class="endpoint-description mb-3">{{ __('Mendapatkan daftar pesan yang telah dikirim atau diterima.') }}</p>
                        
                        <!-- Code Examples -->
                        <div class="code-tabs">
                            <div class="code-tabs-header">
                                <button class="code-tab active" onclick="switchCodeTab(this, 'get-msg-curl')">cURL</button>
                                <button class="code-tab" onclick="switchCodeTab(this, 'get-msg-php')">PHP</button>
                                <button class="code-tab" onclick="switchCodeTab(this, 'get-msg-python')">Python</button>
                            </div>
                            <div id="get-msg-curl" class="code-tab-content active">
                                <div class="api-code mb-0"><code>curl -X GET "{{ $baseUrl }}/api/v1/messages?device_id=YOUR_DEVICE_ID&per_page=20&page=1" \
  -H "X-Api-Key: YOUR_API_KEY" \
  -H "Content-Type: application/json"</code></div>
                            </div>
                            <div id="get-msg-php" class="code-tab-content">
                                <div class="api-code mb-0"><code>&lt;?php
$apiKey = 'YOUR_API_KEY';
$deviceId = 'YOUR_DEVICE_ID';
$url = '{{ $baseUrl }}/api/v1/messages?' . http_build_query([
    'device_id' => $deviceId,
    'per_page' => 20,
    'page' => 1
]);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-Api-Key: ' . $apiKey,
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
if ($data['success']) {
    foreach ($data['data'] as $message) {
        echo "Pesan: " . $message['content'] . "\n";
    }
}</code></div>
                            </div>
                            <div id="get-msg-python" class="code-tab-content">
                                <div class="api-code mb-0"><code>import requests

api_key = 'YOUR_API_KEY'
url = '{{ $baseUrl }}/api/v1/messages'

headers = {
    'X-Api-Key': api_key,
    'Content-Type': 'application/json'
}

params = {
    'device_id': 'YOUR_DEVICE_ID',
    'per_page': 20,
    'page': 1
}

response = requests.get(url, headers=headers, params=params)

if response.status_code == 200:
    data = response.json()
    if data['success']:
        for message in data['data']:
            print(f"Pesan: {message['content']}")</code></div>
                            </div>
                </div>
                        
                        <p class="mt-3 mb-2"><strong>{{ __('Query Parameters') }}</strong></p>
                    <div class="api-code mb-3"><code>device_id=YOUR_DEVICE_ID&per_page=20&page=1</code></div>
                    <p class="mb-1"><strong>{{ __('Keterangan parameter') }}</strong></p>
                        <ul class="api-list mb-0">
                        <li><span class="api-inline-code">device_id</span> – {{ __('ID device WhatsApp (required).') }}</li>
                        <li><span class="api-inline-code">per_page</span> – {{ __('Jumlah pesan per halaman (opsional, default: 20).') }}</li>
                        <li><span class="api-inline-code">page</span> – {{ __('Nomor halaman (opsional, default: 1).') }}</li>
                    </ul>
                    </div>
                    <div class="endpoint-item">
                        <div class="d-flex align-items-center mb-2">
                            <span class="endpoint-method-badge badge-get">GET</span>
                            <span class="api-endpoint-url">{{ $baseUrl }}/api/v1/messages/{message_id}</span>
                        </div>
                        <p class="endpoint-description mb-0">{{ __('Mendapatkan detail pesan berdasarkan ID.') }}</p>
                    </div>
                </div>
            </div>

            <!-- Account Endpoints Group -->
            <div class="endpoint-group">
                <div class="endpoint-group-header">
                    <i class="fas fa-user-circle"></i>
                    <span>{{ __('Account') }}</span>
                </div>
                <div class="endpoint-group-body">
                    <div class="endpoint-item">
                        <div class="d-flex align-items-center mb-2">
                            <span class="endpoint-method-badge badge-get">GET</span>
                            <span class="api-endpoint-url">{{ $baseUrl }}/api/v1/account</span>
                            <span class="badge bg-secondary ms-2">Get Profile</span>
                        </div>
                        <p class="endpoint-description mb-3">{{ __('Mendapatkan informasi profil pengguna (profile) beserta detail quota, subscription, dan statistik akun.') }}</p>
                        
                        <div class="code-tabs mt-3">
                            <div class="code-tab-buttons">
                                <button class="code-tab-button active" onclick="switchCodeTab(this, 'account-curl')">cURL</button>
                                <button class="code-tab-button" onclick="switchCodeTab(this, 'account-php')">PHP</button>
                                <button class="code-tab-button" onclick="switchCodeTab(this, 'account-python')">Python</button>
                            </div>
                            <div id="account-curl" class="code-tab-content active">
                                <div class="api-code mb-0"><code>curl -X GET '{{ $baseUrl }}/api/v1/account' \
  -H 'X-Api-Key: YOUR_API_KEY' \
  -H 'Accept: application/json'</code></div>
                            </div>
                            <div id="account-php" class="code-tab-content">
                                <div class="api-code mb-0"><code>&lt;?php
$apiKey = 'YOUR_API_KEY';
$baseUrl = '{{ $baseUrl }}';
$url = $baseUrl . '/api/v1/account';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-Api-Key: ' . $apiKey,
    'Accept: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$data = json_decode($response, true);
if ($httpCode === 200 && $data['success']) {
    // Profile Information
    $user = $data['data']['user'];
    echo "Profile Information:\n";
    echo "  Name: " . $user['name'] . "\n";
    echo "  Email: " . $user['email'] . "\n";
    echo "  Phone: " . $user['phone'] . "\n";
    echo "  Created: " . $user['created_at'] . "\n\n";
    
    // Quota Information
    $quota = $data['data']['quota'];
    echo "Quota Information:\n";
    echo "  Balance: " . $quota['balance'] . "\n";
    echo "  Text Quota: " . $quota['text_quota'] . "\n";
    echo "  Multimedia Quota: " . $quota['multimedia_quota'] . "\n";
    echo "  Free Text Quota: " . $quota['free_text_quota'] . "\n";
    echo "  Total Text Quota: " . $quota['total_text_quota'] . "\n\n";
    
    // Subscription Information
    if (isset($data['data']['subscription'])) {
        $subscription = $data['data']['subscription'];
        echo "Subscription:\n";
        echo "  Plan: " . $subscription['plan_name'] . "\n";
        echo "  Status: " . $subscription['status'] . "\n";
        echo "  Expires: " . ($subscription['expires_at'] ?? 'N/A') . "\n\n";
    }
    
    // Statistics
    if (isset($data['data']['statistics'])) {
        $stats = $data['data']['statistics'];
        echo "Statistics:\n";
        echo "  Total Messages: " . $stats['total_messages'] . "\n";
        echo "  Total Sessions: " . $stats['total_sessions'] . "\n";
        echo "  Connected Sessions: " . $stats['connected_sessions'] . "\n";
    }
}
?&gt;</code></div>
                            </div>
                            <div id="account-python" class="code-tab-content">
                                <div class="api-code mb-0"><code>import requests

api_key = 'YOUR_API_KEY'
base_url = '{{ $baseUrl }}'
url = f"{base_url}/api/v1/account"

headers = {
    'X-Api-Key': api_key,
    'Accept': 'application/json'
}

response = requests.get(url, headers=headers)
data = response.json()

if response.status_code == 200 and data['success']:
    # Profile Information
    user = data['data']['user']
    print("Profile Information:")
    print(f"  Name: {user['name']}")
    print(f"  Email: {user['email']}")
    print(f"  Phone: {user['phone']}")
    print(f"  Created: {user['created_at']}\n")
    
    # Quota Information
    quota = data['data']['quota']
    print("Quota Information:")
    print(f"  Balance: {quota['balance']}")
    print(f"  Text Quota: {quota['text_quota']}")
    print(f"  Multimedia Quota: {quota['multimedia_quota']}")
    print(f"  Free Text Quota: {quota['free_text_quota']}")
    print(f"  Total Text Quota: {quota['total_text_quota']}\n")
    
    # Subscription Information
    if 'subscription' in data['data'] and data['data']['subscription']:
        subscription = data['data']['subscription']
        print("Subscription:")
        print(f"  Plan: {subscription['plan_name']}")
        print(f"  Status: {subscription['status']}")
        print(f"  Expires: {subscription.get('expires_at', 'N/A')}\n")
    
    # Statistics
    if 'statistics' in data['data']:
        stats = data['data']['statistics']
        print("Statistics:")
        print(f"  Total Messages: {stats['total_messages']}")
        print(f"  Total Sessions: {stats['total_sessions']}")
        print(f"  Connected Sessions: {stats['connected_sessions']}")</code></div>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <p class="small mb-2"><strong>{{ __('Response Example:') }}</strong></p>
                            <pre class="api-code mb-0"><code>{
  "success": true,
  "data": {
    "user": {
      "id": "uuid",
      "name": "John Doe",
      "email": "john@example.com",
      "phone": "6281234567890",
      "created_at": "2025-11-30T00:00:00Z"
    },
    "quota": {
      "balance": 100000.00,
      "text_quota": 500,
      "multimedia_quota": 200,
      "free_text_quota": 100,
      "total_text_quota": 600
    },
    "subscription": {
      "plan_name": "Premium",
      "plan_id": "uuid",
      "status": "active",
      "expires_at": "2025-12-30T00:00:00Z"
    },
    "statistics": {
      "total_messages": 1250,
      "total_sessions": 3,
      "connected_sessions": 2
    }
  }
}</code></pre>
                        </div>
                    </div>
                    <div class="endpoint-item">
                        <div class="d-flex align-items-center mb-2">
                            <span class="endpoint-method-badge badge-get">GET</span>
                            <span class="api-endpoint-url">{{ $baseUrl }}/api/v1/account/usage</span>
                        </div>
                        <p class="endpoint-description mb-0">{{ __('Mendapatkan informasi penggunaan quota dan statistik akun.') }}</p>
                    </div>
                </div>
            </div>

            <!-- Templates Endpoints Group -->
            <div class="endpoint-group">
                <div class="endpoint-group-header">
                    <i class="fas fa-file-alt"></i>
                    <span>{{ __('Templates') }}</span>
                </div>
                <div class="endpoint-group-body">
                    <div class="endpoint-item">
                        <div class="d-flex align-items-center mb-2">
                            <span class="endpoint-method-badge badge-get">GET</span>
                            <span class="api-endpoint-url">{{ $baseUrl }}/api/v1/templates</span>
                        </div>
                        <p class="endpoint-description mb-0">{{ __('Mendapatkan daftar semua template pesan.') }}</p>
                    </div>
                    <div class="endpoint-item">
                        <div class="d-flex align-items-center mb-2">
                            <span class="endpoint-method-badge badge-post">POST</span>
                            <span class="api-endpoint-url">{{ $baseUrl }}/api/v1/templates</span>
                        </div>
                        <p class="endpoint-description mb-0">{{ __('Membuat template pesan baru.') }}</p>
                    </div>
                    <div class="endpoint-item">
                        <div class="d-flex align-items-center mb-2">
                            <span class="endpoint-method-badge badge-get">GET</span>
                            <span class="api-endpoint-url">{{ $baseUrl }}/api/v1/templates/{template_id}</span>
                        </div>
                        <p class="endpoint-description mb-0">{{ __('Mendapatkan detail template berdasarkan ID.') }}</p>
                    </div>
                    <div class="endpoint-item">
                        <div class="d-flex align-items-center mb-2">
                            <span class="endpoint-method-badge badge-put">PUT</span>
                            <span class="api-endpoint-url">{{ $baseUrl }}/api/v1/templates/{template_id}</span>
                        </div>
                        <p class="endpoint-description mb-0">{{ __('Memperbarui template pesan.') }}</p>
                    </div>
                    <div class="endpoint-item">
                        <div class="d-flex align-items-center mb-2">
                            <span class="endpoint-method-badge badge-delete">DELETE</span>
                            <span class="api-endpoint-url">{{ $baseUrl }}/api/v1/templates/{template_id}</span>
                        </div>
                        <p class="endpoint-description mb-0">{{ __('Menghapus template pesan.') }}</p>
                    </div>
                    <div class="endpoint-item">
                        <div class="d-flex align-items-center mb-2">
                            <span class="endpoint-method-badge badge-post">POST</span>
                            <span class="api-endpoint-url">{{ $baseUrl }}/api/v1/templates/{template_id}/preview</span>
                        </div>
                        <p class="endpoint-description mb-0">{{ __('Preview template dengan data contoh.') }}</p>
                    </div>
                </div>
            </div>

            <!-- OTP Endpoints Group -->
            <div class="endpoint-group">
                <div class="endpoint-group-header">
                    <i class="fas fa-key"></i>
                    <span>{{ __('OTP (One Time Password)') }}</span>
                </div>
                <div class="endpoint-group-body">
                    <div class="endpoint-item">
                        <div class="d-flex align-items-center mb-2">
                            <span class="endpoint-method-badge badge-post">POST</span>
                            <span class="api-endpoint-url">{{ $baseUrl }}/api/v1/messages/otp</span>
                        </div>
                        <p class="endpoint-description mb-0">{{ __('Mengirim kode OTP ke nomor tujuan.') }}</p>
                    </div>
                    <div class="endpoint-item">
                        <div class="d-flex align-items-center mb-2">
                            <span class="endpoint-method-badge badge-post">POST</span>
                            <span class="api-endpoint-url">{{ $baseUrl }}/api/v1/messages/verify-otp</span>
                        </div>
                        <p class="endpoint-description mb-0">{{ __('Memverifikasi kode OTP yang telah dikirim.') }}</p>
                    </div>
                    <div class="endpoint-item">
                        <div class="d-flex align-items-center mb-2">
                            <span class="endpoint-method-badge badge-get">GET</span>
                            <span class="api-endpoint-url">{{ $baseUrl }}/api/v1/messages/otp/{otp_id}/status</span>
                        </div>
                        <p class="endpoint-description mb-0">{{ __('Mendapatkan status OTP berdasarkan ID.') }}</p>
                    </div>
                </div>
            </div>

            <!-- Health Check Endpoint -->
            <div class="endpoint-group">
                <div class="endpoint-group-header" style="background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);">
                    <i class="fas fa-heartbeat"></i>
                    <span>{{ __('Health Check') }}</span>
                    <span class="badge bg-light text-dark ms-auto">{{ __('PUBLIC') }}</span>
                </div>
                <div class="endpoint-group-body">
                    <div class="endpoint-item">
                        <div class="d-flex align-items-center mb-2">
                            <span class="endpoint-method-badge badge-get">GET</span>
                        <span class="api-endpoint-url">{{ $baseUrl }}/api/health</span>
                        </div>
                        <p class="endpoint-description mb-3">{{ __('Memeriksa status kesehatan API. Endpoint ini tidak memerlukan autentikasi.') }}</p>
                        <p class="mb-2"><strong>{{ __('Response Example') }}</strong></p>
                        <div class="api-code mb-0"><code>{
  "status": "ok",
  "timestamp": "2025-11-28T12:00:00Z"
}</code></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function switchCodeTab(button, tabId) {
    // Get parent code-tabs container
    const tabsContainer = button.closest('.code-tabs');
    
    // Remove active class from all tabs and contents
    tabsContainer.querySelectorAll('.code-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    tabsContainer.querySelectorAll('.code-tab-content').forEach(content => {
        content.classList.remove('active');
    });
    
    // Add active class to clicked tab and corresponding content
    button.classList.add('active');
    const targetContent = tabsContainer.querySelector('#' + tabId);
    if (targetContent) {
        targetContent.classList.add('active');
    }
}
</script>
@endpush
@endsection

