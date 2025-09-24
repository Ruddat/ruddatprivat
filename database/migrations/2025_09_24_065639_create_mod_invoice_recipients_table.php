<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mod_invoice_recipients', function (Blueprint $table) {
    $table->id();

    // Verknüpfung mit eingeloggtem Customer
    $table->foreignId('customer_id')
          ->constrained('customers') // 👈 deine Haupt-Customer-Tabelle
          ->onDelete('cascade');

    $table->string('first_name')->nullable();
    $table->string('last_name')->nullable();
    $table->string('company_name')->nullable();
    $table->string('name');
    $table->string('email'); // ⚠️ nicht unique, sonst kannst du nicht mehrere Kunden mit gleicher Email haben
    $table->string('address')->nullable();
    $table->string('zip_code')->nullable();
    $table->string('city')->nullable();
    $table->string('country')->default('Germany');
    $table->string('customer_type')->nullable();
    $table->string('vat_number')->nullable();
    $table->string('payment_terms')->nullable();
    $table->boolean('is_active')->default(true);
    $table->text('notes')->nullable();
    $table->boolean('is_e_invoice')->default(false);
    $table->string('e_invoice_format')->nullable();
    $table->string('delivery_method')->nullable();
    $table->string('invoice_language')->default('de');
    $table->string('iban')->nullable();
    $table->string('bic')->nullable();
    $table->string('default_currency')->default('EUR');
    $table->date('last_invoice_date')->nullable();
    $table->decimal('total_invoiced', 15, 2)->default(0);
    $table->boolean('newsletter_opt_in')->default(false);
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mod_invoice_recipients');
    }
};
