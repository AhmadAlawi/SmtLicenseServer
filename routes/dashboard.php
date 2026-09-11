<?php

use App\Http\Controllers\Dashboard\AuthController;
use App\Http\Controllers\Dashboard\CustomerController;
use App\Http\Controllers\Dashboard\ForgotPasswordController;
use App\Http\Controllers\Dashboard\LicenseController;
use App\Http\Controllers\Dashboard\PlanController;
use App\Http\Controllers\Dashboard\SignupController;
use Illuminate\Support\Facades\Route;

/**
 * Platform-owner (SMTGROUP staff) dashboard — SaaS conversion plan Phase 4.
 * Auth is the plain `users` table + session guard already in the Laravel
 * skeleton; deliberately separate from `customers` (Cashier-billable POS
 * shop owners) so a customer can never end up with dashboard access.
 */
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'create'])->name('login');
    Route::post('login', [AuthController::class, 'store']);

    Route::get('forgot-password', [ForgotPasswordController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [ForgotPasswordController::class, 'store'])->name('password.email');
    Route::get('reset-password/{token}', [ForgotPasswordController::class, 'edit'])->name('password.reset');
    Route::post('reset-password', [ForgotPasswordController::class, 'update'])->name('password.update');
});

Route::middleware('auth')->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

    Route::get('/', [CustomerController::class, 'index'])->name('home');

    Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');

    Route::get('signups', [SignupController::class, 'index'])->name('signups.index');

    Route::get('licenses', [LicenseController::class, 'index'])->name('licenses.index');
    Route::post('licenses/{license}/suspend', [LicenseController::class, 'suspend'])->name('licenses.suspend');
    Route::post('licenses/{license}/reinstate', [LicenseController::class, 'reinstate'])->name('licenses.reinstate');
    Route::post('licenses/{license}/reissue-secret', [LicenseController::class, 'reissueSecret'])->name('licenses.reissue-secret');
    Route::post('licenses/{license}/extend-grace', [LicenseController::class, 'extendGrace'])->name('licenses.extend-grace');
    Route::post('licenses/{license}/add-custom-domain', [LicenseController::class, 'addCustomDomain'])->name('licenses.add-custom-domain');

    Route::get('plans', [PlanController::class, 'index'])->name('plans.index');
    Route::get('plans/create', [PlanController::class, 'create'])->name('plans.create');
    Route::post('plans', [PlanController::class, 'store'])->name('plans.store');
    Route::get('plans/{plan}/edit', [PlanController::class, 'edit'])->name('plans.edit');
    Route::put('plans/{plan}', [PlanController::class, 'update'])->name('plans.update');
    Route::post('plans/{plan}/toggle-active', [PlanController::class, 'toggleActive'])->name('plans.toggle-active');
});
