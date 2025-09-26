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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('confiscated_items')->onDelete('cascade');
            $table->string('tracking_number')->nullable();
            $table->decimal('shipping_cost', 10, 2)->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
            $table->string('recipient_name');
            $table->string('recipient_phone');
            $table->text('street_address');
            $table->string('subdistrict');
            $table->string('district');
            $table->string('city');
            $table->string('province');
            $table->string('postal_code');
            $table->string('country');
            $table->timestamps();

            $table->index(['item_id', 'tracking_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
