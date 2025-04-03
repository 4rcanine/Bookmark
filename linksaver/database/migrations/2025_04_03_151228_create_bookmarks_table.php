<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Link to user
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null'); // Link to category (optional)
            $table->string('title');
            $table->text('url');
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_favorite')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookmarks');
    }
};