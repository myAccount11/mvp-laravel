<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_season_sports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('teams');
            $table->foreignId('season_sport_id')->constrained('season_sports');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_season_sports');
    }
};
