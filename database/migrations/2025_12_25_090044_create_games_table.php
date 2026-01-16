<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->integer('number')->nullable();
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->integer('status_id')->nullable();
            $table->integer('points_home')->nullable();
            $table->integer('points_away')->nullable();
            $table->foreignId('court_id')->nullable()->constrained('courts')->onDelete('set null');
            $table->integer('team_id_winner')->nullable();
            $table->foreignId('team_id_home')->nullable()->constrained('teams')->onDelete('set null');
            $table->foreignId('team_id_away')->nullable()->constrained('teams')->onDelete('set null');
            $table->foreignId('tournament_id')->nullable()->constrained('tournaments')->onDelete('set null');
            $table->foreignId('group_id')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->integer('season_sport_id')->nullable();
            $table->integer('pin_code')->nullable();
            $table->integer('penalty_status_id')->nullable();
            $table->integer('home_key')->nullable();
            $table->integer('away_key')->nullable();
            $table->integer('pool_points_fixed_home')->nullable();
            $table->integer('pool_points_fixed_away')->nullable();
            $table->integer('pool_bonus_fixed_home')->nullable();
            $table->integer('pool_bonus_fixed_away')->nullable();
            $table->foreignId('round_id')->nullable()->constrained('rounds')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
