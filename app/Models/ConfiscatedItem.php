<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;


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

    protected function storageStatus(): Attribute
{
    return Attribute::make(
        get: function () {
            $expiryDate = Carbon::parse($this->confiscation_date)->addMonths(2);
            $daysRemaining = floor(now()->diffInDays($expiryDate, false));

            if ($daysRemaining < 0) {
                return ['status' => 'Kedaluwarsa', 'color' => 'danger', 'remaining' => 0];
            }
            if ($daysRemaining <= 30) {
                return ['status' => 'Hampir Kedaluwarsa', 'color' => 'warning', 'remaining' => $daysRemaining];
            }
            return ['status' => 'Aman', 'color' => 'success', 'remaining' => $daysRemaining];
        }
    );
}
}
