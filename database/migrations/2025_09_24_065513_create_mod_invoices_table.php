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
        Schema::create('mod_invoices', function (Blueprint $table) {
            $table->id();

            // Kundenbezug (statt user_id)
            $table->unsignedBigInteger('customer_id');

            $table->string('invoice_number')->unique();
            $table->date('invoice_date');
            $table->date('due_date');
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->enum('status', ['draft', 'sent', 'paid', 'cancelled'])->default('draft');
            $table->string('pdf_path')->nullable(); // Pfad zur generierten PDF
            $table->text('notes')->nullable();
            $table->timestamps();

            // Fremdschlüssel
            $table->foreign('customer_id')
                  ->references('id')
                  ->on('customers')   // oder 'mod_customers', je nach deiner Tabelle!
                  ->onDelete('cascade');

            $table->foreignId('creator_id')
                  ->nullable()
                  ->constrained('mod_invoice_creators')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mod_invoices');
    }
};
