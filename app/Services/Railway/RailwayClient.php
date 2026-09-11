<?php

namespace App\Services\Railway;

use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Thin wrapper over Railway's GraphQL v2 API (SaaS conversion plan Phase 7).
 *
 * CONFIDENCE UPDATE (2026-09-06): a real project-scoped token is now
 * configured and a basic `project { environments }` query has been
 * confirmed working live — but that also revealed the auth header itself
 * was wrong (see `request()`'s note). The mutations below — service
 * creation, upsertVariables, deployLatest, addCustomDomain — are still
 * unverified against a real call — treat them as a strong first draft
 * until one has actually succeeded once; field/mutation names may need
 * small fixes.
 *
 * Design choice worth calling out: rather than deploying Railway's official
 * MySQL *template* (whose auto-generated variable names — MYSQLHOST etc. —
 * are an assumption we can't verify without a live account), each tenant's
 * database is a plain `mysql:8.0` image service with credentials WE
 * generate and set ourselves (see {@see \App\Jobs\ProvisionInstance}). That
 * removes an entire class of "guess Railway's naming" risk — we control
 * every variable name on both sides of the connection.
 */
class RailwayClient
{
    private const ENDPOINT = 'https://backboard.railway.app/graphql/v2';

    public function __construct(private readonly string $apiToken) {}

    /**
     * Create a service from a plain Docker image (used for the per-tenant
     * MySQL instance — no source repo, no build step, starts immediately).
     */
    public function createServiceFromImage(string $projectId, string $environmentId, string $name, string $image): string
    {
        $result = $this->request(<<<'GQL'
            mutation ServiceCreate($input: ServiceCreateInput!) {
                serviceCreate(input: $input) { id name }
            }
        GQL, [
            'input' => [
                'projectId'     => $projectId,
                'environmentId' => $environmentId,
                'name'          => $name,
                'source'        => ['image' => $image],
            ],
        ]);

        return $result['serviceCreate']['id'];
    }

    /**
     * Attach a persistent volume to a service at the given mount path.
     *
     * Confirmed live (2026-09-11): a bare `mysql:8.0` image service with NO
     * volume was silently running on the container's ephemeral filesystem —
     * a restart/reschedule of the DB container (which Railway can do at any
     * time, not just on a manual redeploy) wipes all data. This is not
     * optional for a database service; every tenant's `{slug}-db` needs one.
     */
    public function createVolume(string $projectId, string $environmentId, string $serviceId, string $mountPath): string
    {
        $result = $this->request(<<<'GQL'
            mutation VolumeCreate($input: VolumeCreateInput!) {
                volumeCreate(input: $input) { id }
            }
        GQL, [
            'input' => [
                'projectId'     => $projectId,
                'environmentId' => $environmentId,
                'serviceId'     => $serviceId,
                'mountPath'     => $mountPath,
            ],
        ]);

        return $result['volumeCreate']['id'];
    }

    /** Create the tenant's app service, deployed from the SaaS POS repo's generic Dockerfile. */
    public function createServiceFromRepo(string $projectId, string $environmentId, string $name, string $repo, string $branch): string
    {
        $result = $this->request(<<<'GQL'
            mutation ServiceCreate($input: ServiceCreateInput!) {
                serviceCreate(input: $input) { id name }
            }
        GQL, [
            'input' => [
                'projectId'     => $projectId,
                'environmentId' => $environmentId,
                'name'          => $name,
                'source'        => ['repo' => $repo],
                'branch'        => $branch,
            ],
        ]);

        return $result['serviceCreate']['id'];
    }

    /** @param array<string,string> $variables */
    public function upsertVariables(string $projectId, string $environmentId, string $serviceId, array $variables): void
    {
        $this->request(<<<'GQL'
            mutation VariableCollectionUpsert($input: VariableCollectionUpsertInput!) {
                variableCollectionUpsert(input: $input)
            }
        GQL, [
            'input' => [
                'projectId'     => $projectId,
                'environmentId' => $environmentId,
                'serviceId'     => $serviceId,
                'variables'     => $variables,
            ],
        ]);
    }

    /**
     * Confirmed live (2026-09-11): without `latestCommit: true`, this
     * mutation redeploys whatever commit the service last had pinned —
     * NOT the branch's current HEAD. A plain redeploy (e.g. after just
     * pushing new code) would silently keep serving the stale commit.
     */
    public function deployLatest(string $serviceId, string $environmentId): void
    {
        $this->request(<<<'GQL'
            mutation ServiceInstanceDeploy($serviceId: String!, $environmentId: String!) {
                serviceInstanceDeploy(serviceId: $serviceId, environmentId: $environmentId, latestCommit: true)
            }
        GQL, ['serviceId' => $serviceId, 'environmentId' => $environmentId]);
    }

    /**
     * Registers a custom domain on the service and returns the DNS records
     * Railway wants: both the CNAME (routes traffic) AND a TXT ownership
     * record (`_railway-verify.<slug>` → `railway-verify=<token>`).
     *
     * Confirmed live (2026-09-11): the TXT record isn't optional — without
     * it Railway's cert issuance sits stuck at
     * CERTIFICATE_STATUS_TYPE_VALIDATING_OWNERSHIP forever, even though the
     * CNAME alone gets the domain "working" from a raw DNS-resolves-fine
     * perspective. This was silently broken (only the CNAME was ever
     * created) until this fix.
     *
     * @return array{cname: array{fqdn: string, value: string}, txt: array{fqdn: string, value: string}}
     */
    public function addCustomDomain(string $projectId, string $environmentId, string $serviceId, string $domain): array
    {
        $result = $this->request(<<<'GQL'
            mutation CustomDomainCreate($input: CustomDomainCreateInput!) {
                customDomainCreate(input: $input) {
                    id
                    domain
                    status {
                        dnsRecords { hostlabel fqdn recordType requiredValue currentValue status }
                        verificationDnsHost
                        verificationToken
                    }
                }
            }
        GQL, [
            'input' => [
                'projectId'     => $projectId,
                'environmentId' => $environmentId,
                'serviceId'     => $serviceId,
                'domain'        => $domain,
                // Confirmed live (2026-09-11): omitting this left Railway's
                // edge routing to whatever it defaults to (not port 80,
                // where our Dockerfile's nginx actually listens) —
                // produced a healthy, fully-booted container that Railway
                // still 502'd with "Application failed to respond" on
                // every request. Every image built from this repo's
                // Dockerfile (or SaasPOS's) listens on 80; pin it.
                'targetPort'    => 80,
            ],
        ]);

        $status = $result['customDomainCreate']['status'] ?? [];

        // Confirmed live (2026-09-07): the enum value is
        // "DNS_RECORD_TYPE_CNAME", not the bare "CNAME" the field name
        // would suggest.
        $records = $status['dnsRecords'] ?? [];
        $cname   = collect($records)->firstWhere('recordType', 'DNS_RECORD_TYPE_CNAME');

        if ($cname === null) {
            throw new RuntimeException("Railway did not return a CNAME target for domain [{$domain}].");
        }

        // Confirmed live (2026-09-11): whether the TXT ownership record
        // shows up inside `dnsRecords` is inconsistent — some domains had a
        // DNS_RECORD_TYPE_TXT entry there, others (same zone, same day)
        // didn't, even though `verificationDnsHost`/`verificationToken`
        // were populated for all of them and cert issuance got stuck at
        // VALIDATING_OWNERSHIP without it regardless. These two top-level
        // fields are the reliable source — a domain with a zone Railway
        // has never verified needs the TXT record created from these; a
        // domain whose zone Railway already trusts (verified via an
        // earlier subdomain) has empty/absent values here, meaning no TXT
        // record is needed at all.
        $txt = null;
        if (! empty($status['verificationDnsHost']) && ! empty($status['verificationToken'])) {
            // verificationDnsHost is "_railway-verify.<first label of
            // $domain>" — e.g. "_railway-verify.demo" for
            // "demo.sphereofthesun.com". Append the ROOT domain (everything
            // after that first label), not the full $domain again, or this
            // doubles the label ("_railway-verify.demo.demo.sphere...").
            $rootDomain = str_contains($domain, '.') ? substr($domain, strpos($domain, '.') + 1) : $domain;
            $txt = [
                'fqdn'  => $status['verificationDnsHost'].'.'.$rootDomain,
                'value' => $status['verificationToken'],
            ];
        }

        return [
            'cname' => ['fqdn' => $cname['fqdn'], 'value' => $cname['requiredValue']],
            'txt'   => $txt,
        ];
    }

    /** @return string one of PENDING|BUILDING|DEPLOYING|SUCCESS|FAILED|CRASHED (Railway's DeploymentStatus enum) */
    public function getLatestDeploymentStatus(string $serviceId, string $environmentId): string
    {
        $result = $this->request(<<<'GQL'
            query Deployments($input: DeploymentListInput!) {
                deployments(input: $input, first: 1) {
                    edges { node { status } }
                }
            }
        GQL, ['input' => ['serviceId' => $serviceId, 'environmentId' => $environmentId]]);

        return $result['deployments']['edges'][0]['node']['status'] ?? 'UNKNOWN';
    }

    /** @return array<string,mixed> the `data` portion of a successful response */
    private function request(string $query, array $variables = []): array
    {
        // Confirmed live (2026-09-06): a project-scoped Railway token is
        // NOT sent as `Authorization: Bearer` (Railway returns "Not
        // Authorized" for that) — it needs the `Project-Access-Token`
        // header instead.
        $response = Http::withHeaders(['Project-Access-Token' => $this->apiToken])
            ->timeout(30)
            ->post(self::ENDPOINT, ['query' => $query, 'variables' => $variables]);

        $body = $response->json();

        if ($response->failed() || ! empty($body['errors'])) {
            throw new RuntimeException(
                'Railway API error: '.json_encode($body['errors'] ?? $body, JSON_UNESCAPED_SLASHES)
            );
        }

        return $body['data'] ?? [];
    }
}
