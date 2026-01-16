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
        Schema::create('team_tournament_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('tournament_groups')->onDelete('cascade');
            $table->foreignId('team_id')->constrained('teams')->onDelete('cascade');
            
            // Group stage standings
            $table->integer('position')->default(0)->comment('Current position in group');
            $table->integer('points')->default(0);
            $table->integer('wins')->default(0);
            $table->integer('losses')->default(0);
            $table->integer('draws')->default(0);
            $table->integer('goals_for')->default(0);
            $table->integer('goals_against')->default(0);
            $table->integer('goal_difference')->default(0)->comment('Calculated: goals_for - goals_against');
            
            $table->timestamps();
            
            // Unique constraint: a team can only be in a group once
            $table->unique(['group_id', 'team_id']);
            $table->index(['group_id', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_tournament_groups');
    }
};
