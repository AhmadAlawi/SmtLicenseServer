<?php

namespace App\Providers;

use App\Models\Customer;
use App\Services\Cloudflare\CloudflareClient;
use App\Services\Meta\MetaClient;
use App\Services\Railway\RailwayClient;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransportFactory;
use Symfony\Component\Mailer\Transport\Smtp\Stream\SocketStream;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Cashier's own webhook route would otherwise register
        // POST /stripe/webhook straight to its stock WebhookController —
        // disabled here (in register(), so it wins the race against
        // CashierServiceProvider::boot() regardless of provider load
        // order) so routes/web.php can point that same path at
        // StripeWebhookController instead, which layers license/plan sync
        // on top of Cashier's handling rather than replacing it.
        Cashier::$registersRoutes = false;

        // Both take a plain string token in their constructor — bind them
        // here so anywhere they're type-hinted (ProvisionInstance,
        // PollRailwayDeployments, Dashboard\LicenseController) resolves
        // without every call site having to pull config itself.
        $this->app->singleton(RailwayClient::class, fn () => new RailwayClient((string) config('services.railway.token')));
        $this->app->singleton(CloudflareClient::class, fn () => new CloudflareClient((string) config('services.cloudflare.token')));
        $this->app->singleton(MetaClient::class, fn () => new MetaClient(
            (string) config('services.meta.app_id'),
            (string) config('services.meta.app_secret'),
            route('dashboard.social.callback'),
        ));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Billing belongs to `customers` (paying POS shop owners), not
        // `users` (SMTGROUP staff logging into the platform dashboard).
        Cashier::useCustomerModel(Customer::class);

        // Keyed by the license key itself (not IP alone) so one abusive or
        // misconfigured instance can't exhaust another customer's bucket by
        // sharing a NAT'd IP — falls back to IP when the header is absent,
        // which still lets the 401 in VerifyLicenseSignature do its job.
        RateLimiter::for('license-validate', fn (Request $request) => Limit::perMinute(20)
            ->by($request->header('X-License-Key') ?: $request->ip()));

        // TEMPORARY: mail.sphereofthesun.com's TLS cert expired 2026-08-28
        // (confirmed via `openssl s_client`, not a bug on this app's side).
        // Laravel's own smtp transport builder has no config key for TLS
        // peer-verification — Symfony's SocketStream needs setStreamOptions()
        // called directly — so this overrides transport creation only while
        // MAIL_VERIFY_PEER=false. Remove this whole block (and the .env line)
        // once that cert is renewed; sending unverified TLS to an SMTP host
        // is a real downgrade, not something to leave on permanently.
        if (! filter_var(env('MAIL_VERIFY_PEER', true), FILTER_VALIDATE_BOOL)) {
            Mail::extend('smtp', function (array $config) {
                $scheme = $config['scheme'] ?? (($config['port'] ?? null) == 465 ? 'smtps' : 'smtp');

                $transport = (new EsmtpTransportFactory)->create(new Dsn(
                    $scheme,
                    $config['host'],
                    $config['username'] ?? null,
                    $config['password'] ?? null,
                    $config['port'] ?? null,
                    $config,
                ));

                if (($stream = $transport->getStream()) instanceof SocketStream) {
                    $stream->setStreamOptions([
                        'ssl' => ['verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true],
                    ]);
                }

                return $transport;
            });
        }
    }
}
