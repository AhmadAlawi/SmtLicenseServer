<?php

namespace App\Http\Middleware;

use App\Models\License;
use App\Services\LicenseSigner;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Verifies the HMAC signature on every instance phone-home request (the
 * POST /api/v1/instances/validate contract — SaaS conversion plan Phase 0).
 * Rejects before the controller ever runs a query the caller doesn't own.
 *
 * A 300-second timestamp window stops replay of a captured request; the
 * per-instance secret (never re-transmitted after /register) stops a
 * leaked license key alone from forging a request. Resolves the License
 * model onto the request so the controller doesn't re-do the lookup.
 */
class VerifyLicenseSignature
{
    private const SIGNATURE_WINDOW_SECONDS = 300;

    public function __construct(private readonly LicenseSigner $signer) {}

    public function handle(Request $request, Closure $next): Response
    {
        $licenseKey = (string) $request->header('X-License-Key', '');
        $timestamp  = (string) $request->header('X-Timestamp', '');
        $signature  = (string) $request->header('X-Signature', '');

        if ($licenseKey === '' || $timestamp === '' || $signature === '') {
            return response()->json(['status' => 'invalid', 'message' => 'Missing signature headers.'], 401);
        }

        if (! ctype_digit($timestamp) || abs(time() - (int) $timestamp) > self::SIGNATURE_WINDOW_SECONDS) {
            return response()->json(['status' => 'invalid', 'message' => 'Stale or invalid timestamp.'], 401);
        }

        $license = License::query()->where('license_key_hash', License::hashKey($licenseKey))->first();
        if ($license === null) {
            return response()->json(['status' => 'invalid', 'message' => 'Unknown license key.'], 401);
        }

        $body = $request->getContent();
        if (! $this->signer->verify($licenseKey, $timestamp, $body, (string) $license->hmac_secret, $signature)) {
            return response()->json(['status' => 'invalid', 'message' => 'Signature verification failed.'], 401);
        }

        $request->attributes->set('license', $license);

        return $next($request);
    }
}
