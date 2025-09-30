<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // receipt_templates Tabelle erweitern
        Schema::table('receipt_templates', function (Blueprint $table) {
            $table->string('type')->default('quittung')->after('name');
            $table->string('receiver_name')->nullable()->after('sender_tax_number');
            $table->string('receiver_street')->nullable();
            $table->string('receiver_house_number')->nullable();
            $table->string('receiver_zip')->nullable();
            $table->string('receiver_city')->nullable();
            $table->string('receiver_phone')->nullable();
            $table->string('receiver_email')->nullable();
            $table->text('default_description')->nullable();
        });

        // mod_receipts Tabelle erweitern
        Schema::table('mod_receipts', function (Blueprint $table) {
            $table->string('receipt_type')->default('quittung')->after('type');
            $table->string('title')->nullable()->after('receipt_type');
        });
    }

    public function down(): void
    {
        Schema::table('receipt_templates', function (Blueprint $table) {
            $table->dropColumn([
                'type', 'receiver_name', 'receiver_street', 'receiver_house_number',
                'receiver_zip', 'receiver_city', 'receiver_phone', 'receiver_email',
                'default_description'
            ]);
        });

        Schema::table('mod_receipts', function (Blueprint $table) {
            $table->dropColumn(['receipt_type', 'title']);
        });
    }
};
