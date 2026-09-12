<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cashier's stock migration hardcodes `user_id`, but CASHIER_MODEL is
 * App\Models\Customer here — Cashier's Billable::subscriptions() joins on
 * $this->getForeignKey(), which for a Customer model resolves to
 * `customer_id`, not `user_id`. Every subscription-lifecycle webhook
 * (renewals, cancellations, past-due, etc.) has been querying a column
 * that doesn't exist since this app went live.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'stripe_status']);
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->renameColumn('user_id', 'customer_id');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->index(['customer_id', 'stripe_status']);
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropIndex(['customer_id', 'stripe_status']);
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->renameColumn('customer_id', 'user_id');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->index(['user_id', 'stripe_status']);
        });
    }
};
