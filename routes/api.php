<?php

use App\Http\Controllers\Api\InstanceRegisterController;
use App\Http\Controllers\Api\InstanceValidateController;
use Illuminate\Support\Facades\Route;

/**
 * SaaS conversion plan Phase 0/1 contract. Both routes are rate-limited —
 * /validate per license key (via a custom throttle key so one abusive
 * instance can't exhaust another's bucket) and per IP; /register is a
 * single shared-secret endpoint the provisioning pipeline alone calls, so a
 * tighter IP-only throttle is enough there.
 */
Route::prefix('v1/instances')->group(function () {
    Route::post('validate', InstanceValidateController::class)
        ->middleware(['throttle:license-validate', 'verify.license.signature']);

    Route::post('register', InstanceRegisterController::class)
        ->middleware(['throttle:60,1', 'verify.provisioning.token']);
});
