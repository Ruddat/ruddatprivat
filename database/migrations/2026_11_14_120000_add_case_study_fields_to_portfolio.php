<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Struktur für technische Fallstudien und optimierte Bildergalerien.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('portfolio_items', function (Blueprint $table) {
            $table->string('project_type', 160)->nullable()->after('category');
            $table->string('project_status', 160)->nullable()->after('project_type');
            $table->text('role')->nullable()->after('project_status');            // tatsächliche Rolle von RuddatTech
            $table->text('challenge')->nullable()->after('description');           // Ausgangssituation / Problemstellung
            $table->text('technical_challenges')->nullable()->after('challenge');
            $table->text('architecture')->nullable()->after('technical_challenges');
            $table->json('features')->nullable()->after('architecture');           // Liste
            $table->json('integrations')->nullable()->after('features');           // Liste
            $table->text('operations')->nullable()->after('integrations');         // Datenbank, Sicherheit, Betrieb
            $table->json('complexity_tags')->nullable()->after('badges');          // feste Auswahlliste
            $table->string('cover_alt', 255)->nullable()->after('cover_image');
            $table->json('cover_variants')->nullable()->after('cover_alt');
            $table->string('seo_title', 70)->nullable()->after('sort_order');
            $table->string('meta_description', 170)->nullable()->after('seo_title');
        });

        Schema::table('portfolio_images', function (Blueprint $table) {
            $table->string('alt', 255)->nullable()->after('caption');
            $table->unsignedInteger('sort_order')->default(0)->after('alt');
            $table->unsignedInteger('width')->nullable()->after('sort_order');
            $table->unsignedInteger('height')->nullable()->after('width');
            $table->json('variants')->nullable()->after('height');                // [breite => pfad]
        });
    }

    public function down(): void
    {
        Schema::table('portfolio_images', function (Blueprint $table) {
            $table->dropColumn(['alt', 'sort_order', 'width', 'height', 'variants']);
        });

        Schema::table('portfolio_items', function (Blueprint $table) {
            $table->dropColumn([
                'project_type', 'project_status', 'role', 'challenge', 'technical_challenges', 'architecture',
                'features', 'integrations', 'operations', 'complexity_tags', 'cover_alt', 'cover_variants',
                'seo_title', 'meta_description',
            ]);
        });
    }
};
