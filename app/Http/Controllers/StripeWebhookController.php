<?php

namespace App\Http\Controllers;

use App\Actions\CreateLicenseForCustomer;
use App\Jobs\ProvisionInstance;
use App\Mail\OrderConfirmationMail;
use App\Models\Customer;
use App\Models\License;
use App\Models\PendingSignup;
use App\Models\Plan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Laravel\Cashier\Cashier;
use Laravel\Cashier\Http\Controllers\WebhookController;

/**
 * Extends Cashier's own webhook handling (subscription CRUD, invoice
 * events, payment-method sync — all handled by the parent) with what a
 * plain Cashier install doesn't need (SaaS conversion plan Phase 1/5/7):
 *
 *  - `checkout.session.completed` is what actually kicks off provisioning
 *    for a BRAND NEW customer coming through the public /pricing → Stripe
 *    Checkout flow — mints their first license via the same
 *    {@see CreateLicenseForCustomer} action /register uses, then dispatches
 *    {@see ProvisionInstance} to stand up their actual running instance.
 *  - subscription updated/deleted push plan/status changes onto every one
 *    of that customer's `licenses` rows, so a Stripe-side upgrade/
 *    downgrade/cancellation reaches the running instance(s) without
 *    waiting for their next scheduled phone-home.
 *
 * Register in routes/web.php per Cashier's docs — signature verification
 * is Cashier's own `Cashier::verifySignature` middleware, already wired by
 * the package's route.
 */
class StripeWebhookController extends WebhookController
{
    public function handleCheckoutSessionCompleted(array $payload): \Symfony\Component\HttpFoundation\Response
    {
        $object = $payload['data']['object'] ?? [];
        $stripeCustomerId = $object['customer'] ?? null;
        $signupToken      = $object['metadata']['signup_token'] ?? null;
        $email            = $object['customer_details']['email'] ?? $object['customer_email'] ?? null;
        $name             = $object['customer_details']['name'] ?? $email;

        if ($stripeCustomerId === null || $signupToken === null || $email === null) {
            Log::warning('Stripe checkout.session.completed missing expected fields.', ['object' => $object]);

            return $this->successMethod();
        }

        $signup = PendingSignup::query()->where('token', $signupToken)->first();

        // Stripe redelivers webhooks even after a prior 2xx. The signup row
        // is deleted once consumed (below), so a redelivery lands here with
        // no row — that's expected, not an error: it means this event was
        // already fully handled. Only warn if there's ALSO no license yet
        // (which would mean the first delivery genuinely failed partway).
        if ($signup === null) {
            $alreadyLicensed = License::query()
                ->whereHas('customer', fn ($q) => $q->where('stripe_id', $stripeCustomerId))
                ->exists();

            if (! $alreadyLicensed) {
                Log::warning('Stripe checkout.session.completed: no pending signup and no license — cannot provision.', ['signup_token' => $signupToken]);
            }

            return $this->successMethod();
        }

        // Cashier's own customer.* handlers may not have run yet (event
        // ordering isn't guaranteed) — firstOrCreate by email rather than
        // requiring Cashier to have already linked the stripe_id.
        $customer = Cashier::findBillable($stripeCustomerId)
            ?? Customer::query()->firstOrCreate(['email' => $email], ['name' => $name, 'stripe_id' => $stripeCustomerId]);

        if ($customer->stripe_id === null) {
            $customer->forceFill(['stripe_id' => $stripeCustomerId])->save();
        }

        $plan = Plan::query()->where('code', $signup->plan_code)->first();
        if ($plan === null) {
            Log::warning('Stripe checkout.session.completed: unknown plan_code.', ['plan_code' => $signup->plan_code]);

            return $this->successMethod();
        }

        $result = app(CreateLicenseForCustomer::class)($customer, $plan, "{$signup->subdomain_slug}.".config('services.platform.root_domain'));

        Mail::to($customer->email)->queue(new OrderConfirmationMail($customer, $plan, $signup->subdomain_slug, (string) config('services.platform.root_domain')));

        ProvisionInstance::dispatch(
            $result['license'],
            $result['license_key'],
            $result['hmac_secret'],
            [
                'subdomain_slug' => $signup->subdomain_slug,
                'company_name'   => $signup->company_name,
                'branch_count'   => $signup->branch_count,
                'logo_url'       => $signup->logo_path ? Storage::disk('public')->url($signup->logo_path) : null,
                'admin_name'     => $signup->admin_name,
                'admin_email'    => $signup->admin_email,
                'admin_password' => $signup->admin_password,
            ],
        );

        $signup->delete();

        return $this->successMethod();
    }

    public function handleCustomerSubscriptionUpdated(array $payload): \Symfony\Component\HttpFoundation\Response
    {
        $this->syncLicensesForSubscription($payload, 'valid');

        return parent::handleCustomerSubscriptionUpdated($payload);
    }

    public function handleCustomerSubscriptionDeleted(array $payload): \Symfony\Component\HttpFoundation\Response
    {
        $this->syncLicensesForSubscription($payload, 'suspended');

        return parent::handleCustomerSubscriptionDeleted($payload);
    }

    private function syncLicensesForSubscription(array $payload, string $fallbackStatus): void
    {
        $stripeCustomerId = $payload['data']['object']['customer'] ?? null;
        $stripePriceId    = $payload['data']['object']['items']['data'][0]['price']['id'] ?? null;
        $subStatus        = $payload['data']['object']['status'] ?? null;

        if ($stripeCustomerId === null) {
            return;
        }

        $customer = Cashier::findBillable($stripeCustomerId);
        if ($customer === null) {
            Log::warning('Stripe webhook: no matching customer.', ['stripe_customer_id' => $stripeCustomerId]);

            return;
        }

        $plan = $stripePriceId !== null
            ? Plan::query()->where('stripe_price_id', $stripePriceId)->first()
            : null;

        $status = match ($subStatus) {
            'active', 'trialing' => 'valid',
            'past_due', 'unpaid' => 'past_due',
            'canceled', 'incomplete_expired' => 'suspended',
            default => $fallbackStatus,
        };

        License::query()
            ->where('customer_id', $customer->id)
            ->update(array_filter([
                'plan_id' => $plan?->id,
                'status'  => $status,
            ], fn ($v) => $v !== null));
    }
}
