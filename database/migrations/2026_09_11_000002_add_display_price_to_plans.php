<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Marketing-facing price string (e.g. "$79") shown on the landing/wizard pricing cards — separate from stripe_price_id, which is the actual source of truth for what Stripe charges. Avoids a live Stripe API call on every page load. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->string('display_price', 32)->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('display_price');
        });
    }
};
