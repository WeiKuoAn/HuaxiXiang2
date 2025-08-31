<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GdpaperInventoryItem extends Model
{
    use HasFactory;

    protected $table = "gdpaper_inventory_item";

    protected $fillable = [
        'gdpaper_inventory_id',
        'product_id',
        'variant_id',
        'is_variant',
        'type',
        'old_num',
        'new_num',
        'comment'
    ];

    public function gdpaper_name()
    {
        return $this->hasOne('App\Models\Product','id','product_id');
    }

    /**
     * 關聯到商品變體
     */
    public function variant()
    {
        return $this->belongsTo(\App\Models\ProductVariant::class, 'variant_id');
    }
}
