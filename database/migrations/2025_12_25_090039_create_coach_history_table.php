<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coach_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->nullable()->constrained('teams')->onDelete('set null');
            $table->string('club_name');
            $table->string('tournament_name');
            $table->string('season_name');
            $table->foreignId('coach_id')->constrained('coach')->onDelete('cascade');
            $table->integer('mvp');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coach_history');
    }
};
