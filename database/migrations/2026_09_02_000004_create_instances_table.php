<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Provisioning/deploy metadata for the container behind a license — kept
 * separate from `licenses` (the phone-home/entitlement record) so the
 * provisioning pipeline's concerns don't bleed into validation logic.
 *
 * `db_credentials_ref` is a pointer into a secrets manager (Railway/Vault/
 * whatever the pipeline uses) — this table never holds a raw DB password.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('license_id')->constrained()->cascadeOnDelete();
            $table->string('db_credentials_ref')->nullable();
            $table->string('container_ref')->nullable();
            $table->string('subdomain')->nullable();
            $table->string('provisioning_status', 16)->default('pending'); // pending|provisioning|ready|failed
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instances');
    }
};
