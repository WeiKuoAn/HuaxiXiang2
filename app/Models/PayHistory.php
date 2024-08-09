<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayHistory extends Model
{
    use HasFactory;

    protected $table = "pay_history";

    protected $fillable = [
        'pay_id',
        'state',
        'user_id',
    ];

    public function user_name()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
}
