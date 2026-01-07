<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservation_types', function (Blueprint $table) {
            $table->id();
            $table->string('text');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservation_types');
    }
};
