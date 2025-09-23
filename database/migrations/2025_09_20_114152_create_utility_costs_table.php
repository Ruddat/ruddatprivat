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
        Schema::create('utility_costs', function (Blueprint $table) {
            $table->id();

            // User = Customer
            $table->foreignId('user_id')
                  ->constrained('customers') // ✅ korrekt auf customers
                  ->onDelete('cascade');

            $table->string('name'); 
            $table->string('short_name', 10)->nullable();
            $table->string('category', 50)->nullable();
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2);

            $table->enum('distribution_key', ['consumption', 'area', 'people', 'units'])
                  ->default('units')
                  ->comment('Verteilerschlüssel: consumption = Verbrauch, area = Wohnfläche, people = Personenanzahl, units = Einheiten');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('utility_costs');
    }
};
