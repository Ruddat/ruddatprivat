<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cleanup orphan foreign keys pointing to the unused `users` table.
 *
 * The app uses `admins` and `customers` guards. The original migrations
 * reference `users` which is never populated. This migration drops
 * the FK constraints — the columns themselves are kept for backward compat.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_cards', function (Blueprint $table) {
            if (Schema::hasColumn('project_cards', 'assigned_to')) {
                $table->dropForeign(['assigned_to']);
            }
        });

        Schema::table('project_boards', function (Blueprint $table) {
            if (Schema::hasColumn('project_boards', 'user_id')) {
                $table->dropForeign(['user_id']);
            }
        });

        Schema::table('project_shares', function (Blueprint $table) {
            if (Schema::hasColumn('project_shares', 'created_by')) {
                $table->dropForeign(['created_by']);
            }
        });

        Schema::table('project_card_comments', function (Blueprint $table) {
            if (Schema::hasColumn('project_card_comments', 'user_id')) {
                $table->dropForeign(['user_id']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('project_cards', function (Blueprint $table) {
            if (Schema::hasColumn('project_cards', 'assigned_to')) {
                $table->foreign('assigned_to')
                    ->nullable()
                    ->references('id')
                    ->on('users')
                    ->nullOnDelete();
            }
        });

        Schema::table('project_boards', function (Blueprint $table) {
            if (Schema::hasColumn('project_boards', 'user_id')) {
                $table->foreign('user_id')
                    ->nullable()
                    ->references('id')
                    ->on('users')
                    ->nullOnDelete();
            }
        });

        Schema::table('project_shares', function (Blueprint $table) {
            if (Schema::hasColumn('project_shares', 'created_by')) {
                $table->foreign('created_by')
                    ->nullable()
                    ->references('id')
                    ->on('users')
                    ->nullOnDelete();
            }
        });

        Schema::table('project_card_comments', function (Blueprint $table) {
            if (Schema::hasColumn('project_card_comments', 'user_id')) {
                $table->foreign('user_id')
                    ->nullable()
                    ->references('id')
                    ->on('users')
                    ->nullOnDelete();
            }
        });
    }
};
