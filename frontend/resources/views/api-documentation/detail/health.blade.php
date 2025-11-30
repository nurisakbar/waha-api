<div class="card mb-4">
    <div class="card-header" style="background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%); color: white;">
        <h3 class="mb-0"><i class="fas fa-heartbeat"></i> {{ __('Health Check API') }}</h3>
    </div>
    <div class="card-body">
        <p class="mb-4">{{ __('Cek status kesehatan API. Endpoint ini tidak memerlukan autentikasi.') }}</p>
        
        <div class="endpoint-item">
            <div class="d-flex align-items-center mb-2">
                <span class="endpoint-method-badge badge-get">GET</span>
                <span class="api-endpoint-url">{{ $baseUrl }}/api/health</span>
            </div>
            <p class="endpoint-description mb-3">{{ __('Memeriksa status kesehatan API. Endpoint ini tidak memerlukan autentikasi.') }}</p>
            
            <p class="mt-3 mb-2"><strong>{{ __('Response Example') }}</strong></p>
            <div class="api-code mb-0"><code>{
  "status": "ok",
  "timestamp": "2025-11-30T12:00:00Z"
}</code></div>
        </div>
    </div>
</div>

