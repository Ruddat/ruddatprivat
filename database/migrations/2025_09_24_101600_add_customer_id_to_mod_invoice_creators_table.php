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
        Schema::table('mod_invoice_creators', function (Blueprint $table) {
            if (!Schema::hasColumn('mod_invoice_creators', 'customer_id')) {
                $table->unsignedBigInteger('customer_id')->nullable()->after('user_id')->index();

                // Falls du eine `customers`-Tabelle hast:
                // $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mod_invoice_creators', function (Blueprint $table) {
            if (Schema::hasColumn('mod_invoice_creators', 'customer_id')) {
                $table->dropColumn('customer_id');
            }
        });
    }
};
