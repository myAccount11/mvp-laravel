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
        Schema::table('tournaments', function (Blueprint $table) {
            // Drop name column if it exists (Node.js uses alias instead)
            if (Schema::hasColumn('tournaments', 'name')) {
                $table->dropColumn('name');
            }
            
            // Drop tournament_type_id if it exists (not in Node.js model)
            if (Schema::hasColumn('tournaments', 'tournament_type_id')) {
                $table->dropColumn('tournament_type_id');
            }
            
            // Drop timestamps (Node.js model has timestamps: false)
            if (Schema::hasColumn('tournaments', 'created_at')) {
                $table->dropTimestamps();
            }
            
            // Add required columns
            $table->string('alias')->after('id');
            $table->string('short_name')->after('alias');
            
            // Add nullable columns
            $table->integer('region_id')->nullable()->after('short_name');
            $table->date('start_date')->nullable()->after('region_id');
            $table->date('end_date')->nullable()->after('start_date');
            $table->text('information')->nullable()->after('round_type');
            
            // Add integer columns with defaults
            $table->integer('pool_count')->default(0)->after('end_date');
            $table->integer('standing_group_count')->default(0)->after('pool_count');
            $table->integer('cross_pool_game_count')->default(0)->after('standing_group_count');
            $table->integer('cross_standing_group_game_count')->default(0)->after('cross_pool_game_count');
            $table->integer('round_type')->default(0)->after('cross_standing_group_game_count');
            $table->integer('tournament_group_id')->after('information');
            $table->integer('team_count')->default(0)->after('tournament_group_id');
            $table->integer('tournament_program_id')->default(0)->after('deleted');
            
            // Add boolean column
            $table->boolean('deleted')->default(false)->after('team_count');
            
            // Add foreign keys
            try {
                if (Schema::hasTable('regions')) {
                    $table->foreign('region_id')->references('id')->on('regions')->onDelete('set null');
                }
            } catch (\Exception $e) {
                // Table might not exist, skip foreign key
            }
            
            try {
                if (Schema::hasTable('tournament_groups')) {
                    $table->foreign('tournament_group_id')->references('id')->on('tournament_groups')->onDelete('cascade');
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
        Schema::table('tournaments', function (Blueprint $table) {
            // Drop foreign keys first
            try {
                $table->dropForeign(['region_id']);
            } catch (\Exception $e) {
                // Foreign key might not exist
            }
            
            try {
                $table->dropForeign(['tournament_group_id']);
            } catch (\Exception $e) {
                // Foreign key might not exist
            }
            
            // Drop columns
            $table->dropColumn([
                'alias',
                'short_name',
                'region_id',
                'start_date',
                'end_date',
                'pool_count',
                'standing_group_count',
                'cross_pool_game_count',
                'cross_standing_group_game_count',
                'round_type',
                'information',
                'tournament_group_id',
                'team_count',
                'deleted',
                'tournament_program_id',
            ]);
            
            // Restore original columns
            $table->string('name')->nullable();
            $table->foreignId('tournament_type_id')->nullable();
            $table->timestamps();
        });
    }
};
