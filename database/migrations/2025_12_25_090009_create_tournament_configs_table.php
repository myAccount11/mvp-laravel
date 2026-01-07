<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tournament_configs', function (Blueprint $table) {
            $table->id();
            $table->integer('number')->default(0);
            $table->string('name');
            $table->date('game_dead_line')->nullable();
            $table->date('free_reschedule_until_date')->nullable();
            $table->date('registration_dead_line')->nullable();
            $table->integer('minimum_warmup_minutes')->default(0);
            $table->integer('expected_duration_minutes');
            $table->integer('cooldown_minutes')->default(0);
            $table->integer('refs_per_game')->default(3);
            $table->boolean('refs_from_associations')->default(false);
            $table->boolean('24sec_required')->default(false);
            $table->boolean('stats_required')->default(false);
            $table->boolean('report_required')->default(false);
            $table->integer('default_score_sheet_type')->default(1);
            $table->boolean('allow_adjusted_score_sheet')->default(false);
            $table->boolean('in_active')->default(false);
            $table->integer('court_requirement_id')->nullable();
            $table->integer('season_sport_id')->nullable();
            $table->boolean('deleted')->default(false);
            $table->boolean('tl_edit_enabled')->default(false);
            $table->boolean('games_hidden')->default(false);
            $table->string('information')->nullable();
            $table->string('earliest_start')->nullable();
            $table->timestamp('cm_time_set_until')->nullable();
            $table->string('latest_start')->nullable();
            $table->integer('coach_license_type_id')->nullable();
            $table->integer('ref_prio')->nullable();
            $table->integer('allow_mentor_prospect')->nullable();
            $table->integer('star_rating')->nullable();
            $table->integer('transportation_fee')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tournament_configs');
    }
};
