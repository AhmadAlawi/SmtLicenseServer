<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Railway/DNS bookkeeping for auto-provisioning + white-label domains (SaaS conversion plan Phase 7). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('instances', function (Blueprint $table) {
            $table->string('railway_service_id')->nullable()->after('container_ref');
            $table->string('railway_db_service_id')->nullable()->after('railway_service_id');
            $table->string('subdomain_slug')->nullable()->unique()->after('subdomain');
            $table->string('default_domain')->nullable()->after('subdomain_slug');
            $table->string('custom_domain')->nullable()->after('default_domain');
            $table->string('custom_domain_dns_target')->nullable()->after('custom_domain');
            $table->text('provisioning_error')->nullable()->after('provisioning_status');
        });
    }

    public function down(): void
    {
        Schema::table('instances', function (Blueprint $table) {
            $table->dropColumn([
                'railway_service_id', 'railway_db_service_id', 'subdomain_slug',
                'default_domain', 'custom_domain', 'custom_domain_dns_target', 'provisioning_error',
            ]);
        });
    }
};
