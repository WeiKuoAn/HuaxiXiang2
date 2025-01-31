<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeniorityPauses extends Model
{
    use HasFactory;
    protected $table = "seniority_pauses";
    protected $fillable = ['pause_date', 'user_id', 'resume_date'];
}
