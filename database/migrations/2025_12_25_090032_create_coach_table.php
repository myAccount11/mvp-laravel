<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coach', function (Blueprint $table) {
            $table->id();
            $table->integer('license')->unique();
            $table->foreignId('person_id')->unique()->constrained('person')->onDelete('cascade');
            $table->string('level');
            $table->date('start');
            $table->date('end');
            $table->integer('master_license')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coach');
    }
};
