<?php

namespace App\Actions;

use App\Models\Customer;
use App\Models\Instance;
use App\Models\License;
use App\Models\Plan;
use Illuminate\Support\Str;

/**
 * Mints a license_key/hmac_secret pair for a customer + plan and creates
 * the matching `licenses` + `instances` rows. Shared by the two paths that
 * both need this: {@see \App\Http\Controllers\Api\InstanceRegisterController}
 * (provisioning pipeline, already knows customer+plan) and
 * {@see \App\Http\Controllers\StripeWebhookController} (a brand-new
 * customer's Stripe Checkout just completed — this is what triggers
 * provisioning to begin, per SaaS conversion plan Phase 5).
 *
 * Returns the plaintext license_key/hmac_secret — the ONLY place either is
 * ever visible after this call; everywhere else stores/compares only the
 * hash / encrypted value.
 */
class CreateLicenseForCustomer
{
    /** @return array{license: License, license_key: string, hmac_secret: string} */
    public function __invoke(Customer $customer, Plan $plan, ?string $domainUrl = null): array
    {
        $licenseKey = 'lic_'.Str::random(40);
        $hmacSecret = Str::random(64);

        $license = License::query()->create([
            'customer_id'      => $customer->id,
            'plan_id'          => $plan->id,
            'license_key_hash' => License::hashKey($licenseKey),
            'hmac_secret'      => $hmacSecret,
            'instance_domain'  => $domainUrl,
            'status'           => 'valid',
        ]);

        Instance::query()->create([
            'license_id'          => $license->id,
            'provisioning_status' => 'provisioning',
        ]);

        return ['license' => $license, 'license_key' => $licenseKey, 'hmac_secret' => $hmacSecret];
    }
}
