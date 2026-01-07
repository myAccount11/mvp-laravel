<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coach_education', function (Blueprint $table) {
            $table->id();
            $table->string('module')->nullable();
            $table->date('date')->nullable();
            $table->string('comment')->nullable();
            $table->integer('hours')->nullable();
            $table->foreignId('coach_id')->constrained('coach')->onDelete('cascade');
            $table->integer('mvp');
            $table->boolean('deleted')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coach_education');
    }
};
