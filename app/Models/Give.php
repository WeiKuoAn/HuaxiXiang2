<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Give extends Model
{
    use HasFactory;

    protected $table = 'give';
    protected $fillable = ['sale_on', 'user_id', 'price', 'value'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
