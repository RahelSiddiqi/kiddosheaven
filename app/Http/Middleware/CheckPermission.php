<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Usage in routes:
 *   Route::get('/something')->middleware('permission:view-orders');
 *
 * Super-admins (is_admin = true) bypass all permission checks.
 * Other users must have the permission either directly via their assigned role.
 */
class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403, 'Unauthenticated.');
        }

        if (!$user->hasPermission($permission)) {
            abort(403, "You do not have the \"{$permission}\" permission.");
        }

        return $next($request);
    }
}
