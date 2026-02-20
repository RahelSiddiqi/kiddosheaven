<?php

namespace App\Http\Middleware;

use App\Domains\Site\Models\Site;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        // Skip tenant resolution if sites table doesn't exist (e.g., during testing)
        try {
            if (!Schema::hasTable('sites')) {
                return $next($request);
            }
        } catch (\Exception $e) {
            return $next($request);
        }

        $host = $request->getHost();

        try {
            // Try custom_domain match first (verified custom domains only)
            $site = null;
            if (Schema::hasColumn('sites', 'custom_domain')) {
                $site = Site::where('custom_domain', $host)
                    ->whereNotNull('domain_verified_at')
                    ->where('is_active', true)
                    ->first();
            }

            // Try exact domain match
            if (!$site) {
                $site = Site::where('domain', $host)->where('is_active', true)->first();
            }

            // Try subdomain match (e.g., "merchant.kiddosheaven.com") - only if subdomain column exists
            if (!$site && Schema::hasColumn('sites', 'subdomain')) {
                $parts = explode('.', $host);
                if (count($parts) >= 3) {
                    $subdomain = $parts[0];
                    $site = Site::where('subdomain', $subdomain)->where('is_active', true)->first();
                }
            }

            // Fall back to default site
            if (!$site) {
                // Try is_default column if it exists
                if (Schema::hasColumn('sites', 'is_default')) {
                    $site = Site::where('is_default', true)->where('is_active', true)->first();
                }
                // Otherwise just get the first active site
                if (!$site) {
                    $site = Site::where('is_active', true)->first();
                }
            }

            if ($site) {
                app()->instance('current.site', $site);
                view()->share('currentSite', $site);
            }
        } catch (\Exception $e) {
            // Silently fail if sites table doesn't exist or has issues
        }

        return $next($request);
    }
}
