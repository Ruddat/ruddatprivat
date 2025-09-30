<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::table('entries', function (Blueprint $table) {
        // Beide Felder nullable machen
        $table->unsignedBigInteger('debit_account_id')->nullable()->change();
        $table->unsignedBigInteger('credit_account_id')->nullable()->change();

        // transaction_id als Pflichtfeld für Gruppierung
     //   $table->uuid('transaction_id')->nullable(false)->change();
    });
}

public function down()
{
    Schema::table('entries', function (Blueprint $table) {
        $table->unsignedBigInteger('debit_account_id')->nullable(false)->change();
        $table->unsignedBigInteger('credit_account_id')->nullable(false)->change();
        $table->uuid('transaction_id')->nullable()->change();
    });
}
};
