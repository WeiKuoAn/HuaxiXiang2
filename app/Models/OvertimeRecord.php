<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OvertimeRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'overtime_date',
        'user_id',
        'minutes',
        'reason',
        'overtime_pay',
        'first_two_hours_pay',
        'remaining_hours_pay',
        'first_two_hours',
        'remaining_hours',
        'status',
        'created_by'
    ];

    protected $casts = [
        'overtime_date' => 'date',
        'minutes' => 'integer',
        'overtime_pay' => 'decimal:2',
        'first_two_hours_pay' => 'decimal:2',
        'remaining_hours_pay' => 'decimal:2',
        'first_two_hours' => 'decimal:2',
        'remaining_hours' => 'decimal:2',
    ];

    /**
     * 取得加班人員
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 取得建立者
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }



    /**
     * 計算加班費
     */
    public function calculateOvertimePay()
    {
        $totalHours = $this->minutes / 60;
        
        if ($totalHours <= 2) {
            $this->first_two_hours = $totalHours;
            $this->remaining_hours = 0;
        } else {
            $this->first_two_hours = 2;
            $this->remaining_hours = $totalHours - 2;
        }

        // 假設基本時薪為 200 元（可從設定檔讀取）
        $baseHourlyRate = 200;
        
        // 前兩小時：1.34 倍
        $this->first_two_hours_pay = $this->first_two_hours * $baseHourlyRate * 1.34;
        
        // 剩餘時間：1.67 倍
        $this->remaining_hours_pay = $this->remaining_hours * $baseHourlyRate * 1.67;
        
        // 總加班費
        $this->overtime_pay = $this->first_two_hours_pay + $this->remaining_hours_pay;
        
        return $this->overtime_pay;
    }

    /**
     * 取得格式化的小時顯示
     */
    public function getFormattedHoursAttribute()
    {
        $hours = floor($this->minutes / 60);
        $minutes = $this->minutes % 60;
        
        if ($hours > 0 && $minutes > 0) {
            return "{$hours}小時{$minutes}分鐘";
        } elseif ($hours > 0) {
            return "{$hours}小時";
        } else {
            return "{$minutes}分鐘";
        }
    }

    /**
     * 取得前兩小時格式化顯示
     */
    public function getFormattedFirstTwoHoursAttribute()
    {
        if ($this->first_two_hours == 0) {
            return '';
        }
        
        $hours = floor($this->first_two_hours);
        $minutes = round(($this->first_two_hours - $hours) * 60);
        
        if ($hours > 0 && $minutes > 0) {
            return "{$hours}小時{$minutes}分鐘";
        } elseif ($hours > 0) {
            return "{$hours}小時";
        } else {
            return "{$minutes}分鐘";
        }
    }

    /**
     * 取得剩餘時間格式化顯示
     */
    public function getFormattedRemainingHoursAttribute()
    {
        if ($this->remaining_hours == 0) {
            return '';
        }
        
        $hours = floor($this->remaining_hours);
        $minutes = round(($this->remaining_hours - $hours) * 60);
        
        if ($hours > 0 && $minutes > 0) {
            return "{$hours}小時{$minutes}分鐘";
        } elseif ($hours > 0) {
            return "{$hours}小時";
        } else {
            return "{$minutes}分鐘";
        }
    }

    /**
     * 取得狀態中文名稱
     */
    public function getStatusNameAttribute()
    {
        return '已核准';
    }

    /**
     * 取得狀態徽章顏色
     */
    public function getStatusBadgeAttribute()
    {
        return 'success';
    }

    /**
     * 檢查是否可以編輯
     */
    public function canEdit()
    {
        // 所有記錄都可以編輯
        return true;
    }

    /**
     * 檢查是否可以刪除
     */
    public function canDelete()
    {
        // 所有記錄都可以刪除
        return true;
    }
}
