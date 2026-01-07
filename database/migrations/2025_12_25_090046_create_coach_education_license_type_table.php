<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coach_education_license_type', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coach_education_id')->constrained('coach_education')->onDelete('cascade');
            $table->foreignId('coach_license_type_id')->constrained('coach_license_type')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coach_education_license_type');
    }
};
