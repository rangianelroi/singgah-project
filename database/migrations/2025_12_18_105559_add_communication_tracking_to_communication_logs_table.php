<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('communication_logs', function (Blueprint $table) {
            // Tambah kolom baru untuk tracking status komunikasi
            $table->enum('communication_type', [
                'general',              // Komunikasi umum
                'initial_contact',      // Kontak pertama kali
                'shipment_inquiry',     // Tanya apakah mau kirim
                'payment_follow_up',    // Follow up pembayaran
                'confirmation',         // Konfirmasi berbagai hal
            ])->after('channel')->default('general');

            $table->enum('communication_status', [
                'sent',                      // Baru dikirim, belum ada respon
                'responded',                 // Sudah ada respon
                'shipment_confirmed',        // Penumpang setuju kirim
                'shipment_declined',         // Penumpang tolak kirim
                'payment_requested',         // Sudah minta pembayaran
                'payment_confirmed',         // Pembayaran sudah dikonfirmasi
                'payment_failed',            // Pembayaran gagal/ditolak
                'address_provided',          // Alamat sudah diberikan
                'waiting_response',          // Menunggu respon
                'completed',                 // Komunikasi selesai
            ])->after('communication_type')->default('sent');

            // Tambah field untuk tracking response
            $table->boolean('response_received')->default(false)->after('communication_status');
            $table->timestamp('responded_at')->nullable()->after('response_received');
            
            // Tambah field untuk catatan respon
            $table->text('response_notes')->nullable()->after('responded_at');
        });
    }

    public function down(): void
    {
        Schema::table('communication_logs', function (Blueprint $table) {
            if (Schema::hasColumn('communication_logs', 'communication_type')) {
                $table->dropColumn('communication_type');
            }
            if (Schema::hasColumn('communication_logs', 'communication_status')) {
                $table->dropColumn('communication_status');
            }
            if (Schema::hasColumn('communication_logs', 'response_received')) {
                $table->dropColumn('response_received');
            }
            if (Schema::hasColumn('communication_logs', 'responded_at')) {
                $table->dropColumn('responded_at');
            }
            if (Schema::hasColumn('communication_logs', 'response_notes')) {
                $table->dropColumn('response_notes');
            }
        });
    }
};