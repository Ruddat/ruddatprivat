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
        Schema::create('billing_headers', function (Blueprint $table) {
            $table->id();

            // User = Customer
            $table->foreignId('user_id')
                  ->constrained('customers') // ✅ verweist jetzt korrekt auf customers
                  ->onDelete('cascade');

            $table->string('creator_name');
            $table->string('first_name')->nullable();
            $table->string('street')->nullable();
            $table->string('house_number')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('city')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('iban')->nullable();
            $table->string('bic')->nullable();
            $table->text('footer_text')->nullable();
            $table->text('notes')->nullable();
            $table->string('logo')->nullable();
            $table->string('logo_path')->nullable();
            $table->timestamps();
        });
    }

    /**`
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_headers');
    }
};
