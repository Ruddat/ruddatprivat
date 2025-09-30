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
        Schema::create('invoice_uploads', function (Blueprint $table) {
    $table->id();
    $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
    $table->string('invoice_number')->nullable();
    $table->date('invoice_date');
    $table->decimal('net_amount', 12, 2);
    $table->decimal('vat_amount', 12, 2);
    $table->decimal('gross_amount', 12, 2);
    $table->string('file_path');
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_uploads');
    }
};
