<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_plan', function (Blueprint $table) {
            $table->id();
            $table->integer('game_role_id');
            $table->integer('status_id');
            $table->string('display');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('team_id')->constrained('teams')->onDelete('cascade');
            $table->foreignId('game_id')->constrained('games')->onDelete('cascade');
            $table->string('responsible_type');
            $table->integer('responsible_id');
            $table->integer('responsible_accepted_by_id');
            $table->date('responsible_accepted_time_stamp')->nullable();
            $table->integer('assigned_to_id');
            $table->integer('assigned_accepted_by_id');
            $table->date('assigned_accepted_time_stamp')->nullable();
            $table->integer('fee');
            $table->integer('ferry_fee');
            $table->integer('food_fee');
            $table->integer('game_fee');
            $table->integer('driving_fee');
            $table->integer('fee_changed_by');
            $table->integer('original_fee');
            $table->integer('fee_approved');
            $table->integer('fee_final');
            $table->string('fee_comment');
            $table->timestamp('fee_approved_by_ref_time_stamp')->nullable();
            $table->integer('fee_approved_by_final');
            $table->integer('fee_ref');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_plan');
    }
};
