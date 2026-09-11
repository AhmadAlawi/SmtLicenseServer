<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Signup wizard additions (SaaS conversion plan Phase 8): shop logo + branch count, collected at signup, carried into ProvisionInstance. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pending_signups', function (Blueprint $table) {
            $table->unsignedInteger('branch_count')->nullable()->after('company_name');
            $table->string('logo_path')->nullable()->after('branch_count');
        });
    }

    public function down(): void
    {
        Schema::table('pending_signups', function (Blueprint $table) {
            $table->dropColumn(['branch_count', 'logo_path']);
        });
    }
};
