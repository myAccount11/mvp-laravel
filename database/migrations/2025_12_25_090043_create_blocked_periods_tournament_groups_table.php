<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blocked_periods_tournaments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blocked_period_id')->constrained('blocked_periods')->onDelete('cascade');
            $table->foreignId('tournament_id')->nullable()->constrained('tournaments')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blocked_periods_tournaments');
    }
};
