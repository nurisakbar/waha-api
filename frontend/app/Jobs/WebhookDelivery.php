<?php

namespace App\Jobs;

use App\Models\Webhook;
use App\Models\WebhookLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookDelivery implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 30;
    public $backoff = [5, 15, 30];

    protected $webhookId;
    protected $payload;
    protected $event;

    /**
     * Create a new job instance.
     */
    public function __construct(string $webhookId, string $event, array $payload)
    {
        $this->webhookId = $webhookId;
        $this->event = $event;
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $webhook = Webhook::find($this->webhookId);
        if (!$webhook || !$webhook->is_active) {
            Log::warning('WebhookDelivery Job: Webhook not found or inactive', [
                'webhook_id' => $this->webhookId,
            ]);
            return;
        }

        try {
            Log::info('WebhookDelivery Job: Delivering webhook', [
                'webhook_id' => $this->webhookId,
                'url' => $webhook->url,
                'event' => $this->event,
            ]);

            $response = Http::timeout(10)
                ->withHeaders([
                    'User-Agent' => 'WAHA-SaaS/1.0',
                    'Content-Type' => 'application/json',
                ])
                ->post($webhook->url, $this->payload);

            $statusCode = $response->status();
            $success = $statusCode >= 200 && $statusCode < 300;

            // Log webhook delivery
            WebhookLog::create([
                'webhook_id' => $webhook->id,
                'event_type' => $this->event,
                'payload' => $this->payload,
                'response_status' => $statusCode,
                'response_body' => $response->body(),
                'triggered_at' => now(),
            ]);

            if ($success) {
                $webhook->update([
                    'last_triggered_at' => now(),
                ]);

                Log::info('WebhookDelivery Job: Webhook delivered successfully', [
                    'webhook_id' => $this->webhookId,
                    'status_code' => $statusCode,
                ]);
            } else {
                $webhook->increment('failure_count');
                Log::warning('WebhookDelivery Job: Webhook delivery failed', [
                    'webhook_id' => $this->webhookId,
                    'status_code' => $statusCode,
                    'response' => $response->body(),
                ]);

                throw new \Exception("Webhook delivery failed with status code: {$statusCode}");
            }
        } catch (\Exception $e) {
            $webhook->increment('failure_count');

            // Log failed delivery
            WebhookLog::create([
                'webhook_id' => $webhook->id,
                'event_type' => $this->event,
                'payload' => $this->payload,
                'response_status' => 0,
                'error_message' => $e->getMessage(),
                'triggered_at' => now(),
            ]);

            Log::error('WebhookDelivery Job: Exception occurred', [
                'webhook_id' => $this->webhookId,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $webhook = Webhook::find($this->webhookId);
        if ($webhook) {
            $webhook->increment('failure_count');
        }

        Log::error('WebhookDelivery Job: Job failed permanently', [
            'webhook_id' => $this->webhookId,
            'error' => $exception->getMessage(),
        ]);
    }
}
