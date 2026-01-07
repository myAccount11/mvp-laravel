<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venue_season_sport', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venue_id')->constrained('venues')->onDelete('cascade');
            $table->foreignId('season_sport_id')->constrained('season_sports')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venue_season_sport');
    }
};
