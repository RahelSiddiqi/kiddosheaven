<?php

namespace App\Jobs;

use App\Domains\Site\Models\WebhookSubscription;
use App\Domains\Site\Models\WebhookDelivery;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DispatchWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        public readonly int $siteId,
        public readonly string $event,
        public readonly array $payload,
    ) {}

    public function handle(): void
    {
        $subscriptions = WebhookSubscription::where('site_id', $this->siteId)
            ->where('is_active', true)
            ->whereJsonContains('events', $this->event)
            ->get();

        foreach ($subscriptions as $subscription) {
            $this->deliver($subscription);
        }
    }

    protected function deliver(WebhookSubscription $subscription): void
    {
        $body = json_encode([
            'event' => $this->event,
            'payload' => $this->payload,
            'timestamp' => now()->toIso8601String(),
        ]);

        $signature = hash_hmac('sha256', $body, $subscription->secret);

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'X-Webhook-Event' => $this->event,
                    'X-Webhook-Signature' => $signature,
                ])
                ->post($subscription->url, json_decode($body, true));

            WebhookDelivery::create([
                'webhook_subscription_id' => $subscription->id,
                'event' => $this->event,
                'payload' => $this->payload,
                'http_status' => $response->status(),
                'response_body' => substr($response->body(), 0, 1000),
                'attempt' => $this->attempts(),
                'status' => $response->successful() ? 'delivered' : 'failed',
                'delivered_at' => $response->successful() ? now() : null,
            ]);

            if ($response->successful()) {
                $subscription->update(['last_triggered_at' => now()]);
            }
        } catch (\Exception $e) {
            Log::warning("Webhook delivery failed for subscription {$subscription->id}: {$e->getMessage()}");

            WebhookDelivery::create([
                'webhook_subscription_id' => $subscription->id,
                'event' => $this->event,
                'payload' => $this->payload,
                'http_status' => 0,
                'response_body' => $e->getMessage(),
                'attempt' => $this->attempts(),
                'status' => 'failed',
                'delivered_at' => null,
            ]);
        }
    }
}
