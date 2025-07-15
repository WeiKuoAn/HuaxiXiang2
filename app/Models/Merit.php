<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Merit extends Model
{
    use HasFactory;

    protected $table = 'merit';

    protected $fillable = [
        'date',
        'variety',
        'kg',
        'user_id',
    ];

    public function user_data()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // 確保日期格式為 Y-m-d
    public function getFormattedDateAttribute()
    {
        return $this->date ? date('Y-m-d', strtotime($this->date)) : null;
    }
}
