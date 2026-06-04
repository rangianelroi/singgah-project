<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Delete old transition statuses
        DB::table('item_status_logs')
            ->whereIn('status', ['PENDING_SHIPMENT_CONFIRMATION', 'READY_TO_SHIP'])
            ->delete();

        // Step 2: Update enum dengan status baru (hapus PENDING_SHIPMENT_CONFIRMATION dan READY_TO_SHIP)
        Schema::table('item_status_logs', function (Blueprint $table) {
            $table->enum('status', [
                'RECORDED',
                'VERIFIED_BY_SQUAD_LEADER',
                'PENDING_PICKUP',
                'VERIFIED_FOR_STORAGE',
                'IN_STORAGE',
                'SHIPPED',
                'PICKED_UP',
                'DISPOSED'
            ])->change();
        });

        // Step 3: Tambah pending_action ke confiscated_items
        Schema::table('confiscated_items', function (Blueprint $table) {
            $table->enum('pending_action', [
                'shipment_confirmation',
                'payment_confirmation',
            ])->nullable()->after('storage_location');
        });
    }

    public function down(): void
    {
        Schema::table('confiscated_items', function (Blueprint $table) {
            $table->dropColumn('pending_action');
        });

        Schema::table('item_status_logs', function (Blueprint $table) {
            $table->enum('status', [
                'RECORDED',
                'VERIFIED_BY_SQUAD_LEADER',
                'PENDING_PICKUP',
                'VERIFIED_FOR_STORAGE',
                'IN_STORAGE',
                'PENDING_SHIPMENT_CONFIRMATION',
                'READY_TO_SHIP',
                'SHIPPED',
                'PICKED_UP',
                'DISPOSED'
            ])->change();
        });
    }
};
