<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suit extends Model
{
    use HasFactory;

    use HasFactory;
    protected $table = "suit";

    protected $fillable = [
        'name',
        'status',
        'seq'
    ];
}
