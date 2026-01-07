<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_tournament_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('teams');
            $table->foreignId('tournament_group_id')->constrained('tournament_groups');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_tournament_groups');
    }
};
