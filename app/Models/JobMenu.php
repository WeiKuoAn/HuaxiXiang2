<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobMenu extends Model
{
    use HasFactory;

    protected $table = "job_menu";

    protected $fillable = [
        'id',
        'job_id',
        'menu_id',
    ];

}
