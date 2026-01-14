<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tournament_configs', function (Blueprint $table) {
            $table->dropColumn([
                'number',
                'game_dead_line',
                'cooldown_minutes',
                'refs_per_game',
                'refs_from_associations',
                '24sec_required',
                'stats_required',
                'report_required',
                'default_score_sheet_type',
                'allow_adjusted_score_sheet',
                'in_active',
                'court_requirement_id',
                'deleted',
                'tl_edit_enabled',
                'games_hidden',
                'cm_time_set_until',
                'coach_license_type_id',
                'ref_prio',
                'allow_mentor_prospect',
                'star_rating',
                'transportation_fee',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tournament_configs', function (Blueprint $table) {
            $table->integer('number')->default(0)->after('id');
            $table->date('game_dead_line')->nullable()->after('name');
            $table->integer('cooldown_minutes')->default(0)->after('expected_duration_minutes');
            $table->integer('refs_per_game')->default(3)->after('cooldown_minutes');
            $table->boolean('refs_from_associations')->default(false)->after('refs_per_game');
            $table->boolean('24sec_required')->default(false)->after('refs_from_associations');
            $table->boolean('stats_required')->default(false)->after('24sec_required');
            $table->boolean('report_required')->default(false)->after('stats_required');
            $table->integer('default_score_sheet_type')->default(1)->after('report_required');
            $table->boolean('allow_adjusted_score_sheet')->default(false)->after('default_score_sheet_type');
            $table->boolean('in_active')->default(false)->after('allow_adjusted_score_sheet');
            $table->integer('court_requirement_id')->nullable()->after('in_active');
            $table->boolean('deleted')->default(false)->after('season_sport_id');
            $table->boolean('tl_edit_enabled')->default(false)->after('deleted');
            $table->boolean('games_hidden')->default(false)->after('tl_edit_enabled');
            $table->timestamp('cm_time_set_until')->nullable()->after('earliest_start');
            $table->integer('coach_license_type_id')->nullable()->after('latest_start');
            $table->integer('ref_prio')->nullable()->after('coach_license_type_id');
            $table->integer('allow_mentor_prospect')->nullable()->after('ref_prio');
            $table->integer('star_rating')->nullable()->after('allow_mentor_prospect');
            $table->integer('transportation_fee')->nullable()->after('star_rating');
        });
    }
};
