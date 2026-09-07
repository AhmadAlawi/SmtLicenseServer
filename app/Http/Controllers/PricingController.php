<?php

namespace App\Http\Controllers;

use App\Mail\VerifySignupMail;
use App\Models\Customer;
use App\Models\Instance;
use App\Models\PendingSignup;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Laravel\Cashier\Checkout;

/**
 * Public purchase flow (SaaS conversion plan Phase 5/7).
 *
 * Two steps, not one: submitting the form does NOT go straight to Stripe —
 * it emails a signed confirmation link first (`store()` → `verify-email`
 * view), and clicking THAT link is what actually redirects to Stripe
 * Checkout (`verify()`). This stops a typo'd/fake email from burning a
 * Stripe Checkout session for nothing, and matches "email verification"
 * literally rather than relying on Stripe's own receipt as a stand-in.
 *
 * Neither the license nor the actual instance is created here —
 * {@see StripeWebhookController::handleCheckoutSessionCompleted} does that
 * once Stripe confirms payment, reading back the signup details this
 * controller stashed in `pending_signups` (never Stripe metadata itself,
 * which is visible in the Stripe dashboard — no place for a raw admin
 * password).
 */
class PricingController extends Controller
{
    public function index(): View
    {
        $plans = Plan::query()->where('is_active', true)->whereNotNull('stripe_price_id')->orderBy('seat_limit')->get();

        return view('pricing.index', ['plans' => $plans, 'rootDomain' => config('services.platform.root_domain')]);
    }

    public function store(Request $request): View|RedirectResponse
    {
        $data = $request->validate([
            'plan'           => ['required', 'exists:plans,code'],
            'subdomain'      => ['required', 'string', 'min:3', 'max:30', 'regex:/^[a-z0-9]+(-[a-z0-9]+)*$/', Rule::unique('instances', 'subdomain_slug')],
            'company_name'   => ['required', 'string', 'max:255'],
            'admin_name'     => ['required', 'string', 'max:255'],
            'admin_email'    => ['required', 'email', 'max:255'],
            'admin_password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $plan = Plan::query()->where('code', $data['plan'])->where('is_active', true)->firstOrFail();
        if ($plan->stripe_price_id === null) {
            return back()->withErrors(['plan' => 'This plan is not yet available for online purchase.']);
        }

        $signup = PendingSignup::query()->create([
            'token'          => PendingSignup::makeToken(),
            'plan_code'      => $plan->code,
            'subdomain_slug' => $data['subdomain'],
            'company_name'   => $data['company_name'],
            'admin_name'     => $data['admin_name'],
            'admin_email'    => $data['admin_email'],
            'admin_password' => $data['admin_password'],
        ]);

        $verifyUrl = URL::temporarySignedRoute('pricing.verify', now()->addHours(24), ['token' => $signup->token]);

        Mail::to($data['admin_email'])->send(new VerifySignupMail($signup, $verifyUrl));

        return view('pricing.check-email', ['email' => $data['admin_email']]);
    }

    /** The signed link from VerifySignupMail — this is what actually starts Stripe Checkout. */
    public function verify(Request $request, string $token): RedirectResponse|Checkout
    {
        // The `signed` route middleware already 403'd an invalid/expired
        // link before this runs.
        $signup = PendingSignup::query()->where('token', $token)->firstOrFail();
        $plan   = Plan::query()->where('code', $signup->plan_code)->firstOrFail();

        // Re-check availability — the slug could have been taken by
        // someone else between form submission and clicking the link.
        if (Instance::query()->where('subdomain_slug', $signup->subdomain_slug)->exists()) {
            return redirect()->route('pricing.index')->withErrors(['subdomain' => 'That subdomain was taken while your email confirmation was pending. Please sign up again.']);
        }

        $customer = Customer::query()->firstOrCreate(
            ['email' => $signup->admin_email],
            ['name' => $signup->company_name],
        );

        return $customer
            ->newSubscription('default', $plan->stripe_price_id)
            ->checkout([
                'success_url' => route('pricing.success'),
                'cancel_url'  => route('pricing.index'),
                'metadata'    => ['signup_token' => $signup->token],
            ]);
    }

    public function success(): View
    {
        return view('pricing.success');
    }

    /** Live availability check for the subdomain field (AJAX from the pricing form). */
    public function checkSubdomain(Request $request): \Illuminate\Http\JsonResponse
    {
        $slug = (string) $request->query('subdomain', '');
        $valid = preg_match('/^[a-z0-9]+(-[a-z0-9]+)*$/', $slug) === 1 && strlen($slug) >= 3 && strlen($slug) <= 30;
        $available = $valid && ! Instance::query()->where('subdomain_slug', $slug)->exists();

        return response()->json(['valid' => $valid, 'available' => $available]);
    }
}
