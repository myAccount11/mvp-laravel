<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rounds', function (Blueprint $table) {
            $table->integer('week')->nullable()->change();
            $table->integer('year')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('rounds', function (Blueprint $table) {
            $table->integer('week')->nullable(false)->change();
            $table->integer('year')->nullable(false)->change();
        });
    }
};



