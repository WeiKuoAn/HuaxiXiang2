<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleSouvenir extends Model
{
    use HasFactory;
    protected $table = "sale_souvenir";

    protected $fillable = [
        'sale_id',
        'sale_prom_id',
        'souvenir_type',
        'product_name',
        'product_num',
        'product_variant_id',
        'total',
        'comment'
    ];

    public function souvenir()
    {
        dd($this->sale_id); 
        return $this->hasOne(\App\Models\SaleSouvenir::class, 'prom_id', 'prom_id')
            ->where('sale_id', $this->sale_id);
    }

    /**
     * 關聯到商品細項
     */
    public function variant()
    {
        return $this->belongsTo(\App\Models\ProductVariant::class, 'product_variant_id');
    }
}
