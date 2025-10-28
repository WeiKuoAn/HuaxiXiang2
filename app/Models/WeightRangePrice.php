<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeightRangePrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'time_slot_id',
        'start_weight',
        'end_weight',
        'price',
        'is_active',
        'sort_order',
        'description'
    ];

    protected $casts = [
        'start_weight' => 'decimal:2',
        'end_weight' => 'decimal:2',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * 取得所屬時段
     */
    public function timeSlot()
    {
        return $this->belongsTo(NightShiftTimeSlot::class, 'time_slot_id');
    }

    /**
     * 取得有效的公斤數範圍價格
     */
    public static function getActiveRangesForTimeSlot($timeSlotId)
    {
        return self::where('time_slot_id', $timeSlotId)
                   ->where('is_active', true)
                   ->orderBy('sort_order')
                   ->orderBy('start_weight')
                   ->get();
    }

    /**
     * 根據公斤數取得對應的價格範圍
     */
    public static function getPriceForWeight($timeSlotId, $weight)
    {
        $range = self::where('time_slot_id', $timeSlotId)
                     ->where('is_active', true)
                     ->where('start_weight', '<=', $weight)
                     ->where(function($query) use ($weight) {
                         $query->whereNull('end_weight')
                               ->orWhere('end_weight', '>=', $weight);
                     })
                     ->orderBy('start_weight', 'desc')
                     ->first();

        return $range ? $range->price : null;
    }

    /**
     * 取得範圍顯示名稱
     */
    public function getRangeDisplayAttribute()
    {
        if ($this->end_weight) {
            return "{$this->start_weight} - {$this->end_weight} 公斤";
        } else {
            return "{$this->start_weight} 公斤以上";
        }
    }

    /**
     * 取得價格顯示
     */
    public function getPriceDisplayAttribute()
    {
        return "$" . number_format($this->price, 0);
    }
}
