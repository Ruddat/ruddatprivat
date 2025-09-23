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
        Schema::create('customer_limits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->string('type'); // z. B. invoices, bookings, storage
            $table->integer('used')->default(0);
            $table->integer('max')->nullable(); // null = unlimited
            $table->timestamps();

            $table->unique(['customer_id', 'type']); // jeder Typ nur 1x pro Kunde
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_limits');
    }
};
