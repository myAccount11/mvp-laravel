<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tournament_configs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('free_reschedule_until_date')->nullable();
            $table->date('registration_dead_line')->nullable();
            $table->integer('minimum_warmup_minutes')->default(0);
            $table->integer('expected_duration_minutes');
            $table->integer('season_sport_id')->nullable();
            $table->string('information')->nullable();
            $table->string('earliest_start')->nullable();
            $table->string('latest_start')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tournament_configs');
    }
};
