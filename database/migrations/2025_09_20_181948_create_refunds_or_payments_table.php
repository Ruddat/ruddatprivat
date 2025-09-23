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
        Schema::create('refunds_or_payments', function (Blueprint $table) {
            $table->id();

            // Customer statt User
            $table->foreignId('user_id')
                  ->constrained('customers') // ✅ verweist jetzt korrekt auf customers
                  ->onDelete('cascade');

            // Tenant (UtilityTenant)
            $table->foreignId('tenant_id')
                  ->constrained('utility_tenants')
                  ->onDelete('cascade');

            // Mietobjekt
            $table->foreignId('rental_object_id')
                  ->constrained('rental_objects')
                  ->onDelete('cascade');

            $table->year('year')->nullable();
            $table->tinyInteger('month')->nullable();
            $table->string('type'); // refund oder payment
            $table->decimal('amount', 10, 2);
            $table->date('payment_date')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refunds_or_payments');
    }
};
