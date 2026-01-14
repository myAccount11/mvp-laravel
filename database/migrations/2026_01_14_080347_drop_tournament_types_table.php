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
        // Drop the tournament_types table
        Schema::dropIfExists('tournament_types');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the tournament_types table
        Schema::create('tournament_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('season_sport_id')->constrained('season_sports')->onDelete('cascade');
        });

        // Re-add foreign key to tournament_groups if column exists
        if (Schema::hasColumn('tournament_groups', 'tournament_type_id')) {
            Schema::table('tournament_groups', function (Blueprint $table) {
                try {
                    if (Schema::hasTable('tournament_types')) {
                        $table->foreign('tournament_type_id')->references('id')->on('tournament_types')->onDelete('set null');
                    }
                } catch (\Exception $e) {
                    // Table might not exist
                }
            });
        }
    }
};
