<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** First-touch campaign attribution, captured by CaptureUtmParameters and read back for the dashboard signups list. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pending_signups', function (Blueprint $table) {
            $table->string('utm_source', 255)->nullable()->after('admin_password');
            $table->string('utm_medium', 255)->nullable()->after('utm_source');
            $table->string('utm_campaign', 255)->nullable()->after('utm_medium');
            $table->string('utm_term', 255)->nullable()->after('utm_campaign');
            $table->string('utm_content', 255)->nullable()->after('utm_term');
            $table->string('landing_page', 255)->nullable()->after('utm_content');
        });
    }

    public function down(): void
    {
        Schema::table('pending_signups', function (Blueprint $table) {
            $table->dropColumn(['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'landing_page']);
        });
    }
};
