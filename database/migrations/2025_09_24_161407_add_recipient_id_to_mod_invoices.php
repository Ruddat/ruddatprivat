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
        Schema::table('mod_invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('mod_invoices', 'recipient_id')) {
                $table->unsignedBigInteger('recipient_id')->nullable()->after('customer_id')->index();

                $table->foreign('recipient_id')
                      ->references('id')
                      ->on('mod_invoice_recipients')
                      ->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mod_invoices', function (Blueprint $table) {
            if (Schema::hasColumn('mod_invoices', 'recipient_id')) {
                $table->dropForeign(['recipient_id']);
                $table->dropColumn('recipient_id');
            }
        });
    }
};
