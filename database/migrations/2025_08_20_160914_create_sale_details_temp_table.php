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
        Schema::create('sale_details_temp', function (Blueprint $table) {
            $table->id();

            // Product & warehouse reference
            $table->unsignedBigInteger('sale_summary_id'); 
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('warehouse_id')->nullable();;

            $table->integer('quantity');
            $table->decimal('cost_unit_price', 15, 2);
            $table->decimal('selling_unit_price', 15, 2);
            $table->decimal('subtotal', 15, 2);

            $table->dateTime('sale_date')->nullable();

            // Tracking
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('session_id', 100)->nullable();

            $table->timestamps();

            // Foreign keys (same as final table)
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_details_temp');
    }
};
