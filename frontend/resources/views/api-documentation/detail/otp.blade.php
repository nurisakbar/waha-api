<div class="card mb-4">
    <div class="card-header" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white;">
        <h3 class="mb-0"><i class="fas fa-key"></i> {{ __('OTP API') }}</h3>
    </div>
    <div class="card-body">
        <p class="mb-4">{{ __('Kirim dan verifikasi kode OTP melalui WhatsApp. Mendukung template OTP dengan variabel dinamis.') }}</p>

        <!-- POST /messages/otp -->
        <div class="endpoint-item">
            <div class="d-flex align-items-center mb-2">
                <span class="endpoint-method-badge badge-post">POST</span>
                <span class="api-endpoint-url">{{ $baseUrl }}/api/v1/messages/otp</span>
            </div>
            <p class="endpoint-description mb-3">{{ __('Mengirim kode OTP ke nomor tujuan. OTP akan expire dalam waktu yang ditentukan (default: 10 menit).') }}</p>
            
            <p class="mt-3 mb-2"><strong>{{ __('Request Body') }}</strong></p>
            <div class="api-code mb-2"><code>{
  "device_id": "string (required)",
  "to": "string (required, phone number)",
  "template_id": "string (optional, UUID)",
  "expiry_minutes": "integer (optional, 1-60, default: 10)"
}</code></div>
            
            <p class="mt-3 mb-2"><strong>{{ __('Response Example') }}</strong></p>
            <div class="api-code mb-0"><code>{
  "success": true,
  "data": {
    "otp_id": "otp-uuid",
    "expires_at": "2025-11-30T12:10:00Z",
    "expires_in_minutes": 10
  }
}</code></div>
        </div>

        <!-- POST /messages/verify-otp -->
        <div class="endpoint-item">
            <div class="d-flex align-items-center mb-2">
                <span class="endpoint-method-badge badge-post">POST</span>
                <span class="api-endpoint-url">{{ $baseUrl }}/api/v1/messages/verify-otp</span>
            </div>
            <p class="endpoint-description mb-3">{{ __('Memverifikasi kode OTP yang telah dikirim.') }}</p>
            
            <p class="mt-3 mb-2"><strong>{{ __('Request Body') }}</strong></p>
            <div class="api-code mb-2"><code>{
  "phone_number": "string (required)",
  "code": "string (required, 6 digits)"
}</code></div>
            
            <p class="mt-3 mb-2"><strong>{{ __('Response Example (Success)') }}</strong></p>
            <div class="api-code mb-2"><code>{
  "success": true,
  "message": "OTP verified successfully"
}</code></div>
            
            <p class="mt-3 mb-2"><strong>{{ __('Response Example (Failed)') }}</strong></p>
            <div class="api-code mb-0"><code>{
  "success": false,
  "error": "Invalid OTP code",
  "message": "The OTP code you entered is incorrect or has expired"
}</code></div>
        </div>

        <!-- GET /messages/otp/{id}/status -->
        <div class="endpoint-item">
            <div class="d-flex align-items-center mb-2">
                <span class="endpoint-method-badge badge-get">GET</span>
                <span class="api-endpoint-url">{{ $baseUrl }}/api/v1/messages/otp/{otp_id}/status</span>
            </div>
            <p class="endpoint-description mb-3">{{ __('Mendapatkan status OTP berdasarkan ID.') }}</p>
            
            <p class="mt-3 mb-2"><strong>{{ __('Response Example') }}</strong></p>
            <div class="api-code mb-0"><code>{
  "success": true,
  "data": {
    "id": "otp-uuid",
    "phone_number": "81234567890",
    "status": "verified",
    "expires_at": "2025-11-30T12:10:00Z",
    "verified_at": "2025-11-30T12:05:00Z",
    "created_at": "2025-11-30T12:00:00Z"
  }
}</code></div>
        </div>
    </div>
</div>
