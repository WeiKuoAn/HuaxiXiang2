<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleHistory extends Model
{
    use HasFactory;
    protected $table = "sale_history";

    protected $fillable = [
        'sale_id',
        'state',
        'user_id',
    ];

    public function user_name()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
}
