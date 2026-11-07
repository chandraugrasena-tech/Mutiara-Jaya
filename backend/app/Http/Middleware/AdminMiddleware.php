<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Hanya user dengan role "admin" yang boleh masuk.
 * Dipakai di halaman /admin/* dan endpoint tulis REST API.
 */
class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isAdmin()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Forbidden. Hanya admin yang boleh mengakses.'], 403);
            }
            abort(403, 'Halaman ini khusus admin.');
        }

        return $next($request);
    }
}
