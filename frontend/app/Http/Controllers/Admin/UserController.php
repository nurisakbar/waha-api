<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserQuota;
use App\Models\WhatsAppSession;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(\App\Http\Middleware\EnsureUserIsAdmin::class);
    }

    /**
     * Display list of users
     */
    public function index(Request $request)
    {
        $query = User::whereNotIn('role', ['admin', 'super_admin']);

        // Search by name, email, or phone
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->has('role') && $request->role !== '') {
            $query->where('role', $request->role);
        }

        $users = $query->withCount(['whatsappSessions', 'messages'])
            ->latest()
            ->paginate(20);

        // Statistics
        $stats = [
            'total_users' => User::whereNotIn('role', ['admin', 'super_admin'])->count(),
            'active_users' => User::whereNotIn('role', ['admin', 'super_admin'])
                ->whereHas('whatsappSessions', function($q) {
                    $q->where('status', 'connected');
                })
                ->count(),
            'users_this_month' => User::whereNotIn('role', ['admin', 'super_admin'])
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    /**
     * Show user details
     */
    public function show(User $user)
    {
        // Prevent viewing admin users
        if (in_array($user->role, ['admin', 'super_admin'])) {
            abort(404);
        }

        $user->load(['whatsappSessions', 'activeSubscription', 'subscriptionPlan']);
        
        $quota = $user->getQuota();
        
        $stats = [
            'total_sessions' => $user->whatsappSessions()->count(),
            'connected_sessions' => $user->whatsappSessions()->where('status', 'connected')->count(),
            'total_messages' => $user->messages()->count(),
            'messages_sent' => $user->messages()->where('direction', 'outgoing')->count(),
            'messages_received' => $user->messages()->where('direction', 'incoming')->count(),
        ];

        return view('admin.users.show', compact('user', 'quota', 'stats'));
    }
}

