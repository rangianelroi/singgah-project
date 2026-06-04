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
        Schema::table('shipments', function (Blueprint $table) {
            $table->string('tracking_number')->nullable()->change();
            $table->decimal('shipping_price', 12, 2)->nullable();
            $table->decimal('service_price', 12, 2)->nullable();
            $table->string('payment_proof_path')->nullable();
            $table->foreignId('address_id')->nullable()->constrained('addresses')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Address::class);
            $table->dropColumn(['tracking_number', 'shipping_price', 'service_price', 'payment_proof_path', 'address_id']);
        });
    }
};
