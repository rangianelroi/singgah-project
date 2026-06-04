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
        Schema::table('communication_logs', function (Blueprint $table) {
            $table->enum('communication_status', [
                'sent',
                'responded',
                'shipment_confirmed',
                'shipment_declined',
                'payment_requested',
                'payment_confirmed',
                'payment_failed',
                'address_provided',
                'waiting_response',
                'completed',
                'tracking_sent',
            ])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('communication_logs', function (Blueprint $table) {
            $table->enum('communication_status', [
                'sent',
                'responded',
                'shipment_confirmed',
                'shipment_declined',
                'payment_requested',
                'payment_confirmed',
                'payment_failed',
                'address_provided',
                'waiting_response',
                'completed',
            ])->change();
        });
    }
};
