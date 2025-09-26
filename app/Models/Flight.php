<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\Airline;
use App\Models\Airport;

class Flight extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'airline_id', 
        'flight_number',
        'origin_airport_id', 
        'destination_airport_id',
        'departure_time'
    ];

    protected $casts = [
        'departure_time' => 'datetime',
    ];

    public function airline()
    {
        return $this->belongsTo(Airline::class);
    }

    public function origin()
    {
        return $this->belongsTo(Airport::class, 'origin_airport_id');
    }

    public function destination()
    {
        return $this->belongsTo(Airport::class, 'destination_airport_id');
    }

    protected function fullFlightNumber(): Attribute
    {
        // 'get' dijalankan saat kita meminta data ini
        // Mengembalikan gabungan dari kode maskapai + nomor penerbangan
        return Attribute::make(
            get: fn () => $this->airline->code . $this->flight_number,
        );
    }
}
