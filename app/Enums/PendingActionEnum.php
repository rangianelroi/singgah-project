<?php

namespace App\Enums;

enum PendingActionEnum: string
{
    case SHIPMENT_CONFIRMATION = 'shipment_confirmation';
    case PAYMENT_CONFIRMATION = 'payment_confirmation';
    case PAYMENT_PAID = 'payment_paid';

    public function label(): string
    {
        return match($this) {
            self::SHIPMENT_CONFIRMATION => 'Konfirmasi Pengiriman',
            self::PAYMENT_CONFIRMATION => 'Konfirmasi Pembayaran',
            self::PAYMENT_PAID => 'Pembayaran Lunas',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::SHIPMENT_CONFIRMATION => 'Menunggu penerima mengkonfirmasi pengiriman',
            self::PAYMENT_CONFIRMATION => 'Menunggu bukti pembayaran diverifikasi',
            self::PAYMENT_PAID => 'Pembayaran telah diterima',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
