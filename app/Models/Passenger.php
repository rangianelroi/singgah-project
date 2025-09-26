<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Passenger extends Model
{
    protected $fillable = [
        'full_name', 
        'identity_number', 
        'phone_number', 
        'email',
        'identity_image_path', 
        'boardingpass_image_path'
    ];

    public function items()
    {
        return $this->hasMany(ConfiscatedItem::class);
    }
}
