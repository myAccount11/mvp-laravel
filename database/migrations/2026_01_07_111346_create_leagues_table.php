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
        Schema::create('leagues', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->foreignId('club_id')->nullable()->constrained('clubs')->onDelete('set null');
            $table->boolean('deleted')->default(false);
            $table->integer('season_sport_id');
            $table->boolean('is_active')->default(true);
            $table->integer('user_id')->nullable();
            $table->string('information')->nullable();
            $table->foreignId('organizer_id')->nullable()->constrained('organizers')->onDelete('set null');
            $table->integer('sport_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leagues');
    }
};
