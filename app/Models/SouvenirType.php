<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SouvenirType extends Model
{
    use HasFactory;
    protected $table = "souvenir_type";

    protected $fillable = [
        'name',
        'status',
    ];
}
