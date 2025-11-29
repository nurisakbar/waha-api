<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Webhook;
use App\Models\WhatsAppSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class WebhookController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('receive');
    }

    public function index()
    {
        $webhooks = Auth::user()->webhooks()->with('session')->latest()->paginate(10);
        $sessions = Auth::user()->whatsappSessions()->where('status', 'connected')->get();
        return view('webhooks.index', compact('webhooks', 'sessions'));
    }

    public function create()
    {
        $sessions = Auth::user()->whatsappSessions()->where('status', 'connected')->get();
        return view('webhooks.create', compact('sessions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:500',
            'session_id' => 'nullable|exists:whatsapp_sessions,id',
            'events' => 'required|array',
            'events.*' => 'in:message,status,session',
        ]);

        Webhook::create([
            'user_id' => Auth::id(),
            'session_id' => $request->session_id,
            'name' => $request->name,
            'url' => $request->url,
            'events' => $request->events,
            'secret' => $request->secret ? bcrypt($request->secret) : null,
        ]);

        return redirect()->route('webhooks.index')->with('success', 'Webhook created successfully.');
    }

    public function show(Webhook $webhook)
    {
        if ($webhook->user_id !== Auth::id()) abort(403);
        return view('webhooks.show', compact('webhook'));
    }

    public function update(Request $request, Webhook $webhook)
    {
        if ($webhook->user_id !== Auth::id()) abort(403);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:500',
            'events' => 'required|array',
            'is_active' => 'boolean',
        ]);

        $webhook->update($request->only(['name', 'url', 'events', 'is_active']));
        return redirect()->route('webhooks.index')->with('success', 'Webhook updated successfully.');
    }

    public function destroy(Webhook $webhook)
    {
        if ($webhook->user_id !== Auth::id()) abort(403);
        $webhook->delete();
        return redirect()->route('webhooks.index')->with('success', 'Webhook deleted successfully.');
    }

    public function receive(Request $request, $sessionId)
    {
        Log::info('Webhook received', [
            'session_id' => $sessionId,
            'event' => $request->input('event'),
            'payload_keys' => array_keys($request->input('payload', [])),
        ]);

        $session = WhatsAppSession::where('session_id', $sessionId)->first();
        if (!$session) {
            Log::warning('Webhook: Session not found', ['session_id' => $sessionId]);
            return response()->json(['error' => 'Session not found'], 404);
        }

        $event = $request->input('event');
        $payload = $request->input('payload', []);

        // Handle message events - save incoming messages to database
        if ($event === 'message' && !empty($payload)) {
            $this->handleIncomingMessage($session, $payload);
        }

        // Handle message.ack events - update message status
        if ($event === 'message.ack' && !empty($payload)) {
            $this->handleMessageAck($session, $payload);
        }

        // Forward to user's webhooks
        $webhooks = Webhook::where('user_id', $session->user_id)
            ->where('is_active', true)
            ->where(function($q) use ($session) {
                $q->whereNull('session_id')->orWhere('session_id', $session->id);
            })
            ->where(function($q) use ($event) {
                $q->whereJsonContains('events', $event)
                  ->orWhereJsonContains('events', 'message'); // Also forward if webhook listens to all messages
            })
            ->get();

        foreach ($webhooks as $webhook) {
            try {
                Http::timeout(10)->post($webhook->url, $request->all());
                $webhook->update(['last_triggered_at' => now()]);
            } catch (\Exception $e) {
                Log::error('Webhook delivery failed: ' . $e->getMessage());
                $webhook->increment('failure_count');
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * Handle incoming message from WAHA webhook
     */
    protected function handleIncomingMessage(WhatsAppSession $session, array $payload)
    {
        try {
            // Skip if message is from me (outgoing messages are handled separately)
            if (!empty($payload['fromMe']) && $payload['fromMe'] === true) {
                return;
            }

            $whatsappMessageId = $payload['id'] ?? null;
            if (!$whatsappMessageId) {
                Log::warning('Webhook: Message ID not found in payload', ['payload' => $payload]);
                return;
            }

            // Check if message already exists
            $existingMessage = Message::where('whatsapp_message_id', $whatsappMessageId)
                ->where('session_id', $session->id)
                ->first();

            if ($existingMessage) {
                Log::info('Webhook: Message already exists', ['message_id' => $whatsappMessageId]);
                return;
            }

            // Extract phone numbers
            $from = $payload['from'] ?? null;
            $to = $payload['to'] ?? null;
            $participant = $payload['participant'] ?? null; // For group messages

            // Convert @s.whatsapp.net to @c.us if needed
            $fromNumber = $this->extractPhoneNumber($from);
            $toNumber = $this->extractPhoneNumber($to);

            // Determine message type
            $messageType = $this->determineMessageType($payload);
            
            // Extract content
            $content = $payload['body'] ?? null;
            $mediaUrl = null;
            $mediaMimeType = null;
            $mediaSize = null;
            $caption = null;

            if (!empty($payload['hasMedia']) && !empty($payload['media'])) {
                $media = $payload['media'];
                $mediaUrl = $media['url'] ?? null;
                $mediaMimeType = $media['mimetype'] ?? null;
                $mediaSize = $media['fileLength'] ?? $media['size'] ?? null;
                
                // Caption might be in body if hasMedia is true
                if (!empty($payload['body']) && $messageType !== 'text') {
                    $caption = $payload['body'];
                }
            }

            // Handle special message types
            if ($messageType === 'poll' && !empty($payload['poll'])) {
                $content = json_encode($payload['poll']);
            } elseif ($messageType === 'button' && !empty($payload['buttons'])) {
                $content = json_encode([
                    'body' => $payload['body'] ?? '',
                    'buttons' => $payload['buttons'] ?? [],
                ]);
            } elseif ($messageType === 'list' && !empty($payload['list'])) {
                $content = json_encode($payload['list']);
            }

            // Parse timestamp
            $timestamp = $payload['timestamp'] ?? time();
            if (is_float($timestamp)) {
                $createdAt = Carbon::createFromTimestamp($timestamp);
            } else {
                $createdAt = Carbon::parse($timestamp);
            }

            // Create message record
            Message::create([
                'user_id' => $session->user_id,
                'session_id' => $session->id,
                'whatsapp_message_id' => $whatsappMessageId,
                'from_number' => $fromNumber,
                'to_number' => $toNumber,
                'message_type' => $messageType,
                'content' => $content,
                'media_url' => $mediaUrl,
                'media_mime_type' => $mediaMimeType,
                'media_size' => $mediaSize,
                'caption' => $caption,
                'direction' => 'incoming',
                'status' => 'delivered', // Incoming messages are considered delivered
                'sent_at' => $createdAt,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            Log::info('Webhook: Incoming message saved', [
                'whatsapp_message_id' => $whatsappMessageId,
                'from' => $fromNumber,
                'type' => $messageType,
            ]);
        } catch (\Exception $e) {
            Log::error('Webhook: Error handling incoming message', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => $payload,
            ]);
        }
    }

    /**
     * Handle message acknowledgment (ack) events
     */
    protected function handleMessageAck(WhatsAppSession $session, array $payload)
    {
        try {
            $whatsappMessageId = $payload['id'] ?? null;
            if (!$whatsappMessageId) {
                return;
            }

            $message = Message::where('whatsapp_message_id', $whatsappMessageId)
                ->where('session_id', $session->id)
                ->where('direction', 'outgoing')
                ->first();

            if (!$message) {
                return;
            }

            $ack = $payload['ack'] ?? null;
            $status = $message->status;

            // Update status based on ack value
            // ack: 0 = pending, 1 = delivered, 2 = read, 3 = played
            if ($ack === 1 && $status !== 'read') {
                $status = 'delivered';
                $message->update([
                    'status' => 'delivered',
                    'delivered_at' => now(),
                ]);
            } elseif ($ack === 2) {
                $status = 'read';
                $message->update([
                    'status' => 'read',
                    'read_at' => now(),
                ]);
            }

            Log::info('Webhook: Message ack updated', [
                'whatsapp_message_id' => $whatsappMessageId,
                'ack' => $ack,
                'status' => $status,
            ]);
        } catch (\Exception $e) {
            Log::error('Webhook: Error handling message ack', [
                'error' => $e->getMessage(),
                'payload' => $payload,
            ]);
        }
    }

    /**
     * Extract phone number from WAHA format
     */
    protected function extractPhoneNumber(?string $chatId): ?string
    {
        if (!$chatId) {
            return null;
        }

        // Remove @c.us, @s.whatsapp.net, @g.us, @lid, @newsletter
        $number = preg_replace('/@.*$/', '', $chatId);
        
        // Remove + if present
        $number = ltrim($number, '+');
        
        return $number ?: null;
    }

    /**
     * Determine message type from WAHA payload
     */
    protected function determineMessageType(array $payload): string
    {
        // Check for specific message types
        if (!empty($payload['poll'])) {
            return 'poll';
        }
        
        if (!empty($payload['buttons']) || !empty($payload['interactiveMessage'])) {
            return 'button';
        }
        
        if (!empty($payload['list'])) {
            return 'list';
        }

        if (!empty($payload['location'])) {
            return 'location';
        }

        if (!empty($payload['contact'])) {
            return 'contact';
        }

        if (!empty($payload['sticker'])) {
            return 'sticker';
        }

        // Check media type
        if (!empty($payload['hasMedia']) && !empty($payload['media'])) {
            $mimetype = $payload['media']['mimetype'] ?? '';
            
            if (strpos($mimetype, 'image/') === 0) {
                return 'image';
            }
            
            if (strpos($mimetype, 'video/') === 0) {
                return 'video';
            }
            
            if (strpos($mimetype, 'audio/') === 0 || strpos($mimetype, 'voice') !== false) {
                return 'voice';
            }
            
            // Default to document for other media types
            return 'document';
        }

        // Default to text
        return 'text';
    }
}
