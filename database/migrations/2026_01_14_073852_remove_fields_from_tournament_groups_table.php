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
        Schema::table('tournament_groups', function (Blueprint $table) {
            $table->dropColumn([
                'levels',
                'ref_nomination_id',
                'officials_type_id',
                'player_license_type_id',
                'score_sheet_type_id',
                'penalty_type_id',
                'star_rating',
                'tournament_type_id',
                'show_birth_in_score_sheet',
                'registration_fee',
                'hide_from_rankings',
                'allow_mentor_prospect',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tournament_groups', function (Blueprint $table) {
            $table->boolean('hide_from_rankings')->default(false)->after('age_group');
            $table->boolean('allow_mentor_prospect')->default(false)->after('hide_from_rankings');
            $table->integer('star_rating')->nullable()->after('allow_mentor_prospect');
            $table->integer('score_sheet_type_id')->nullable()->after('star_rating');
            $table->integer('ref_nomination_id')->nullable()->after('end_date');
            $table->integer('officials_type_id')->nullable()->after('ref_nomination_id');
            $table->integer('levels')->nullable()->after('officials_type_id');
            $table->integer('player_license_type_id')->nullable()->after('moving_strategy_id');
            $table->integer('penalty_type_id')->nullable()->after('player_license_type_id');
            $table->boolean('show_birth_in_score_sheet')->default(false)->after('penalty_type_id');
            $table->integer('registration_fee')->nullable()->after('region_id');
            $table->integer('tournament_type_id')->nullable()->after('information');
        });
    }
};
