<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemorialDateLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'memorial_date_id',
        'user_id',
        'action',
        'changes',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    public function memorialDate()
    {
        return $this->belongsTo(MemorialDate::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


