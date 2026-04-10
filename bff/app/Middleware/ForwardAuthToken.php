<?php

namespace App\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Extracts the Bearer token from the incoming request and
 * stores it on the request object for downstream services.
 * The BFF never validates the token — backend is authoritative.
 */
class ForwardAuthToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (! $token) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Attach for retrieval in services: $request->attributes->get('bearer_token')
        $request->attributes->set('bearer_token', $token);

        return $next($request);
    }
}
