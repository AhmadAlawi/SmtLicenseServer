<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * One row per provisioned POS instance's license. The plaintext license_key
 * and hmac_secret are each shown to the customer / written into their
 * instance's .env exactly once, at provisioning — this table only ever
 * stores a lookup hash of the key (license_key_hash) and the secret
 * encrypted at rest (Laravel's `encrypted` cast on the model), so a
 * database leak alone doesn't hand out working credentials for every
 * customer's instance.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->nullable()->constrained()->nullOnDelete();
            $table->string('license_key_hash', 64)->unique(); // sha256 hex
            $table->text('hmac_secret'); // encrypted cast on the model
            $table->string('instance_domain')->nullable();
            $table->string('fingerprint')->nullable();
            $table->string('status', 16)->default('pending'); // pending|valid|invalid|suspended|past_due
            $table->timestamp('grace_until')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('licenses');
    }
};
