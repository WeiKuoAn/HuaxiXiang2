<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TargetCategories extends Model
{
    use HasFactory;
    protected $table = "target_categories";
    protected $fillable = ['name', 'description', 'status'];
}
