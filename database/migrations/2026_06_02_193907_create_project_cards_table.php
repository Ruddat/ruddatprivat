<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_cards', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_board_id')
                ->constrained('project_boards')
                ->cascadeOnDelete();

            $table->foreignId('project_list_id')
                ->constrained('project_lists')
                ->cascadeOnDelete();

            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('title');
            $table->longText('description')->nullable();

            $table->string('priority')->default('normal');
            $table->string('status')->default('open');

            $table->date('due_date')->nullable();
            $table->timestamp('approved_at')->nullable();

            $table->unsignedInteger('position')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_cards');
    }
};
