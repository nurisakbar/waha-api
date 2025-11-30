<div class="card mb-4">
    <div class="card-header" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); color: white;">
        <h3 class="mb-0"><i class="fas fa-file-alt"></i> {{ __('Templates API') }}</h3>
    </div>
    <div class="card-body">
        <p class="mb-4">{{ __('Kelola template pesan dengan variabel dinamis. Buat, edit, dan preview template sebelum digunakan.') }}</p>

        <!-- GET /templates -->
        <div class="endpoint-item">
            <div class="d-flex align-items-center mb-2">
                <span class="endpoint-method-badge badge-get">GET</span>
                <span class="api-endpoint-url">{{ $baseUrl }}/api/v1/templates</span>
            </div>
            <p class="endpoint-description mb-3">{{ __('Mendapatkan daftar semua template pesan. Mendukung filter dan pagination.') }}</p>
            
            <p class="mt-3 mb-2"><strong>{{ __('Query Parameters') }}</strong></p>
            <ul class="mb-2">
                <li><code>is_active</code> - Filter by active status (true/false)</li>
                <li><code>message_type</code> - Filter by type (text/image/video/document/button/list)</li>
                <li><code>search</code> - Search by template name</li>
                <li><code>per_page</code> - Items per page (default: 20)</li>
            </ul>
            
            <p class="mt-3 mb-2"><strong>{{ __('Response Example') }}</strong></p>
            <div class="api-code mb-0"><code>{
  "success": true,
  "data": [
    {
      "id": "template-uuid",
      "name": "Welcome Message",
      "content": "Halo @{{name}}, selamat datang!",
      "message_type": "text",
      "variables": ["name"],
      "description": "Template pesan selamat datang",
      "is_active": true,
      "created_at": "2025-11-30T12:00:00Z"
    }
  ],
  "pagination": {
    "current_page": 1,
    "per_page": 20,
    "total": 1,
    "last_page": 1
  }
}</code></div>
        </div>

        <!-- POST /templates -->
        <div class="endpoint-item">
            <div class="d-flex align-items-center mb-2">
                <span class="endpoint-method-badge badge-post">POST</span>
                <span class="api-endpoint-url">{{ $baseUrl }}/api/v1/templates</span>
            </div>
            <p class="endpoint-description mb-3">{{ __('Membuat template pesan baru. Variabel akan otomatis diekstrak dari content jika tidak disediakan.') }}</p>
            
            <p class="mt-3 mb-2"><strong>{{ __('Request Body') }}</strong></p>
            <div class="api-code mb-2"><code>{
  "name": "string (required)",
  "content": "string (required, max 4096 chars)",
  "message_type": "string (required: text|image|video|document|button|list)",
  "description": "string (optional)",
  "is_active": "boolean (optional, default: true)",
  "variables": "array (optional, auto-extracted if not provided)"
}</code></div>
            
            <p class="mt-3 mb-2"><strong>{{ __('Response Example') }}</strong></p>
            <div class="api-code mb-0"><code>{
  "success": true,
  "message": "Template created successfully",
  "data": {
    "id": "template-uuid",
    "name": "Welcome Message",
    "content": "Halo @{{name}}, selamat datang!",
    "message_type": "text",
    "variables": ["name"],
    "is_active": true,
    "created_at": "2025-11-30T12:00:00Z"
  }
}</code></div>
        </div>

        <!-- GET /templates/{id} -->
        <div class="endpoint-item">
            <div class="d-flex align-items-center mb-2">
                <span class="endpoint-method-badge badge-get">GET</span>
                <span class="api-endpoint-url">{{ $baseUrl }}/api/v1/templates/{template_id}</span>
            </div>
            <p class="endpoint-description mb-3">{{ __('Mendapatkan detail template berdasarkan ID.') }}</p>
            
            <p class="mt-3 mb-2"><strong>{{ __('Response Example') }}</strong></p>
            <div class="api-code mb-0"><code>{
  "success": true,
  "data": {
    "id": "template-uuid",
    "name": "Welcome Message",
    "content": "Halo @{{name}}, selamat datang!",
    "message_type": "text",
    "variables": ["name"],
    "description": "Template pesan selamat datang",
    "is_active": true,
    "created_at": "2025-11-30T12:00:00Z",
    "updated_at": "2025-11-30T12:00:00Z"
  }
}</code></div>
        </div>

        <!-- PUT /templates/{id} -->
        <div class="endpoint-item">
            <div class="d-flex align-items-center mb-2">
                <span class="endpoint-method-badge badge-put">PUT</span>
                <span class="api-endpoint-url">{{ $baseUrl }}/api/v1/templates/{template_id}</span>
            </div>
            <p class="endpoint-description mb-3">{{ __('Memperbarui template pesan. Semua field bersifat optional.') }}</p>
            
            <p class="mt-3 mb-2"><strong>{{ __('Response Example') }}</strong></p>
            <div class="api-code mb-0"><code>{
  "success": true,
  "message": "Template updated successfully",
  "data": {
    "id": "template-uuid",
    "name": "Updated Welcome Message",
    "content": "Halo @{{name}}, selamat datang!",
    "message_type": "text",
    "variables": ["name"],
    "is_active": false,
    "updated_at": "2025-11-30T13:00:00Z"
  }
}</code></div>
        </div>

        <!-- DELETE /templates/{id} -->
        <div class="endpoint-item">
            <div class="d-flex align-items-center mb-2">
                <span class="endpoint-method-badge badge-delete">DELETE</span>
                <span class="api-endpoint-url">{{ $baseUrl }}/api/v1/templates/{template_id}</span>
            </div>
            <p class="endpoint-description mb-3">{{ __('Menghapus template pesan.') }}</p>
            
            <p class="mt-3 mb-2"><strong>{{ __('Response Example') }}</strong></p>
            <div class="api-code mb-0"><code>{
  "success": true,
  "message": "Template deleted successfully"
}</code></div>
        </div>

        <!-- POST /templates/{id}/preview -->
        <div class="endpoint-item">
            <div class="d-flex align-items-center mb-2">
                <span class="endpoint-method-badge badge-post">POST</span>
                <span class="api-endpoint-url">{{ $baseUrl }}/api/v1/templates/{template_id}/preview</span>
            </div>
            <p class="endpoint-description mb-3">{{ __('Preview template dengan data contoh. Berguna untuk melihat hasil template setelah variabel diganti.') }}</p>
            
            <p class="mt-3 mb-2"><strong>{{ __('Request Body') }}</strong></p>
            <div class="api-code mb-2"><code>{
  "variables": {
    "name": "John Doe",
    "order_id": "ORD12345"
  }
}</code></div>
            
            <p class="mt-3 mb-2"><strong>{{ __('Response Example') }}</strong></p>
            <div class="api-code mb-0"><code>{
  "success": true,
  "data": {
    "original": {
      "content": "Halo @{{name}}, pesanan @{{order_id}} telah dikonfirmasi.",
      "metadata": {}
    },
    "processed": {
      "content": "Halo John Doe, pesanan ORD12345 telah dikonfirmasi.",
      "metadata": {}
    }
  }
}</code></div>
        </div>
    </div>
</div>
