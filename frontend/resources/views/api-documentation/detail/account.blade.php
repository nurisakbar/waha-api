<div class="card mb-4">
    <div class="card-header" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white;">
        <h3 class="mb-0"><i class="fas fa-user"></i> {{ __('Account API') }}</h3>
    </div>
    <div class="card-body">
        <p class="mb-4">{{ __('Informasi akun dan penggunaan quota. Cek detail akun dan statistik penggunaan API.') }}</p>

        <!-- GET /account -->
        <div class="endpoint-item">
            <div class="d-flex align-items-center mb-2">
                <span class="endpoint-method-badge badge-get">GET</span>
                <span class="api-endpoint-url">{{ $baseUrl }}/api/v1/account</span>
            </div>
            <p class="endpoint-description mb-3">{{ __('Mendapatkan informasi akun lengkap termasuk quota, subscription, dan statistik.') }}</p>
            
            <p class="mt-3 mb-2"><strong>{{ __('Response Example') }}</strong></p>
            <div class="api-code mb-0"><code>{
  "success": true,
  "data": {
    "user": {
      "id": "user-uuid",
      "name": "John Doe",
      "email": "john@example.com",
      "phone": "81234567890",
      "created_at": "2025-01-01T00:00:00Z"
    },
    "quota": {
      "balance": 100000.00,
      "text_quota": 1000,
      "multimedia_quota": 500,
      "free_text_quota": 100,
      "total_text_quota": 1100
    },
    "subscription": {
      "plan_name": "Premium",
      "plan_id": 2,
      "status": "active",
      "expires_at": "2025-12-31T23:59:59Z"
    },
    "statistics": {
      "total_messages": 1250,
      "total_sessions": 3,
      "connected_sessions": 2
    }
  }
}</code></div>
        </div>

        <!-- GET /account/usage -->
        <div class="endpoint-item">
            <div class="d-flex align-items-center mb-2">
                <span class="endpoint-method-badge badge-get">GET</span>
                <span class="api-endpoint-url">{{ $baseUrl }}/api/v1/account/usage</span>
            </div>
            <p class="endpoint-description mb-3">{{ __('Mendapatkan statistik penggunaan API. Dapat difilter berdasarkan tanggal.') }}</p>
            
            <p class="mt-3 mb-2"><strong>{{ __('Query Parameters') }}</strong></p>
            <ul class="mb-2">
                <li><code>start_date</code> - Tanggal mulai (optional, format: YYYY-MM-DD)</li>
                <li><code>end_date</code> - Tanggal akhir (optional, format: YYYY-MM-DD)</li>
            </ul>
            
            <p class="mt-3 mb-2"><strong>{{ __('Response Example') }}</strong></p>
            <div class="api-code mb-0"><code>{
  "success": true,
  "data": {
    "total_requests": 1500,
    "successful_requests": 1450,
    "failed_requests": 50,
    "average_response_time": 125.5,
    "requests_by_endpoint": [
      {
        "endpoint": "/api/v1/messages",
        "count": 800
      },
      {
        "endpoint": "/api/v1/devices",
        "count": 200
      }
    ],
    "requests_by_status": [
      {
        "status_code": 200,
        "count": 1400
      },
      {
        "status_code": 201,
        "count": 50
      },
      {
        "status_code": 400,
        "count": 30
      }
    ]
  }
}</code></div>
        </div>
    </div>
</div>
