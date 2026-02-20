<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Domains\Site\Models\WebhookSubscription;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WebhookController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $site = $request->get('_api_site');
        $webhooks = WebhookSubscription::where('site_id', $site->id)->get();
        return response()->json(['data' => $webhooks]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'url' => 'required|url|max:500',
            'events' => 'required|array|min:1',
            'events.*' => 'string|in:order.placed,order.completed,order.cancelled,product.updated,inventory.low',
        ]);

        $site = $request->get('_api_site');

        $webhook = WebhookSubscription::create([
            'site_id' => $site->id,
            'url' => $validated['url'],
            'events' => $validated['events'],
            'secret' => \Illuminate\Support\Str::random(40),
            'is_active' => true,
        ]);

        return response()->json(['data' => $webhook], 201);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $site = $request->get('_api_site');
        $webhook = WebhookSubscription::where('id', $id)->where('site_id', $site->id)->firstOrFail();
        $webhook->delete();
        return response()->json(['message' => 'Webhook deleted.']);
    }
}
