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
        Schema::table('bwa_groups', function (Blueprint $table) {
            $table->integer('order')->default(0)->after('account_number_to');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bwa_groups', function (Blueprint $table) {
                // Rollback der Migration
                $table->dropColumn('order');

        });
    }
};
