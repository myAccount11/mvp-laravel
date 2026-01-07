<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('court_requirements', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('season_sport_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('court_requirements');
    }
};
