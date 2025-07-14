<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustGroup extends Model
{
    use HasFactory;

    protected $table = "cust_group";

    protected $fillable = [
        'id',
        'name',
        'status',
    ];
}
