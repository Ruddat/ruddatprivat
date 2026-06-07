<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_lists', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_board_id')
                ->constrained('project_boards')
                ->cascadeOnDelete();

            $table->string('title');
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_done_list')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_lists');
    }
};
