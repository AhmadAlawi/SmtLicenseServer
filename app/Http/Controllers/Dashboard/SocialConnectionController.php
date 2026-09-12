<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\SocialConnection;
use App\Services\Meta\MetaClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

/** Ads-only "Connect with Facebook" — no organic posting permissions, just enough to create/manage ad campaigns via the Marketing API later. */
class SocialConnectionController extends Controller
{
    public function index(): View
    {
        $connection = SocialConnection::query()->where('provider', 'facebook')->first();

        return view('dashboard.social.index', compact('connection'));
    }

    public function redirectToFacebook(MetaClient $meta): RedirectResponse
    {
        $state = Str::random(40);
        session(['meta_oauth_state' => $state]);

        return redirect()->away($meta->getAuthUrl($state));
    }

    public function callback(Request $request, MetaClient $meta): RedirectResponse
    {
        if (! $request->filled('state') || $request->query('state') !== session('meta_oauth_state')) {
            return redirect()->route('dashboard.social.index')->with('status', 'Connection failed: invalid state (possible CSRF). Try again.');
        }

        session()->forget('meta_oauth_state');

        if (! $request->filled('code')) {
            return redirect()->route('dashboard.social.index')->with('status', 'Connection cancelled.');
        }

        $shortLived = $meta->exchangeCodeForToken((string) $request->query('code'));
        $longLived = $meta->getLongLivedToken($shortLived['access_token']);
        $userToken = $longLived['access_token'];

        $adAccounts = $meta->getAdAccounts($userToken);
        $pages = $meta->getPages($userToken);

        if (empty($adAccounts)) {
            return redirect()->route('dashboard.social.index')->with('status', 'No ad accounts found on this Facebook account — connect an account that has access to at least one ad account.');
        }

        if (count($adAccounts) > 1 || count($pages) > 1) {
            session([
                'meta_user_token'    => $userToken,
                'meta_token_expires' => now()->addSeconds((int) ($longLived['expires_in'] ?? 5184000)),
                'meta_ad_accounts'   => $adAccounts,
                'meta_pages'         => $pages,
            ]);

            return redirect()->route('dashboard.social.select-account');
        }

        $this->saveConnection($userToken, now()->addSeconds((int) ($longLived['expires_in'] ?? 5184000)), $adAccounts[0], $pages[0] ?? null, $meta);

        return redirect()->route('dashboard.social.index')->with('status', 'Facebook connected.');
    }

    public function showSelectAccount(): View|RedirectResponse
    {
        if (! session()->has('meta_user_token')) {
            return redirect()->route('dashboard.social.index');
        }

        return view('dashboard.social.select-account', [
            'adAccounts' => session('meta_ad_accounts', []),
            'pages'      => session('meta_pages', []),
        ]);
    }

    public function selectAccount(Request $request, MetaClient $meta): RedirectResponse
    {
        $data = $request->validate([
            'ad_account_id' => ['required', 'string'],
            'page_id'       => ['nullable', 'string'],
        ]);

        $userToken = session('meta_user_token');
        if (! $userToken) {
            return redirect()->route('dashboard.social.index')->with('status', 'Session expired — connect again.');
        }

        $adAccount = collect(session('meta_ad_accounts', []))->firstWhere('id', $data['ad_account_id']);
        $page = collect(session('meta_pages', []))->firstWhere('id', $data['page_id'] ?? null);

        $this->saveConnection($userToken, session('meta_token_expires'), $adAccount, $page, $meta);

        session()->forget(['meta_user_token', 'meta_token_expires', 'meta_ad_accounts', 'meta_pages']);

        return redirect()->route('dashboard.social.index')->with('status', 'Facebook connected.');
    }

    public function disconnect(): RedirectResponse
    {
        SocialConnection::query()->where('provider', 'facebook')->delete();

        return redirect()->route('dashboard.social.index')->with('status', 'Facebook disconnected.');
    }

    /** @param array{id:string,name:string}|null $adAccount @param array{id:string,name:string}|null $page */
    private function saveConnection(string $userToken, $tokenExpires, ?array $adAccount, ?array $page, MetaClient $meta): void
    {
        $igAccountId = $page ? $meta->getInstagramBusinessAccountId($page['id'], $userToken) : null;

        SocialConnection::query()->updateOrCreate(['provider' => 'facebook'], [
            'user_access_token'              => $userToken,
            'ad_account_id'                  => $adAccount['id'] ?? '',
            'ad_account_name'                => $adAccount['name'] ?? null,
            'page_id'                        => $page['id'] ?? null,
            'page_name'                      => $page['name'] ?? null,
            'instagram_business_account_id'  => $igAccountId,
            'token_expires_at'               => $tokenExpires,
        ]);
    }
}
