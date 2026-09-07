<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Holds signup details collected on the /pricing form (company name, admin
 * name/email/password, chosen subdomain slug) between "customer submitted
 * the form" and "Stripe confirms payment". Only an opaque `token` — never
 * the admin password — goes into Stripe Checkout session metadata; Stripe's
 * dashboard/webhook logs are not a safe place for a raw password. The row
 * is deleted once ProvisionInstance has read it (SaaS conversion plan Phase 7).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pending_signups', function (Blueprint $table) {
            $table->id();
            $table->string('token', 64)->unique();
            $table->string('plan_code');
            $table->string('subdomain_slug');
            $table->string('company_name');
            $table->string('admin_name');
            $table->string('admin_email');
            $table->text('admin_password'); // encrypted cast on the model
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pending_signups');
    }
};
