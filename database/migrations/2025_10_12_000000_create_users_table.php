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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            // $table->unsignedBigInteger('store_id')->nullable();
            $table->unsignedBigInteger('role_id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone_number')->nullable();
            $table->text('address')->nullable();
            $table->string('user_image')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->tinyInteger('status')->default(0);
            $table->rememberToken();
            $table->timestamps();

            // $table->foreign('store_id')->references('id')->on('store')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('role')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
