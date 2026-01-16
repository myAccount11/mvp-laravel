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
            $table->foreignId('pool_id')->nullable()->constrained('pools')->onDelete('set null');
            $table->foreignId('group_id')->nullable()->constrained('tournaments')->onDelete('set null');
            $table->boolean('is_deleted')->default(false);
            $table->date('last_official_date')->nullable();
            $table->time('last_official_time')->nullable();
            $table->integer('last_official_court_id')->nullable();
            $table->integer('season_sport_id')->nullable();
            $table->date('original_term_date')->nullable();
            $table->boolean('is_locked')->default(false);
            $table->boolean('force_next_update')->default(false);
            $table->integer('original_home_team_id')->nullable();
            $table->foreignId('organizer_club_id')->nullable()->constrained('clubs')->onDelete('set null');
            $table->integer('organizer_team_id')->nullable();
            $table->integer('pin_code')->nullable();
            $table->integer('stats_calculated')->nullable();
            $table->integer('star_rating')->nullable();
            $table->string('title_prefix')->nullable();
            $table->integer('penalty_status_id')->nullable();
            $table->integer('home_key')->nullable();
            $table->integer('away_key')->nullable();
            $table->integer('draft_id')->nullable();
            $table->boolean('auto_time')->nullable();
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
