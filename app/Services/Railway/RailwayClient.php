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

    public function deployLatest(string $serviceId, string $environmentId): void
    {
        $this->request(<<<'GQL'
            mutation ServiceInstanceDeploy($serviceId: String!, $environmentId: String!) {
                serviceInstanceDeploy(serviceId: $serviceId, environmentId: $environmentId)
            }
        GQL, ['serviceId' => $serviceId, 'environmentId' => $environmentId]);
    }

    /**
     * Registers a custom domain on the service and returns the CNAME
     * target Railway wants it pointed at. Used both for the automatic
     * white-label subdomain and for a customer's own separate domain
     * (added later, manually, via the staff dashboard).
     */
    public function addCustomDomain(string $projectId, string $environmentId, string $serviceId, string $domain): string
    {
        $result = $this->request(<<<'GQL'
            mutation CustomDomainCreate($input: CustomDomainCreateInput!) {
                customDomainCreate(input: $input) {
                    id
                    domain
                    status { dnsRecords { hostlabel fqdn recordType requiredValue currentValue status } }
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

        // Confirmed live (2026-09-07): the enum value is
        // "DNS_RECORD_TYPE_CNAME", not the bare "CNAME" the field name
        // would suggest.
        $records = $result['customDomainCreate']['status']['dnsRecords'] ?? [];
        $cname   = collect($records)->firstWhere('recordType', 'DNS_RECORD_TYPE_CNAME');

        if ($cname === null) {
            throw new RuntimeException("Railway did not return a CNAME target for domain [{$domain}].");
        }

        return $cname['requiredValue'];
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
