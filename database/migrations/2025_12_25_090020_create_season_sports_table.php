<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('season_sports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('season_id')->constrained('seasons');
            $table->foreignId('sport_id')->constrained('sports');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('season_sports');
    }
};
