<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * PollRailwayDeployments used to flip an instance to 'failed' on the very
 * first FAILED/CRASHED status it saw, permanently excluding it from every
 * future poll (the query is `where provisioning_status = 'deploying'`) —
 * even a transient crash-loop that genuinely recovers on its own (Railway's
 * own restart policy retries up to 10 times) or a bug staff later fix
 * server-side never got re-checked, so the customer never got their
 * confirmation email without a human noticing and manually re-queuing it
 * (confirmed live 2026-09-12, lulia's signup). This adds the counter that
 * lets the poller retry a few times before genuinely giving up.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('instances', function (Blueprint $table) {
            $table->unsignedTinyInteger('provisioning_check_attempts')->default(0)->after('provisioning_error');
        });
    }

    public function down(): void
    {
        Schema::table('instances', function (Blueprint $table) {
            $table->dropColumn('provisioning_check_attempts');
        });
    }
};
