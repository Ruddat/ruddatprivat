<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Mandanten
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();

            // Basis
            $table->string('name');                 // z. B. Ruddat UG
            $table->string('slug')->unique();       // technischer Schlüssel
            $table->string('logo_path')->nullable(); // Logo-Pfad

            // Buchhaltungs-Settings
            $table->date('fiscal_year_start')->default('2025-01-01');
            $table->string('currency', 10)->default('EUR');

            // Adresse & Kontakt
            $table->string('street')->nullable();
            $table->string('house_number', 20)->nullable();
            $table->string('zip', 20)->nullable();
            $table->string('city')->nullable();
            $table->string('country', 100)->default('Deutschland');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();

            // Steuerliche Angaben
            $table->string('tax_number')->nullable();          // Steuernummer vom FA
            $table->string('vat_id')->nullable();              // USt-IdNr. (DE123…)
            $table->string('commercial_register')->nullable(); // HRB-Nummer
            $table->string('court_register')->nullable();      // Registergericht

            // Bankverbindung (für Rechnungen / Auszahlungen)
            $table->string('bank_name')->nullable();
            $table->string('iban')->nullable();
            $table->string('bic')->nullable();

            // Metadaten
            $table->boolean('active')->default(true);
            $table->boolean('is_current')->default(false);
            $table->timestamps();
        });

        // Konten
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('number', 10);
            $table->string('name');
            $table->enum('type', ['asset', 'liability', 'expense', 'revenue', 'equity']);
            $table->timestamps();
            $table->unique(['tenant_id', 'number']);
        });

        // Buchungsjahre
        Schema::create('fiscal_years', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->integer('year');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_current')->default(false);
            $table->boolean('closed')->default(false);
            $table->timestamps();
        });

        // Eröffnungsbilanz
        Schema::create('opening_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fiscal_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 12, 2)->default(0);
            $table->timestamps();
        });

        // Belege
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->date('date')->nullable();
            $table->decimal('total', 12, 2)->nullable();
            $table->string('vendor')->nullable();
            $table->decimal('vat', 12, 2)->nullable();
            $table->json('parsed_fields')->nullable();
            $table->tinyInteger('status')->default(0); // 0=new,1=done,2=needs review
            $table->timestamps();
        });

        // Buchungen
        Schema::create('entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fiscal_year_id')->nullable()->constrained()->nullOnDelete();
            $table->date('booking_date');
            $table->foreignId('debit_account_id')->constrained('accounts');
            $table->foreignId('credit_account_id')->constrained('accounts');
            $table->decimal('amount', 12, 2);
            $table->string('description')->nullable();
            $table->foreignId('receipt_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entries');
        Schema::dropIfExists('receipts');
        Schema::dropIfExists('opening_balances');
        Schema::dropIfExists('fiscal_years');
        Schema::dropIfExists('accounts');
        Schema::dropIfExists('tenants');
    }
};
