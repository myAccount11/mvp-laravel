<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conflicts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained('games')->onDelete('cascade');
            $table->string('start_time')->nullable();
            $table->text('blocked_association')->nullable();
            $table->text('blocked_team')->nullable();
            $table->text('games_to_close')->nullable();
            $table->text('games_on_court')->nullable();
            $table->text('reservations')->nullable();
            $table->text('coaches')->nullable();
            $table->text('has_court')->nullable();
            $table->boolean('ignore_associations')->default(false);
            $table->boolean('ignore_home')->default(false);
            $table->boolean('ignore_away')->default(false);
            $table->timestamp('created_at')->useCurrent();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conflicts');
    }
};
