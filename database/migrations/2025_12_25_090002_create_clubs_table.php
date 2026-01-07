<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clubs', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('building')->nullable();
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('postal_city')->nullable();
            $table->string('country')->nullable();
            $table->string('region_id')->nullable();
            $table->string('phone_number1')->nullable();
            $table->string('phone_number2')->nullable();
            $table->string('email')->nullable();
            $table->text('public_notes')->nullable();
            $table->text('internal_notes')->nullable();
            $table->string('web_address')->nullable();
            $table->string('short_name')->nullable();
            $table->boolean('deleted')->default(false);
            $table->string('district')->nullable();
            $table->string('status')->default('active');
            $table->boolean('is_active')->default(true);
            $table->string('cal_key')->default('0');
            $table->integer('license')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clubs');
    }
};
