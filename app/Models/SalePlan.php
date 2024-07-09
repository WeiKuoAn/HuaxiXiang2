<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalePlan extends Model
{
    use HasFactory;

    protected $table = "sale_plan";

    protected $fillable = [
        'plan_id',
        'sale_id',
        'new_plan_id',
    ];

    public function old_plan_data()
    {
        return $this->hasOne('App\Models\Plan', 'id', 'plan_id');
    }

    public function new_plan_data()
    {
        return $this->hasOne('App\Models\Plan', 'id', 'new_plan_id');
    }

}
