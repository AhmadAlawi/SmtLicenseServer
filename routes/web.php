<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\UpdateFeedController;
use Illuminate\Support\Facades\Route;
use Laravel\Cashier\Http\Middleware\VerifyWebhookSignature;

// Marketing site (SaaS conversion plan Phase 8) — the actual front door.
// /signup below is the wizard; landing's pricing section links into it.
Route::get('/', [LandingController::class, 'index'])->name('home');

// SEO/comparison landing page (marketing-ideas skill, #11 — competitor
// comparison page) — targets "foodics alternative" search intent.
Route::get('foodics-alternative', [LandingController::class, 'foodicsAlternative'])->name('foodics-alternative');

// SEO/comparison landing page — targets "vtech alternative" search intent
// (V-TECH / vtech-sys.com, a Jordan/KSA/Kuwait/UAE POS+ERP vendor).
Route::get('vtech-alternative', [LandingController::class, 'vtechAlternative'])->name('vtech-alternative');

// SEO content — gives the sitemap real size and organic search something
// to index beyond the handful of static marketing pages.
Route::get('blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('blog/{blogPost:slug}', [BlogController::class, 'show'])->name('blog.show');

Route::get('sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

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

// Public update feed — every SaasPOS tenant's own updater engine polls
// this (see UpdateFeedController). No auth; nothing here is sensitive
// beyond the release binaries themselves, which are meant to be
// downloadable by any tenant.
Route::get('updates/feed.json', [UpdateFeedController::class, 'feed'])->name('updates.feed');
Route::get('updates/download/{release}', [UpdateFeedController::class, 'download'])->name('updates.download');

// TEMPORARY diagnostic — a real tenant's DB got stuck mid-migration from a
// crash-loop (some migrations applied, `migrations` tracking table never
// caught up, every retry then fails on "table already exists" forever).
// No real customer data exists yet on a signup that never finished
// provisioning, so wiping and letting migrate run clean is safe. This
// connects directly to the tenant's own DB over Railway's private network
// (this app and every tenant DB share the same project/environment) — NOT
// something this app would ever need to do again once fixed. Remove after
// use. Guarded by both the app key and an explicit host/database match so
// a typo can't nuke the wrong thing.
Route::get('__diag-reset-tenant-db', function (\Illuminate\Http\Request $request) {
    abort_unless($request->query('key') === config('app.key'), 404);

    $host = $request->query('host');
    $db   = $request->query('db');
    $user = $request->query('user');
    $pass = $request->query('pass');

    abort_unless($host && $db && $user && $pass, 400);

    try {
        $pdo = new \PDO("mysql:host={$host};port=3306", $user, $pass, [\PDO::ATTR_TIMEOUT => 10]);
        $pdo->exec("DROP DATABASE IF EXISTS `{$db}`");
        $pdo->exec("CREATE DATABASE `{$db}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

        return response()->json(['ok' => true, 'message' => "Dropped and recreated {$db} on {$host}."]);
    } catch (\Throwable $e) {
        return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
    }
});

require __DIR__.'/dashboard.php';
