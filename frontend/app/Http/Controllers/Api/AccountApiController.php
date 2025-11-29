<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ApiUsageService;
use Illuminate\Http\Request;

class AccountApiController extends Controller
{
    protected ApiUsageService $usageService;

    public function __construct(ApiUsageService $usageService)
    {
        $this->usageService = $usageService;
    }

    /**
     * Get account information
     */
    public function show(Request $request)
    {
        $startTime = microtime(true);
        
        $user = $request->user;
        $subscription = $user->activeSubscription;

        $this->usageService->log($request, 200, $startTime);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'subscription' => $subscription ? [
                    'plan' => $subscription->plan->name ?? null,
                    'status' => $subscription->status,
                ] : null,
            ],
        ]);
    }

    /**
     * Get usage statistics
     */
    public function usage(Request $request)
    {
        $startTime = microtime(true);
        
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $stats = $this->usageService->getUsageStats(
            $request->user->id,
            $startDate,
            $endDate
        );

        $this->usageService->log($request, 200, $startTime);

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}


