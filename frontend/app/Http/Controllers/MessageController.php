<?php

namespace App\Http\Controllers;

use App\Helpers\PhoneNumberHelper;
use App\Models\Message;
use App\Models\WhatsAppSession;
use App\Services\WahaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class MessageController extends Controller
{
    protected WahaService $wahaService;

    public function __construct(WahaService $wahaService)
    {
        $this->middleware('auth');
        $this->wahaService = $wahaService;
    }

    /**
     * Display a listing of messages.
     */
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            \Log::info('DataTables request received', [
                'is_ajax' => $request->ajax(),
                'wants_json' => $request->wantsJson(),
                'headers' => $request->headers->all(),
            ]);
            return $this->datatables($request);
        }

        $sessions = Auth::user()->whatsappSessions()->where('status', 'connected')->get();
        return view('messages.index', compact('sessions'));
    }

    /**
     * DataTables server-side processing.
     */
    public function datatables(Request $request)
    {
        try {
            $query = Auth::user()->messages()->with('session');

            // Filter by session
            if ($request->has('session_id') && $request->session_id) {
                $query->where('session_id', $request->session_id);
            }

            // Filter by direction
            if ($request->has('direction') && $request->direction) {
                $query->where('direction', $request->direction);
            }

            // Custom search filter (from filter form - separate from DataTables search)
            // DataTables sends search as object with 'value' property, but our custom filter sends as string
            if ($request->has('search') && is_string($request->search) && !empty($request->search)) {
                $searchValue = $request->search;
                $query->where(function($q) use ($searchValue) {
                    $q->where('content', 'like', '%' . $searchValue . '%')
                      ->orWhere('from_number', 'like', '%' . $searchValue . '%')
                      ->orWhere('to_number', 'like', '%' . $searchValue . '%');
                });
            }

            return DataTables::of($query)
            ->addColumn('type_badge', function ($message) {
                // Different colors for different message types
                $typeColors = [
                    'text' => 'bg-primary',
                    'image' => 'bg-info',
                    'video' => 'bg-danger',
                    'document' => 'bg-secondary',
                    'poll' => 'bg-warning',
                    'button' => 'bg-success',
                    'list' => 'bg-purple',
                ];
                
                $typeColor = $typeColors[$message->message_type] ?? 'bg-dark';
                $badges = '<span class="badge ' . $typeColor . ' text-white" style="font-weight: 600;">' . ucfirst($message->message_type) . '</span> ';
                
                if ($message->isIncoming()) {
                    $badges .= '<span class="badge bg-primary text-white" style="font-weight: 600;">Incoming</span>';
                } else {
                    $badges .= '<span class="badge bg-success text-white" style="font-weight: 600;">Outgoing</span>';
                }
                return $badges;
            })
            ->addColumn('from_to', function ($message) {
                if ($message->isIncoming()) {
                    return '<strong>From:</strong> ' . ($message->from_number ?? 'N/A');
                } else {
                    return '<strong>To:</strong> ' . ($message->to_number ?? 'N/A');
                }
            })
            ->addColumn('content_preview', function ($message) {
                if ($message->message_type === 'text') {
                    return \Str::limit($message->content, 50);
                } elseif ($message->message_type === 'image') {
                    $html = '<i class="fas fa-image"></i> Image';
                    if ($message->caption) {
                        $html .= '<br><small>' . \Str::limit($message->caption, 30) . '</small>';
                    }
                    return $html;
                } elseif ($message->message_type === 'video') {
                    $html = '<i class="fas fa-video"></i> Video';
                    if ($message->caption) {
                        $html .= '<br><small>' . \Str::limit($message->caption, 30) . '</small>';
                    }
                    return $html;
                } elseif ($message->message_type === 'document') {
                    return '<i class="fas fa-file"></i> Document';
                } elseif ($message->message_type === 'poll') {
                    return '<i class="fas fa-poll"></i> Poll';
                } elseif ($message->message_type === 'button') {
                    return '<i class="fas fa-mouse-pointer"></i> Button';
                } else {
                    return '<i class="fas fa-file"></i> ' . ucfirst($message->message_type);
                }
            })
            ->addColumn('status_badge', function ($message) {
                $status = $message->status;
                $badgeClass = 'bg-secondary';
                $textColor = 'text-white';
                $label = ucfirst($status);
                
                switch ($status) {
                    case 'sent':
                        $badgeClass = 'bg-success';
                        $textColor = 'text-white';
                        $label = 'Sent';
                        break;
                    case 'delivered':
                        $badgeClass = 'bg-info';
                        $textColor = 'text-white';
                        $label = 'Delivered';
                        break;
                    case 'read':
                        $badgeClass = 'bg-primary';
                        $textColor = 'text-white';
                        $label = 'Read';
                        break;
                    case 'failed':
                        $badgeClass = 'bg-danger';
                        $textColor = 'text-white';
                        $label = 'Failed';
                        break;
                    case 'pending':
                        $badgeClass = 'bg-warning';
                        $textColor = 'text-dark';
                        $label = 'Pending';
                        break;
                }
                
                return '<span class="badge ' . $badgeClass . ' ' . $textColor . '" style="font-weight: 600;">' . $label . '</span>';
            })
            ->addColumn('session_name', function ($message) {
                return $message->session->session_name ?? 'N/A';
            })
            ->addColumn('formatted_date', function ($message) {
                return $message->created_at->format('Y-m-d H:i');
            })
            ->addColumn('actions', function ($message) {
                return '<a href="' . route('messages.show', $message) . '" class="btn btn-sm btn-info">
                    <i class="fas fa-eye"></i>
                </a>';
            })
            ->rawColumns(['type_badge', 'from_to', 'content_preview', 'status_badge', 'actions'])
            ->make(true);
        } catch (\Exception $e) {
            \Log::error('DataTables error: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'draw' => $request->input('draw', 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Error loading data: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for sending a new message.
     */
    public function create(Request $request)
    {
        $sessionId = $request->get('session_id');
        $toNumber = $request->get('to_number');
        $sessions = Auth::user()->whatsappSessions()->where('status', 'connected')->get();

        if ($sessions->isEmpty()) {
            return redirect()->route('sessions.index')
                ->with('error', 'You need at least one connected session to send messages.');
        }

        $selectedSession = $sessionId ? $sessions->find($sessionId) : $sessions->first();

        return view('messages.create', compact('sessions', 'selectedSession', 'toNumber'));
    }

    /**
     * Store a newly sent message.
     */
    public function store(Request $request)
    {
        $rules = [
            'session_id'    => 'required|exists:whatsapp_sessions,id',
            'to_number'     => 'required|string|max:255',
            'chat_type'     => 'nullable|in:personal,group',
            'message_type'  => 'required|in:text,image,document',
            'content'       => 'nullable|string|max:4096|required_if:message_type,text',
            'media'         => 'required_if:message_type,image|file|max:10240',
            'caption'       => 'nullable|string|max:1024',
        ];

        // For document type, require either file or URL (but not both empty)
        if ($request->message_type === 'document') {
            $rules['document_file'] = 'required_without:document_url|file|max:10240';
            $rules['document_url'] = 'required_without:document_file|nullable|url|max:2048';
        }

        $request->validate($rules);

        // Determine chat type (personal or group)
        $chatType = $request->input('chat_type', 'personal'); // Default to personal
        
        // Handle chat ID based on type
        if ($chatType === 'group') {
            // For group, the 'to_number' field should be the group ID
            $toValue = $request->to_number;
            
            // If already contains @g.us, use as is
            if (strpos($toValue, '@g.us') !== false) {
                $chatId = $toValue;
            } else {
                // Otherwise, append @g.us
                $chatId = $toValue . '@g.us';
            }
            
            // For groups, we don't normalize as phone number
            $normalizedNumber = $toValue;
        } else {
            // For personal chat, normalize phone number
            $normalizedNumber = PhoneNumberHelper::normalize($request->to_number);
            if (!$normalizedNumber) {
                return back()->withErrors(['to_number' => 'Invalid phone number format. Please use 08xxxxxxxxxx or 628xxxxxxxxxx']);
            }
            
            $chatId = $normalizedNumber . '@c.us';
        }

        $session = WhatsAppSession::where('user_id', Auth::id())
            ->where('id', $request->session_id)
            ->where('status', 'connected')
            ->firstOrFail();
        $result = null;
        
        // Try to get phone number from device_info or session data
        $fromNumber = null;
        if ($session->device_info && isset($session->device_info['phone'])) {
            $fromNumber = $session->device_info['phone'];
        } elseif ($session->device_info && isset($session->device_info['wid'])) {
            // Extract phone from wid (format: 6281234567890@c.us)
            $wid = $session->device_info['wid'];
            if (is_string($wid) && strpos($wid, '@') !== false) {
                $fromNumber = explode('@', $wid)[0];
            }
        }
        
        $messageData = [
            'user_id' => Auth::id(),
            'session_id' => $session->id,
            'from_number' => $fromNumber, // Phone number from device_info, or null if not available
            'to_number' => $normalizedNumber, // Normalized phone number (62 format) or group ID
            'chat_type' => $chatType, // personal or group
            'message_type' => $request->message_type,
            'direction' => 'outgoing',
            'status' => 'pending',
        ];

        try {
            switch ($request->message_type) {
                case 'text':
                    // Log the request details for debugging
                    \Log::info('Browser: Sending text message', [
                        'session_id' => $session->session_id,
                        'chat_id' => $chatId,
                        'text_length' => strlen($request->content),
                        'user_id' => Auth::id(),
                    ]);
                    
                    $result = $this->wahaService->sendText(
                        $session->session_id,
                        $chatId,
                        $request->content
                    );
                    
                    // Log the result (same format as API for easy comparison)
                    \Log::info('Browser: WAHA sendText response', [
                        'success' => $result['success'] ?? false,
                        'error' => $result['error'] ?? null,
                        'data' => $result['data'] ?? null,
                        'session_id' => $session->session_id,
                        'chat_id' => $chatId,
                    ]);
                    
                    $messageData['content'] = $request->content;
                    break;

                case 'image':
                    \Log::info('Browser: Sending image message', [
                        'session_id' => $session->session_id,
                        'chat_id' => $chatId,
                        'user_id' => Auth::id(),
                    ]);
                    
                    $file = $request->file('media');
                    $path = $file->store('messages/media', 'public');
                    $result = $this->wahaService->sendImage(
                        $session->session_id,
                        $chatId,
                        storage_path('app/public/' . $path),
                        $request->caption
                    );
                    
                    \Log::info('Browser: WAHA sendImage response', [
                        'success' => $result['success'] ?? false,
                        'error' => $result['error'] ?? null,
                    ]);
                    
                    $messageData['media_url'] = Storage::url($path);
                    $messageData['media_mime_type'] = $file->getMimeType();
                    $messageData['media_size'] = $file->getSize();
                    $messageData['caption'] = $request->caption;
                    break;

                case 'document':
                    \Log::info('Browser: Sending document message', [
                        'session_id' => $session->session_id,
                        'chat_id' => $chatId,
                        'user_id' => Auth::id(),
                        'has_file' => $request->hasFile('document_file'),
                        'has_url' => !empty($request->document_url),
                    ]);
                    
                    // Support both file upload and URL
                    if ($request->hasFile('document_file')) {
                        $file = $request->file('document_file');
                        $path = $file->store('messages/documents', 'public');
                        $result = $this->wahaService->sendDocument(
                            $session->session_id,
                            $chatId,
                            storage_path('app/public/' . $path),
                            $file->getClientOriginalName()
                        );
                        $messageData['media_url'] = Storage::url($path);
                        $messageData['media_mime_type'] = $file->getMimeType();
                        $messageData['media_size'] = $file->getSize();
                    } elseif ($request->document_url) {
                        $documentUrl = $request->document_url;
                        $result = $this->wahaService->sendDocumentByUrl(
                            $session->session_id,
                            $chatId,
                            $documentUrl
                        );
                        $messageData['media_url'] = $documentUrl;
                    } else {
                        return back()->withErrors(['error' => 'Please provide either a document file or document URL.']);
                    }
                    
                    \Log::info('Browser: WAHA sendDocument response', [
                        'success' => $result['success'] ?? false,
                        'error' => $result['error'] ?? null,
                    ]);
                    
                    $messageData['caption'] = $request->caption;
                    break;
            }

            if ($result && $result['success']) {
                $whatsappId = $result['data']['id'] ?? null;
                if (is_array($whatsappId)) {
                    $whatsappId = json_encode($whatsappId);
                }
                
                // Check ack status from WAHA response
                $ack = $result['data']['ack'] ?? $result['data']['_data']['ack'] ?? null;
                $status = 'sent';
                
                // ack: 0 = pending, 1 = delivered, 2 = read, 3 = played
                // If ack is 0, message might not be actually sent yet
                if ($ack === 0) {
                    $status = 'pending';
                    \Log::warning('Browser: Message sent but ack=0 (pending delivery)', [
                        'session_id' => $session->session_id,
                        'chat_id' => $chatId,
                        'whatsapp_message_id' => $whatsappId,
                    ]);
                }
                
                $messageData['whatsapp_message_id'] = $whatsappId;
                $messageData['status'] = $status;
                $messageData['sent_at'] = now();

                $message = Message::create($messageData);

                \Log::info('Browser: Message sent successfully', [
                    'message_id' => $message->id,
                    'whatsapp_message_id' => $whatsappId,
                    'status' => $status,
                    'ack' => $ack,
                    'session_id' => $session->session_id,
                    'chat_id' => $chatId,
                    'user_id' => Auth::id(),
                ]);

                return redirect()->route('messages.index')
                    ->with('success', 'Message sent successfully!');
            } else {
                // Log error details
                $errorMessage = $result['error'] ?? 'Failed to send message';
                \Log::error('Browser: Failed to send message', [
                    'error' => $errorMessage,
                    'result' => $result,
                    'session_id' => $session->session_id,
                    'chat_id' => $chatId,
                ]);
                
                $messageData['status'] = 'failed';
                $error = $result['error'] ?? 'Unknown error';
                if (is_array($error)) {
                    $error = json_encode($error);
                }
                $messageData['error_message'] = $error;
                Message::create($messageData);

                return back()->withErrors(['error' => $result['error'] ?? 'Failed to send message']);
            }
        } catch (\Exception $e) {
            $messageData['status'] = 'failed';
            $errorMessage = $e->getMessage();
            // Guard against very long / complex error payloads
            if (is_array($errorMessage)) {
                $errorMessage = json_encode($errorMessage);
            }
            $messageData['error_message'] = $errorMessage;
            Message::create($messageData);

            return back()->withErrors(['error' => 'Error: ' . $errorMessage]);
        }
    }

    /**
     * Display the specified message.
     */
    public function show(Message $message)
    {
        if ($message->user_id !== Auth::id()) {
            abort(403);
        }

        return view('messages.show', compact('message'));
    }
}
