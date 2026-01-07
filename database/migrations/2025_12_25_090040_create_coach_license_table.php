<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coach_license', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coach_license_type_id')->constrained('coach_license_type')->onDelete('cascade');
            $table->date('start');
            $table->date('end');
            $table->boolean('deleted')->default(false);
            $table->foreignId('coach_id')->constrained('coach')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coach_license');
    }
};
