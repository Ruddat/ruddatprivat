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
        Schema::create('tenant_payments', function (Blueprint $table) {
            $table->id();

            // user_id zeigt auf customers (nicht auf users!)
            $table->foreignId('user_id')
                  ->constrained('customers') // ✅ korrekte Tabelle
                  ->onDelete('cascade');

            // tenant_id zeigt auf utility_tenants
            $table->foreignId('tenant_id')
                  ->constrained('utility_tenants') // ✅ explizit angeben
                  ->onDelete('cascade');

            // rental_object_id zeigt auf rental_objects
            $table->foreignId('rental_object_id')
                  ->constrained('rental_objects')
                  ->onDelete('cascade');

            $table->year('year');
            $table->unsignedTinyInteger('month')->nullable();
            $table->decimal('amount', 10, 2);
            $table->date('payment_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_payments');
    }
};
