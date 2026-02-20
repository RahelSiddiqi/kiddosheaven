<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPlanFeature
{
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $site = app()->bound('current.site') ? app('current.site') : null;

        if (!$site || !$site->plan) {
            abort(403, 'No active plan.');
        }

        if (!$site->plan->hasFeature($feature)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Your plan does not include this feature.'], 403);
            }

            return redirect()->route('merchant.upgrade')->with('error', 'Upgrade your plan to access this feature.');
        }

        return $next($request);
    }
}
