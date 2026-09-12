<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\Plan;
use App\Services\Railway\RailwayClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** Manual overrides SMTGROUP support/ops needs (SaaS conversion plan Phase 4/7). */
class LicenseController extends Controller
{
    public function index(): View
    {
        $licenses = License::query()->with(['customer', 'plan', 'instance'])->latest()->paginate(25);
        $plans    = Plan::query()->orderBy('seat_limit')->get(['id', 'name']);

        return view('dashboard.licenses.index', compact('licenses', 'plans'));
    }

    public function suspend(License $license): RedirectResponse
    {
        $license->update(['status' => 'suspended']);

        return back()->with('status', "License #{$license->id} suspended.");
    }

    public function reinstate(License $license): RedirectResponse
    {
        $license->update(['status' => 'valid', 'grace_until' => null]);

        return back()->with('status', "License #{$license->id} reinstated.");
    }

    /** Suspected-compromise response: mint a new secret, the instance's next phone-home fails until its .env is updated with it. */
    public function reissueSecret(License $license): RedirectResponse
    {
        $secret = Str::random(64);
        $license->update(['hmac_secret' => $secret]);

        return back()->with('status', "New secret for license #{$license->id}: {$secret} (shown once — update the instance's LICENSE_HMAC_SECRET).");
    }

    public function extendGrace(License $license): RedirectResponse
    {
        $license->update(['grace_until' => now()->addDays(14)]);

        return back()->with('status', "Grace period for license #{$license->id} extended to {$license->grace_until->toDateString()}.");
    }

    /**
     * Staff-driven plan change (upgrade/downgrade a customer's own
     * subscription without going through Stripe Checkout again) — takes
     * effect immediately on the license row; the instance picks up the new
     * seat_limit/features on its next phone-home (max ~30 days per the
     * "never lock the app" recheck cadence, or immediately if staff also
     * tells the customer to hit "Re-check now" in their own panel).
     */
    public function changePlan(Request $request, License $license): RedirectResponse
    {
        $data = $request->validate(['plan_id' => ['required', 'integer', Rule::exists('plans', 'id')]]);

        $plan = Plan::findOrFail($data['plan_id']);
        $license->update(['plan_id' => $plan->id]);

        return back()->with('status', "License #{$license->id} moved to plan \"{$plan->name}\".");
    }

    /**
     * Re-arm an instance PollRailwayDeployments gave up on. Confirmed live
     * (2026-09-12): once an instance flips to 'failed', the poller's own
     * query (`where provisioning_status = 'deploying'`) permanently
     * excludes it — even after staff fix the underlying Railway problem
     * and the deployment genuinely succeeds, nothing ever re-checks it or
     * sends the customer their confirmation email. This puts it back in
     * the poller's queue so the next scheduled run (every minute) picks
     * up Railway's REAL current status rather than assuming success here.
     */
    public function retryProvisioning(License $license): RedirectResponse
    {
        $instance = $license->instance;
        if ($instance === null) {
            return back()->withErrors(['retry' => 'This license has no instance yet.']);
        }

        $instance->forceFill(['provisioning_status' => 'deploying', 'provisioning_error' => null])->save();

        return back()->with('status', "License #{$license->id}'s instance re-queued for a status check (next run within a minute).");
    }

    /**
     * Register a customer's own separate domain (e.g. ahmadpos.com — not a
     * subdomain of the platform's own domain) on their Railway service.
     * Staff-driven (SaaS conversion plan Phase 7 decision): shows the
     * required CNAME target here for staff to relay to the customer — no
     * customer self-service portal yet.
     */
    public function addCustomDomain(Request $request, License $license, RailwayClient $railway): RedirectResponse
    {
        $data = $request->validate(['domain' => ['required', 'string', 'max:255']]);

        $instance = $license->instance;
        if ($instance === null || $instance->railway_service_id === null) {
            return back()->withErrors(['domain' => 'This license has no provisioned Railway service yet.']);
        }

        try {
            $records = $railway->addCustomDomain(
                (string) config('services.railway.project_id'),
                (string) config('services.railway.environment_id'),
                $instance->railway_service_id,
                $data['domain'],
            );
        } catch (\Throwable $e) {
            return back()->withErrors(['domain' => "Railway rejected this domain: {$e->getMessage()}"]);
        }

        $instance->update(['custom_domain' => $data['domain'], 'custom_domain_dns_target' => $records['cname']['value']]);

        // Railway won't issue a TLS cert without BOTH records — a customer
        // who only adds the CNAME (the obvious one) will see the same
        // stuck-at-VALIDATING_OWNERSHIP problem this fix addresses for the
        // automatic white-label subdomain path.
        $message = "Domain {$data['domain']} registered. Tell the customer to add a CNAME: {$data['domain']} -> {$records['cname']['value']}";
        $message .= $records['txt'] !== null
            ? ", and a TXT record: {$records['txt']['fqdn']} -> {$records['txt']['value']} (required for Railway to issue the certificate)."
            : '.';

        return back()->with('status', $message);
    }
}
