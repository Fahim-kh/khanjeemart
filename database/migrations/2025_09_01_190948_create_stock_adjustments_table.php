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
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by'); // user who created adjustment
            $table->unsignedBigInteger('store_id')->nullable();
            $table->date('adjustment_date');
            $table->string('reference')->unique();
            $table->text('notes')->nullable();
            $table->timestamps();

            // foreign keys (optional)
            // $table->foreign('created_by')->references('id')->on('users');
            // $table->foreign('store_id')->references('id')->on('stores');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_adjustments');
    }
};
