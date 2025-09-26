<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Airport extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name', 
        'iata_code', 
        'city', 
        'country'
    ];

    public function departures()
    {
        return $this->hasMany(Flight::class, 'origin_airport_id');
    }

    public function arrivals()
    {
        return $this->hasMany(Flight::class, 'destination_airport_id');
    }
}
