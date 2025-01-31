<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Job;

class TargetData extends Model
{
    use HasFactory;
    protected $fillable = ['category_id', 'job_id', 'target_amount', 'frequency','target_condition','target_quantity'];

    protected $casts = [
        'job_id' => 'array', // 將 job_ids 自動轉換為陣列
    ];

    public function items()
    {
        return $this->hasMany('App\Models\TargetItem', 'target_data_id', 'id');
    }

    public function category_name()
    {
        return $this->belongsTo('App\Models\TargetCategories', 'category_id', 'id');
    }
}
