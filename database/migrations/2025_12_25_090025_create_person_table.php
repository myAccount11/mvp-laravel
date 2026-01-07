<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('person', function (Blueprint $table) {
            $table->id();
            $table->integer('external_id')->nullable();
            $table->string('email')->unique();
            $table->string('name');
            $table->foreignId('season_sport_id')->nullable()->constrained('season_sports')->onDelete('set null');
            $table->boolean('deleted')->default(false);
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('phone_numbers')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('latlng')->nullable();
            $table->string('place_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('person');
    }
};
