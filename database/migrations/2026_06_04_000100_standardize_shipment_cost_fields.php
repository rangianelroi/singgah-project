<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Standardize shipping cost fields:
     * - Remove shipping_price (tidak digunakan, gunakan shipping_cost saja)
     * - Rename service_price ke service_fee untuk clarity
     */
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            // Rename service_price ke service_fee
            $table->renameColumn('service_price', 'service_fee');
            // Drop shipping_price karena shipping_cost sudah ada
            $table->dropColumn('shipping_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->renameColumn('service_fee', 'service_price');
            $table->decimal('shipping_price', 12, 2)->nullable();
        });
    }
};
