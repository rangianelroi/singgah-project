<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shipment extends Model
{
    use HasFactory;
    protected $fillable = [
        'item_id',
        'tracking_number',
        'shipping_cost',
        'payment_status',
        'recipient_name',
        'recipient_phone',
        'street_address',
        'subdistrict',
        'district',
        'city',
        'province',
        'postal_code',
        'country'
    ];

    public function item()
    {
        return $this->belongsTo(ConfiscatedItem::class);
    }
}
