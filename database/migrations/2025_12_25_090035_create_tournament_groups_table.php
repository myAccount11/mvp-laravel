<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tournament_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('short_name')->nullable();
            $table->string('gender')->nullable();
            $table->string('age_group')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('min_teams')->nullable();
            $table->integer('max_teams')->nullable();
            $table->integer('region_id')->nullable();
            $table->text('information')->nullable();
            $table->integer('tournament_structure_id')->nullable();
            $table->integer('tournament_registration_type_id')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->integer('set_game_strategy_id')->default(0);
            $table->integer('moving_strategy_id')->nullable();
            $table->integer('league_id')->nullable();
            $table->date('free_reschedule_until_date')->nullable();
            $table->date('registration_dead_line')->nullable();
            $table->integer('minimum_warmup_minutes')->default(0);
            $table->integer('expected_duration_minutes');
            $table->string('earliest_start')->nullable();
            $table->string('latest_start')->nullable();
            $table->timestamps();
            
            $table->foreign('region_id')->references('id')->on('regions')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tournament_groups');
    }
};
