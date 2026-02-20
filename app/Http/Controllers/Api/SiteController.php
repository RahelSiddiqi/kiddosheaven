<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SiteController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $site = $request->get('_api_site');
        return response()->json([
            'data' => [
                'id' => $site->id,
                'name' => $site->name,
                'subdomain' => $site->subdomain,
                'domain' => $site->domain,
                'currency' => $site->currency,
                'locale' => $site->locale,
                'timezone' => $site->timezone,
                'plan' => $site->plan?->name,
                'trial_ends_at' => $site->trial_ends_at?->toIso8601String(),
            ]
        ]);
    }
}
