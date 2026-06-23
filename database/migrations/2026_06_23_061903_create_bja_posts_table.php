<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('bja_posts')) {
            Schema::create('bja_posts', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->string('category', 60)->default('Umum');
                $table->text('excerpt');
                $table->longText('content')->nullable();
                $table->string('cover_url')->nullable();
                $table->string('cover_alt')->nullable();
                $table->string('author', 100)->default('Tim BJA Logistic');
                $table->date('published_at')->nullable();
                $table->boolean('is_published')->default(false);
                $table->string('meta_title', 60)->nullable();
                $table->string('meta_description', 160)->nullable();
                $table->string('focus_keyword', 100)->nullable();
                $table->json('tags')->nullable();
                $table->string('og_title')->nullable();
                $table->string('og_image')->nullable();
                $table->string('og_description', 200)->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bja_posts');
    }
};
