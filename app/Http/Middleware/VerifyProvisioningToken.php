<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Guards POST /api/v1/instances/register — called once per new customer by
 * the provisioning pipeline (SaaS conversion plan Phase 3), never by an
 * instance itself. A single shared bearer token (PROVISIONING_TOKEN in
 * .env), not per-instance HMAC, since no license exists yet at this point
 * for the instance to sign with.
 */
class VerifyProvisioningToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $expected = (string) config('services.provisioning.token', '');
        $given    = (string) $request->bearerToken();

        if ($expected === '' || $given === '' || ! hash_equals($expected, $given)) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        return $next($request);
    }
}
