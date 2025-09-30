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
        Schema::create('bk_booking_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->foreignId('debit_account_id')->constrained('accounts')->onDelete('cascade');
            $table->foreignId('credit_account_id')->constrained('accounts')->onDelete('cascade');
            $table->decimal('vat_rate', 5, 2)->default(19);
            $table->boolean('with_vat')->default(false);
            $table->string('description');
            $table->string('receipt_type')->nullable();
            $table->boolean('is_global')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bk_booking_templates');
    }
};
