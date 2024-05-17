<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleAddress extends Model
{
    use HasFactory;

    protected $table = "sale_address";

    protected $fillable = [
        'sale_id',
        'send',
        'county',
        'district',
        'address',
    ];
}
