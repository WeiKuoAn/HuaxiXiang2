<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrematoriumEquipmentType extends Model
{
    use HasFactory;

    protected $table = 'crematorium_equipment_types';

    protected $fillable = [
        'name',
        'exclude_from_inventory',
        'stock_new',
        'stock_usable',
        'description',
    ];

    protected $casts = [
        'exclude_from_inventory' => 'boolean',
    ];

    /**
     * 關聯到設備實例
     */
    public function instances()
    {
        return $this->hasMany(CrematoriumEquipmentInstance::class, 'equipment_type_id');
    }

    /**
     * 正常使用中的設備實例
     */
    public function activeInstances()
    {
        return $this->hasMany(CrematoriumEquipmentInstance::class, 'equipment_type_id')
                    ->where('status', 'active');
    }

    /**
     * 故障的設備實例
     */
    public function brokenInstances()
    {
        return $this->hasMany(CrematoriumEquipmentInstance::class, 'equipment_type_id')
                    ->where('status', 'broken');
    }

    /**
     * 是否顯示庫存
     */
    public function shouldShowStock()
    {
        return !$this->exclude_from_inventory;
    }

    /**
     * 計算總庫存（全新 + 堪用）
     */
    public function getStockTotalAttribute()
    {
        return $this->stock_new + $this->stock_usable;
    }

    /**
     * 庫存狀態
     */
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

    /**
     * 庫存狀態文字
     */
    public function getStockStatusTextAttribute()
    {
        $statuses = [
            'out_of_stock' => '缺貨',
            'low_stock' => '庫存不足',
            'in_stock' => '庫存充足',
        ];

        return $statuses[$this->stock_status] ?? '未知狀態';
    }

    /**
     * 統計正常使用中的設備數量
     */
    public function getActiveCountAttribute()
    {
        return $this->instances()->where('status', 'active')->count();
    }

    /**
     * 統計故障的設備數量
     */
    public function getBrokenCountAttribute()
    {
        return $this->instances()->where('status', 'broken')->count();
    }

    /**
     * 使用庫存（當安裝新設備或更換時）
     */
    public function useStock($quantity = 1, $isNew = true)
    {
        if ($isNew && $this->stock_new >= $quantity) {
            $this->stock_new -= $quantity;
            $this->save();
            return true;
        } elseif (!$isNew && $this->stock_usable >= $quantity) {
            $this->stock_usable -= $quantity;
            $this->save();
            return true;
        }
        
        return false;
    }

    /**
     * 歸還庫存（當設備報廢或拆除時）
     */
    public function returnStock($quantity = 1, $isUsable = true)
    {
        if ($isUsable) {
            $this->stock_usable += $quantity;
        } else {
            // 如果不堪用，可以選擇不歸還或其他處理
        }
        
        $this->save();
        return true;
    }
}

