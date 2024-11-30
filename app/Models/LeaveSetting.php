<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveSetting extends Model
{
    use HasFactory;

    protected $table = "leave_setting";

    protected $fillable = [
        'leave_id',
        'year',
        'approved_days',
        'unit',
    ];

}
