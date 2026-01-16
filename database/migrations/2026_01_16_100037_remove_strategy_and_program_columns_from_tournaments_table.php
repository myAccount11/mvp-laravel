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
            $table->dropColumn([
                'moving_strategy_id',
                'set_game_strategy_id',
                'tournament_program_id',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tournaments', function (Blueprint $table) {
            $table->integer('moving_strategy_id')->nullable()->after('set_game_strategy_id');
            $table->integer('set_game_strategy_id')->default(0)->after('tournament_registration_type_id');
            $table->integer('tournament_program_id')->default(0)->after('tournament_structure_id');
        });
    }
};
