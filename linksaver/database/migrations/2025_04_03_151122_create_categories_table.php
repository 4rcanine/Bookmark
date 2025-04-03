<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Link to the user who owns it
            $table->string('name');
            $table->timestamps();

            $table->unique(['user_id', 'name']); // Ensure a user doesn't have duplicate category names
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};