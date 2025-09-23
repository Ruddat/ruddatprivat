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
        Schema::create('billing_records', function (Blueprint $table) {
            $table->id();

            // Customer statt User
            $table->foreignId('user_id')
                  ->constrained('customers') // ✅ verweist auf customers
                  ->onDelete('cascade');

            // Abrechnungskopf
            $table->foreignId('billing_header_id')
                  ->constrained('billing_headers') // ✅ verweist auf billing_headers
                  ->onDelete('cascade');

            // Mieter (UtilityTenant)
            $table->foreignId('tenant_id')
                  ->constrained('utility_tenants') // ✅ verweist auf utility_tenants
                  ->onDelete('cascade');

            // Mietobjekt (nullable)
            $table->unsignedBigInteger('rental_object_id')->nullable();

            $table->string('billing_period');
            $table->decimal('total_cost', 10, 2);
            $table->decimal('prepayment', 10, 2);
            $table->decimal('balance_due', 10, 2);

            $table->string('pdf_path')->nullable();
            $table->string('pdf_path_second')->nullable();
            $table->string('pdf_path_third')->nullable();

            $table->json('standard_costs');
            $table->json('heating_costs');

            $table->timestamps();

            // Mietobjekt-Bezug
            $table->foreign('rental_object_id')
                  ->references('id')
                  ->on('rental_objects')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_records');
    }
};
