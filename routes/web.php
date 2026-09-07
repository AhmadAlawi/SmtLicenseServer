<?php

use App\Http\Controllers\PricingController;
use App\Http\Controllers\StripeWebhookController;
use Illuminate\Support\Facades\Route;
use Laravel\Cashier\Http\Middleware\VerifyWebhookSignature;

Route::get('/', [PricingController::class, 'index'])->name('home');

// Public purchase flow (SaaS conversion plan Phase 5). No auth — a
// prospective customer doesn't have an account yet; the license itself is
// minted by the Stripe webhook once payment is confirmed, not here.
Route::get('pricing', [PricingController::class, 'index'])->name('pricing.index');
Route::get('pricing/check-subdomain', [PricingController::class, 'checkSubdomain'])->name('pricing.check-subdomain');
Route::post('pricing/signup', [PricingController::class, 'store'])->name('pricing.signup');
// Signed — the emailed link itself IS the auth; `signed` middleware 403s an invalid/expired/tampered one.
Route::get('pricing/verify/{token}', [PricingController::class, 'verify'])->middleware('signed')->name('pricing.verify');
Route::get('pricing/success', [PricingController::class, 'success'])->name('pricing.success');

// Cashier's own auto-registration is disabled (AppServiceProvider::register())
// so this route — same path Cashier would have used — can point at
// StripeWebhookController, which extends Cashier's handling with
// license/plan sync (SaaS conversion plan Phase 1).
Route::post('stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])
    ->middleware(VerifyWebhookSignature::class)
    ->name('cashier.webhook');

require __DIR__.'/dashboard.php';
