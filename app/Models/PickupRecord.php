<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PickupRecord extends Model
{
    use HasFactory;
    protected $fillable = [
        'item_id', 
        'pickup_by_name', 
        'pickup_by_identity_number',
        'photo_of_recipient_path',
        'photo_of_identity_path',
        'relationship_to_passenger',
        'verified_by_user_id',
        'pickup_timestamp'
    ];

    protected $casts = [
        'pickup_timestamp' => 'datetime',
    ];

    public function item()
    {
        return $this->belongsTo(ConfiscatedItem::class);
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by_user_id');
    }
}
