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
        // Tabelle für Quittungsvorlagen erstellen
        if (!Schema::hasTable('receipt_templates')) {
            Schema::create('receipt_templates', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('customer_id');
                $table->string('name');
                $table->string('sender_name');
                $table->string('sender_street')->nullable();
                $table->string('sender_house_number')->nullable();
                $table->string('sender_zip')->nullable();
                $table->string('sender_city')->nullable();
                $table->string('sender_phone')->nullable();
                $table->string('sender_email')->nullable();
                $table->string('sender_tax_number')->nullable();
                $table->boolean('include_tax')->default(false);
                $table->decimal('tax_percent', 5, 2)->nullable();
                $table->boolean('is_default')->default(false);
                $table->timestamps();

                $table->foreign('customer_id')
                      ->references('id')
                      ->on('customers')
                      ->onDelete('cascade');
            });
        }

        // mod_receipts Tabelle erweitern
        Schema::table('mod_receipts', function (Blueprint $table) {
            // Von user_id zu customer_id ändern
            if (Schema::hasColumn('mod_receipts', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->renameColumn('user_id', 'customer_id');
            } else if (!Schema::hasColumn('mod_receipts', 'customer_id')) {
                $table->unsignedBigInteger('customer_id')->after('id');
            }

            // Adressfelder für Absender hinzufügen
            if (!Schema::hasColumn('mod_receipts', 'sender_street')) {
                $table->string('sender_street')->nullable()->after('sender');
                $table->string('sender_house_number')->nullable()->after('sender_street');
                $table->string('sender_zip')->nullable()->after('sender_house_number');
                $table->string('sender_city')->nullable()->after('sender_zip');
                $table->string('sender_phone')->nullable()->after('sender_city');
                $table->string('sender_email')->nullable()->after('sender_phone');
                $table->string('sender_tax_number')->nullable()->after('sender_email');
            }

            // Adressfelder für Empfänger hinzufügen
            if (!Schema::hasColumn('mod_receipts', 'receiver_street')) {
                $table->string('receiver_street')->nullable()->after('receiver');
                $table->string('receiver_house_number')->nullable()->after('receiver_street');
                $table->string('receiver_zip')->nullable()->after('receiver_house_number');
                $table->string('receiver_city')->nullable()->after('receiver_zip');
                $table->string('receiver_phone')->nullable()->after('receiver_city');
                $table->string('receiver_email')->nullable()->after('receiver_phone');
            }

            // Template Referenz
            if (!Schema::hasColumn('mod_receipts', 'template_id')) {
                $table->unsignedBigInteger('template_id')->nullable()->after('customer_id');
            }

            // Foreign keys
            $table->foreign('customer_id')
                  ->references('id')
                  ->on('customers')
                  ->onDelete('cascade');

            $table->foreign('template_id')
                  ->references('id')
                  ->on('receipt_templates')
                  ->onDelete('set null');
        });

        // data_quittung_texts Tabelle anpassen
        if (Schema::hasTable('data_quittung_texts')) {
            Schema::table('data_quittung_texts', function (Blueprint $table) {
                if (Schema::hasColumn('data_quittung_texts', 'user_id')) {
                    $table->dropForeign(['user_id']);
                    $table->renameColumn('user_id', 'customer_id');

                    $table->foreign('customer_id')
                          ->references('id')
                          ->on('customers')
                          ->onDelete('cascade');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // receipt_templates Tabelle löschen
        Schema::dropIfExists('receipt_templates');

        // mod_receipts Änderungen rückgängig machen
        Schema::table('mod_receipts', function (Blueprint $table) {
            // Foreign keys entfernen
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['template_id']);

            // Spalten entfernen
            $table->dropColumn([
                'sender_street', 'sender_house_number', 'sender_zip', 'sender_city',
                'sender_phone', 'sender_email', 'sender_tax_number',
                'receiver_street', 'receiver_house_number', 'receiver_zip',
                'receiver_city', 'receiver_phone', 'receiver_email', 'template_id'
            ]);

            // Zurück zu user_id
            $table->renameColumn('customer_id', 'user_id');

            // Ursprünglichen foreign key wiederherstellen
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });

        // data_quittung_texts rückgängig machen
        if (Schema::hasTable('data_quittung_texts')) {
            Schema::table('data_quittung_texts', function (Blueprint $table) {
                if (Schema::hasColumn('data_quittung_texts', 'customer_id')) {
                    $table->dropForeign(['customer_id']);
                    $table->renameColumn('customer_id', 'user_id');

                    $table->foreign('user_id')
                          ->references('id')
                          ->on('users')
                          ->onDelete('cascade');
                }
            });
        }
    }
};
