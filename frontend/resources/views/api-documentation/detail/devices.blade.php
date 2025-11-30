<div class="card mb-4">
    <div class="card-header" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
        <h3 class="mb-0"><i class="fas fa-mobile-alt"></i> {{ __('Devices API') }}</h3>
    </div>
    <div class="card-body">
        <p class="mb-4">{{ __('Kelola device WhatsApp Anda melalui API. Buat device baru, dapatkan QR code untuk pairing, dan cek status koneksi.') }}</p>

        <!-- GET /devices -->
        <div class="endpoint-item">
            <div class="d-flex align-items-center mb-2">
                <span class="endpoint-method-badge badge-get">GET</span>
                <span class="api-endpoint-url">{{ $baseUrl }}/api/v1/devices</span>
            </div>
            <p class="endpoint-description mb-3">{{ __('Mendapatkan daftar semua device WhatsApp yang terhubung.') }}</p>
            
            <div class="code-tabs">
                <div class="code-tabs-header">
                    <button class="code-tab active" onclick="switchCodeTab(this, 'devices-list-curl')">cURL</button>
                    <button class="code-tab" onclick="switchCodeTab(this, 'devices-list-php')">PHP</button>
                    <button class="code-tab" onclick="switchCodeTab(this, 'devices-list-python')">Python</button>
                    <button class="code-tab" onclick="switchCodeTab(this, 'devices-list-nodejs')">Node.js</button>
                    <button class="code-tab" onclick="switchCodeTab(this, 'devices-list-javascript')">JavaScript</button>
                    <button class="code-tab" onclick="switchCodeTab(this, 'devices-list-golang')">Golang</button>
                </div>
                <div id="devices-list-curl" class="code-tab-content active">
                    <div class="api-code mb-0"><code>curl -X GET "{{ $baseUrl }}/api/v1/devices" \
  -H "X-Api-Key: YOUR_API_KEY" \
  -H "Content-Type: application/json"</code></div>
                </div>
                <div id="devices-list-php" class="code-tab-content">
                    <div class="api-code mb-0"><code>&lt;?php
$apiKey = 'YOUR_API_KEY';
$url = '{{ $baseUrl }}/api/v1/devices';

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
    foreach ($data['data'] as $device) {
        echo "Device: " . $device['name'] . "\n";
    }
}</code></div>
                </div>
                <div id="devices-list-python" class="code-tab-content">
                    <div class="api-code mb-0"><code>import requests

api_key = 'YOUR_API_KEY'
url = '{{ $baseUrl }}/api/v1/devices'

headers = {
    'X-Api-Key': api_key,
    'Content-Type': 'application/json'
}

response = requests.get(url, headers=headers)

if response.status_code == 200:
    data = response.json()
    if data['success']:
        for device in data['data']:
            print(f"Device: {device['name']}")</code></div>
                </div>
                <div id="devices-list-nodejs" class="code-tab-content">
                    <div class="api-code mb-0"><code>const axios = require('axios');

const apiKey = 'YOUR_API_KEY';
const url = '{{ $baseUrl }}/api/v1/devices';

axios.get(url, {
    headers: {
        'X-Api-Key': apiKey,
        'Content-Type': 'application/json'
    }
})
.then(response => {
    if (response.data.success) {
        response.data.data.forEach(device => {
            console.log(`Device: ${device.name}`);
        });
    }
})
.catch(error => {
    console.error('Error:', error.response?.data || error.message);
});</code></div>
                </div>
                <div id="devices-list-javascript" class="code-tab-content">
                    <div class="api-code mb-0"><code>const apiKey = 'YOUR_API_KEY';
const url = '{{ $baseUrl }}/api/v1/devices';

fetch(url, {
    method: 'GET',
    headers: {
        'X-Api-Key': apiKey,
        'Content-Type': 'application/json'
    }
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        data.data.forEach(device => {
            console.log(`Device: ${device.name}`);
        });
    }
})
.catch(error => {
    console.error('Error:', error);
});</code></div>
                </div>
                <div id="devices-list-golang" class="code-tab-content">
                    <div class="api-code mb-0"><code>package main

import (
    "encoding/json"
    "fmt"
    "io"
    "net/http"
)

type DeviceResponse struct {
    Success bool     `json:"success"`
    Data    []Device `json:"data"`
}

type Device struct {
    ID        string `json:"id"`
    Name      string `json:"name"`
    Status    string `json:"status"`
    CreatedAt string `json:"created_at"`
}

func main() {
    apiKey := "YOUR_API_KEY"
    url := "{{ $baseUrl }}/api/v1/devices"
    
    req, _ := http.NewRequest("GET", url, nil)
    req.Header.Set("X-Api-Key", apiKey)
    req.Header.Set("Content-Type", "application/json")
    
    client := &http.Client{}
    resp, err := client.Do(req)
    if err != nil {
        panic(err)
    }
    defer resp.Body.Close()
    
    body, _ := io.ReadAll(resp.Body)
    
    var result DeviceResponse
    json.Unmarshal(body, &result)
    
    if result.Success {
        for _, device := range result.Data {
            fmt.Printf("Device: %s\n", device.Name)
        }
    }
}</code></div>
                </div>
            </div>
            
            <p class="mt-3 mb-2"><strong>{{ __('Response Example') }}</strong></p>
            <div class="api-code mb-0"><code>{
  "success": true,
  "data": [
    {
      "id": "abc123def456",
      "name": "My Device",
      "status": "connected",
      "created_at": "2025-11-28T12:00:00Z"
    }
  ]
}</code></div>
        </div>

        <!-- POST /devices -->
        <div class="endpoint-item">
            <div class="d-flex align-items-center mb-2">
                <span class="endpoint-method-badge badge-post">POST</span>
                <span class="api-endpoint-url">{{ $baseUrl }}/api/v1/devices</span>
            </div>
            <p class="endpoint-description mb-3">{{ __('Membuat device WhatsApp baru. Device akan dibuat dengan status pairing dan perlu di-pair menggunakan QR code.') }}</p>
            
            <div class="code-tabs">
                <div class="code-tabs-header">
                    <button class="code-tab active" onclick="switchCodeTab(this, 'create-device-curl')">cURL</button>
                    <button class="code-tab" onclick="switchCodeTab(this, 'create-device-php')">PHP</button>
                    <button class="code-tab" onclick="switchCodeTab(this, 'create-device-python')">Python</button>
                    <button class="code-tab" onclick="switchCodeTab(this, 'create-device-nodejs')">Node.js</button>
                    <button class="code-tab" onclick="switchCodeTab(this, 'create-device-javascript')">JavaScript</button>
                    <button class="code-tab" onclick="switchCodeTab(this, 'create-device-golang')">Golang</button>
                </div>
                <div id="create-device-curl" class="code-tab-content active">
                    <div class="api-code mb-0"><code>curl -X POST "{{ $baseUrl }}/api/v1/devices" \
  -H "X-Api-Key: YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "My Device",
    "phone_number": "81234567890"
  }'</code></div>
                </div>
                <div id="create-device-php" class="code-tab-content">
                    <div class="api-code mb-0"><code>&lt;?php
$apiKey = 'YOUR_API_KEY';
$url = '{{ $baseUrl }}/api/v1/devices';

$data = [
    'name' => 'My Device',
    'phone_number' => '81234567890'
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
    echo "Device ID: " . $result['data']['id'] . "\n";
}</code></div>
                </div>
                <div id="create-device-python" class="code-tab-content">
                    <div class="api-code mb-0"><code>import requests

api_key = 'YOUR_API_KEY'
url = '{{ $baseUrl }}/api/v1/devices'

headers = {
    'X-Api-Key': api_key,
    'Content-Type': 'application/json'
}

data = {
    'name': 'My Device',
    'phone_number': '81234567890'
}

response = requests.post(url, headers=headers, json=data)

if response.status_code == 201:
    result = response.json()
    if result['success']:
        print(f"Device ID: {result['data']['id']}")</code></div>
                </div>
                <div id="create-device-nodejs" class="code-tab-content">
                    <div class="api-code mb-0"><code>const axios = require('axios');

const apiKey = 'YOUR_API_KEY';
const url = '{{ $baseUrl }}/api/v1/devices';

const data = {
    name: 'My Device',
    phone_number: '81234567890'
};

axios.post(url, data, {
    headers: {
        'X-Api-Key': apiKey,
        'Content-Type': 'application/json'
    }
})
.then(response => {
    if (response.data.success) {
        console.log(`Device ID: ${response.data.data.id}`);
    }
})
.catch(error => {
    console.error('Error:', error.response?.data || error.message);
});</code></div>
                </div>
                <div id="create-device-javascript" class="code-tab-content">
                    <div class="api-code mb-0"><code>const apiKey = 'YOUR_API_KEY';
const url = '{{ $baseUrl }}/api/v1/devices';

const data = {
    name: 'My Device',
    phone_number: '81234567890'
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
        console.log(`Device ID: ${result.data.id}`);
    }
})
.catch(error => {
    console.error('Error:', error);
});</code></div>
                </div>
                <div id="create-device-golang" class="code-tab-content">
                    <div class="api-code mb-0"><code>package main

import (
    "bytes"
    "encoding/json"
    "fmt"
    "io"
    "net/http"
)

type CreateDeviceRequest struct {
    Name        string `json:"name"`
    PhoneNumber string `json:"phone_number"`
}

type CreateDeviceResponse struct {
    Success bool   `json:"success"`
    Message string `json:"message"`
    Data    Device `json:"data"`
}

type Device struct {
    ID        string `json:"id"`
    Name      string `json:"name"`
    Status    string `json:"status"`
    CreatedAt string `json:"created_at"`
}

func main() {
    apiKey := "YOUR_API_KEY"
    url := "{{ $baseUrl }}/api/v1/devices"
    
    data := CreateDeviceRequest{
        Name:        "My Device",
        PhoneNumber: "81234567890",
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
    
    var result CreateDeviceResponse
    json.Unmarshal(body, &result)
    
    if result.Success {
        fmt.Printf("Device ID: %s\n", result.Data.ID)
    }
}</code></div>
                </div>
            </div>
            
            <p class="mt-3 mb-2"><strong>{{ __('Request Body') }}</strong></p>
            <div class="api-code mb-2"><code>{
  "name": "string (required, max 255 chars)",
  "phone_number": "string (required, 9-13 digits without leading 0)"
}</code></div>
            
            <p class="mt-3 mb-2"><strong>{{ __('Response Example') }}</strong></p>
            <div class="api-code mb-0"><code>{
  "success": true,
  "message": "Device created successfully. Use the pair endpoint to get QR code.",
  "data": {
    "id": "abc123def456",
    "name": "My Device",
    "status": "pairing",
    "created_at": "2025-11-30T12:00:00Z"
  }
}</code></div>
        </div>

        <!-- GET /devices/{id} -->
        <div class="endpoint-item">
            <div class="d-flex align-items-center mb-2">
                <span class="endpoint-method-badge badge-get">GET</span>
                <span class="api-endpoint-url">{{ $baseUrl }}/api/v1/devices/{device_id}</span>
            </div>
            <p class="endpoint-description mb-3">{{ __('Mendapatkan detail device WhatsApp berdasarkan ID.') }}</p>
            
            <p class="mt-3 mb-2"><strong>{{ __('Response Example') }}</strong></p>
            <div class="api-code mb-0"><code>{
  "success": true,
  "data": {
    "id": "abc123def456",
    "name": "My Device",
    "status": "connected",
    "created_at": "2025-11-28T12:00:00Z",
    "last_activity_at": "2025-11-30T10:30:00Z"
  }
}</code></div>
        </div>

        <!-- GET /devices/{id}/pair -->
        <div class="endpoint-item">
            <div class="d-flex align-items-center mb-2">
                <span class="endpoint-method-badge badge-get">GET</span>
                <span class="api-endpoint-url">{{ $baseUrl }}/api/v1/devices/{device_id}/pair</span>
            </div>
            <p class="endpoint-description mb-3">{{ __('Mendapatkan QR code untuk pairing device WhatsApp. QR code akan expire dalam 2 menit.') }}</p>
            
            <p class="mt-3 mb-2"><strong>{{ __('Response Example (Pairing)') }}</strong></p>
            <div class="api-code mb-2"><code>{
  "success": true,
  "data": {
    "id": "abc123def456",
    "name": "My Device",
    "status": "pairing",
    "qr_code": "data:image/png;base64,iVBORw0KG...",
    "qr_code_expires_at": "2025-11-30T12:02:00Z",
    "is_connected": false
  }
}</code></div>
            
            <p class="mt-3 mb-2"><strong>{{ __('Response Example (Already Connected)') }}</strong></p>
            <div class="api-code mb-0"><code>{
  "success": true,
  "message": "Device is already connected",
  "data": {
    "id": "abc123def456",
    "status": "connected",
    "is_connected": true
  }
}</code></div>
        </div>

        <!-- GET /devices/{id}/status -->
        <div class="endpoint-item">
            <div class="d-flex align-items-center mb-2">
                <span class="endpoint-method-badge badge-get">GET</span>
                <span class="api-endpoint-url">{{ $baseUrl }}/api/v1/devices/{device_id}/status</span>
            </div>
            <p class="endpoint-description mb-3">{{ __('Mendapatkan status koneksi device WhatsApp.') }}</p>
            
            <p class="mt-3 mb-2"><strong>{{ __('Response Example') }}</strong></p>
            <div class="api-code mb-0"><code>{
  "success": true,
  "data": {
    "status": "connected",
    "is_connected": true
  }
}</code></div>
        </div>
    </div>
</div>

