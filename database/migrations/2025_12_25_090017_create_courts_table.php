<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venue_id')->constrained('venues');
            $table->string('name')->nullable();
            $table->string('court_type')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('length')->default(0);
            $table->integer('width')->default(0);
            $table->integer('side_space')->default(0);
            $table->integer('end_space')->default(0);
            $table->integer('parent_id')->nullable();
            $table->timestamps();
            
            $table->foreign('parent_id')->references('id')->on('courts')->onDelete('set null')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courts');
    }
};
