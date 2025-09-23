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
        Schema::create('annual_billing_records', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained('customers')->onDelete('cascade');
    $table->foreignId('rental_object_id')->constrained()->onDelete('cascade');
    $table->foreignId('tenant_id')->constrained('utility_tenants')->onDelete('cascade');
    $table->foreignId('utility_cost_id')->nullable()->constrained('utility_costs')->onDelete('set null');
    $table->year('year');
    $table->decimal('amount', 10, 2);
    $table->enum('distribution_key', ['area', 'people', 'units'])->default('units');
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('annual_billing_records');
    }
};
