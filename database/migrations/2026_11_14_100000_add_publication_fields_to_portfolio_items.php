<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Veröffentlichungssteuerung für Referenzen.
 *
 * Bestehende Einträge bleiben bewusst unveröffentlicht (Standard false),
 * bis sie im Adminbereich geprüft und freigegeben wurden.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('portfolio_items', function (Blueprint $table) {
            $table->boolean('is_published')->default(false)->after('cta_link');
            $table->boolean('is_featured')->default(false)->after('is_published');
            $table->unsignedInteger('sort_order')->default(0)->after('is_featured');

            $table->index(['is_published', 'is_featured', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::table('portfolio_items', function (Blueprint $table) {
            $table->dropIndex(['is_published', 'is_featured', 'sort_order']);
            $table->dropColumn(['is_published', 'is_featured', 'sort_order']);
        });
    }
};
