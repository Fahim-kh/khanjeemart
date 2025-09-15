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
        Schema::create('pos_sale_details', function (Blueprint $table) {
            $table->id();
            // Link with sale_summary
            $table->unsignedBigInteger('sale_summary_id'); // instead of varchar invoice_id
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->integer('quantity');
            $table->decimal('cost_unit_price', 15, 2);
            $table->decimal('selling_unit_price', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->dateTime('sale_date');
            $table->timestamps();
            // Foreign keys
            $table->foreign('sale_summary_id')->references('id')->on('sale_summary')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_details');
    }
};
