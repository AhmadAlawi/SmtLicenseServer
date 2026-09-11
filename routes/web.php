<?php

use App\Http\Controllers\LandingController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\StripeWebhookController;
use Illuminate\Support\Facades\Route;
use Laravel\Cashier\Http\Middleware\VerifyWebhookSignature;

// Marketing site (SaaS conversion plan Phase 8) — the actual front door.
// /signup below is the wizard; landing's pricing section links into it.
Route::get('/', [LandingController::class, 'index'])->name('home');

// SEO/comparison landing page (marketing-ideas skill, #11 — competitor
// comparison page) — targets "foodics alternative" search intent.
Route::get('foodics-alternative', [LandingController::class, 'foodicsAlternative'])->name('foodics-alternative');

// Signup wizard. No auth — a prospective customer doesn't have an account
// yet; the license itself is minted by the Stripe webhook once payment is
// confirmed, not here.
Route::get('pricing', [PricingController::class, 'index'])->name('signup.index');
Route::get('pricing/check-subdomain', [PricingController::class, 'checkSubdomain'])->name('signup.check-subdomain');
Route::post('pricing/signup', [PricingController::class, 'store'])->name('signup.store');
// Signed — the emailed link itself IS the auth; `signed` middleware 403s an invalid/expired/tampered one.
Route::get('pricing/verify/{token}', [PricingController::class, 'verify'])->middleware('signed')->name('signup.verify');
Route::get('pricing/success', [PricingController::class, 'success'])->name('signup.success');

// Cashier's own auto-registration is disabled (AppServiceProvider::register())
// so this route — same path Cashier would have used — can point at
// StripeWebhookController, which extends Cashier's handling with
// license/plan sync (SaaS conversion plan Phase 1).
Route::post('stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])
    ->middleware(VerifyWebhookSignature::class)
    ->name('cashier.webhook');

// TEMPORARY diagnostic — reads back a pending signup's token without email
// access, for multi-plan provisioning tests. Guarded by a shared secret via
// query param, not left in permanently. Remove after testing.
Route::get('__diag-signup-token', function (\Illuminate\Http\Request $request) {
    abort_unless($request->query('key') === config('app.key'), 404);
    $signup = \App\Models\PendingSignup::query()->where('subdomain_slug', $request->query('slug'))->first();

    return response()->json($signup ? ['token' => $signup->token] : ['error' => 'not found']);
});

// TEMPORARY diagnostic — production was never seeded (only `migrate --force`
// runs on boot, not db:seed). Seeds the staff user + real plans + 4 test
// plans in one shot. One-time use, remove after testing.
Route::get('__diag-seed', function (\Illuminate\Http\Request $request) {
    abort_unless($request->query('key') === config('app.key'), 404);

    $log = [];

    try {
        if (! \App\Models\User::where('email', 'ahmad.alalawi@smt.com.jo')->exists()) {
            \App\Models\User::create([
                'name' => 'SMTGROUP Admin',
                'email' => 'ahmad.alalawi@smt.com.jo',
                'password' => bcrypt('change-me-now'),
            ]);
            $log[] = 'staff user created';
        } else {
            $log[] = 'staff user already exists';
        }

        (new \Database\Seeders\PlansSeeder())->run();
        $log[] = 'PlansSeeder ran';

        $testPlans = [
            ['code' => 'test1', 'name' => 'Test Plan 1', 'display_price' => '$10', 'seat_limit' => 2, 'features' => ['multi_store' => false, 'advanced_reporting' => false], 'stripe_price_id' => 'price_1UETvxDyFexUg5KSas2UryOa'],
            ['code' => 'test2', 'name' => 'Test Plan 2', 'display_price' => '$10', 'seat_limit' => 5, 'features' => ['multi_store' => true, 'advanced_reporting' => false], 'stripe_price_id' => 'price_1UETvzDyFexUg5KSmJXx0wsD'],
            ['code' => 'test3', 'name' => 'Test Plan 3', 'display_price' => '$10', 'seat_limit' => 10, 'features' => ['multi_store' => true, 'advanced_reporting' => true], 'stripe_price_id' => 'price_1UETw1DyFexUg5KSnaDxWVVg'],
            ['code' => 'test4', 'name' => 'Test Plan Unlimited', 'display_price' => '$10', 'seat_limit' => null, 'features' => ['multi_store' => true, 'advanced_reporting' => true], 'stripe_price_id' => 'price_1UETw2DyFexUg5KStjIM0QQI'],
        ];
        foreach ($testPlans as $p) {
            $code = $p['code'];
            unset($p['code']);
            $p['is_active'] = true;
            \App\Models\Plan::updateOrCreate(['code' => $code], $p);
        }
        $log[] = '4 test plans created';

        return response()->json(['log' => $log, 'plans' => \App\Models\Plan::pluck('code')]);
    } catch (\Throwable $e) {
        return response()->json([
            'log' => $log,
            'error' => $e->getMessage(),
            'file' => $e->getFile().':'.$e->getLine(),
            'trace' => collect($e->getTrace())->take(5),
        ], 500);
    }
});

// TEMPORARY diagnostic — signup POST returns bare 500 (mail send is the
// prime suspect, SMTP config just got wired). Test mail delivery in
// isolation. Remove after testing.
Route::get('__diag-mail', function (\Illuminate\Http\Request $request) {
    abort_unless($request->query('key') === config('app.key'), 404);

    // Raw socket test first — isolates "can this container reach the SMTP
    // host/port at all" from "does Laravel's mail config work", since the
    // symptom (60s+ hang, MaxAttemptsExceededException) matches an
    // outbound connection that's being silently dropped, not refused.
    $socketResult = null;
    $start = microtime(true);
    $fp = @fsockopen(config('mail.mailers.smtp.host'), (int) config('mail.mailers.smtp.port'), $errno, $errstr, 8);
    $elapsed = round(microtime(true) - $start, 2);
    if ($fp) {
        $banner = fgets($fp, 512);
        fclose($fp);
        $socketResult = ['connected' => true, 'elapsed_s' => $elapsed, 'banner' => trim((string) $banner)];
    } else {
        $socketResult = ['connected' => false, 'elapsed_s' => $elapsed, 'errno' => $errno, 'errstr' => $errstr];
    }

    // Return here if only the socket test is wanted — Mail::raw() below has
    // no timeout of its own and can hang well past this request's client
    // timeout, so the two need to be checkable independently.
    if ($request->boolean('socket_only')) {
        return response()->json(['socket' => $socketResult]);
    }

    try {
        \Illuminate\Support\Facades\Mail::raw('diag test', function ($m) {
            $m->to('ahmad.alalawi@smt.com.jo')->subject('diag test');
        });

        return response()->json(['socket' => $socketResult, 'mail' => ['ok' => true]]);
    } catch (\Throwable $e) {
        return response()->json([
            'socket' => $socketResult,
            'mail' => [
                'error' => $e->getMessage(),
                'class' => get_class($e),
                'file'  => $e->getFile().':'.$e->getLine(),
            ],
        ], 500);
    }
});

// TEMPORARY diagnostic — no confirmation emails arriving, need to see
// real jobs/failed_jobs table state (SMTP was confirmed hanging earlier
// via __diag-mail). Remove after testing.
Route::get('__diag-queue', function (\Illuminate\Http\Request $request) {
    abort_unless($request->query('key') === config('app.key'), 404);

    return response()->json([
        'pending' => \Illuminate\Support\Facades\DB::table('jobs')->get(['id', 'queue', 'attempts', 'reserved_at', 'available_at', 'created_at']),
        'failed'  => \Illuminate\Support\Facades\DB::table('failed_jobs')->orderByDesc('id')->limit(10)->get(['id', 'queue', 'exception', 'failed_at']),
    ]);
});

require __DIR__.'/dashboard.php';
