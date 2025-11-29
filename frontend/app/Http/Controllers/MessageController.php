<?php

namespace App\Http\Controllers;

use App\Helpers\PhoneNumberHelper;
use App\Jobs\SendMessage as SendMessageJob;
use App\Models\Message;
use App\Models\WhatsAppSession;
use App\Services\WahaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
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
            'from_number' => $fromNumber,
            'to_number' => $normalizedNumber,
            'chat_type' => $chatType,
            'message_type' => $request->message_type,
            'direction' => 'outgoing',
            'status' => 'pending',
        ];

        try {
            $mediaPath = null;
            $documentPath = null;
            $documentUrl = null;

            switch ($request->message_type) {
                case 'text':
                    $messageData['content'] = $request->content;
                    break;

                case 'image':
                    $file = $request->file('media');
                    $path = $file->store('messages/media', 'public');
                    $messageData['media_url'] = Storage::url($path);
                    $messageData['media_mime_type'] = $file->getMimeType();
                    $messageData['media_size'] = $file->getSize();
                    $messageData['caption'] = $request->caption;
                    $mediaPath = $path;
                    break;

                case 'document':
                    if ($request->hasFile('document_file')) {
                        $file = $request->file('document_file');
                        $path = $file->store('messages/documents', 'public');
                        $messageData['media_url'] = Storage::url($path);
                        $messageData['media_mime_type'] = $file->getMimeType();
                        $messageData['media_size'] = $file->getSize();
                        $documentPath = $path;
                    } elseif ($request->document_url) {
                        $messageData['media_url'] = $request->document_url;
                        $documentUrl = $request->document_url;
                    } else {
                        return back()->withErrors(['error' => 'Please provide either a document file or document URL.']);
                    }
                    $messageData['caption'] = $request->caption;
                    break;
            }

            // Create message record first
            $message = Message::create($messageData);

            // Dispatch job to send message asynchronously
            SendMessageJob::dispatch(
                $message->id,
                $session->id,
                $chatId,
                $request->message_type,
                $request->content,
                $mediaPath,
                $documentPath,
                $documentUrl,
                $request->caption,
                $chatType
            );

            \Log::info('Browser: Message job dispatched', [
                'message_id' => $message->id,
                'session_id' => $session->session_id,
                'chat_id' => $chatId,
                'user_id' => Auth::id(),
            ]);

            return redirect()->route('messages.index')
                ->with('success', 'Message queued for sending!');
        } catch (\Exception $e) {
            $messageData['status'] = 'failed';
            $errorMessage = $e->getMessage();
            if (is_array($errorMessage)) {
                $errorMessage = json_encode($errorMessage);
            }
            $messageData['error_message'] = $errorMessage;
            Message::create($messageData);

            return back()->withErrors(['error' => 'Error: ' . $errorMessage]);
        }
    }

    /**
     * Store bulk messages from Excel file.
     */
    public function storeBulk(Request $request)
    {
        $rules = [
            'session_id' => 'required|exists:whatsapp_sessions,id',
            'excel_file' => 'required|file|mimes:xlsx,xls|max:10240',
            'delay_enabled' => 'nullable|boolean',
            'delay_seconds' => 'nullable|integer|min:1|max:60',
        ];

        $request->validate($rules);

        $session = WhatsAppSession::where('user_id', Auth::id())
            ->where('id', $request->session_id)
            ->where('status', 'connected')
            ->firstOrFail();

        $delayEnabled = $request->has('delay_enabled') && $request->delay_enabled;
        $delaySeconds = $delayEnabled ? (int)($request->delay_seconds ?? 2) : 0;

        try {
            $file = $request->file('excel_file');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            if (count($rows) < 2) {
                return back()->withErrors(['excel_file' => 'Excel file must have at least one data row (excluding header).']);
            }

            // Get header row (first row)
            $headerRow = array_shift($rows);
            $headerRow = array_map('strtolower', array_map('trim', $headerRow));

            // Find column indices
            $phoneColumnIndex = null;
            $messageColumnIndex = null;

            foreach ($headerRow as $index => $header) {
                if (in_array($header, ['nomor', 'phone', 'phone_number', 'number', 'no', 'telepon'])) {
                    $phoneColumnIndex = $index;
                }
                if (in_array($header, ['pesan', 'message', 'text', 'content', 'isi'])) {
                    $messageColumnIndex = $index;
                }
            }

            if ($phoneColumnIndex === null || $messageColumnIndex === null) {
                return back()->withErrors([
                    'excel_file' => 'Excel file must have columns: "nomor" (or phone/number) and "pesan" (or message/text/content).'
                ]);
            }

            $results = [
                'total' => 0,
                'success' => 0,
                'failed' => 0,
                'errors' => []
            ];

            // Try to get phone number from device_info
            $fromNumber = null;
            if ($session->device_info && isset($session->device_info['phone'])) {
                $fromNumber = $session->device_info['phone'];
            } elseif ($session->device_info && isset($session->device_info['wid'])) {
                $wid = $session->device_info['wid'];
                if (is_string($wid) && strpos($wid, '@') !== false) {
                    $fromNumber = explode('@', $wid)[0];
                }
            }

            // Process each row
            foreach ($rows as $rowIndex => $row) {
                $results['total']++;

                // Skip empty rows
                if (empty($row[$phoneColumnIndex]) || empty($row[$messageColumnIndex])) {
                    $results['failed']++;
                    $results['errors'][] = "Row " . ($rowIndex + 2) . ": Missing phone number or message";
                    continue;
                }

                $phoneNumber = trim($row[$phoneColumnIndex]);
                $messageContent = trim($row[$messageColumnIndex]);

                // Normalize phone number
                $normalizedNumber = PhoneNumberHelper::normalize($phoneNumber);
                if (!$normalizedNumber) {
                    $results['failed']++;
                    $results['errors'][] = "Row " . ($rowIndex + 2) . ": Invalid phone number format: " . $phoneNumber;
                    continue;
                }

                $chatId = $normalizedNumber . '@c.us';

                // Prepare message data
                $messageData = [
                    'user_id' => Auth::id(),
                    'session_id' => $session->id,
                    'from_number' => $fromNumber,
                    'to_number' => $normalizedNumber,
                    'chat_type' => 'personal',
                    'message_type' => 'text',
                    'content' => $messageContent,
                    'direction' => 'outgoing',
                    'status' => 'pending',
                ];

                try {
                    // Create message record
                    $message = Message::create($messageData);

                    // Calculate delay for this message (if enabled)
                    $delay = 0;
                    if ($delayEnabled && $delaySeconds > 0) {
                        $delay = $rowIndex * $delaySeconds; // Cumulative delay
                    }

                    // Dispatch job with delay
                    if ($delay > 0) {
                        SendMessageJob::dispatch(
                            $message->id,
                            $session->id,
                            $chatId,
                            'text',
                            $messageContent,
                            null,
                            null,
                            null,
                            null,
                            'personal'
                        )->delay(now()->addSeconds($delay));
                    } else {
                        SendMessageJob::dispatch(
                            $message->id,
                            $session->id,
                            $chatId,
                            'text',
                            $messageContent,
                            null,
                            null,
                            null,
                            null,
                            'personal'
                        );
                    }

                    $results['success']++;

                    \Log::info('Bulk: Message job dispatched', [
                        'row' => $rowIndex + 2,
                        'phone' => $normalizedNumber,
                        'message_id' => $message->id,
                        'delay' => $delay,
                    ]);
                } catch (\Exception $e) {
                    $messageData['status'] = 'failed';
                    $errorMessage = $e->getMessage();
                    if (is_array($errorMessage)) {
                        $errorMessage = json_encode($errorMessage);
                    }
                    $messageData['error_message'] = $errorMessage;
                    Message::create($messageData);
                    $results['failed']++;
                    $results['errors'][] = "Row " . ($rowIndex + 2) . ": " . $errorMessage;

                    \Log::error('Bulk: Error creating message', [
                        'row' => $rowIndex + 2,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // Prepare success message
            $queuedCount = $results['success'];
            $successMessage = "Bulk messaging queued! Total: {$results['total']}, Queued: {$queuedCount}, Failed: {$results['failed']}. Messages will be sent in the background.";
            
            if ($results['failed'] > 0 && count($results['errors']) > 0) {
                $errorDetails = implode("\n", array_slice($results['errors'], 0, 10));
                if (count($results['errors']) > 10) {
                    $errorDetails .= "\n... and " . (count($results['errors']) - 10) . " more errors";
                }
                return redirect()->route('messages.index')
                    ->with('success', $successMessage)
                    ->with('error_details', $errorDetails);
            }

            return redirect()->route('messages.index')
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            \Log::error('Bulk: Error processing Excel file', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors(['excel_file' => 'Error processing Excel file: ' . $e->getMessage()]);
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
