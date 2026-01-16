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
        Schema::create('tournament_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->unique()->constrained('tournaments')->onDelete('cascade');
            
            // Structure-specific settings stored as JSON for flexibility
            // This allows different structures to have different settings
            // Example: {"teams_count": 16, "games_between": 2, "final_games_between": 1}
            $table->json('settings')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournament_configs');
    }
};
