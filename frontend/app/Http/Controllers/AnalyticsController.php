<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\WhatsAppSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        $stats = [
            'total_messages_sent' => Message::where('user_id', Auth::id())
                ->where('direction', 'outgoing')
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->count(),
            'total_messages_received' => Message::where('user_id', Auth::id())
                ->where('direction', 'incoming')
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->count(),
            'active_sessions' => WhatsAppSession::where('user_id', Auth::id())
                ->where('status', 'connected')
                ->count(),
            'total_sessions' => WhatsAppSession::where('user_id', Auth::id())->count(),
        ];

        $messagesByType = Message::where('user_id', Auth::id())
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->select('message_type', DB::raw('count(*) as total'))
            ->groupBy('message_type')
            ->get();

        return view('analytics.index', compact('stats', 'messagesByType', 'dateFrom', 'dateTo'));
    }
}
