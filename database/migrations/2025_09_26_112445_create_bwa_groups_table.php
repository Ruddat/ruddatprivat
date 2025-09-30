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
        Schema::create('bwa_groups', function (Blueprint $table) {
    $table->id();
    $table->string('skr'); // 'skr03' oder 'skr04'
    $table->integer('account_number_from');
    $table->integer('account_number_to');
    $table->string('group_key');
    $table->string('group_label');
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bwa_groups');
    }
};
