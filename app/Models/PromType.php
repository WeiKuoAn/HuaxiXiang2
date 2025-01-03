<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromType extends Model
{
    use HasFactory;
    protected $table = "prom_type";

    protected $fillable = [
        'name',
        'code',
        'status',
    ];
}
