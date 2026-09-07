<?php

namespace App\Services;

/**
 * Server-side counterpart of the POS instance's LicenseSigner
 * (SmtSaasPOS app/Services/Licensing/LicenseSigner.php) — both sides must
 * compute the exact same HMAC over `license_key|timestamp|body` for a
 * signed request to verify. Kept as a tiny standalone class (not tied to
 * any one model) so both the validate and register flows can reuse it.
 */
class LicenseSigner
{
    public function sign(string $licenseKey, string $timestamp, string $body, string $secret): string
    {
        return hash_hmac('sha256', "{$licenseKey}|{$timestamp}|{$body}", $secret);
    }

    public function verify(string $licenseKey, string $timestamp, string $body, string $secret, string $signature): bool
    {
        return hash_equals($this->sign($licenseKey, $timestamp, $body, $secret), $signature);
    }
}
