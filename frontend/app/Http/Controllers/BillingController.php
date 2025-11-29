<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();
        $currentSubscription = Auth::user()->activeSubscription;
        return view('billing.index', compact('plans', 'currentSubscription'));
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        $plan = Plan::findOrFail($request->plan_id);
        
        // Cancel existing subscription
        if ($currentSub = Auth::user()->activeSubscription) {
            $currentSub->update(['status' => 'cancelled']);
        }

        // Create new subscription
        Subscription::create([
            'user_id' => Auth::id(),
            'plan_id' => $plan->id,
            'status' => 'active',
            'current_period_start' => now(),
            'current_period_end' => now()->addMonth(),
        ]);

        Auth::user()->update([
            'subscription_plan_id' => $plan->id,
            'subscription_status' => 'active',
        ]);

        return back()->with('success', 'Subscription updated successfully.');
    }
}
