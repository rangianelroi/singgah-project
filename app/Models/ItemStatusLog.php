<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItemStatusLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'item_id',
        'user_id',
        'status',
        'notes'];

    public function item()
    {
        return $this->belongsTo(ConfiscatedItem::class, 'item_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
