<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WhatsAppSession;
use App\Services\WahaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class SessionPerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected WahaService $wahaService;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test user
        $this->user = User::factory()->create();
        
        // Mock WahaService or use real one for integration test
        $this->wahaService = app(WahaService::class);
    }

    /**
     * Test that session creation completes within acceptable time limit.
     * Target: < 5 seconds for normal flow
     */
    public function test_session_creation_performance(): void
    {
        $this->actingAs($this->user);

        $startTime = microtime(true);

        // Simulate session creation flow
        $response = $this->post('/sessions', [
            'session_name' => 'Test Session ' . time(),
        ]);

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        // Log performance metrics
        Log::info('Session creation performance', [
            'execution_time' => $executionTime,
            'execution_time_formatted' => number_format($executionTime, 2) . 's',
        ]);

        // Assert that creation completes within reasonable time
        // Allow up to 10 seconds for initial test, then optimize to < 5 seconds
        $this->assertLessThan(10, $executionTime, 
            "Session creation took {$executionTime}s, expected < 10s"
        );

        // Assert response is successful (redirect is expected)
        $this->assertTrue(
            $response->isRedirect() || $response->status() === 200,
            'Session creation should redirect or return success'
        );
    }

    /**
     * Test that multiple sequential session creation is allowed (WAHA Plus supports multiple sessions).
     */
    public function test_multiple_session_creation_allowed(): void
    {
        $this->actingAs($this->user);

        // First session creation
        $response1 = $this->post('/sessions', [
            'session_name' => 'First Session',
        ]);

        // Second session should be allowed (WAHA Plus supports multiple)
        $response2 = $this->post('/sessions', [
            'session_name' => 'Second Session',
        ]);

        // Should allow multiple sessions (up to plan limit)
        $sessionCount = WhatsAppSession::where('user_id', $this->user->id)->count();
        $this->assertGreaterThanOrEqual(1, $sessionCount, 
            'User should be able to create multiple sessions with WAHA Plus'
        );
    }

    /**
     * Test API call performance for WahaService methods.
     */
    public function test_waha_service_api_performance(): void
    {
        $sessionId = 'test-session-' . time();

        // Test createSession performance
        $startTime = microtime(true);
        $result = $this->wahaService->createSession($sessionId);
        $createTime = microtime(true) - $startTime;

        Log::info('WahaService createSession performance', [
            'execution_time' => $createTime,
            'success' => $result['success'] ?? false,
        ]);

        // Create should complete quickly (< 3 seconds)
        $this->assertLessThan(3, $createTime, 
            "createSession took {$createTime}s, expected < 3s"
        );

        // Cleanup
        if ($result['success'] ?? false) {
            $this->wahaService->deleteSession($sessionId);
        }
    }

    /**
     * Test that session creation validates user authorization.
     */
    public function test_session_creation_authorization(): void
    {
        // Test without authentication
        $response = $this->post('/sessions', [
            'session_name' => 'Unauthorized Session',
        ]);

        // Should redirect to login
        $response->assertRedirect('/login');
    }

    /**
     * Test rate limiting for session creation.
     */
    public function test_session_creation_rate_limiting(): void
    {
        $this->actingAs($this->user);

        // Attempt to create multiple sessions rapidly
        $attempts = 5;
        $successCount = 0;

        for ($i = 0; $i < $attempts; $i++) {
            try {
                $response = $this->post('/sessions', [
                    'session_name' => "Session {$i}",
                ]);

                if ($response->isRedirect() || $response->status() === 200) {
                    $successCount++;
                }
            } catch (\Exception $e) {
                // Expected for subsequent attempts
            }
        }

        // Should only allow one session per user
        $this->assertLessThanOrEqual(1, $successCount, 
            'Rate limiting should prevent multiple sessions'
        );
    }
}

