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
        Schema::table('sale_summary', function (Blueprint $table) {
            $table->decimal('extra_amount', 15, 2)->nullable()->after('shipping_charge');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_summary', function (Blueprint $table) {
            //
        });
    }
};
