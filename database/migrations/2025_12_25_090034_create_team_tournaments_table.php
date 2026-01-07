<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_tournaments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('teams');
            $table->foreignId('tournament_id')->constrained('tournaments');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_tournaments');
    }
};
