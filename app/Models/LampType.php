<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LampType extends Model
{
    use HasFactory;
    protected $table = "lamp_type";

    protected $fillable = [
        'name',
        'status',
    ];
}
