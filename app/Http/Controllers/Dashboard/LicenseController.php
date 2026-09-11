<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Services\Railway\RailwayClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

/** Manual overrides SMTGROUP support/ops needs (SaaS conversion plan Phase 4/7). */
class LicenseController extends Controller
{
    public function index(): View
    {
        $licenses = License::query()->with(['customer', 'plan', 'instance'])->latest()->paginate(25);

        return view('dashboard.licenses.index', compact('licenses'));
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
        return back()->with('status', "Domain {$data['domain']} registered. Tell the customer to add TWO DNS records: CNAME {$data['domain']} -> {$records['cname']['value']}, and TXT {$records['txt']['fqdn']} -> {$records['txt']['value']} (required for Railway to issue the certificate).");
    }
}
