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
        Schema::create('sale_summary', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('store_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->enum('document_type', ['S', 'SR'])->default('S');
            $table->enum('customer_type', ['cash', 'credit'])->default('cash');
            $table->string('invoice_number')->unique();
            $table->string('customer_name', 100)->nullable();
            $table->dateTime('sale_date');
            $table->decimal('total_amount', 15, 2);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('tax', 15, 2)->default(0);
            $table->decimal('shipping_charge', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->boolean('is_returned')->default(0);
            $table->boolean('is_canceled')->default(0);
            $table->text('ref_document_no')->nullable();
            $table->enum('status', ['received', 'pending', 'canceled','complete'])->default('pending');
            $table->dateTime('is_return_date')->nullable();
            $table->dateTime('is_cancel_date')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_summary');
    }
};
