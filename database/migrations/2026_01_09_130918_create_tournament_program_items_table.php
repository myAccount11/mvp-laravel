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
        if (!Schema::hasTable('tournament_program_items')) {
            Schema::create('tournament_program_items', function (Blueprint $table) {
                $table->id();
                $table->integer('round_number');
                $table->integer('home_key');
                $table->integer('away_key');
                $table->foreignId('tournament_program_id')->constrained('tournament_programs')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournament_program_items');
    }
};
