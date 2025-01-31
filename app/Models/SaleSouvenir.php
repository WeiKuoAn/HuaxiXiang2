<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleSouvenir extends Model
{
    use HasFactory;
    protected $table = "sale_souvenir";

    protected $fillable = [
        'sale_id',
        'prom_id',
        'name',
        'total',
        'shape'
    ];
}
