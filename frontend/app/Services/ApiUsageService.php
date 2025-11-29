<?php

namespace App\Services;

use App\Models\ApiUsageLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApiUsageService
{
    /**
     * Log API usage
     */
    public function log(Request $request, int $statusCode, float $startTime): void
    {
        try {
            $responseTime = (microtime(true) - $startTime) * 1000; // Convert to milliseconds

            ApiUsageLog::create([
                'user_id' => $request->user->id ?? null,
                'api_key_id' => $request->api_key->id ?? null,
                'endpoint' => $request->path(),
                'method' => $request->method(),
                'ip_address' => $request->ip(),
                'status_code' => $statusCode,
                'response_time' => (int) $responseTime,
                'request_size' => strlen($request->getContent()) ?: null,
                'response_size' => null, // Can be set later if needed
            ]);
        } catch (\Exception $e) {
            // Log error but don't break the request
            \Log::error('Failed to log API usage: ' . $e->getMessage());
        }
    }

    /**
     * Get usage statistics for a user
     */
    public function getUsageStats($userId, $startDate = null, $endDate = null)
    {
        $query = ApiUsageLog::where('user_id', $userId);

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        return [
            'total_requests' => $query->count(),
            'successful_requests' => (clone $query)->where('status_code', '>=', 200)->where('status_code', '<', 300)->count(),
            'failed_requests' => (clone $query)->where('status_code', '>=', 400)->count(),
            'average_response_time' => (clone $query)->avg('response_time'),
            'requests_by_endpoint' => (clone $query)
                ->selectRaw('endpoint, COUNT(*) as count')
                ->groupBy('endpoint')
                ->get(),
            'requests_by_status' => (clone $query)
                ->selectRaw('status_code, COUNT(*) as count')
                ->groupBy('status_code')
                ->get(),
        ];
    }
}

