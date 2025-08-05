<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerMobile extends Model
{
    use HasFactory;

    protected $table = 'customer_mobile';

    protected $fillable = [
        'customer_id',
        'mobile',
        'is_primary',
    ];
}
