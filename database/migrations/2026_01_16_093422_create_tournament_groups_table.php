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
        Schema::create('tournament_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained('tournaments')->onDelete('cascade');
            
            $table->string('name')->comment('Group name (e.g., "Group A", "Group 1")');
            $table->integer('group_number')->comment('Group number for ordering');
            $table->integer('teams_count')->comment('Number of teams in this group');
            $table->integer('games_between')->comment('Number of games between teams in group stage');
            $table->integer('advancing_teams_count')->default(2)->comment('Number of teams that advance to playoff');
            
            $table->boolean('is_deleted')->default(false);
            
            $table->timestamps();
            
            // Indexes
            $table->index(['tournament_id', 'group_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournament_groups');
    }
};
