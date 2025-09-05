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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            // Enum fields
            $table->enum('transaction_type', ['PaymentFromCustomer', 'PaymentToVendor'])
                  ->comment('Type of transaction: Payment received from customer OR payment to vendor');

            $table->enum('trans_mode', ['cash', 'cheque', 'bank'])
                  ->default('cash')
                  ->comment('Transaction mode: cash, cheque, or bank transfer');

            // Relations
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();

            // Amount
            $table->double('amount', 15, 2)->default(0);

            // Cheque details
            $table->string('cheque_no', 100)->nullable();
            $table->date('cheque_date')->nullable();

            // Extra info
            $table->string('received_from', 100)->nullable();
            $table->string('payee_from', 100)->nullable();
            $table->text('comments')->nullable();

            // Dates
            $table->date('entry_date')->nullable();

            // Foreign key constraints (optional if you have customers/suppliers table)
             $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
             $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
