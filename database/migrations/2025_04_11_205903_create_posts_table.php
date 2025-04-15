<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Título do post
            $table->text('content'); // Conteúdo do post
            $table->string('slug')->unique(); // Slug único para URLs amigáveis
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Relacionamento com usuário
            $table->timestamps(); // created_at e updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
}