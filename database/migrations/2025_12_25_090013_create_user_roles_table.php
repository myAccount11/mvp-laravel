<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('club_id')->nullable();
            $table->foreignId('team_id')->nullable();
            $table->integer('season_sport_id')->nullable();
            $table->integer('user_role_approved_by_user_id')->nullable();
            $table->string('user_role_spec')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_roles');
    }
};
