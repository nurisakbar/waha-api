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
            
            <div class="code-tabs">
                <div class="code-tabs-header">
                    <button class="code-tab active" onclick="switchCodeTab(this, 'send-otp-curl')">cURL</button>
                    <button class="code-tab" onclick="switchCodeTab(this, 'send-otp-php')">PHP</button>
                    <button class="code-tab" onclick="switchCodeTab(this, 'send-otp-python')">Python</button>
                    <button class="code-tab" onclick="switchCodeTab(this, 'send-otp-nodejs')">Node.js</button>
                    <button class="code-tab" onclick="switchCodeTab(this, 'send-otp-javascript')">JavaScript</button>
                    <button class="code-tab" onclick="switchCodeTab(this, 'send-otp-golang')">Golang</button>
                </div>
                <div id="send-otp-curl" class="code-tab-content active">
                    <div class="api-code mb-0"><code>curl -X POST "{{ $baseUrl }}/api/v1/messages/otp" \
  -H "X-Api-Key: YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "device_id": "abc123def456",
    "to": "81234567890",
    "expiry_minutes": 10
  }'</code></div>
                </div>
                <div id="send-otp-php" class="code-tab-content">
                    <div class="api-code mb-0"><code>&lt;?php
$apiKey = 'YOUR_API_KEY';
$url = '{{ $baseUrl }}/api/v1/messages/otp';

$data = [
    'device_id' => 'abc123def456',
    'to' => '81234567890',
    'expiry_minutes' => 10
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
    echo "OTP ID: " . $result['data']['otp_id'] . "\n";
    echo "Expires At: " . $result['data']['expires_at'] . "\n";
}</code></div>
                </div>
                <div id="send-otp-python" class="code-tab-content">
                    <div class="api-code mb-0"><code>import requests

api_key = 'YOUR_API_KEY'
url = '{{ $baseUrl }}/api/v1/messages/otp'

headers = {
    'X-Api-Key': api_key,
    'Content-Type': 'application/json'
}

data = {
    'device_id': 'abc123def456',
    'to': '81234567890',
    'expiry_minutes': 10
}

response = requests.post(url, headers=headers, json=data)

if response.status_code == 200:
    result = response.json()
    if result['success']:
        print(f"OTP ID: {result['data']['otp_id']}")
        print(f"Expires At: {result['data']['expires_at']}")</code></div>
                </div>
                <div id="send-otp-nodejs" class="code-tab-content">
                    <div class="api-code mb-0"><code>const axios = require('axios');

const apiKey = 'YOUR_API_KEY';
const url = '{{ $baseUrl }}/api/v1/messages/otp';

const data = {
    device_id: 'abc123def456',
    to: '81234567890',
    expiry_minutes: 10
};

axios.post(url, data, {
    headers: {
        'X-Api-Key': apiKey,
        'Content-Type': 'application/json'
    }
})
.then(response => {
    if (response.data.success) {
        console.log(`OTP ID: ${response.data.data.otp_id}`);
        console.log(`Expires At: ${response.data.data.expires_at}`);
    }
})
.catch(error => {
    console.error('Error:', error.response?.data || error.message);
});</code></div>
                </div>
                <div id="send-otp-javascript" class="code-tab-content">
                    <div class="api-code mb-0"><code>const apiKey = 'YOUR_API_KEY';
const url = '{{ $baseUrl }}/api/v1/messages/otp';

const data = {
    device_id: 'abc123def456',
    to: '81234567890',
    expiry_minutes: 10
};

fetch(url, {
    method: 'POST',
    headers: {
        'X-Api-Key': apiKey,
        'Content-Type': 'application/json'
    },
    body: JSON.stringify(data)
})
.then(response => response.json())
.then(result => {
    if (result.success) {
        console.log(`OTP ID: ${result.data.otp_id}`);
        console.log(`Expires At: ${result.data.expires_at}`);
    }
})
.catch(error => {
    console.error('Error:', error);
});</code></div>
                </div>
                <div id="send-otp-golang" class="code-tab-content">
                    <div class="api-code mb-0"><code>package main

import (
    "bytes"
    "encoding/json"
    "fmt"
    "io"
    "net/http"
)

type SendOTPRequest struct {
    DeviceID      string `json:"device_id"`
    To            string `json:"to"`
    ExpiryMinutes int    `json:"expiry_minutes"`
}

type SendOTPResponse struct {
    Success bool `json:"success"`
    Data    struct {
        OTPID      string `json:"otp_id"`
        ExpiresAt  string `json:"expires_at"`
    } `json:"data"`
}

func main() {
    apiKey := "YOUR_API_KEY"
    url := "{{ $baseUrl }}/api/v1/messages/otp"
    
    data := SendOTPRequest{
        DeviceID:      "abc123def456",
        To:            "81234567890",
        ExpiryMinutes: 10,
    }
    
    jsonData, _ := json.Marshal(data)
    
    req, _ := http.NewRequest("POST", url, bytes.NewBuffer(jsonData))
    req.Header.Set("X-Api-Key", apiKey)
    req.Header.Set("Content-Type", "application/json")
    
    client := &http.Client{}
    resp, err := client.Do(req)
    if err != nil {
        panic(err)
    }
    defer resp.Body.Close()
    
    body, _ := io.ReadAll(resp.Body)
    
    var result SendOTPResponse
    json.Unmarshal(body, &result)
    
    if result.Success {
        fmt.Printf("OTP ID: %s\n", result.Data.OTPID)
        fmt.Printf("Expires At: %s\n", result.Data.ExpiresAt)
    }
}</code></div>
                </div>
            </div>
            
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
            
            <div class="code-tabs">
                <div class="code-tabs-header">
                    <button class="code-tab active" onclick="switchCodeTab(this, 'verify-otp-curl')">cURL</button>
                    <button class="code-tab" onclick="switchCodeTab(this, 'verify-otp-php')">PHP</button>
                    <button class="code-tab" onclick="switchCodeTab(this, 'verify-otp-python')">Python</button>
                    <button class="code-tab" onclick="switchCodeTab(this, 'verify-otp-nodejs')">Node.js</button>
                    <button class="code-tab" onclick="switchCodeTab(this, 'verify-otp-javascript')">JavaScript</button>
                    <button class="code-tab" onclick="switchCodeTab(this, 'verify-otp-golang')">Golang</button>
                </div>
                <div id="verify-otp-curl" class="code-tab-content active">
                    <div class="api-code mb-0"><code>curl -X POST "{{ $baseUrl }}/api/v1/messages/verify-otp" \
  -H "X-Api-Key: YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "phone_number": "81234567890",
    "code": "123456"
  }'</code></div>
                </div>
                <div id="verify-otp-php" class="code-tab-content">
                    <div class="api-code mb-0"><code>&lt;?php
$apiKey = 'YOUR_API_KEY';
$url = '{{ $baseUrl }}/api/v1/messages/verify-otp';

$data = [
    'phone_number' => '81234567890',
    'code' => '123456'
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
    echo "OTP verified successfully!\n";
} else {
    echo "Error: " . $result['error'] . "\n";
}</code></div>
                </div>
                <div id="verify-otp-python" class="code-tab-content">
                    <div class="api-code mb-0"><code>import requests

api_key = 'YOUR_API_KEY'
url = '{{ $baseUrl }}/api/v1/messages/verify-otp'

headers = {
    'X-Api-Key': api_key,
    'Content-Type': 'application/json'
}

data = {
    'phone_number': '81234567890',
    'code': '123456'
}

response = requests.post(url, headers=headers, json=data)

if response.status_code == 200:
    result = response.json()
    if result['success']:
        print("OTP verified successfully!")
    else:
        print(f"Error: {result['error']}")</code></div>
                </div>
                <div id="verify-otp-nodejs" class="code-tab-content">
                    <div class="api-code mb-0"><code>const axios = require('axios');

const apiKey = 'YOUR_API_KEY';
const url = '{{ $baseUrl }}/api/v1/messages/verify-otp';

const data = {
    phone_number: '81234567890',
    code: '123456'
};

axios.post(url, data, {
    headers: {
        'X-Api-Key': apiKey,
        'Content-Type': 'application/json'
    }
})
.then(response => {
    if (response.data.success) {
        console.log('OTP verified successfully!');
    } else {
        console.error('Error:', response.data.error);
    }
})
.catch(error => {
    console.error('Error:', error.response?.data || error.message);
});</code></div>
                </div>
                <div id="verify-otp-javascript" class="code-tab-content">
                    <div class="api-code mb-0"><code>const apiKey = 'YOUR_API_KEY';
const url = '{{ $baseUrl }}/api/v1/messages/verify-otp';

const data = {
    phone_number: '81234567890',
    code: '123456'
};

fetch(url, {
    method: 'POST',
    headers: {
        'X-Api-Key': apiKey,
        'Content-Type': 'application/json'
    },
    body: JSON.stringify(data)
})
.then(response => response.json())
.then(result => {
    if (result.success) {
        console.log('OTP verified successfully!');
    } else {
        console.error('Error:', result.error);
    }
})
.catch(error => {
    console.error('Error:', error);
});</code></div>
                </div>
                <div id="verify-otp-golang" class="code-tab-content">
                    <div class="api-code mb-0"><code>package main

import (
    "bytes"
    "encoding/json"
    "fmt"
    "io"
    "net/http"
)

type VerifyOTPRequest struct {
    PhoneNumber string `json:"phone_number"`
    Code        string `json:"code"`
}

type VerifyOTPResponse struct {
    Success bool   `json:"success"`
    Message string `json:"message"`
    Error   string `json:"error,omitempty"`
}

func main() {
    apiKey := "YOUR_API_KEY"
    url := "{{ $baseUrl }}/api/v1/messages/verify-otp"
    
    data := VerifyOTPRequest{
        PhoneNumber: "81234567890",
        Code:        "123456",
    }
    
    jsonData, _ := json.Marshal(data)
    
    req, _ := http.NewRequest("POST", url, bytes.NewBuffer(jsonData))
    req.Header.Set("X-Api-Key", apiKey)
    req.Header.Set("Content-Type", "application/json")
    
    client := &http.Client{}
    resp, err := client.Do(req)
    if err != nil {
        panic(err)
    }
    defer resp.Body.Close()
    
    body, _ := io.ReadAll(resp.Body)
    
    var result VerifyOTPResponse
    json.Unmarshal(body, &result)
    
    if result.Success {
        fmt.Println("OTP verified successfully!")
    } else {
        fmt.Printf("Error: %s\n", result.Error)
    }
}</code></div>
                </div>
            </div>
            
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
