<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venues', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('postal_city')->nullable();
            $table->string('country')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('web_address')->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('lat_lng')->nullable();
            $table->string('place_id')->nullable();
            $table->string('cal_key')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venues');
    }
};
