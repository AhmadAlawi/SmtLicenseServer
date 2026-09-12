<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The public update feed's backing store (SaaS conversion plan — update-push
 * system). One row per published release; `pos:demo-seed` and every real
 * SaasPOS tenant reads these back via GET /updates/feed.json, shaped to
 * match exactly what SaasPOS's own UpdateClient/CheckForUpdate already
 * expect (see SaasPOS app/Actions/Updater/CheckForUpdate.php) — that
 * self-update engine already exists and is untouched by this migration,
 * this just gives it something real to poll.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('releases', function (Blueprint $table) {
            $table->id();
            $table->string('version', 32)->unique();
            $table->string('channel', 16)->default('stable');
            $table->text('changelog')->nullable();
            $table->string('zip_path');
            $table->char('sha256', 64);
            // Ed25519 detached signature over the zip, base64 — computed at
            // publish time from UPDATE_SIGNING_SECRET_KEY (never stored in
            // this table or the repo, Railway-variable only), so staff never
            // handle a signing step manually.
            $table->text('signature');
            $table->timestamp('published_at')->nullable();
            $table->foreignId('published_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('releases');
    }
};
