<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MemorialDate extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'seventh_day',
        'seventh_reserved',
        'seventh_reserved_at',
        'forty_ninth_day',
        'forty_ninth_reserved',
        'forty_ninth_reserved_at',
        'hundredth_day',
        'hundredth_reserved',
        'hundredth_reserved_at',
        'anniversary_day',
        'anniversary_reserved',
        'anniversary_reserved_at',
        'notes'
    ];

    protected $casts = [
        'seventh_day' => 'date',
        'seventh_reserved' => 'boolean',
        'seventh_reserved_at' => 'date',
        'forty_ninth_day' => 'date',
        'forty_ninth_reserved' => 'boolean',
        'forty_ninth_reserved_at' => 'date',
        'hundredth_day' => 'date',
        'hundredth_reserved' => 'boolean',
        'hundredth_reserved_at' => 'date',
        'anniversary_day' => 'date',
        'anniversary_reserved' => 'boolean',
        'anniversary_reserved_at' => 'date',
    ];

    /**
     * 關聯到 Sale
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * 關聯到 MemorialDateLog
     */
    public function logs()
    {
        return $this->hasMany(MemorialDateLog::class);
    }



    /**
     * 計算重要日期
     */
    public static function calculateMemorialDates($deathDate, $planId = null)
    {
        $death = Carbon::parse($deathDate);
        
        $dates = [
            'seventh_day' => $planId == 4 ? null : $death->copy()->addDays(6)->format('Y-m-d'), // 浪浪方案無頭七
            'forty_ninth_day' => $death->copy()->addDays(48)->format('Y-m-d'),
            'hundredth_day' => $death->copy()->addDays(99)->format('Y-m-d'),
            'anniversary_day' => null, // 對年設為空
        ];

        return $dates;
    }

    /**
     * 格式化顯示日期
     */
    public function getFormattedDatesAttribute()
    {
        $formatDate = function($date) {
            if (!$date) return null;
            $carbon = Carbon::parse($date);
            $weekdays = ['日', '一', '二', '三', '四', '五', '六'];
            return $carbon->format('Y/m/d') . ' (' . $weekdays[$carbon->dayOfWeek] . ')';
        };

        return [
            'death_date' => $formatDate($this->sale->death_date),
            'seventh_day' => $formatDate($this->seventh_day),
            'forty_ninth_day' => $formatDate($this->forty_ninth_day),
            'hundredth_day' => $formatDate($this->hundredth_day),
            'anniversary_day' => $formatDate($this->anniversary_day),
        ];
    }

    /**
     * 檢查是否需要顯示頭七
     */
    public function shouldDisplaySeventhDay()
    {
        return $this->sale->plan_id != 4 && !is_null($this->seventh_day);
    }
}
