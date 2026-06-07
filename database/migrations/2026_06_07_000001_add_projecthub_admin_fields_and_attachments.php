<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_boards', function (Blueprint $table) {
            if (! Schema::hasColumn('project_boards', 'owner_admin_id')) {
                $table->foreignId('owner_admin_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('admins')
                    ->nullOnDelete();
            }
        });

        Schema::table('project_shares', function (Blueprint $table) {
            if (! Schema::hasColumn('project_shares', 'created_by_admin_id')) {
                $table->foreignId('created_by_admin_id')
                    ->nullable()
                    ->after('created_by')
                    ->constrained('admins')
                    ->nullOnDelete();
            }
        });

        if (! Schema::hasTable('project_card_attachments')) {
            Schema::create('project_card_attachments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('project_card_id')
                    ->constrained('project_cards')
                    ->cascadeOnDelete();
                $table->foreignId('uploaded_by_admin_id')
                    ->nullable()
                    ->constrained('admins')
                    ->nullOnDelete();
                $table->string('author_name')->nullable();
                $table->string('file_path');
                $table->string('original_name');
                $table->string('mime_type')->nullable();
                $table->unsignedBigInteger('size')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('project_card_attachments');

        Schema::table('project_shares', function (Blueprint $table) {
            if (Schema::hasColumn('project_shares', 'created_by_admin_id')) {
                $table->dropConstrainedForeignId('created_by_admin_id');
            }
        });

        Schema::table('project_boards', function (Blueprint $table) {
            if (Schema::hasColumn('project_boards', 'owner_admin_id')) {
                $table->dropConstrainedForeignId('owner_admin_id');
            }
        });
    }
};
