<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system', function (Blueprint $table) {
            $table->id();
            $table->foreignId('season_sport_id')->constrained('season_sports')->onDelete('cascade');
            $table->integer('next_coach_license');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system');
    }
};
