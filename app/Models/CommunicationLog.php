<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommunicationLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'item_id',
        'user_id',
        'channel',
        'message_summary',
        'sent_at'
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function item()
    {
        return $this->belongsTo(ConfiscatedItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
