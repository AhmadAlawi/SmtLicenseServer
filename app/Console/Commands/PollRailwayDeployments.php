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
 *
 * A single FAILED/CRASHED reading does NOT immediately give up — Railway's
 * own restart policy already retries a crashed container automatically
 * (up to 10 times), and a transient/self-recovering crash-loop deserves a
 * few more minutes before staff need to get involved. Confirmed live
 * 2026-09-12: marking 'failed' on the first bad reading meant this query's
 * `where provisioning_status = 'deploying'` permanently excluded the row
 * afterward — even once the real problem got fixed and the deployment
 * genuinely succeeded, nothing ever re-checked it and the customer's
 * confirmation email never sent, with no way to notice except a human
 * manually investigating. MAX_CHECK_ATTEMPTS gives real transient failures
 * room to clear on their own before falling back to that same manual
 * "Retry check" dashboard action for whatever's left.
 */
class PollRailwayDeployments extends Command
{
    protected $signature = 'railway:poll-deployments';

    protected $description = 'Check Railway deployment status for every instance still marked deploying.';

    /** Consecutive FAILED/CRASHED readings tolerated before giving up — each check is ~1 minute apart. */
    private const MAX_CHECK_ATTEMPTS = 10;

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
                'FAILED', 'CRASHED' => $this->markFailedOrRetry($instance, $status),
                default => null, // still building/deploying — check again next minute
            };
        }

        return self::SUCCESS;
    }

    private function markReady(Instance $instance): void
    {
        $instance->forceFill([
            'provisioning_status'         => 'ready',
            'provisioning_error'          => null,
            'provisioning_check_attempts' => 0,
        ])->save();

        $email = $instance->license?->customer?->email;
        if ($email !== null) {
            Mail::to($email)->send(new InstanceReadyMail($instance, $email));
        }
    }

    private function markFailedOrRetry(Instance $instance, string $status): void
    {
        $attempts = $instance->provisioning_check_attempts + 1;

        if ($attempts < self::MAX_CHECK_ATTEMPTS) {
            // Stay 'deploying' — next minute's run tries again. Railway's
            // own restart policy may already be recovering the container.
            $instance->forceFill([
                'provisioning_check_attempts' => $attempts,
                'provisioning_error'          => "Railway deployment status: {$status} (check {$attempts}/".self::MAX_CHECK_ATTEMPTS.")",
            ])->save();

            return;
        }

        $instance->forceFill([
            'provisioning_status' => 'failed',
            'provisioning_error'  => "Railway deployment status: {$status} after ".self::MAX_CHECK_ATTEMPTS.' checks — needs manual attention.',
        ])->save();
    }
}
