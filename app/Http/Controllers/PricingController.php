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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Laravel\Cashier\Checkout;

/**
 * The signup wizard (SaaS conversion plan Phase 5/7/8) — reached from the
 * Tillora landing page's "Start now"/plan links, route names `signup.*`.
 *
 * Two steps, not one: submitting the wizard does NOT go straight to
 * Stripe — it emails a signed confirmation link first (`store()` →
 * `signup.check-email` view), and clicking THAT link is what actually
 * redirects to Stripe Checkout (`verify()`). This stops a typo'd/fake
 * email from burning a Stripe Checkout session for nothing, and matches
 * "email verification" literally rather than relying on Stripe's own
 * receipt as a stand-in.
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

        return view('signup.wizard', ['plans' => $plans, 'rootDomain' => config('services.platform.root_domain')]);
    }

    public function store(Request $request): View|RedirectResponse
    {
        $data = $request->validate([
            'plan'           => ['required', 'exists:plans,code'],
            'subdomain'      => ['required', 'string', 'min:3', 'max:30', 'regex:/^[a-z0-9]+(-[a-z0-9]+)*$/', Rule::unique('instances', 'subdomain_slug')],
            'company_name'   => ['required', 'string', 'max:255'],
            'branch_count'   => ['nullable', 'integer', 'min:1', 'max:500'],
            'logo'           => ['nullable', 'image', 'max:4096'],
            'admin_name'     => ['required', 'string', 'max:255'],
            'admin_email'    => ['required', 'email', 'max:255'],
            'admin_password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $plan = Plan::query()->where('code', $data['plan'])->where('is_active', true)->firstOrFail();
        if ($plan->stripe_price_id === null) {
            return back()->withErrors(['plan' => 'This plan is not yet available for online purchase.']);
        }

        // Stored on this app's own public disk — the tenant instance being
        // provisioned doesn't exist yet and has no storage of its own to
        // upload into. ProvisionInstance passes the resulting public URL
        // through as TENANT_LOGO_URL; TenantProvision (SaasPOS repo) fetches
        // it into the new instance's own storage the same way
        // ProvisionMeccaMall::fetchToPublicDisk() already does.
        // Not using UploadedFile::store()/storeAs() here — both rely on
        // getRealPath(), which returns false for the temp upload on this
        // (Windows) dev server, throwing "Path cannot be empty" from deep
        // inside FilesystemAdapter. getPathname() returns the raw tmp_name
        // without going through realpath() and works reliably — same
        // read-bytes-then-put pattern already used by
        // ProvisionMeccaMall::fetchToPublicDisk() / TenantProvision::fetchLogo().
        $logoPath = null;
        if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
            $logo = $request->file('logo');
            $filename = Str::random(24).'.'.$logo->getClientOriginalExtension();
            $logoPath = 'signup-logos/'.$filename;
            Storage::disk('public')->put($logoPath, file_get_contents($logo->getPathname()));
        }

        $utm = $request->session()->get('utm_attribution', []);

        $signup = PendingSignup::query()->create([
            'token'          => PendingSignup::makeToken(),
            'plan_code'      => $plan->code,
            'subdomain_slug' => $data['subdomain'],
            'company_name'   => $data['company_name'],
            'branch_count'   => $data['branch_count'] ?? null,
            'logo_path'      => $logoPath,
            'admin_name'     => $data['admin_name'],
            'admin_email'    => $data['admin_email'],
            'admin_password' => $data['admin_password'],
            'utm_source'     => $utm['utm_source'] ?? null,
            'utm_medium'     => $utm['utm_medium'] ?? null,
            'utm_campaign'   => $utm['utm_campaign'] ?? null,
            'utm_term'       => $utm['utm_term'] ?? null,
            'utm_content'    => $utm['utm_content'] ?? null,
            'landing_page'   => $utm['landing_page'] ?? null,
        ]);

        $verifyUrl = URL::temporarySignedRoute('signup.verify', now()->addHours(24), ['token' => $signup->token]);

        Mail::to($data['admin_email'])->queue(new VerifySignupMail($signup, $verifyUrl));

        return view('signup.check-email', ['email' => $data['admin_email']]);
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
            return redirect()->route('signup.index')->withErrors(['subdomain' => 'That subdomain was taken while your email confirmation was pending. Please sign up again.']);
        }

        $customer = Customer::query()->firstOrCreate(
            ['email' => $signup->admin_email],
            ['name' => $signup->company_name],
        );

        return $customer
            ->newSubscription('default', $plan->stripe_price_id)
            ->checkout([
                'success_url' => route('signup.success'),
                'cancel_url'  => route('signup.index'),
                'metadata'    => ['signup_token' => $signup->token],
            ]);
    }

    public function success(): View
    {
        return view('signup.success');
    }

    /** Live availability check for the subdomain field (AJAX from the wizard). */
    public function checkSubdomain(Request $request): \Illuminate\Http\JsonResponse
    {
        $slug = (string) $request->query('subdomain', '');
        $valid = preg_match('/^[a-z0-9]+(-[a-z0-9]+)*$/', $slug) === 1 && strlen($slug) >= 3 && strlen($slug) <= 30;
        $available = $valid && ! Instance::query()->where('subdomain_slug', $slug)->exists();

        return response()->json(['valid' => $valid, 'available' => $available]);
    }
}
