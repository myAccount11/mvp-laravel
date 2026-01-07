<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_drafts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('round_id')->nullable()->constrained('rounds')->onDelete('set null');
            $table->foreignId('tournament_id')->nullable()->constrained('tournaments')->onDelete('set null');
            $table->foreignId('pool_id')->nullable()->constrained('pools')->onDelete('set null');
            $table->date('term_date')->nullable();
            $table->integer('home_key')->nullable();
            $table->integer('away_key')->nullable();
            $table->integer('round_number')->nullable();
            $table->integer('pool_id_cross_master')->default(0);
            $table->integer('pool_id_cross_slave')->default(0);
            $table->boolean('switch_optimized')->default(false);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_drafts');
    }
};
