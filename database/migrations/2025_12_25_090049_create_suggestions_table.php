<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suggestions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained('games')->onDelete('cascade');
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->foreignId('court_id')->nullable()->constrained('courts')->onDelete('set null');
            $table->foreignId('requested_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('accepted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('rejected_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('requested_date')->nullable();
            $table->timestamp('accepted_date')->nullable();
            $table->timestamp('rejected_date')->nullable();
            $table->timestamp('approved_date')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suggestions');
    }
};
