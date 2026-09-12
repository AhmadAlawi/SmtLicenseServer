<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Local mirror of a Stripe Coupon + PromotionCode pair — Stripe is the
 * actual source of truth for discount logic (percent/amount off,
 * duration), this table is just what lets staff list/manage codes and
 * lets the signup wizard validate a typed code without an extra Stripe
 * round-trip for the common "does this code exist and is it active" check.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('stripe_coupon_id');
            $table->string('stripe_promotion_code_id');
            $table->enum('discount_type', ['percent', 'amount']);
            // Percent: whole number 1-100. Amount: minor units (cents), USD only — an
            // amount-off coupon in a multi-currency Stripe setup needs one amount per
            // currency, out of scope for a first version; percent-off works everywhere.
            $table->unsignedInteger('discount_value');
            // 'once' = first invoice only, 'repeating' = discount_in_months cycles then
            // full price resumes automatically (Stripe's own behavior, nothing to do on
            // our side when it lapses), 'forever' = every renewal, indefinitely.
            $table->enum('duration', ['once', 'repeating', 'forever']);
            $table->unsignedTinyInteger('duration_in_months')->nullable();
            $table->unsignedInteger('max_redemptions')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promo_codes');
    }
};
