<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drive_folders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('drive_folders')->nullOnDelete();
            $table->string('name');
            $table->timestamps();

            $table->index(['owner_id', 'parent_id']);
        });

        Schema::create('drive_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('folder_id')->nullable()->constrained('drive_folders')->nullOnDelete();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('original_name');
            $table->string('stored_name');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->string('disk')->default('local');
            $table->string('path');
            $table->string('checksum')->nullable();
            $table->timestamps();

            $table->index(['owner_id', 'folder_id']);
        });

        Schema::create('drive_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('folder_id')->nullable()->constrained('drive_folders')->cascadeOnDelete();
            $table->string('name');
            $table->string('token', 80)->unique();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('can_view')->default(true);
            $table->boolean('can_download')->default(true);
            $table->boolean('can_upload')->default(false);
            $table->boolean('can_delete')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['owner_id', 'folder_id', 'is_active']);
        });

        Schema::create('drive_share_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('share_id')->constrained('drive_shares')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('can_view')->default(true);
            $table->boolean('can_download')->default(true);
            $table->boolean('can_upload')->default(false);
            $table->boolean('can_delete')->default(false);
            $table->timestamps();

            $table->unique(['share_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drive_share_members');
        Schema::dropIfExists('drive_shares');
        Schema::dropIfExists('drive_files');
        Schema::dropIfExists('drive_folders');
    }
};
