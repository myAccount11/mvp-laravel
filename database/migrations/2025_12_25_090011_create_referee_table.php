<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referee', function (Blueprint $table) {
            $table->id();
            $table->integer('license')->unique();
            $table->boolean('is_active')->default(true);
            $table->integer('recalc_coordinates')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('prio')->nullable();
            $table->integer('prio_max')->nullable();
            $table->boolean('only_with_better')->default(false);
            $table->integer('max_star_rating')->nullable();
            $table->boolean('mentor')->default(false);
            $table->boolean('prospect')->default(false);
            $table->boolean('reserve')->default(false);
            $table->integer('showwin_mvp_min')->default(1);
            $table->integer('showwin_mvp_max')->default(6);
            $table->integer('showwin_mvp_max_distance')->default(100);
            $table->integer('showwin_mvp_notify_on_new')->default(1);
            $table->integer('commisioner_level')->nullable();
            $table->integer('evaluator_level')->nullable();
            $table->boolean('can_three')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referee');
    }
};
