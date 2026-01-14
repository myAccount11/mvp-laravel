<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();
            $table->string('alias');
            $table->string('short_name');
            $table->integer('region_id')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('pool_count')->default(0);
            $table->integer('standing_group_count')->default(0);
            $table->integer('cross_pool_game_count')->default(0);
            $table->integer('cross_standing_group_game_count')->default(0);
            $table->integer('round_type')->default(0);
            $table->text('information')->nullable();
            $table->integer('tournament_group_id');
            $table->integer('team_count')->default(0);
            $table->boolean('deleted')->default(false);
            $table->integer('tournament_program_id')->default(0);
            $table->foreignId('season_sport_id')->nullable()->constrained('season_sports');

            $table->foreign('region_id')->references('id')->on('regions')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tournaments');
    }
};
