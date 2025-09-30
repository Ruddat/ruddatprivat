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
        Schema::create('bk_balance_groups', function (Blueprint $table) {
        $table->id();
        $table->string('skr', 10)->default('skr03'); // SKR-Plan: skr03 oder skr04
        $table->string('side', 20); // asset | liability | equity
        $table->unsignedInteger('account_number_from');
        $table->unsignedInteger('account_number_to');
        $table->string('group_key', 50);  // technischer Key
        $table->string('group_label');    // Bezeichnung für Anzeige
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bk_balance_groups');
    }
};
