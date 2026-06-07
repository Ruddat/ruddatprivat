<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_card_comments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_card_id')
                ->constrained('project_cards')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('author_name')->nullable();
            $table->text('comment');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_card_comments');
    }
};
