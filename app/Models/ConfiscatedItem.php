<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ConfiscatedItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'passenger_id', 
        'flight_id', 
        'recorded_by_user_id',
        'item_name', 
        'item_image_path', 
        'category',
        'item_quantity', 
        'item_unit', 
        'notes',
        'confiscation_date', 
        'storage_location', 
    ];

    protected $casts = [
        'confiscation_date' => 'datetime',
    ];

    public function passenger()
    {
        return $this->belongsTo(Passenger::class);
    }

    public function flight()
    {
        return $this->belongsTo(Flight::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by_user_id');
    }

    public function statusLogs()
    {
        return $this->hasMany(ItemStatusLog::class, 'item_id');
    }

    public function latestStatusLog()
    {
        return $this->hasOne(ItemStatusLog::class, 'item_id')->latestOfMany();
    }

    public function communications()
    {
        return $this->hasMany(CommunicationLog::class, 'item_id');
    }

    public function pickups()
    {
        return $this->hasOne(PickupRecord::class, 'item_id');
    }

    public function shipment()
    {
        return $this->hasOne(Shipment::class, 'item_id');
    }

    public function disposal()
    {
        return $this->hasOne(DisposalRecord::class, 'item_id');
    }
}
