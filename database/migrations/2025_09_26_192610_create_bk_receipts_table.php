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
        Schema::create('bk_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();

            // Beleg-Typ: invoice_in, invoice_out, fuel, cash, sonstiges
            $table->string('type')->default('invoice_in')->index();

            // Belegnummer (falls vorhanden, z. B. Rechnungsnummer)
            $table->string('number')->nullable();

            // Belegdatum
            $table->date('date');

            // Beträge
            $table->decimal('net_amount', 12, 2)->default(0);
            $table->decimal('vat_amount', 12, 2)->default(0);
            $table->decimal('gross_amount', 12, 2)->default(0);

            // Zusatzinfos
            $table->string('currency', 10)->default('EUR');
            $table->string('file_path')->nullable(); // Pfad zur hochgeladenen Datei (PDF, JPG, PNG)

            $table->json('meta')->nullable(); // z. B. OCR-Text, Liter, Tankstelle, FuelType

            $table->timestamps();
        });

// Entries verknüpfen mit bk_receipts
Schema::table('entries', function (Blueprint $table) {
    $table->dropForeign(['receipt_id']); // alten FK killen
});

Schema::table('entries', function (Blueprint $table) {
    $table->foreign('receipt_id')
        ->references('id')
        ->on('bk_receipts')
        ->nullOnDelete();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entries', function (Blueprint $table) {
            $table->dropForeign(['receipt_id']);
        });

        Schema::dropIfExists('bk_receipts');
    }
};
