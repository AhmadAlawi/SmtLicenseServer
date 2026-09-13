<?php

namespace App\Services\Meta;

use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Thin wrapper over Meta's Graph/OAuth API for the ads-only "Connect with
 * Facebook" flow (Dashboard\SocialConnectionController) — no SDK, same
 * pattern as {@see \App\Services\Railway\RailwayClient}. Only touches
 * ad-account/Page *read* endpoints; never posts content.
 */
class MetaClient
{
    private const GRAPH_VERSION = 'v25.0';

    private const GRAPH_BASE = 'https://graph.facebook.com/'.self::GRAPH_VERSION;

    private const OAUTH_DIALOG = 'https://www.facebook.com/'.self::GRAPH_VERSION.'/dialog/oauth';

    /** Ads-only — no pages_manage_posts/instagram_content_publish. business_management lists ad accounts under a Business Manager; pages_show_list is read-only Page visibility (needed just to see id/name — /me/accounts returns nothing without it). */
    private const SCOPES = 'ads_management,ads_read,business_management,pages_show_list';

    public function __construct(
        private readonly string $appId,
        private readonly string $appSecret,
        private readonly string $redirectUri,
    ) {}

    public function getAuthUrl(string $state): string
    {
        return self::OAUTH_DIALOG.'?'.http_build_query([
            'client_id'     => $this->appId,
            'redirect_uri'  => $this->redirectUri,
            'state'         => $state,
            'scope'         => self::SCOPES,
            'response_type' => 'code',
        ]);
    }

    /** @return array{access_token: string} */
    public function exchangeCodeForToken(string $code): array
    {
        return $this->get('/oauth/access_token', [
            'client_id'     => $this->appId,
            'client_secret' => $this->appSecret,
            'redirect_uri'  => $this->redirectUri,
            'code'          => $code,
        ]);
    }

    /** Exchanges a short-lived user token for a long-lived one (~60 days). */
    public function getLongLivedToken(string $shortLivedToken): array
    {
        return $this->get('/oauth/access_token', [
            'grant_type'        => 'fb_exchange_token',
            'client_id'         => $this->appId,
            'client_secret'     => $this->appSecret,
            'fb_exchange_token' => $shortLivedToken,
        ]);
    }

    /** @return array<int, array{id: string, name: string}> */
    public function getAdAccounts(string $userToken): array
    {
        $result = $this->get('/me/adaccounts', ['fields' => 'id,name', 'access_token' => $userToken]);

        return $result['data'] ?? [];
    }

    /** @return array<int, array{id: string, name: string}> Read-only listing — no posting permission required just to read id/name. */
    public function getPages(string $userToken): array
    {
        $result = $this->get('/me/accounts', ['fields' => 'id,name', 'access_token' => $userToken]);

        return $result['data'] ?? [];
    }

    public function getInstagramBusinessAccountId(string $pageId, string $userToken): ?string
    {
        $result = $this->get("/{$pageId}", ['fields' => 'instagram_business_account', 'access_token' => $userToken]);

        return $result['instagram_business_account']['id'] ?? null;
    }

    /** @return array<string,mixed> */
    private function get(string $path, array $query): array
    {
        $response = Http::timeout(30)->get(self::GRAPH_BASE.$path, $query);

        $body = $response->json();

        if ($response->failed()) {
            throw new RuntimeException('Meta Graph API error: '.json_encode($body['error'] ?? $body, JSON_UNESCAPED_SLASHES));
        }

        return $body ?? [];
    }
}
