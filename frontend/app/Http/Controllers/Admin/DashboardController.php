<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuotaPurchase;
use App\Models\User;
use App\Models\WhatsAppSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(\App\Http\Middleware\EnsureUserIsAdmin::class);
    }

    /**
     * Display admin dashboard with statistics
     */
    public function index()
    {
        // Total users (excluding admin)
        $totalUsers = User::whereNotIn('role', ['admin', 'super_admin'])->count();
        
        // Active sessions (connected status)
        $activeSessions = WhatsAppSession::where('status', 'connected')->count();
        
        // Total revenue from completed purchases
        $totalRevenue = QuotaPurchase::where('status', 'completed')->sum('amount');
        
        // Revenue this month
        $revenueThisMonth = QuotaPurchase::where('status', 'completed')
            ->whereMonth('completed_at', now()->month)
            ->whereYear('completed_at', now()->year)
            ->sum('amount');
        
        // Revenue last month
        $revenueLastMonth = QuotaPurchase::where('status', 'completed')
            ->whereMonth('completed_at', now()->subMonth()->month)
            ->whereYear('completed_at', now()->subMonth()->year)
            ->sum('amount');
        
        // Pending purchases count
        $pendingPurchases = QuotaPurchase::where('status', 'pending')->count();
        
        // Completed purchases count
        $completedPurchases = QuotaPurchase::where('status', 'completed')->count();
        
        // Recent purchases (last 10)
        $recentPurchases = QuotaPurchase::with('user')
            ->latest()
            ->limit(10)
            ->get();
        
        // New users this month
        $newUsersThisMonth = User::whereNotIn('role', ['admin', 'super_admin'])
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        // Total sessions
        $totalSessions = WhatsAppSession::count();
        
        // Revenue growth percentage
        $revenueGrowth = 0;
        if ($revenueLastMonth > 0) {
            $revenueGrowth = (($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100;
        } elseif ($revenueThisMonth > 0) {
            $revenueGrowth = 100;
        }

        // User growth data for chart (last 12 months)
        $userGrowthData = [];
        $userGrowthLabels = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();
            
            $userCount = User::whereNotIn('role', ['admin', 'super_admin'])
                ->where('created_at', '<=', $monthEnd)
                ->count();
            
            $userGrowthLabels[] = $date->format('M Y');
            $userGrowthData[] = $userCount;
        }

        return view('admin.dashboard.index', compact(
            'totalUsers',
            'activeSessions',
            'totalRevenue',
            'revenueThisMonth',
            'revenueLastMonth',
            'revenueGrowth',
            'pendingPurchases',
            'completedPurchases',
            'recentPurchases',
            'newUsersThisMonth',
            'totalSessions',
            'userGrowthLabels',
            'userGrowthData'
        ));
    }
}
