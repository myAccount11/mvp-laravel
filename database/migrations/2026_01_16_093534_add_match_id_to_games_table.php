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
        Schema::table('games', function (Blueprint $table) {
            // Round information for bracket structure
            $table->integer('round_number')->nullable();
            $table->string('round_name')->nullable();
            $table->integer('match_number')->nullable();
            $table->integer('position')->nullable();

            // Match-specific fields for playoff brackets
            $table->integer('games_between')->nullable()->default(1);
            $table->integer('home_wins')->nullable()->default(0);
            $table->integer('away_wins')->nullable()->default(0);

            // For playoff brackets: which matches feed into this match (self-referential)
            $table->foreignId('parent_match_1_id')->nullable()->constrained('games')->onDelete('set null');
            $table->foreignId('parent_match_2_id')->nullable()->constrained('games')->onDelete('set null');

            // For group stage + playoff: which group/position feeds into this match
            $table->integer('group_position')->nullable();

            $table->boolean('is_final')->default(false);

            // Indexes for performance
            $table->index(['tournament_id', 'round_number']);
            $table->index(['tournament_id', 'round_number', 'match_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropIndex(['tournament_id', 'round_number']);
            $table->dropIndex(['tournament_id', 'round_number', 'match_number']);
            $table->dropForeign(['parent_match_1_id']);
            $table->dropForeign(['parent_match_2_id']);
            $table->dropColumn([
                'round_number',
                'round_name',
                'match_number',
                'position',
                'games_between',
                'home_wins',
                'away_wins',
                'parent_match_1_id',
                'parent_match_2_id',
                'group_position',
                'is_final',
            ]);
        });
    }
};
