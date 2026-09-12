<?php

use App\Http\Middleware\CaptureUtmParameters;
use App\Http\Middleware\VerifyLicenseSignature;
use App\Http\Middleware\VerifyProvisioningToken;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Railway terminates TLS at its own edge and forwards to this
        // container over plain HTTP — without trusting the proxy headers,
        // Laravel thinks every request is insecure. Confirmed live
        // (2026-09-12): generated URLs (e.g. the update feed's
        // download_url) came out as http:// instead of https://. `*`
        // trusts Railway's edge (the only thing that can reach this
        // container directly); harmless since there's no other network
        // path to it.
        $middleware->trustProxies(at: '*');

        $middleware->web(append: [CaptureUtmParameters::class]);

        $middleware->alias([
            'verify.license.signature'  => VerifyLicenseSignature::class,
            'verify.provisioning.token' => VerifyProvisioningToken::class,
        ]);

        // Stripe can't send Laravel's CSRF token — its own
        // Stripe-Signature header (verified by Cashier's
        // VerifyWebhookSignature middleware) is the real auth here.
        $middleware->validateCsrfTokens(except: ['stripe/webhook']);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
