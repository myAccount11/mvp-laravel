<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rounds', function (Blueprint $table) {
            $table->id();
            $table->integer('number')->default(0);
            $table->foreignId('tournament_id')->nullable()->constrained('tournaments')->onDelete('set null');
            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();
            $table->integer('week');
            $table->integer('year');
            $table->integer('type')->default(0);
            $table->boolean('force_cross')->default(false);
            $table->boolean('deleted')->default(false);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rounds');
    }
};
