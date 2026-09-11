<?php

namespace App\Services\Cloudflare;

use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Thin wrapper over Cloudflare's REST v4 API — used to auto-create the DNS
 * record for each tenant's white-label subdomain (SaaS conversion plan
 * Phase 7). Much lower risk than {@see \App\Services\Railway\RailwayClient}:
 * this is a stable, well-documented API and the two calls used here are
 * about as simple as Cloudflare's API gets.
 */
class CloudflareClient
{
    private const ENDPOINT = 'https://api.cloudflare.com/client/v4';

    public function __construct(private readonly string $apiToken) {}

    /**
     * DNS-only (grey-cloud) by default: Railway terminates its own TLS per
     * custom domain, so proxying through Cloudflare's edge too is an extra
     * layer with no clear benefit here — flip `$proxied` per-zone later if
     * ever wanted (e.g. for Cloudflare's WAF/DDoS layer in front of Railway).
     *
     * @return string the created DNS record's id
     */
    public function createCnameRecord(string $zoneId, string $name, string $content, bool $proxied = false): string
    {
        $response = Http::withToken($this->apiToken)
            ->timeout(15)
            ->post(self::ENDPOINT."/zones/{$zoneId}/dns_records", [
                'type'    => 'CNAME',
                'name'    => $name,
                'content' => $content,
                'proxied' => $proxied,
                'ttl'     => 1, // "automatic"
            ]);

        $body = $response->json();
        if ($response->failed() || ! ($body['success'] ?? false)) {
            throw new RuntimeException('Cloudflare API error: '.json_encode($body['errors'] ?? $body, JSON_UNESCAPED_SLASHES));
        }

        return $body['result']['id'];
    }

    /**
     * Railway requires this alongside the CNAME to validate domain
     * ownership before it will issue a TLS cert — without it, custom
     * domains sit stuck at CERTIFICATE_STATUS_TYPE_VALIDATING_OWNERSHIP
     * forever (confirmed live 2026-09-11: 4 test domains all stuck this
     * way because only the CNAME was ever created).
     *
     * @return string the created DNS record's id
     */
    public function createTxtRecord(string $zoneId, string $name, string $content): string
    {
        $response = Http::withToken($this->apiToken)
            ->timeout(15)
            ->post(self::ENDPOINT."/zones/{$zoneId}/dns_records", [
                'type'    => 'TXT',
                'name'    => $name,
                'content' => $content,
                'ttl'     => 1, // "automatic"
            ]);

        $body = $response->json();
        if ($response->failed() || ! ($body['success'] ?? false)) {
            throw new RuntimeException('Cloudflare API error: '.json_encode($body['errors'] ?? $body, JSON_UNESCAPED_SLASHES));
        }

        return $body['result']['id'];
    }

    public function deleteDnsRecord(string $zoneId, string $recordId): void
    {
        $response = Http::withToken($this->apiToken)
            ->timeout(15)
            ->delete(self::ENDPOINT."/zones/{$zoneId}/dns_records/{$recordId}");

        $body = $response->json();
        if ($response->failed() || ! ($body['success'] ?? false)) {
            throw new RuntimeException('Cloudflare API error: '.json_encode($body['errors'] ?? $body, JSON_UNESCAPED_SLASHES));
        }
    }
}
