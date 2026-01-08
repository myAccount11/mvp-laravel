<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('team_tournaments', function (Blueprint $table) {
            // Remove timestamps (model has timestamps: false)
            if (Schema::hasColumn('team_tournaments', 'created_at')) {
                $table->dropTimestamps();
            }
            
            // Add missing columns
            $table->foreignId('pool_id')->nullable()->after('tournament_id')->constrained('pools')->onDelete('set null');
            $table->integer('pool_key')->nullable()->after('pool_id');
            $table->integer('start_points')->nullable()->after('pool_key');
            $table->boolean('is_deleted')->default(false)->after('start_points');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('team_tournaments', function (Blueprint $table) {
            // Drop foreign key first
            if (Schema::hasColumn('team_tournaments', 'pool_id')) {
                $table->dropForeign(['pool_id']);
            }
            
            // Drop columns
            $table->dropColumn(['pool_id', 'pool_key', 'start_points', 'is_deleted']);
            
            // Restore timestamps
            $table->timestamps();
        });
    }
};
