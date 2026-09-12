<?php

use App\Http\Controllers\LandingController;
use App\Http\Controllers\PricingController;
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

require __DIR__.'/dashboard.php';
