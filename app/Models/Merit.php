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
        'user_id',
    ];

    public function user_data()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
