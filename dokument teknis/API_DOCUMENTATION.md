# 📚 API Documentation - WAHA Gateway

**Base URL:** `http://localhost:8000/api/v1`  
**Authentication:** API Key (required for all endpoints)

---

## 🔑 Authentication

All API requests require an API key. Include your API key in the header:

```http
X-Api-Key: waha_your_api_key_here
```

### Getting Your API Key
1. Login to your dashboard: `http://localhost:8000`
2. Go to **API Keys** section
3. Click **Create API Key**
4. Copy and save your API key (it won't be shown again!)

---

## 📡 Endpoints

### Health Check
```http
GET /api/health
```

**Response:**
```json
{
  "status": "ok",
  "timestamp": "2025-11-28T12:00:00Z"
}
```

---

### Sessions

#### List All Sessions
```http
GET /api/v1/sessions
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": "session_123",
      "name": "My Session",
      "status": "connected",
      "created_at": "2025-11-28T12:00:00Z"
    }
  ]
}
```

#### Get Session Details
```http
GET /api/v1/sessions/{session_id}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": "session_123",
    "name": "My Session",
    "status": "connected",
    "created_at": "2025-11-28T12:00:00Z",
    "last_activity_at": "2025-11-28T13:00:00Z"
  }
}
```

#### Get Session Status
```http
GET /api/v1/sessions/{session_id}/status
```

**Response:**
```json
{
  "success": true,
  "data": {
    "status": "connected",
    "is_connected": true
  }
}
```

---

### Messages

#### Send Text Message
```http
POST /api/v1/sessions/{session_id}/messages/text
```

**Request Body:**
```json
{
  "to": "081234567890",
  "message": "Hello, this is a test message!"
}
```

**Note:** Phone number can be in format `08xxxxxxxxxx` or `628xxxxxxxxxx`. It will be automatically normalized to `62xxxxxxxxxx`.

**Response:**
```json
{
  "success": true,
  "data": {
    "message_id": "uuid-here",
    "whatsapp_message_id": "3EB0A93DDA1E92B08B1741",
    "status": "sent",
    "to": "6281234567890"
  }
}
```

#### Send Image Message
```http
POST /api/v1/sessions/{session_id}/messages/image
```

**Request Body:**
```json
{
  "to": "081234567890",
  "image": "https://example.com/image.jpg",
  "caption": "Optional caption"
}
```

**Note:** The API uses WAHA's JSON format internally. The image URL is automatically converted to the following format:
```json
{
  "session": "session_id",
  "chatId": "6281234567890@c.us",
  "file": {
    "mimetype": "image/jpeg",
    "url": "https://example.com/image.jpg",
    "filename": "image.jpg"
  },
  "caption": "Optional caption"
}
```

**Supported Image Formats:**
- JPEG (`image/jpeg`)
- PNG (`image/png`)
- GIF (`image/gif`)
- WebP (`image/webp`)

**Response:**
```json
{
  "success": true,
  "data": {
    "message_id": "uuid-here",
    "whatsapp_message_id": "3EB0A93DDA1E92B08B1741",
    "status": "sent",
    "to": "6281234567890"
  }
}
```

#### Send Video Message
```http
POST /api/v1/sessions/{session_id}/messages/video
```

**Request Body:**
```json
{
  "to": "081234567890",
  "video": "https://example.com/video.mp4",
  "caption": "Watch this video!",
  "as_note": false,
  "convert": false
}
```

**Parameters:**
- `video` (required): URL of the video file
- `caption` (optional): Caption text for the video
- `as_note` (optional, default: `false`): Send as video note (rounded video)
- `convert` (optional, default: `false`): Convert video format if needed

**Note:** The API uses WAHA's JSON format internally:
```json
{
  "session": "session_id",
  "chatId": "6281234567890@c.us",
  "file": {
    "mimetype": "video/mp4",
    "url": "https://example.com/video.mp4",
    "filename": "video.mp4"
  },
  "caption": "Watch this video!",
  "asNote": false,
  "convert": false
}
```

**Supported Video Formats:**
- MP4 (`video/mp4`)
- WebM (`video/webm`)
- OGG (`video/ogg`)
- QuickTime (`video/quicktime`)
- AVI (`video/x-msvideo`)

**Response:**
```json
{
  "success": true,
  "data": {
    "message_id": "uuid-here",
    "whatsapp_message_id": "3EB0A93DDA1E92B08B1741",
    "status": "sent",
    "to": "6281234567890"
  }
}
```

#### Send Document Message
```http
POST /api/v1/sessions/{session_id}/messages/document
```

**Request Body:**
```json
{
  "to": "081234567890",
  "document": "https://example.com/document.pdf",
  "filename": "document.pdf",
  "caption": "Optional caption"
}
```

**Note:** The API uses WAHA's JSON format internally. The document URL is automatically converted to the following format:
```json
{
  "session": "session_id",
  "chatId": "6281234567890@c.us",
  "file": {
    "mimetype": "application/pdf",
    "url": "https://example.com/document.pdf",
    "filename": "document.pdf"
  },
  "caption": "Optional caption"
}
```

**Supported Document Formats:**
- PDF (`application/pdf`)
- Word Documents (`application/msword`, `application/vnd.openxmlformats-officedocument.wordprocessingml.document`)
- Excel Spreadsheets (`application/vnd.ms-excel`, `application/vnd.openxmlformats-officedocument.spreadsheetml.sheet`)
- PowerPoint Presentations (`application/vnd.ms-powerpoint`, `application/vnd.openxmlformats-officedocument.presentationml.presentation`)
- ZIP Archives (`application/zip`)
- Text Files (`text/plain`)
- CSV Files (`text/csv`)
- JSON Files (`application/json`)

**Response:**
```json
{
  "success": true,
  "data": {
    "message_id": "uuid-here",
    "whatsapp_message_id": "3EB0A93DDA1E92B08B1741",
    "status": "sent",
    "to": "6281234567890"
  }
}
```

#### Get Messages
```http
GET /api/v1/sessions/{session_id}/messages?per_page=20&page=1
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": "uuid-here",
      "to_number": "6281234567890",
      "message_type": "text",
      "content": "Hello!",
      "status": "sent",
      "direction": "outgoing",
      "created_at": "2025-11-28T12:00:00Z"
    }
  ],
  "pagination": {
    "current_page": 1,
    "per_page": 20,
    "total": 100,
    "last_page": 5
  }
}
```

#### Get Message Details
```http
GET /api/v1/messages/{message_id}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": "uuid-here",
    "to_number": "6281234567890",
    "message_type": "text",
    "content": "Hello!",
    "status": "sent",
    "direction": "outgoing",
    "created_at": "2025-11-28T12:00:00Z"
  }
}
```

---

### Account

#### Get Account Information
```http
GET /api/v1/account
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": "user-uuid",
    "name": "John Doe",
    "email": "john@example.com",
    "subscription": {
      "plan": "Pro",
      "status": "active"
    }
  }
}
```

#### Get Usage Statistics
```http
GET /api/v1/account/usage?start_date=2025-11-01&end_date=2025-11-30
```

**Response:**
```json
{
  "success": true,
  "data": {
    "total_requests": 1500,
    "successful_requests": 1450,
    "failed_requests": 50,
    "average_response_time": 245.5,
    "requests_by_endpoint": [
      {
        "endpoint": "/api/v1/sessions/{session}/messages/text",
        "count": 800
      }
    ],
    "requests_by_status": [
      {
        "status_code": 200,
        "count": 1450
      }
    ]
  }
}
```

---

## 📊 API Usage Tracking

Every API request is automatically logged with:
- Endpoint
- Method
- Status code
- Response time
- IP address
- Timestamp

You can view your usage statistics via the `/api/v1/account/usage` endpoint.

---

## ⚠️ Error Responses

All errors follow this format:

```json
{
  "success": false,
  "error": "Error message",
  "message": "Detailed error description"
}
```

### Common Error Codes

- `400` - Bad Request (validation errors)
- `401` - Unauthorized (invalid or missing API key)
- `404` - Not Found (resource doesn't exist)
- `500` - Internal Server Error

### Example Error Response
```json
{
  "success": false,
  "error": "Validation failed",
  "errors": {
    "to": ["The to field is required."],
    "message": ["The message field is required."]
  }
}
```

---

## 📝 Code Examples

### cURL

#### Send Text Message
```bash
curl -X POST http://localhost:8000/api/v1/sessions/session_123/messages/text \
  -H "X-Api-Key: waha_your_api_key_here" \
  -H "Content-Type: application/json" \
  -d '{
    "to": "081234567890",
    "message": "Hello from API!"
  }'
```

#### Send Image Message
```bash
curl -X POST http://localhost:8000/api/v1/sessions/session_123/messages/image \
  -H "X-Api-Key: waha_your_api_key_here" \
  -H "Content-Type: application/json" \
  -d '{
    "to": "081234567890",
    "image": "https://example.com/image.jpg",
    "caption": "Check this out!"
  }'
```

#### Send Video Message
```bash
curl -X POST http://localhost:8000/api/v1/sessions/session_123/messages/video \
  -H "X-Api-Key: waha_your_api_key_here" \
  -H "Content-Type: application/json" \
  -d '{
    "to": "081234567890",
    "video": "https://example.com/video.mp4",
    "caption": "Watch this video!",
    "as_note": false,
    "convert": false
  }'
```

#### Send Document Message
```bash
curl -X POST http://localhost:8000/api/v1/sessions/session_123/messages/document \
  -H "X-Api-Key: waha_your_api_key_here" \
  -H "Content-Type: application/json" \
  -d '{
    "to": "081234567890",
    "document": "https://example.com/document.pdf",
    "filename": "document.pdf",
    "caption": "Check this out!"
  }'
```

### PHP
```php
$response = Http::withHeaders([
    'X-Api-Key' => 'waha_your_api_key_here',
])->post('http://localhost:8000/api/v1/sessions/session_123/messages/text', [
    'to' => '081234567890',
    'message' => 'Hello from API!',
]);

$data = $response->json();
```

### JavaScript (Fetch)
```javascript
fetch('http://localhost:8000/api/v1/sessions/session_123/messages/text', {
  method: 'POST',
  headers: {
    'X-Api-Key': 'waha_your_api_key_here',
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    to: '081234567890',
    message: 'Hello from API!',
  }),
})
  .then(response => response.json())
  .then(data => console.log(data));
```

### Python (Requests)
```python
import requests

response = requests.post(
    'http://localhost:8000/api/v1/sessions/session_123/messages/text',
    headers={
        'X-Api-Key': 'waha_your_api_key_here',
        'Content-Type': 'application/json',
    },
    json={
        'to': '081234567890',
        'message': 'Hello from API!',
    }
)

data = response.json()
```

---

## 🔒 Rate Limiting

Rate limits are based on your subscription plan:

- **Free:** 10 requests/minute
- **Basic:** 50 requests/minute
- **Pro:** 200 requests/minute
- **Enterprise:** 1,000 requests/minute

Rate limit headers are included in responses:
- `X-RateLimit-Limit` - Maximum requests per minute
- `X-RateLimit-Remaining` - Remaining requests in current window

---

## 📞 Support

For API support, please contact: [Support Email]

---

**Last Updated:** 2025-11-28


