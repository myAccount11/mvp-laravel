<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('player_license', function (Blueprint $table) {
            $table->id();
            $table->timestamp('start')->nullable();
            $table->timestamp('end')->nullable();
            $table->string('club_name')->nullable();
            $table->foreignId('club_id')->nullable()->constrained('clubs')->onDelete('set null');
            $table->foreignId('player_id')->constrained('player')->onDelete('cascade');
            $table->string('status')->nullable();
            $table->foreignId('season_sport_id')->nullable()->constrained('season_sports')->onDelete('set null');
            $table->integer('identity_id')->nullable();
            $table->integer('on_contract')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_license');
    }
};
