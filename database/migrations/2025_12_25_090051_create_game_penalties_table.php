<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_penalties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained('games')->onDelete('cascade');
            $table->integer('value')->nullable();
            $table->integer('number')->default(1);
            $table->enum('side', ['home', 'away'])->default('home');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_penalties');
    }
};
