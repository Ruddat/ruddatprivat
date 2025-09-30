<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            // neues Feld für den Customer
            $table->foreignId('customer_id')
                  ->after('id')
                  ->nullable()
                  ->constrained()
                  ->cascadeOnDelete();
        });

        // Falls du bestehende Daten hast, kannst du hier eine Default-Zuweisung machen
        // Beispiel: alle bestehenden Tenants -> customer_id = 1 (Admin)
        DB::table('tenants')->update(['customer_id' => 1]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropConstrainedForeignId('customer_id');
        });
    }
};
