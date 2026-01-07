<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('court_usage', function (Blueprint $table) {
            $table->id();
            $table->integer('court_usage_count')->default(0);
            $table->foreignId('court_requirement_id')->constrained('court_requirements')->onDelete('cascade');
            $table->foreignId('court_id')->constrained('courts')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('court_usage');
    }
};
