<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DisposalRecord extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'item_id',
        'disposal_method',
        'authorized_by_user_id',
        'report_document_url',
        'disposal_date',
        'witnesses'
    ];

    protected $casts = [
        'disposal_date' => 'date',
    ];

    public function item()
    {
        return $this->belongsTo(ConfiscatedItem::class);
    }

    public function authorizedBy()
    {
        return $this->belongsTo(User::class, 'authorized_by_user_id');
    }
}
