<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $table = "product_variants";

    protected $fillable = [
        'product_id',
        'variant_name',
        'color',
        'sku',
        'price',
        'cost',
        'stock_quantity',
        'status',
        'sort_order'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
        'stock_quantity' => 'integer',
        'sort_order' => 'integer'
    ];

    /**
     * 關聯到主商品
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * 取得變體的完整名稱
     */
    public function getFullNameAttribute()
    {
        return $this->product->name . ' - ' . $this->variant_name;
    }

    /**
     * 取得變體的實際價格（如果變體沒有特定價格，則使用主商品價格）
     */
    public function getActualPriceAttribute()
    {
        return $this->price ?? $this->product->price;
    }

    /**
     * 取得變體的實際成本（如果變體沒有特定成本，則使用主商品成本）
     */
    public function getActualCostAttribute()
    {
        return $this->cost ?? $this->product->cost;
    }
}
