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
        Schema::create('heating_costs', function (Blueprint $table) {
            $table->id();

            // Customer als Besitzer
            $table->foreignId('user_id')
                  ->constrained('customers') // ✅ verweist jetzt korrekt auf customers
                  ->onDelete('cascade');

            $table->foreignId('rental_object_id')
                  ->constrained('rental_objects')
                  ->onDelete('cascade');

            $table->enum('heating_type', ['gas', 'oil']);
            $table->decimal('price_per_unit', 8, 2)->nullable();
            $table->integer('initial_reading')->nullable();
            $table->integer('final_reading')->nullable();
            $table->decimal('total_oil_used', 8, 2)->nullable();
            $table->decimal('warm_water_percentage', 5, 2)->default(0.2);
            $table->integer('year')->nullable()->comment('Abrechnungsjahr');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('heating_costs');
    }
};
