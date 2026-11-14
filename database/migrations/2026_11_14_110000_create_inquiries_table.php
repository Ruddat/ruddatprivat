<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Geschäftliche Anfragen aus dem neuen Kontaktformular.
 * Die bisherige Tabelle contact_messages bleibt unverändert bestehen.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 20)->nullable()->unique();

            $table->string('name', 120);
            $table->string('company', 160);
            $table->string('email', 190);
            $table->string('phone', 40)->nullable();
            $table->string('type', 40)->index();
            $table->text('application_summary');
            $table->text('requirements')->nullable();
            $table->timestamp('privacy_accepted_at');

            $table->string('status', 30)->default('new')->index();
            $table->text('internal_notes')->nullable();
            $table->timestamp('status_changed_at')->nullable();
            $table->timestamp('first_viewed_at')->nullable();
            $table->timestamp('archived_at')->nullable();

            // Missbrauchs- und Duplikaterkennung ohne Klartext-IP
            $table->string('ip_hash', 64)->nullable();
            $table->string('fingerprint', 64)->index();

            $table->timestamps();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inquiries');
    }
};
