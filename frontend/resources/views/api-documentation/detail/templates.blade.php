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
            
            <div class="code-tabs">
                <div class="code-tabs-header">
                    <button class="code-tab active" onclick="switchCodeTab(this, 'templates-list-curl')">cURL</button>
                    <button class="code-tab" onclick="switchCodeTab(this, 'templates-list-php')">PHP</button>
                    <button class="code-tab" onclick="switchCodeTab(this, 'templates-list-python')">Python</button>
                    <button class="code-tab" onclick="switchCodeTab(this, 'templates-list-nodejs')">Node.js</button>
                    <button class="code-tab" onclick="switchCodeTab(this, 'templates-list-javascript')">JavaScript</button>
                    <button class="code-tab" onclick="switchCodeTab(this, 'templates-list-golang')">Golang</button>
                </div>
                <div id="templates-list-curl" class="code-tab-content active">
                    <div class="api-code mb-0"><code>curl -X GET "{{ $baseUrl }}/api/v1/templates" \
  -H "X-Api-Key: YOUR_API_KEY" \
  -H "Content-Type: application/json"</code></div>
                </div>
                <div id="templates-list-php" class="code-tab-content">
                    <div class="api-code mb-0"><code>&lt;?php
$apiKey = 'YOUR_API_KEY';
$url = '{{ $baseUrl }}/api/v1/templates';

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
    foreach ($data['data'] as $template) {
        echo "Template: " . $template['name'] . "\n";
    }
}</code></div>
                </div>
                <div id="templates-list-python" class="code-tab-content">
                    <div class="api-code mb-0"><code>import requests

api_key = 'YOUR_API_KEY'
url = '{{ $baseUrl }}/api/v1/templates'

headers = {
    'X-Api-Key': api_key,
    'Content-Type': 'application/json'
}

response = requests.get(url, headers=headers)

if response.status_code == 200:
    data = response.json()
    if data['success']:
        for template in data['data']:
            print(f"Template: {template['name']}")</code></div>
                </div>
                <div id="templates-list-nodejs" class="code-tab-content">
                    <div class="api-code mb-0"><code>const axios = require('axios');

const apiKey = 'YOUR_API_KEY';
const url = '{{ $baseUrl }}/api/v1/templates';

axios.get(url, {
    headers: {
        'X-Api-Key': apiKey,
        'Content-Type': 'application/json'
    }
})
.then(response => {
    if (response.data.success) {
        response.data.data.forEach(template => {
            console.log(`Template: ${template.name}`);
        });
    }
})
.catch(error => {
    console.error('Error:', error.response?.data || error.message);
});</code></div>
                </div>
                <div id="templates-list-javascript" class="code-tab-content">
                    <div class="api-code mb-0"><code>const apiKey = 'YOUR_API_KEY';
const url = '{{ $baseUrl }}/api/v1/templates';

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
        data.data.forEach(template => {
            console.log(`Template: ${template.name}`);
        });
    }
})
.catch(error => {
    console.error('Error:', error);
});</code></div>
                </div>
                <div id="templates-list-golang" class="code-tab-content">
                    <div class="api-code mb-0"><code>package main

import (
    "encoding/json"
    "fmt"
    "io"
    "net/http"
)

type TemplatesResponse struct {
    Success bool       `json:"success"`
    Data    []Template `json:"data"`
}

type Template struct {
    ID          string   `json:"id"`
    Name        string   `json:"name"`
    Content     string   `json:"content"`
    MessageType string   `json:"message_type"`
    Variables   []string `json:"variables"`
}

func main() {
    apiKey := "YOUR_API_KEY"
    url := "{{ $baseUrl }}/api/v1/templates"
    
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
    
    var result TemplatesResponse
    json.Unmarshal(body, &result)
    
    if result.Success {
        for _, template := range result.Data {
            fmt.Printf("Template: %s\n", template.Name)
        }
    }
}</code></div>
                </div>
            </div>
            
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
      "content": "Halo @{{ '{' . '{name}' . '}' }}, selamat datang!",
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
            
            <div class="code-tabs">
                <div class="code-tabs-header">
                    <button class="code-tab active" onclick="switchCodeTab(this, 'create-template-curl')">cURL</button>
                    <button class="code-tab" onclick="switchCodeTab(this, 'create-template-php')">PHP</button>
                    <button class="code-tab" onclick="switchCodeTab(this, 'create-template-python')">Python</button>
                    <button class="code-tab" onclick="switchCodeTab(this, 'create-template-nodejs')">Node.js</button>
                    <button class="code-tab" onclick="switchCodeTab(this, 'create-template-javascript')">JavaScript</button>
                    <button class="code-tab" onclick="switchCodeTab(this, 'create-template-golang')">Golang</button>
                </div>
                <div id="create-template-curl" class="code-tab-content active">
                    <div class="api-code mb-0"><code>curl -X POST "{{ $baseUrl }}/api/v1/templates" \
  -H "X-Api-Key: YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Welcome Message",
    "content": "Halo @{{ '{' . '{name}' . '}' }}, selamat datang!",
    "message_type": "text",
    "description": "Template pesan selamat datang"
  }'</code></div>
                </div>
                <div id="create-template-php" class="code-tab-content">
                    <div class="api-code mb-0"><code>&lt;?php
$apiKey = 'YOUR_API_KEY';
$url = '{{ $baseUrl }}/api/v1/templates';

$data = [
    'name' => 'Welcome Message',
    'content' => 'Halo @{{name}}, selamat datang!',
    'message_type' => 'text',
    'description' => 'Template pesan selamat datang'
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
    echo "Template ID: " . $result['data']['id'] . "\n";
}</code></div>
                </div>
                <div id="create-template-python" class="code-tab-content">
                    <div class="api-code mb-0"><code>import requests

api_key = 'YOUR_API_KEY'
url = '{{ $baseUrl }}/api/v1/templates'

headers = {
    'X-Api-Key': api_key,
    'Content-Type': 'application/json'
}

data = {
    'name': 'Welcome Message',
    'content': 'Halo @{{name}}, selamat datang!',
    'message_type': 'text',
    'description': 'Template pesan selamat datang'
}

response = requests.post(url, headers=headers, json=data)

if response.status_code == 201:
    result = response.json()
    if result['success']:
        print(f"Template ID: {result['data']['id']}")</code></div>
                </div>
                <div id="create-template-nodejs" class="code-tab-content">
                    <div class="api-code mb-0"><code>const axios = require('axios');

const apiKey = 'YOUR_API_KEY';
const url = '{{ $baseUrl }}/api/v1/templates';

const data = {
    name: 'Welcome Message',
    content: 'Halo @{{name}}, selamat datang!',
    message_type: 'text',
    description: 'Template pesan selamat datang'
};

axios.post(url, data, {
    headers: {
        'X-Api-Key': apiKey,
        'Content-Type': 'application/json'
    }
})
.then(response => {
    if (response.data.success) {
        console.log(`Template ID: ${response.data.data.id}`);
    }
})
.catch(error => {
    console.error('Error:', error.response?.data || error.message);
});</code></div>
                </div>
                <div id="create-template-javascript" class="code-tab-content">
                    <div class="api-code mb-0"><code>const apiKey = 'YOUR_API_KEY';
const url = '{{ $baseUrl }}/api/v1/templates';

const data = {
    name: 'Welcome Message',
    content: 'Halo @{{name}}, selamat datang!',
    message_type: 'text',
    description: 'Template pesan selamat datang'
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
        console.log(`Template ID: ${result.data.id}`);
    }
})
.catch(error => {
    console.error('Error:', error);
});</code></div>
                </div>
                <div id="create-template-golang" class="code-tab-content">
                    <div class="api-code mb-0"><code>package main

import (
    "bytes"
    "encoding/json"
    "fmt"
    "io"
    "net/http"
)

type CreateTemplateRequest struct {
    Name        string `json:"name"`
    Content     string `json:"content"`
    MessageType string `json:"message_type"`
    Description string `json:"description"`
}

type CreateTemplateResponse struct {
    Success bool     `json:"success"`
    Message string   `json:"message"`
    Data    Template `json:"data"`
}

type Template struct {
    ID          string   `json:"id"`
    Name        string   `json:"name"`
    Content     string   `json:"content"`
    MessageType string   `json:"message_type"`
    Variables   []string `json:"variables"`
}

func main() {
    apiKey := "YOUR_API_KEY"
    url := "{{ $baseUrl }}/api/v1/templates"
    
    data := CreateTemplateRequest{
        Name:        "Welcome Message",
        Content:     "Halo @{{name}}, selamat datang!",
        MessageType: "text",
        Description: "Template pesan selamat datang",
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
    
    var result CreateTemplateResponse
    json.Unmarshal(body, &result)
    
    if result.Success {
        fmt.Printf("Template ID: %s\n", result.Data.ID)
    }
}</code></div>
                </div>
            </div>
            
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
    "content": "Halo @{{ '{' . '{name}' . '}' }}, selamat datang!",
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
    "content": "Halo @{{ '{' . '{name}' . '}' }}, selamat datang!",
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
    "content": "Halo @{{ '{' . '{name}' . '}' }}, selamat datang!",
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
            
            <div class="code-tabs">
                <div class="code-tabs-header">
                    <button class="code-tab active" onclick="switchCodeTab(this, 'preview-template-curl')">cURL</button>
                    <button class="code-tab" onclick="switchCodeTab(this, 'preview-template-php')">PHP</button>
                    <button class="code-tab" onclick="switchCodeTab(this, 'preview-template-python')">Python</button>
                    <button class="code-tab" onclick="switchCodeTab(this, 'preview-template-nodejs')">Node.js</button>
                    <button class="code-tab" onclick="switchCodeTab(this, 'preview-template-javascript')">JavaScript</button>
                    <button class="code-tab" onclick="switchCodeTab(this, 'preview-template-golang')">Golang</button>
                </div>
                <div id="preview-template-curl" class="code-tab-content active">
                    <div class="api-code mb-0"><code>curl -X POST "{{ $baseUrl }}/api/v1/templates/template-uuid/preview" \
  -H "X-Api-Key: YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "variables": {
      "name": "John Doe",
      "order_id": "ORD12345"
    }
  }'</code></div>
                </div>
                <div id="preview-template-php" class="code-tab-content">
                    <div class="api-code mb-0"><code>&lt;?php
$apiKey = 'YOUR_API_KEY';
$templateId = 'template-uuid';
$url = '{{ $baseUrl }}/api/v1/templates/' . $templateId . '/preview';

$data = [
    'variables' => [
        'name' => 'John Doe',
        'order_id' => 'ORD12345'
    ]
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
    echo "Processed: " . $result['data']['processed']['content'] . "\n";
}</code></div>
                </div>
                <div id="preview-template-python" class="code-tab-content">
                    <div class="api-code mb-0"><code>import requests

api_key = 'YOUR_API_KEY'
template_id = 'template-uuid'
url = f'{{ $baseUrl }}/api/v1/templates/{template_id}/preview'

headers = {
    'X-Api-Key': api_key,
    'Content-Type': 'application/json'
}

data = {
    'variables': {
        'name': 'John Doe',
        'order_id': 'ORD12345'
    }
}

response = requests.post(url, headers=headers, json=data)

if response.status_code == 200:
    result = response.json()
    if result['success']:
        print(f"Processed: {result['data']['processed']['content']}")</code></div>
                </div>
                <div id="preview-template-nodejs" class="code-tab-content">
                    <div class="api-code mb-0"><code>const axios = require('axios');

const apiKey = 'YOUR_API_KEY';
const templateId = 'template-uuid';
const url = `{{ $baseUrl }}/api/v1/templates/${templateId}/preview`;

const data = {
    variables: {
        name: 'John Doe',
        order_id: 'ORD12345'
    }
};

axios.post(url, data, {
    headers: {
        'X-Api-Key': apiKey,
        'Content-Type': 'application/json'
    }
})
.then(response => {
    if (response.data.success) {
        console.log(`Processed: ${response.data.data.processed.content}`);
    }
})
.catch(error => {
    console.error('Error:', error.response?.data || error.message);
});</code></div>
                </div>
                <div id="preview-template-javascript" class="code-tab-content">
                    <div class="api-code mb-0"><code>const apiKey = 'YOUR_API_KEY';
const templateId = 'template-uuid';
const url = `{{ $baseUrl }}/api/v1/templates/${templateId}/preview`;

const data = {
    variables: {
        name: 'John Doe',
        order_id: 'ORD12345'
    }
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
        console.log(`Processed: ${result.data.processed.content}`);
    }
})
.catch(error => {
    console.error('Error:', error);
});</code></div>
                </div>
                <div id="preview-template-golang" class="code-tab-content">
                    <div class="api-code mb-0"><code>package main

import (
    "bytes"
    "encoding/json"
    "fmt"
    "io"
    "net/http"
)

type PreviewTemplateRequest struct {
    Variables map[string]string `json:"variables"`
}

type PreviewTemplateResponse struct {
    Success bool `json:"success"`
    Data    struct {
        Processed struct {
            Content string `json:"content"`
        } `json:"processed"`
    } `json:"data"`
}

func main() {
    apiKey := "YOUR_API_KEY"
    templateId := "template-uuid"
    url := fmt.Sprintf("{{ $baseUrl }}/api/v1/templates/%s/preview", templateId)
    
    data := PreviewTemplateRequest{
        Variables: map[string]string{
            "name":     "John Doe",
            "order_id": "ORD12345",
        },
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
    
    var result PreviewTemplateResponse
    json.Unmarshal(body, &result)
    
    if result.Success {
        fmt.Printf("Processed: %s\n", result.Data.Processed.Content)
    }
}</code></div>
                </div>
            </div>
            
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
      "content": "Halo @{{ '{' . '{name}' . '}' }}, pesanan @{{ '{' . '{order_id}' . '}' }} telah dikonfirmasi.",
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
