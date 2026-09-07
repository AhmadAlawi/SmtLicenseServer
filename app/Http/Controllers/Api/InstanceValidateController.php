<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\UsageReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * POST /api/v1/instances/validate — the phone-home target (SaaS conversion
 * plan Phase 0 contract). Signature already verified by
 * {@see \App\Http\Middleware\VerifyLicenseSignature}, which resolved the
 * License onto the request.
 *
 * Every call — valid or not — updates last_seen_at and logs a usage_report:
 * an instance that stops calling in is itself a signal (support/abuse), and
 * the report history is the audit trail for seat-limit disputes.
 */
class InstanceValidateController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        /** @var License $license */
        $license = $request->attributes->get('license');
        $data    = $request->json()->all();
        $usage   = (array) ($data['usage'] ?? []);

        $license->forceFill([
            'instance_domain' => $data['domain_url'] ?? $license->instance_domain,
            'fingerprint'     => $data['fingerprint'] ?? $license->fingerprint,
            'last_seen_at'    => now(),
        ])->save();

        UsageReport::query()->create([
            'license_id'  => $license->id,
            'user_count'  => (int) ($usage['user_count'] ?? 0),
            'store_count' => (int) ($usage['store_count'] ?? 0),
            'reported_at' => now(),
        ]);

        $license->loadMissing('plan');
        $plan = $license->plan;

        return response()->json([
            'status'      => $license->status,
            'plan'        => $plan ? ['code' => $plan->code, 'name' => $plan->name] : null,
            'seats'       => [
                'limit' => $plan?->seat_limit,
                'used'  => (int) ($usage['user_count'] ?? 0),
            ],
            'features'    => $plan?->features ?? [],
            'expires_at'  => null,
            'support_until' => null,
            'grace_until' => $license->grace_until?->toIso8601String(),
        ]);
    }
}
