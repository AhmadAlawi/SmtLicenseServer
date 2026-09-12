<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Country/currency-based pricing (the header currency dropdown on the
 * landing page). `stripe_price_id` stays the USD default (unchanged,
 * every existing caller keeps working); this adds the other currencies
 * as their own real Stripe Price objects, since a Checkout Session picks
 * exactly one Price ID — no live-conversion trick, just a real price per
 * currency staff sets (same manual-entry philosophy as display_price).
 *
 * Shape: {"JOD": {"amount": 3900, "stripe_price_id": "price_..."}, ...}
 * — amount is in the currency's smallest unit (fils/halalas/piastres),
 * matching Stripe's own unit_amount convention.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->json('currency_prices')->nullable()->after('stripe_price_id');
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('currency_prices');
        });
    }
};
