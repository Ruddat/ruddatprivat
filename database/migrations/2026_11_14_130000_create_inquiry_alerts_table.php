<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Versandprotokoll der Sofortbenachrichtigungen.
 *
 * Status:
 *  queued   – angestoßen, wartet auf den Queue-Worker
 *  accepted – vom Anbieter angenommen (NICHT: zugestellt oder gelesen)
 *  failed   – endgültig fehlgeschlagen (nach allen Wiederholungen)
 *  skipped  – nicht versendet (z. B. Kanal nicht konfiguriert)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inquiry_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inquiry_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('channel', 20);
            $table->string('provider', 40)->nullable();
            $table->string('recipient_masked', 80)->nullable();
            $table->string('status', 20)->default('queued')->index();
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->string('provider_message_id', 120)->nullable();
            $table->text('last_error')->nullable();
            $table->boolean('is_test')->default(false);
            $table->boolean('is_fallback')->default(false);
            // verhindert doppelte Alarme für dieselbe Anfrage und denselben Kanal
            $table->string('dedupe_key', 120)->unique();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inquiry_alerts');
    }
};
