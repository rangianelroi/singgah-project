<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shipment extends Model
{
    use HasFactory;
    
    /**
     * Fields yang dapat diisi. 
     * CATATAN: Address fields (recipient_name, street_address, etc.) harus dikelola melalui Address model
     * dan menggunakan address_id untuk relasi. Jangan gunakan duplicate address fields di sini.
     */
    protected $fillable = [
        'item_id',
        'address_id',
        'tracking_number',
        'shipping_cost',
        'service_fee',
        'payment_status',
        'payment_proof_path',
        'tracking_number_sent_at',
    ];

    public function item()
    {
        return $this->belongsTo(ConfiscatedItem::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }
}
