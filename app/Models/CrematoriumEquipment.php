<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrematoriumEquipment extends Model
{
    use HasFactory;

    protected $table = 'crematorium_equipment';

    protected $fillable = [
        'name',
        'location',
        'stock_new',
        'stock_usable',
        'stock_broken',
        'status',
        'description',
        'category',
        'sub_category',
    ];

    public function bookings()
    {
        return $this->hasMany(CrematoriumBooking::class, 'equipment_id');
    }

    public function maintenance()
    {
        return $this->hasMany(CrematoriumMaintenance::class, 'equipment_id');
    }

    public function getStatusTextAttribute()
    {
        $statuses = [
            'active' => '正常使用',
            'maintenance' => '維護中',
            'inactive' => '停用',
        ];

        return $statuses[$this->status] ?? '未知狀態';
    }

    public function getCategoryTextAttribute()
    {
        $categories = [
            'furnace_1' => '一爐',
            'furnace_2' => '二爐',
            'ventilation' => '抽風',
        ];

        return $categories[$this->category] ?? '未知類別';
    }

    public function getSubCategoryTextAttribute()
    {
        if ($this->category == 'ventilation') {
            return '無';
        }

        $subCategories = [
            'fire_1' => '一火',
            'fire_2' => '二火',
            'fire_1a' => '一火A',
            'fire_1b' => '一火B',
        ];

        return $subCategories[$this->sub_category] ?? '未知子類別';
    }

    public function getFullCategoryTextAttribute()
    {
        if ($this->category == 'ventilation') {
            return $this->category_text;
        }
        
        return $this->category_text . ' - ' . $this->sub_category_text;
    }

    // 計算總庫存（不含故障品）
    public function getStockTotalAttribute()
    {
        return $this->stock_new + $this->stock_usable;
    }

    // 計算所有設備總數（含故障品）
    public function getAllEquipmentCountAttribute()
    {
        return $this->stock_new + $this->stock_usable + $this->stock_broken;
    }

    // 庫存狀態（基於總庫存）
    public function getStockStatusAttribute()
    {
        $total = $this->stock_total;
        
        if ($total <= 0) {
            return 'out_of_stock';
        } elseif ($total <= 5) {
            return 'low_stock';
        } else {
            return 'in_stock';
        }
    }

    // 庫存狀態文字
    public function getStockStatusTextAttribute()
    {
        $statuses = [
            'out_of_stock' => '缺貨',
            'low_stock' => '庫存不足',
            'in_stock' => '庫存充足',
        ];

        return $statuses[$this->stock_status] ?? '未知狀態';
    }
    
    // 全新庫存狀態
    public function getStockNewStatusAttribute()
    {
        if ($this->stock_new <= 0) {
            return 'out_of_stock';
        } elseif ($this->stock_new <= 3) {
            return 'low_stock';
        } else {
            return 'in_stock';
        }
    }
}
