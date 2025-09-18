<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Gdpaperrestock;
use App\Models\ProductRestockItem;

class Product extends Model
{
    use HasFactory;

    protected $table = "product";

    protected $fillable = [
        'name',
        'type',
        'category_id',
        'description',
        'name',
        'seq',
        'alarm_num',
        'commission',
        'status',
        'cost',
        'stock',
        'restock',
        'prom_id',
        'has_variants',
        'initial_stock'
    ];

    public function category_data()
    {
        return $this->hasOne('App\Models\Category', 'id', 'category_id');
    }    

    public function prom_data()
    {
        return $this->hasOne('App\Models\Prom', 'id', 'prom_id');
    }    

    // public function gdpaper_restock_num()
    // {
    //     $restock_nums = Gdpaperrestock::where('gdpaper_id',$this->id)->sum('num');
    //     return  $restock_nums;
    // }
    

    // public function restock(){
    //     $gdpaper_num = Sale_gdpaper::where('gdpaper_id',$this->id)->sum('gdpaper_num');
    //     $restock_nums = Gdpaperrestock::where('gdpaper_id',$this->id)->sum('num');
    //     $num = intval($restock_nums) - intval($gdpaper_num);
    //     return $num;
    // }

    public function restock_date()
    {
        $data = ProductRestockItem::where('product_id',$this->id)->orderby('date','desc')->first();
        return $data;
    }

    /**
     * 關聯到商品細項
     */
    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id')->orderBy('sort_order', 'asc');
    }

    /**
     * 取得啟用的細項
     */
    public function activeVariants()
    {
        return $this->variants()->where('status', 'active');
    }

    /**
     * 取得細項總數
     */
    public function getVariantsCountAttribute()
    {
        return $this->variants()->count();
    }

    /**
     * 取得所有細項的總庫存
     */
    public function getTotalVariantsStockAttribute()
    {
        return $this->variants()->sum('stock_quantity');
    }

    /**
     * 取得細項的最低價格
     */
    public function getMinVariantPriceAttribute()
    {
        $minPrice = $this->variants()->min('price');
        return $minPrice ?? $this->price;
    }

    /**
     * 取得細項的最高價格
     */
    public function getMaxVariantPriceAttribute()
    {
        $maxPrice = $this->variants()->max('price');
        return $maxPrice ?? $this->price;
    }
}
