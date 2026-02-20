<?php

namespace App\Http\Middleware;

use App\Domains\Site\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;

class AuthenticateApiKey
{
    public function handle(Request $request, Closure $next, string ...$scopes): mixed
    {
        $key = $request->header('X-API-Key') ?? $request->query('api_key');

        if (!$key) {
            return response()->json(['error' => 'API key required.'], 401);
        }

        $hashedKey = hash('sha256', $key);
        $apiKey = ApiKey::where('key', $hashedKey)
            ->where('is_active', true)
            ->first();

        if (!$apiKey) {
            return response()->json(['error' => 'Invalid API key.'], 401);
        }

        if ($apiKey->expires_at && $apiKey->expires_at->isPast()) {
            return response()->json(['error' => 'API key expired.'], 401);
        }

        // Check scopes
        if (!empty($scopes)) {
            $keyScopes = $apiKey->scopes ?? [];
            foreach ($scopes as $scope) {
                if (!in_array($scope, $keyScopes) && !in_array('*', $keyScopes)) {
                    return response()->json(['error' => "Missing required scope: {$scope}"], 403);
                }
            }
        }

        // Touch last_used_at
        $apiKey->update(['last_used_at' => now()]);

        // Bind the site for this request
        $site = $apiKey->site;
        app()->instance('current.site', $site);

        $request->merge(['_api_key' => $apiKey, '_api_site' => $site]);

        return $next($request);
    }
}
