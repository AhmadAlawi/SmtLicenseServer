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

// TEMP: one-off DB export for the Railway->VPS migration. Streams a
// mysqldump of this instance's own DB back over HTTPS so it can be
// restored on the new host. Remove immediately after use.
Route::get('_tmp/export-db', function (\Illuminate\Http\Request $request) {
    abort_unless($request->query('secret') === 'vps-migrate-2026-10-05', 403);
    $pdo = \Illuminate\Support\Facades\DB::connection()->getPdo();
    $db = config('database.connections.mysql.database');
    $tables = $pdo->query('SHOW TABLES')->fetchAll(\PDO::FETCH_COLUMN);
    $sql = "SET FOREIGN_KEY_CHECKS=0;\n";
    foreach ($tables as $table) {
        $create = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch(\PDO::FETCH_ASSOC);
        $sql .= "DROP TABLE IF EXISTS `{$table}`;\n" . $create['Create Table'] . ";\n";
        $rows = $pdo->query("SELECT * FROM `{$table}`")->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            $cols = array_map(fn($c) => "`{$c}`", array_keys($row));
            $vals = array_map(fn($v) => $v === null ? 'NULL' : $pdo->quote((string) $v), array_values($row));
            $sql .= "INSERT INTO `{$table}` (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ");\n";
        }
    }
    $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
    return response($sql, 200, [
        'Content-Type' => 'application/sql',
        'Content-Disposition' => 'attachment; filename="license_server.sql"',
    ]);
});

require __DIR__.'/dashboard.php';
