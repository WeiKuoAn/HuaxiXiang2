<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prom extends Model
{
    use HasFactory;
    protected $table = "prom";

    protected $fillable = [
        'type',
        'name',
        'status',
        'seq',
        'is_custom_product'
    ];

    public function prom_type()
    {
        return $this->belongsTo('App\Models\PromType', 'type', 'code');
    }

    public function product_datas()
    {
        return $this->hasMany('App\Models\Product', 'prom_id', 'id');
    }
}
