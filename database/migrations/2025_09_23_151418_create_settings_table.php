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
        Schema::create('settings', function (Blueprint $table) {
        $table->id();
        $table->string('group')->default('general'); // z. B. "company", "limits", "branding"
        $table->string('key');                       // z. B. "name", "phone", "email"
        $table->string('description')->nullable();   // Lesbarer Hinweis fürs Formular
        $table->text('value')->nullable();           // der eigentliche Wert
        $table->string('type')->default('string');   // string, number, boolean, json, text
        $table->timestamps();

        $table->unique(['group', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
