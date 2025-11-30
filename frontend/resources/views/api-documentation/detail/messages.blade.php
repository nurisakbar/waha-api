<div class="card mb-4">
    <div class="card-header" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white;">
        <h3 class="mb-0"><i class="fas fa-paper-plane"></i> {{ __('Messages API') }}</h3>
    </div>
    <div class="card-body">
        <p class="mb-4">{{ __('Kirim berbagai jenis pesan WhatsApp melalui API: text, image, video, document, poll, button, dan list.') }}</p>

        <!-- POST /messages -->
        <div class="endpoint-item">
            <div class="d-flex align-items-center mb-2">
                <span class="endpoint-method-badge badge-post">POST</span>
                <span class="api-endpoint-url">{{ $baseUrl }}/api/v1/messages</span>
            </div>
            <p class="endpoint-description mb-3">{{ __('Mengirim pesan WhatsApp. Mendukung berbagai jenis pesan: text, image, video, document, poll, button, dan list.') }}</p>
            
            <p class="mt-3 mb-2"><strong>{{ __('Request Body (Text Message)') }}</strong></p>
            <div class="api-code mb-2"><code>{
  "device_id": "string (required)",
  "to": "string (required, phone number)",
  "message_type": "text",
  "text": "string (required)",
  "chat_type": "personal (optional, default: personal)"
}</code></div>
            
            <p class="mt-3 mb-2"><strong>{{ __('Response Example (Success)') }}</strong></p>
            <div class="api-code mb-0"><code>{
  "success": true,
  "data": {
    "message_id": "message-uuid",
    "whatsapp_message_id": "whatsapp-msg-id",
    "status": "sent",
    "ack": 1,
    "to": "81234567890"
  }
}</code></div>
        </div>

        <!-- POST /devices/{id}/messages -->
        <div class="endpoint-item">
            <div class="d-flex align-items-center mb-2">
                <span class="endpoint-method-badge badge-post">POST</span>
                <span class="api-endpoint-url">{{ $baseUrl }}/api/v1/devices/{device_id}/messages</span>
            </div>
            <p class="endpoint-description mb-3">{{ __('Mengirim pesan menggunakan device ID di URL. Format alternatif untuk mengirim pesan.') }}</p>
            
            <p class="mt-3 mb-2"><strong>{{ __('Request Body') }}</strong></p>
            <div class="api-code mb-2"><code>{
  "to": "string (required, phone number)",
  "message_type": "text|image|video|document|poll|button|list",
  "text": "string (for text message)",
  "image": "string (for image, URL or base64)",
  "caption": "string (optional, for image/video/document)"
}</code></div>
            
            <p class="mt-3 mb-2"><strong>{{ __('Response Example') }}</strong></p>
            <div class="api-code mb-0"><code>{
  "success": true,
  "data": {
    "message_id": "message-uuid",
    "whatsapp_message_id": "whatsapp-msg-id",
    "status": "sent",
    "ack": 1,
    "to": "81234567890"
  }
}</code></div>
        </div>

        <!-- GET /messages -->
        <div class="endpoint-item">
            <div class="d-flex align-items-center mb-2">
                <span class="endpoint-method-badge badge-get">GET</span>
                <span class="api-endpoint-url">{{ $baseUrl }}/api/v1/messages</span>
            </div>
            <p class="endpoint-description mb-3">{{ __('Mendapatkan daftar pesan. Memerlukan device_id sebagai query parameter.') }}</p>
            
            <p class="mt-3 mb-2"><strong>{{ __('Query Parameters') }}</strong></p>
            <ul class="mb-2">
                <li><code>device_id</code> - Device ID (required)</li>
                <li><code>per_page</code> - Items per page (default: 20)</li>
            </ul>
            
            <p class="mt-3 mb-2"><strong>{{ __('Response Example') }}</strong></p>
            <div class="api-code mb-0"><code>{
  "success": true,
  "data": [
    {
      "id": "message-uuid",
      "from_number": null,
      "to_number": "81234567890",
      "message_type": "text",
      "content": "Hello, this is a test message",
      "status": "sent",
      "created_at": "2025-11-30T12:00:00Z"
    }
  ],
  "pagination": {
    "current_page": 1,
    "per_page": 20,
    "total": 100,
    "last_page": 5
  }
}</code></div>
        </div>

        <!-- GET /devices/{id}/messages -->
        <div class="endpoint-item">
            <div class="d-flex align-items-center mb-2">
                <span class="endpoint-method-badge badge-get">GET</span>
                <span class="api-endpoint-url">{{ $baseUrl }}/api/v1/devices/{device_id}/messages</span>
            </div>
            <p class="endpoint-description mb-3">{{ __('Mendapatkan daftar pesan untuk device tertentu.') }}</p>
            
            <p class="mt-3 mb-2"><strong>{{ __('Response Example') }}</strong></p>
            <div class="api-code mb-0"><code>{
  "success": true,
  "data": [
    {
      "id": "message-uuid",
      "from_number": null,
      "to_number": "81234567890",
      "message_type": "text",
      "content": "Hello, this is a test message",
      "status": "sent",
      "created_at": "2025-11-30T12:00:00Z"
    }
  ],
  "pagination": {
    "current_page": 1,
    "per_page": 20,
    "total": 100,
    "last_page": 5
  }
}</code></div>
        </div>

        <!-- GET /messages/{id} -->
        <div class="endpoint-item">
            <div class="d-flex align-items-center mb-2">
                <span class="endpoint-method-badge badge-get">GET</span>
                <span class="api-endpoint-url">{{ $baseUrl }}/api/v1/messages/{message_id}</span>
            </div>
            <p class="endpoint-description mb-3">{{ __('Mendapatkan detail pesan berdasarkan ID.') }}</p>
            
            <p class="mt-3 mb-2"><strong>{{ __('Response Example') }}</strong></p>
            <div class="api-code mb-0"><code>{
  "success": true,
  "data": {
    "id": "message-uuid",
    "from_number": null,
    "to_number": "81234567890",
    "message_type": "text",
    "content": "Hello, this is a test message",
    "status": "sent",
    "whatsapp_message_id": "whatsapp-msg-id",
    "created_at": "2025-11-30T12:00:00Z",
    "sent_at": "2025-11-30T12:00:01Z"
  }
}</code></div>
        </div>

        <!-- POST /devices/{id}/messages/sync -->
        <div class="endpoint-item">
            <div class="d-flex align-items-center mb-2">
                <span class="endpoint-method-badge badge-post">POST</span>
                <span class="api-endpoint-url">{{ $baseUrl }}/api/v1/devices/{device_id}/messages/sync</span>
            </div>
            <p class="endpoint-description mb-3">{{ __('Sinkronisasi pesan dari WAHA API ke database. Berguna untuk mengambil pesan yang masuk.') }}</p>
            
            <p class="mt-3 mb-2"><strong>{{ __('Request Body') }}</strong></p>
            <div class="api-code mb-2"><code>{
  "chatId": "string (optional, specific chat ID)",
  "limit": "integer (optional, default: 100)"
}</code></div>
            
            <p class="mt-3 mb-2"><strong>{{ __('Response Example') }}</strong></p>
            <div class="api-code mb-0"><code>{
  "success": true,
  "message": "Messages synced successfully",
  "data": {
    "synced_count": 25,
    "total_messages": 1250
  }
}</code></div>
        </div>
    </div>
</div>
