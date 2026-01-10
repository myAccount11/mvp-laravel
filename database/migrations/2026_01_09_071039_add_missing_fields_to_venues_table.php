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
        // Rename city to postal_city using raw SQL (PostgreSQL)
        \DB::statement('ALTER TABLE venues RENAME COLUMN city TO postal_city');
        
        Schema::table('venues', function (Blueprint $table) {
            // Add missing fields
            $table->string('web_address')->nullable()->after('phone_number');
            $table->boolean('is_active')->default(true)->after('email');
            $table->string('lat_lng')->nullable()->after('is_active');
            $table->string('place_id')->nullable()->after('lat_lng');
            $table->string('cal_key')->nullable()->after('place_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('venues', function (Blueprint $table) {
            // Remove added fields
            $table->dropColumn(['web_address', 'is_active', 'lat_lng', 'place_id', 'cal_key']);
        });
        
        // Rename back to city using raw SQL
        \DB::statement('ALTER TABLE venues RENAME COLUMN postal_city TO city');
    }
};
