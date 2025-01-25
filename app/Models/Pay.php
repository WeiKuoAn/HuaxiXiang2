<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pay extends Model
{
    use HasFactory;

    protected $table = "pay";

    protected $fillable = [
        'name',
        'status',
        'seq',
        'comment',
        'suject_type',
        'view_status',
        'calculate'
    ];
}
