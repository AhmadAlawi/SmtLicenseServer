<?php

namespace App\Services\Meta;

use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Thin wrapper over Meta's Graph/OAuth API for the "Connect with Facebook"
 * flow (Dashboard\SocialConnectionController) — no SDK, same pattern as
 * {@see \App\Services\Railway\RailwayClient}. Covers ad-account/Page reads
 * (Marketing API) plus posting a photo to a Page and publishing to
 * Instagram (Content Publishing API) — the two organic-posting entry
 * points this app actually uses. No comment/message/DM handling.
 */
class MetaClient
{
    private const GRAPH_VERSION = 'v25.0';

    private const GRAPH_BASE = 'https://graph.facebook.com/'.self::GRAPH_VERSION;

    private const OAUTH_DIALOG = 'https://www.facebook.com/'.self::GRAPH_VERSION.'/dialog/oauth';

    /**
     * ads_management/ads_read/business_management — Marketing API access.
     * pages_show_list/pages_read_engagement — read-only Page visibility,
     * needed just to see id/name/instagram_business_account.
     * pages_manage_posts/instagram_basic/instagram_content_publish —
     * organic posting to the Page feed and Instagram.
     */
    private const SCOPES = 'ads_management,ads_read,business_management,pages_show_list,pages_read_engagement,pages_manage_posts,instagram_basic,instagram_content_publish';

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

    /** @return array<int, array{id: string, name: string, access_token: string}> access_token here is the PAGE token — required for posting, distinct from the user token. */
    public function getPages(string $userToken): array
    {
        $result = $this->get('/me/accounts', ['fields' => 'id,name,access_token', 'access_token' => $userToken]);

        return $result['data'] ?? [];
    }

    public function getInstagramBusinessAccountId(string $pageId, string $userToken): ?string
    {
        $result = $this->get("/{$pageId}", ['fields' => 'instagram_business_account', 'access_token' => $userToken]);

        return $result['instagram_business_account']['id'] ?? null;
    }

    /** Posts a photo + caption directly to a Page's feed. Returns the created post id. */
    public function postPhotoToFacebookPage(string $pageId, string $pageToken, string $imageUrl, string $caption): string
    {
        $result = $this->post("/{$pageId}/photos", [
            'url'          => $imageUrl,
            'caption'      => $caption,
            'published'    => 'true',
            'access_token' => $pageToken,
        ]);

        return $result['post_id'] ?? $result['id'];
    }

    /** Step 1 of Instagram's two-step publish: create the media container. Returns the creation id. */
    public function createInstagramMediaContainer(string $igUserId, string $pageToken, string $imageUrl, string $caption): string
    {
        $result = $this->post("/{$igUserId}/media", [
            'image_url'    => $imageUrl,
            'caption'      => $caption,
            'access_token' => $pageToken,
        ]);

        return $result['id'];
    }

    /**
     * Step 2: publish a container created above. Container processing is
     * occasionally async, so this polls status_code briefly before
     * publishing rather than assuming it's immediately ready.
     */
    public function publishInstagramMedia(string $igUserId, string $pageToken, string $creationId): string
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $status = $this->get("/{$creationId}", ['fields' => 'status_code', 'access_token' => $pageToken]);

            if (($status['status_code'] ?? null) === 'FINISHED') {
                break;
            }

            sleep(2);
        }

        $result = $this->post("/{$igUserId}/media_publish", [
            'creation_id'  => $creationId,
            'access_token' => $pageToken,
        ]);

        return $result['id'];
    }

    /** Uploads an image to the ad account's image library from a public URL. Returns the image hash (used by ad creatives). */
    public function uploadAdImage(string $adAccountId, string $userToken, string $imageUrl): string
    {
        $result = $this->post("/{$adAccountId}/adimages", [
            'url'          => $imageUrl,
            'access_token' => $userToken,
        ]);

        $images = $result['images'] ?? [];
        $first = reset($images);

        if (! $first || empty($first['hash'])) {
            throw new RuntimeException('Meta Graph API error: adimages upload returned no hash: '.json_encode($result, JSON_UNESCAPED_SLASHES));
        }

        return $first['hash'];
    }

    /** Creates a paused Campaign. special_ad_categories is required by the API even when empty (non-regulated verticals like retail software pass []). Returns the campaign id. */
    public function createCampaign(string $adAccountId, string $userToken, string $name, string $objective): string
    {
        $result = $this->post("/{$adAccountId}/campaigns", [
            'name'                   => $name,
            'objective'              => $objective,
            'special_ad_categories'  => json_encode([]),
            'status'                 => 'PAUSED',
            'access_token'           => $userToken,
        ]);

        return $result['id'];
    }

    /**
     * Creates a paused Ad Set under a Campaign.
     *
     * @param array{geo_locations: array{countries: string[]}, age_min: int, age_max: int} $targeting
     */
    public function createAdSet(string $adAccountId, string $userToken, string $name, string $campaignId, int $dailyBudgetCents, array $targeting): string
    {
        $result = $this->post("/{$adAccountId}/adsets", [
            'name'               => $name,
            'campaign_id'        => $campaignId,
            'daily_budget'       => (string) $dailyBudgetCents,
            'billing_event'      => 'IMPRESSIONS',
            'optimization_goal'  => 'LINK_CLICKS',
            'bid_strategy'       => 'LOWEST_COST_WITHOUT_CAP',
            'targeting'          => json_encode($targeting),
            'status'             => 'PAUSED',
            'access_token'       => $userToken,
        ]);

        return $result['id'];
    }

    /** Creates an Ad Creative — a Page-identity link ad pointing at $link, using an already-uploaded image hash. Returns the creative id. */
    public function createAdCreative(string $adAccountId, string $userToken, string $name, string $pageId, string $imageHash, string $message, string $link): string
    {
        $objectStorySpec = [
            'page_id'   => $pageId,
            'link_data' => [
                'image_hash'    => $imageHash,
                'link'          => $link,
                'message'       => $message,
                'call_to_action' => ['type' => 'LEARN_MORE', 'value' => ['link' => $link]],
            ],
        ];

        $result = $this->post("/{$adAccountId}/adcreatives", [
            'name'              => $name,
            'object_story_spec' => json_encode($objectStorySpec),
            'access_token'      => $userToken,
        ]);

        return $result['id'];
    }

    /** Creates a paused Ad under an Ad Set, using an already-created creative. Returns the ad id. */
    public function createAd(string $adAccountId, string $userToken, string $name, string $adSetId, string $creativeId): string
    {
        $result = $this->post("/{$adAccountId}/ads", [
            'name'         => $name,
            'adset_id'     => $adSetId,
            'creative'     => json_encode(['creative_id' => $creativeId]),
            'status'       => 'PAUSED',
            'access_token' => $userToken,
        ]);

        return $result['id'];
    }

    /** @return array<string,mixed> */
    private function get(string $path, array $query): array
    {
        $response = Http::timeout(30)->get(self::GRAPH_BASE.$path, $query);

        return $this->parse($response);
    }

    /** @return array<string,mixed> */
    private function post(string $path, array $params): array
    {
        $response = Http::timeout(30)->asForm()->post(self::GRAPH_BASE.$path, $params);

        return $this->parse($response);
    }

    /** @return array<string,mixed> */
    private function parse(\Illuminate\Http\Client\Response $response): array
    {
        $body = $response->json();

        if ($response->failed()) {
            throw new RuntimeException('Meta Graph API error: '.json_encode($body['error'] ?? $body, JSON_UNESCAPED_SLASHES));
        }

        return $body ?? [];
    }
}
