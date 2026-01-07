<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('club_season_sports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs');
            $table->foreignId('season_sport_id')->constrained('season_sports');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('club_season_sports');
    }
};
