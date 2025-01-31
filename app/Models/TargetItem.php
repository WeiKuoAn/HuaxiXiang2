<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TargetItem extends Model
{
    use HasFactory;
    protected $table = "target_item";
    protected $fillable = ['target_data_id', 'start_date', 'end_date', 'status', 'manual_achieved','gift'];

}
