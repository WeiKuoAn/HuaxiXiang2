<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NightShiftTimeSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'is_active',
        'sort_order',
        'description',
        'min_weight',
        'max_weight',
        'price'
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_active' => 'boolean',
        'min_weight' => 'decimal:2',
        'max_weight' => 'decimal:2',
        'price' => 'decimal:2',
    ];

    /**
     * 取得有效的時段設定
     */
    public static function getActiveTimeSlots()
    {
        return self::where('is_active', true)
                   ->orderBy('sort_order')
                   ->orderBy('start_time')
                   ->get();
    }

    /**
     * 根據時間取得對應的時段
     */
    public static function getTimeSlotByTime($time)
    {
        $time = is_string($time) ? \Carbon\Carbon::parse($time)->format('H:i:s') : $time->format('H:i:s');
        
        return self::where('is_active', true)
                   ->where('start_time', '<=', $time)
                   ->where('end_time', '>=', $time)
                   ->first();
    }

    /**
     * 取得時段持續時間（小時）
     */
    public function getDurationAttribute()
    {
        $start = \Carbon\Carbon::parse($this->start_time);
        $end = \Carbon\Carbon::parse($this->end_time);
        
        // 處理跨日情況
        if ($end->lessThan($start)) {
            $end->addDay();
        }
        
        return $start->diffInHours($end);
    }

    /**
     * 取得時段顯示名稱
     */
    public function getDisplayNameAttribute()
    {
        return $this->name . ' (' . $this->start_time->format('H:i') . '-' . $this->end_time->format('H:i') . ')';
    }

    /**
     * 關聯到加成項目
     */
    public function increaseItems()
    {
        return $this->hasMany(IncreaseItem::class, 'time_slot_id');
    }

    /**
     * 檢查公斤數是否在此時段的範圍內
     */
    public function isWeightInRange($weight)
    {
        if (!$weight) {
            return false;
        }

        $inMinRange = $this->min_weight === null || $weight >= $this->min_weight;
        $inMaxRange = $this->max_weight === null || $weight <= $this->max_weight;

        return $inMinRange && $inMaxRange;
    }

    /**
     * 取得公斤數範圍顯示
     */
    public function getWeightRangeDisplayAttribute()
    {
        if ($this->min_weight === null && $this->max_weight === null) {
            return "不限公斤數";
        } elseif ($this->min_weight === null) {
            return "{$this->max_weight} 公斤以下";
        } elseif ($this->max_weight === null) {
            return "{$this->min_weight} 公斤以上";
        } else {
            return "{$this->min_weight} - {$this->max_weight} 公斤";
        }
    }

    /**
     * 取得價格顯示
     */
    public function getPriceDisplayAttribute()
    {
        return "$" . number_format($this->price, 0);
    }

    /**
     * 取得完整的時段描述
     */
    public function getFullDescriptionAttribute()
    {
        return $this->name . " (" . $this->start_time->format('H:i') . "-" . $this->end_time->format('H:i') . ") - " . $this->weight_range_display . " - " . $this->price_display;
    }
}
