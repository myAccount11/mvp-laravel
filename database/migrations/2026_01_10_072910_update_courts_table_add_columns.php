<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add missing columns if they don't exist (matching NestJS structure)
        Schema::table('courts', function (Blueprint $table) {
            $table->integer('length')->default(0);
            $table->integer('width')->default(0);
            $table->integer('side_space')->default(0);
            $table->integer('end_space')->default(0);
            $table->integer('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('courts')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courts', function (Blueprint $table) {
            $table->dropColumn('length');
            $table->dropColumn('width');
            $table->dropColumn('side_space');
            $table->dropColumn('end_space');
            $table->dropColumn('parent_id');
        });
    }
};
