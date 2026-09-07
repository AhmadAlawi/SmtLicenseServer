<?php

namespace App\Console\Commands;

use App\Mail\InstanceReadyMail;
use App\Models\Instance;
use App\Services\Railway\RailwayClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

/**
 * Railway builds/deploys are asynchronous — ProvisionInstance marks an
 * instance 'deploying' and returns immediately rather than blocking the
 * queue worker on a multi-minute build. This command (scheduled every
 * minute, see routes/console.php) checks each still-deploying instance and
 * flips it to 'ready' or 'failed' (SaaS conversion plan Phase 7).
 */
class PollRailwayDeployments extends Command
{
    protected $signature = 'railway:poll-deployments';

    protected $description = 'Check Railway deployment status for every instance still marked deploying.';

    public function handle(RailwayClient $railway): int
    {
        $environmentId = (string) config('services.railway.environment_id');

        $pending = Instance::query()->where('provisioning_status', 'deploying')->with('license.customer')->get();

        foreach ($pending as $instance) {
            if ($instance->railway_service_id === null) {
                continue;
            }

            try {
                $status = $railway->getLatestDeploymentStatus($instance->railway_service_id, $environmentId);
            } catch (\Throwable $e) {
                $this->warn("Instance #{$instance->id}: status check failed — {$e->getMessage()}");

                continue;
            }

            match ($status) {
                'SUCCESS' => $this->markReady($instance),
                'FAILED', 'CRASHED' => $instance->forceFill([
                    'provisioning_status' => 'failed',
                    'provisioning_error'  => "Railway deployment status: {$status}",
                ])->save(),
                default => null, // still building/deploying — check again next minute
            };
        }

        return self::SUCCESS;
    }

    private function markReady(Instance $instance): void
    {
        $instance->forceFill(['provisioning_status' => 'ready', 'provisioning_error' => null])->save();

        $email = $instance->license?->customer?->email;
        if ($email !== null) {
            Mail::to($email)->send(new InstanceReadyMail($instance, $email));
        }
    }
}
