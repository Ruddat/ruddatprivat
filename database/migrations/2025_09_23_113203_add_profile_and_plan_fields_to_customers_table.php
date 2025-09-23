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
        Schema::table('customers', function (Blueprint $table) {
    $table->string('street')->nullable()->after('email');
    $table->string('house_number', 20)->nullable()->after('street');
    $table->string('zip', 20)->nullable()->after('house_number');
    $table->string('city')->nullable()->after('zip');
    $table->string('phone', 30)->nullable()->after('city');

    // Plan- und Limit-System
    $table->enum('plan', ['free', 'premium', 'pro'])->default('free')->after('phone');
    $table->json('limits_used')->nullable()->after('plan');
    $table->date('premium_until')->nullable()->after('limits_used');

    $table->boolean('onboarding_done')->default(false)->after('premium_until');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['street', 'house_number', 'zip', 'city', 'phone']);
            $table->dropColumn(['plan', 'limits_used', 'premium_until']);
            $table->dropColumn('onboarding_done');
        });
    }
};
