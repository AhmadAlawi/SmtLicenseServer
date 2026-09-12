<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** The header/wizard currency dropdown's selection, carried through to which Stripe Price the checkout actually uses (see PricingController::verify()). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pending_signups', function (Blueprint $table) {
            $table->string('currency', 3)->default('USD')->after('plan_code');
        });
    }

    public function down(): void
    {
        Schema::table('pending_signups', function (Blueprint $table) {
            $table->dropColumn('currency');
        });
    }
};
