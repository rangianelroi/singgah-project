<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommunicationLog extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'item_id',
        'user_id',
        'channel',
        'communication_type',
        'communication_status',
        'message_summary',
        'response_received',
        'responded_at',
        'response_notes',
        'sent_at'
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'responded_at' => 'datetime',
        'response_received' => 'boolean',
    ];

    // Relasi
    public function item()
    {
        return $this->belongsTo(ConfiscatedItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helper methods untuk status komunikasi
    public function markAsResponded(?string $notes = null)
    {
        $this->update([
            'response_received' => true,
            'responded_at' => now(),
            'response_notes' => $notes,
        ]);
    }

    public function markShipmentConfirmed()
    {
        $this->update([
            'communication_status' => 'shipment_confirmed',
            'response_received' => true,
            'responded_at' => now(),
        ]);
    }

    public function markShipmentDeclined()
    {
        $this->update([
            'communication_status' => 'shipment_declined',
            'response_received' => true,
            'responded_at' => now(),
        ]);
    }

    public function markPaymentRequested()
    {
        $this->update([
            'communication_status' => 'payment_requested',
        ]);
    }

    public function markPaymentConfirmed()
    {
        $this->update([
            'communication_status' => 'payment_confirmed',
            'response_received' => true,
            'responded_at' => now(),
        ]);
    }

    // Scopes untuk filtering
    public function scopeWaitingResponse($query)
    {
        return $query->where('response_received', false)
                    ->where('communication_status', '!=', 'completed');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('communication_type', $type);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('communication_status', $status);
    }

    // Accessor untuk label yang human-readable
    public function getStatusLabelAttribute(): string
    {
        return match($this->communication_status) {
            'sent' => '📤 Terkirim',
            'responded' => '✅ Direspon',
            'shipment_confirmed' => '🚚 Setuju Kirim',
            'shipment_declined' => '❌ Tolak Kirim',
            'payment_requested' => '💰 Menunggu Bayar',
            'payment_confirmed' => '✅ Sudah Bayar',
            'payment_failed' => '❌ Gagal Bayar',
            'address_provided' => '📍 Alamat Diberikan',
            'waiting_response' => '⏳ Menunggu',
            'completed' => '✅ Selesai',
            default => '📝 ' . ucfirst($this->communication_status),
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->communication_type) {
            'general' => '💬 Umum',
            'initial_contact' => '👋 Kontak Awal',
            'shipment_inquiry' => '🚚 Tanya Pengiriman',
            'payment_follow_up' => '💰 Follow Up Bayar',
            'confirmation' => '✅ Konfirmasi',
            default => ucfirst($this->communication_type),
        };
    }

    // Badge color helper
    public function getStatusColorAttribute(): string
    {
        return match($this->communication_status) {
            'sent', 'waiting_response' => 'warning',
            'responded', 'shipment_confirmed', 'payment_confirmed', 'completed' => 'success',
            'shipment_declined', 'payment_failed' => 'danger',
            'payment_requested', 'address_provided' => 'info',
            default => 'gray',
        };
    }
}