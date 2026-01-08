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
            // String columns
            $table->string('short_name')->nullable()->after('name');
            $table->string('gender')->nullable()->after('short_name');
            $table->string('age_group')->nullable()->after('gender');
            $table->text('information')->nullable()->after('registration_fee');
            
            // Boolean columns
            $table->boolean('hide_from_rankings')->default(false)->after('age_group');
            $table->boolean('allow_mentor_prospect')->default(false)->after('hide_from_rankings');
            $table->boolean('is_active')->default(true)->after('score_sheet_type_id');
            $table->boolean('show_birth_in_score_sheet')->default(false)->after('penalty_type_id');
            
            // Integer columns
            $table->integer('star_rating')->nullable()->after('allow_mentor_prospect');
            $table->integer('score_sheet_type_id')->nullable()->after('star_rating');
            $table->integer('min_teams')->nullable()->after('is_active');
            $table->integer('max_teams')->nullable()->after('min_teams');
            $table->integer('region_id')->nullable()->after('max_teams');
            $table->integer('registration_fee')->nullable()->after('region_id');
            $table->integer('tournament_type_id')->nullable()->after('information');
            $table->integer('tournament_structure_id')->nullable()->after('tournament_type_id');
            $table->integer('tournament_registration_type_id')->nullable()->after('tournament_structure_id');
            $table->integer('ref_nomination_id')->nullable()->after('end_date');
            $table->integer('officials_type_id')->nullable()->after('ref_nomination_id');
            $table->integer('levels')->nullable()->after('officials_type_id');
            $table->integer('set_game_strategy_id')->default(0)->after('levels');
            $table->integer('moving_strategy_id')->nullable()->after('set_game_strategy_id');
            $table->integer('player_license_type_id')->nullable()->after('moving_strategy_id');
            $table->integer('penalty_type_id')->nullable()->after('player_license_type_id');
            $table->integer('tournament_configs_id')->nullable()->after('show_birth_in_score_sheet');
            
            // Date columns
            $table->dateTime('start_date')->nullable()->after('tournament_registration_type_id');
            $table->dateTime('end_date')->nullable()->after('start_date');
            
            // Foreign keys (only add if tables exist)
            try {
                if (Schema::hasTable('regions')) {
                    $table->foreign('region_id')->references('id')->on('regions')->onDelete('set null');
                }
            } catch (\Exception $e) {
                // Table might not exist, skip foreign key
            }
            
            try {
                if (Schema::hasTable('tournament_types')) {
                    $table->foreign('tournament_type_id')->references('id')->on('tournament_types')->onDelete('set null');
                }
            } catch (\Exception $e) {
                // Table might not exist, skip foreign key
            }
            
            try {
                if (Schema::hasTable('tournament_structures')) {
                    $table->foreign('tournament_structure_id')->references('id')->on('tournament_structures')->onDelete('set null');
                }
            } catch (\Exception $e) {
                // Table might not exist, skip foreign key
            }
            
            try {
                if (Schema::hasTable('tournament_registration_types')) {
                    $table->foreign('tournament_registration_type_id')->references('id')->on('tournament_registration_types')->onDelete('set null');
                }
            } catch (\Exception $e) {
                // Table might not exist, skip foreign key
            }
            
            try {
                if (Schema::hasTable('tournament_configs')) {
                    $table->foreign('tournament_configs_id')->references('id')->on('tournament_configs')->onDelete('set null');
                }
            } catch (\Exception $e) {
                // Table might not exist, skip foreign key
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tournament_groups', function (Blueprint $table) {
            // Drop foreign keys first (if they exist)
            try {
                $table->dropForeign(['region_id']);
            } catch (\Exception $e) {
                // Foreign key might not exist
            }
            
            try {
                $table->dropForeign(['tournament_type_id']);
            } catch (\Exception $e) {
                // Foreign key might not exist
            }
            
            try {
                $table->dropForeign(['tournament_structure_id']);
            } catch (\Exception $e) {
                // Foreign key might not exist
            }
            
            try {
                $table->dropForeign(['tournament_registration_type_id']);
            } catch (\Exception $e) {
                // Foreign key might not exist
            }
            
            try {
                $table->dropForeign(['tournament_configs_id']);
            } catch (\Exception $e) {
                // Foreign key might not exist
            }
            
            // Drop columns
            $table->dropColumn([
                'short_name',
                'gender',
                'age_group',
                'hide_from_rankings',
                'allow_mentor_prospect',
                'star_rating',
                'score_sheet_type_id',
                'is_active',
                'min_teams',
                'max_teams',
                'region_id',
                'registration_fee',
                'information',
                'tournament_type_id',
                'tournament_structure_id',
                'tournament_registration_type_id',
                'start_date',
                'end_date',
                'ref_nomination_id',
                'officials_type_id',
                'levels',
                'set_game_strategy_id',
                'moving_strategy_id',
                'player_license_type_id',
                'penalty_type_id',
                'show_birth_in_score_sheet',
                'tournament_configs_id',
            ]);
        });
    }
};
