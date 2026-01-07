<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->nullable()->constrained('clubs');
            $table->string('local_name')->nullable();
            $table->boolean('deleted')->default(false);
            $table->integer('ancestor_id')->nullable();
            $table->string('cal_key')->default('0');
            $table->integer('license')->default(0);
            $table->string('tournament_name')->nullable();
            $table->string('gender')->nullable();
            $table->string('age_group')->nullable();
            $table->integer('official_type_id')->default(0);
            $table->integer('official_team_id')->nullable();
            $table->integer('club_rank')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
