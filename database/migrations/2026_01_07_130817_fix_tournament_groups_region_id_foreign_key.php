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
        // Drop the existing foreign key constraint if it exists
        try {
            Schema::table('tournament_groups', function (Blueprint $table) {
                $table->dropForeign(['region_id']);
            });
        } catch (\Exception $e) {
            // Foreign key might not exist, continue
        }

        // Convert any existing 0 values to NULL
        DB::table('tournament_groups')
            ->where('region_id', 0)
            ->update(['region_id' => null]);

        // Recreate the foreign key constraint with proper NULL handling
        if (Schema::hasTable('regions')) {
            Schema::table('tournament_groups', function (Blueprint $table) {
                $table->foreign('region_id')
                    ->references('id')
                    ->on('regions')
                    ->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tournament_groups', function (Blueprint $table) {
            $table->dropForeign(['region_id']);
        });

        Schema::table('tournament_groups', function (Blueprint $table) {
            if (Schema::hasTable('regions')) {
                $table->foreign('region_id')
                    ->references('id')
                    ->on('regions')
                    ->onDelete('set null');
            }
        });
    }
};
