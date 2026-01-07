<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->integer('type_id');
            $table->text('html');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('to_id');
            $table->integer('restriction')->nullable();
            $table->timestamp('notification_time')->nullable();
            $table->string('email')->nullable();
            $table->string('subject')->nullable();
            $table->timestamp('sent')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
