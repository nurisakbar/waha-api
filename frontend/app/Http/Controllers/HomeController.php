<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = auth()->user();

        $metrics = [
            'totalSessions' => $this->countUserRecords('whatsapp_sessions', $user->id),
            'activeSessions' => $this->countUserRecords('whatsapp_sessions', $user->id, ['status' => 'connected']),
            'messagesSentToday' => $this->countMessages($user->id, 'outgoing'),
            'messagesReceivedToday' => $this->countMessages($user->id, 'incoming'),
        ];

        $recentActivity = $this->fetchRecentActivity($user->id);
        
        // Get message statistics for current month
        $messageStats = $this->getMessageStatsForCurrentMonth($user->id);

        return view('home', [
            'metrics' => $metrics,
            'recentActivity' => $recentActivity,
            'messageStats' => $messageStats,
        ]);
    }

    protected function countUserRecords(string $table, string $userId, array $additionalWhere = []): int
    {
        if (! Schema::hasTable($table)) {
            return 0;
        }

        $query = DB::table($table)->where('user_id', $userId);

        foreach ($additionalWhere as $column => $value) {
            $query->where($column, $value);
        }

        return (int) $query->count();
    }

    protected function countMessages(string $userId, string $direction): int
    {
        if (! Schema::hasTable('messages')) {
            return 0;
        }

        return (int) DB::table('messages')
            ->where('user_id', $userId)
            ->where('direction', $direction)
            ->whereDate('created_at', Carbon::today())
            ->count();
    }

    protected function fetchRecentActivity(string $userId): array
    {
        if (! Schema::hasTable('messages')) {
            return [];
        }

        return DB::table('messages')
            ->select('direction', 'message_type', 'created_at')
            ->where('user_id', $userId)
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($message) {
                return [
                    'direction' => $message->direction,
                    'label' => $message->direction === 'outgoing' ? 'Message Sent' : 'Message Received',
                    'type' => ucfirst($message->message_type),
                    'timestamp' => Carbon::parse($message->created_at)->format('d M Y H:i'),
                ];
            })
            ->toArray();
    }

    /**
     * Get message statistics for current month (daily breakdown)
     */
    protected function getMessageStatsForCurrentMonth(string $userId): array
    {
        if (! Schema::hasTable('messages')) {
            return [
                'labels' => [],
                'data' => [],
            ];
        }

        $startOfMonth = Carbon::now()->startOfMonth();
        $today = Carbon::today();

        // Get all days from start of month to today
        $days = [];
        $currentDate = $startOfMonth->copy();
        while ($currentDate->lte($today)) {
            $days[] = $currentDate->format('Y-m-d');
            $currentDate->addDay();
        }

        // Get message counts per day
        $messageCounts = DB::table('messages')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->where('user_id', $userId)
            ->where('direction', 'outgoing')
            ->whereBetween('created_at', [$startOfMonth, $today->endOfDay()])
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        // Format labels and data
        $labels = [];
        $data = [];

        foreach ($days as $day) {
            $date = Carbon::parse($day);
            $labels[] = $date->format('d M');
            $data[] = $messageCounts[$day] ?? 0;
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'total' => array_sum($data),
        ];
    }
}
