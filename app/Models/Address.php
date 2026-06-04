<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'passenger_id',
        'recipient_name',
        'recipient_phone',
        'street_address',
        'city',
        'province',
        'postal_code',
        'country',
    ];

    public function passenger()
    {
        return $this->belongsTo(Passenger::class);
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }
}
