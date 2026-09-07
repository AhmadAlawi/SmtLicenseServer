<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Audit trail of every reported usage snapshot — dispute resolution + abuse detection, not the live source of truth (that's licenses.*). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usage_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('license_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('user_count')->default(0);
            $table->unsignedInteger('store_count')->default(0);
            $table->timestamp('reported_at');
            $table->timestamps();

            $table->index(['license_id', 'reported_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usage_reports');
    }
};
