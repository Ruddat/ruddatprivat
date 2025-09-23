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
        Schema::create('recorded_utility_costs', function (Blueprint $table) {
            $table->id();

            // User = Customer
            $table->foreignId('user_id')
                  ->constrained('customers') // ✅ Korrekt auf customers
                  ->onDelete('cascade');

            $table->foreignId('rental_object_id')
                  ->constrained('rental_objects')
                  ->onDelete('cascade');

            $table->foreignId('utility_cost_id')
                  ->constrained('utility_costs')
                  ->onDelete('cascade');

            $table->decimal('amount', 10, 2);

            $table->enum('distribution_key', ['consumption', 'area', 'people', 'units'])
                  ->default('units')
                  ->comment('consumption = Verbrauch, area = Wohnfläche, people = Personenanzahl, units = Einheiten');

            $table->string('custom_name')->nullable();

            $table->year('year')->default(date('Y'));

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recorded_utility_costs');
    }
};
