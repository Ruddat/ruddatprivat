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
        Schema::create('landing_pages', function (Blueprint $table) {
            $table->id();

            $table->string('slug')->unique(); // URL-Slug, z.B. "webentwicklung-peine"
            $table->string('title'); // SEO-Title
            $table->string('meta_description')->nullable(); // Meta-Desc
            $table->string('h1'); // Hauptüberschrift
            $table->text('content')->nullable(); // Hauptinhalt (HTML/Text)

            $table->string('template')->default('default'); // Template-Typ
            $table->string('hero_image')->nullable(); // Bild im Header
            $table->json('features')->nullable(); // Vorteile / Bulletpoints
            $table->json('faq')->nullable(); // FAQ-Fragen & Antworten

            $table->boolean('published')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_pages');
    }
};
