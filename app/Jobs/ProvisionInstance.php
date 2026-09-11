<?php

namespace App\Jobs;

use App\Models\Instance;
use App\Models\License;
use App\Services\Cloudflare\CloudflareClient;
use App\Services\Railway\RailwayClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

/**
 * Turns a paid-for license into a running, reachable instance with zero
 * manual steps (SaaS conversion plan Phase 7): a dedicated Railway MySQL
 * service, the app service deployed from this SaaS POS repo's generic
 * Dockerfile, entitlements + tenant credentials wired in as Railway
 * variables, and the white-label subdomain registered + pointed at via
 * Cloudflare — Railway itself is never visible to the customer.
 *
 * `$licenseKey` is passed explicitly (not re-read off `$license`) because
 * the license_key is stored ONLY as a hash — this job's dispatch is the
 * one and only place after minting where the plaintext is still available.
 *
 * Every step's failure records `provisioning_status = 'failed'` +
 * `provisioning_error` on the `instances` row rather than failing silently
 * — visible in the staff dashboard, safe to retry once the underlying
 * issue (a Railway API field-name mismatch, most likely on first real run
 * — see the confidence flag on {@see RailwayClient}) is fixed.
 */
class ProvisionInstance implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param array{subdomain_slug: string, company_name: string, branch_count: ?int, logo_url: ?string, admin_name: string, admin_email: string, admin_password: string} $signup
     */
    public function __construct(
        public License $license,
        public string $licenseKey,
        public string $hmacSecret,
        public array $signup,
    ) {}

    public function handle(RailwayClient $railway, CloudflareClient $cloudflare): void
    {
        $instance = $this->license->instance()->firstOrCreate([], ['provisioning_status' => 'provisioning']);

        try {
            $projectId     = (string) config('services.railway.project_id');
            $environmentId = (string) config('services.railway.environment_id');
            $rootDomain    = (string) config('services.platform.root_domain');
            $slug          = $this->signup['subdomain_slug'];
            $domain        = "{$slug}.{$rootDomain}";

            // 1. Dedicated MySQL service — a plain mysql:8.0 image with
            // credentials WE generate, not Railway's official template
            // (whose variable naming we can't verify without a live
            // account — see RailwayClient's confidence flag).
            $dbName     = 'tenant_'.str_replace('-', '_', $slug);
            $dbUser     = 'u_'.Str::random(12);
            $dbPassword = Str::random(32);

            $dbServiceId = $railway->createServiceFromImage($projectId, $environmentId, "{$slug}-db", 'mysql:8.0');
            $instance->forceFill(['railway_db_service_id' => $dbServiceId])->save();

            // Without this, the DB container runs on ephemeral storage —
            // any restart/reschedule (not just a manual redeploy) silently
            // wipes the tenant's entire database. Must exist before the
            // service's first deploy so mysqld initializes its data
            // directory on the volume from the start.
            $railway->createVolume($projectId, $environmentId, $dbServiceId, '/var/lib/mysql');

            $railway->upsertVariables($projectId, $environmentId, $dbServiceId, [
                'MYSQL_ROOT_PASSWORD' => Str::random(32),
                'MYSQL_DATABASE'      => $dbName,
                'MYSQL_USER'          => $dbUser,
                'MYSQL_PASSWORD'      => $dbPassword,
            ]);
            $railway->deployLatest($dbServiceId, $environmentId);

            // Railway's private network convention: <service-name>.railway.internal
            $dbHost = "{$slug}-db.railway.internal";

            // 2. App service, deployed from the SaaS POS repo's generic Dockerfile.
            $appServiceId = $railway->createServiceFromRepo(
                $projectId,
                $environmentId,
                $slug,
                (string) config('services.railway.source_repo'),
                (string) config('services.railway.source_branch'),
            );
            $instance->forceFill(['railway_service_id' => $appServiceId])->save();

            // 3. Entitlements + tenant credentials, all in one variable set.
            $railway->upsertVariables($projectId, $environmentId, $appServiceId, [
                'APP_KEY'               => 'base64:'.base64_encode(random_bytes(32)),
                'APP_URL'               => "https://{$domain}",
                'DB_CONNECTION'         => 'mysql',
                'DB_HOST'               => $dbHost,
                'DB_PORT'               => '3306',
                'DB_DATABASE'           => $dbName,
                'DB_USERNAME'           => $dbUser,
                'DB_PASSWORD'           => $dbPassword,
                'LICENSE_KEY'           => $this->licenseKey,
                'LICENSE_HMAC_SECRET'   => $this->hmacSecret,
                'TENANT_COMPANY_NAME'   => $this->signup['company_name'],
                'TENANT_ADMIN_NAME'     => $this->signup['admin_name'],
                'TENANT_ADMIN_EMAIL'    => $this->signup['admin_email'],
                'TENANT_ADMIN_PASSWORD' => $this->signup['admin_password'],
                // Optional wizard fields (Phase 8) — TenantProvision (SaasPOS
                // repo) fetches the logo URL into its own storage the same
                // way ProvisionMeccaMall::fetchToPublicDisk() already does.
                // Branch count isn't consumed tenant-side at all, it's
                // license-server-only (staff visibility / plan sizing).
                'TENANT_LOGO_URL'       => $this->signup['logo_url'] ?? '',
                'TENANT_BRANCH_COUNT'   => (string) ($this->signup['branch_count'] ?? ''),
            ]);

            // 4. Deploy.
            $railway->deployLatest($appServiceId, $environmentId);

            // 5. White-label subdomain — Railway tells us the CNAME target
            // AND a TXT ownership record it needs; both are required or
            // cert issuance sits stuck at VALIDATING_OWNERSHIP forever.
            $records = $railway->addCustomDomain($projectId, $environmentId, $appServiceId, $domain);

            // 6. Auto-create both DNS records on the owner's own Cloudflare zone.
            $zoneId = (string) config('services.cloudflare.zone_id');
            $cloudflare->createCnameRecord($zoneId, $slug, $records['cname']['value']);
            $cloudflare->createTxtRecord($zoneId, $records['txt']['fqdn'], $records['txt']['value']);

            // 7. Deploy is asynchronous on Railway's side — PollRailwayDeployments
            // (scheduled every minute) flips 'deploying' to 'ready'/'failed'.
            $instance->forceFill([
                'subdomain_slug'      => $slug,
                'default_domain'      => $domain,
                'provisioning_status' => 'deploying',
                'provisioning_error'  => null,
            ])->save();
        } catch (Throwable $e) {
            $instance->forceFill([
                'provisioning_status' => 'failed',
                'provisioning_error'  => $e->getMessage(),
            ])->save();

            Log::error('ProvisionInstance failed.', ['license_id' => $this->license->id, 'error' => $e->getMessage()]);

            throw $e;
        }
    }
}
