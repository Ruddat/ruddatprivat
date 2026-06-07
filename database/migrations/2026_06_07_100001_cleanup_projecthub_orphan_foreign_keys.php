<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cleanup orphan foreign keys pointing to the unused `users` table.
 *
 * The app uses `admins` and `customers` guards. The original migrations
 * reference `users` which is never populated. This migration:
 * - Drops the `assigned_to` FK on project_cards (users) — field kept for future use
 * - Drops the `user_id` FK on project_boards (users) — field kept for backward compat
 * - Drops the `created_by` FK on project_shares (users) — field kept for backward compat
 * - Drops the `user_id` FK on project_card_comments (users) — field kept for backward compat
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_cards', function (Blueprint $table) {
            if (Schema::hasColumn('project_cards', 'assigned_to')) {
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $foreignKeys = collect($sm->listTableForeignKeys('project_cards'))
                    ->map(fn ($fk) => $fk->getName());

                $fkName = $foreignKeys->first(fn ($name) => str_contains($name, 'assigned_to'));
                if ($fkName) {
                    $table->dropForeign($fkName);
                }
            }
        });

        Schema::table('project_boards', function (Blueprint $table) {
            if (Schema::hasColumn('project_boards', 'user_id')) {
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $foreignKeys = collect($sm->listTableForeignKeys('project_boards'))
                    ->map(fn ($fk) => $fk->getName());

                $fkName = $foreignKeys->first(fn ($name) => str_contains($name, 'user_id'));
                if ($fkName) {
                    $table->dropForeign($fkName);
                }
            }
        });

        Schema::table('project_shares', function (Blueprint $table) {
            if (Schema::hasColumn('project_shares', 'created_by')) {
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $foreignKeys = collect($sm->listTableForeignKeys('project_shares'))
                    ->map(fn ($fk) => $fk->getName());

                $fkName = $foreignKeys->first(fn ($name) => str_contains($name, 'created_by'));
                if ($fkName) {
                    $table->dropForeign($fkName);
                }
            }
        });

        Schema::table('project_card_comments', function (Blueprint $table) {
            if (Schema::hasColumn('project_card_comments', 'user_id')) {
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $foreignKeys = collect($sm->listTableForeignKeys('project_card_comments'))
                    ->map(fn ($fk) => $fk->getName());

                $fkName = $foreignKeys->first(fn ($name) => str_contains($name, 'user_id'));
                if ($fkName) {
                    $table->dropForeign($fkName);
                }
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
