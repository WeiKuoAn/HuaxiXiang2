<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncreaseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'increase_id',
        'item_type',
        'phone_person_id',
        'receive_person_id',
        'furnace_person_id',
        'phone_exclude_bonus',
        'time_slot_id',
        'weight',
        'calculated_price',
        'night_phone_amount',
        'night_receive_amount',
        'evening_phone_amount',
        'evening_receive_amount',
        'typhoon_phone_amount',
        'typhoon_receive_amount',
        'total_phone_amount',
        'total_receive_amount',
        'total_amount',
        'overtime_record_id',
        'custom_amount'
    ];

    protected $casts = [
        'item_type' => 'string',
        'phone_exclude_bonus' => 'boolean',
        'weight' => 'decimal:2',
        'calculated_price' => 'decimal:2',
        'night_phone_amount' => 'decimal:2',
        'night_receive_amount' => 'decimal:2',
        'evening_phone_amount' => 'decimal:2',
        'evening_receive_amount' => 'decimal:2',
        'typhoon_phone_amount' => 'decimal:2',
        'typhoon_receive_amount' => 'decimal:2',
        'total_phone_amount' => 'decimal:2',
        'total_receive_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'overtime_record_id' => 'integer',
        'custom_amount' => 'decimal:2',
    ];

    /**
     * 取得加成主檔
     */
    public function increase()
    {
        return $this->belongsTo(Increase::class);
    }

    /**
     * 取得接電話人員
     */
    public function phonePerson()
    {
        return $this->belongsTo(User::class, 'phone_person_id');
    }

    /**
     * 取得接件人員
     */
    public function receivePerson()
    {
        return $this->belongsTo(User::class, 'receive_person_id');
    }

    /**
     * 取得夜間開爐負責人員
     */
    public function furnacePerson()
    {
        return $this->belongsTo(User::class, 'furnace_person_id');
    }

    /**
     * 取得時段設定
     */
    public function timeSlot()
    {
        return $this->belongsTo(NightShiftTimeSlot::class, 'time_slot_id');
    }

    /**
     * 取得加班記錄
     */
    public function overtimeRecord()
    {
        return $this->belongsTo(OvertimeRecord::class, 'overtime_record_id');
    }

    /**
     * 計算總金額
     */
    public function calculateTotalAmount()
    {
        // 如果是加班費記錄，使用自定義金額
        if ($this->overtime_record_id) {
            $this->calculateOvertimeAmount();
        } elseif ($this->time_slot_id && $this->timeSlot) {
            // 如果有時段設定，使用時段價格計算
            $this->calculateTimeSlotAmount();
        } else {
            // 使用原有的計算方式
            $this->calculateTraditionalAmount();
        }
        
        return $this->total_amount;
    }

    /**
     * 計算時段價格金額
     */
    public function calculateTimeSlotAmount()
    {
        $timeSlot = $this->timeSlot;
        
        // 夜間開爐只有總價格，不分電話和接件
        if ($timeSlot) {
            $this->calculated_price = $timeSlot->price;
            $this->total_phone_amount = 0; // 夜間開爐不計電話獎金
            $this->total_receive_amount = $this->calculated_price;
        } else {
            // 如果沒有時段設定，使用預設值
            $this->total_phone_amount = 0;
            $this->total_receive_amount = 0;
        }
        
        // 計算總金額
        $this->total_amount = $this->total_phone_amount + $this->total_receive_amount;
    }

    /**
     * 計算傳統加成金額
     */
    public function calculateTraditionalAmount()
    {
        // 計算接電話總金額（如果不計入獎金則為0）
        if ($this->phone_exclude_bonus) {
            $this->total_phone_amount = 0;
            // 細項金額也設為0
            $this->night_phone_amount = 0;
            $this->evening_phone_amount = 0;
            $this->typhoon_phone_amount = 0;
        } else {
            $this->total_phone_amount = $this->night_phone_amount + $this->evening_phone_amount + $this->typhoon_phone_amount;
        }
        
        // 計算接件總金額
        $this->total_receive_amount = $this->night_receive_amount + $this->evening_receive_amount + $this->typhoon_receive_amount;
        
        // 計算總金額
        $this->total_amount = $this->total_phone_amount + $this->total_receive_amount;
    }

    /**
     * 計算加班費金額
     */
    public function calculateOvertimeAmount()
    {
        // 加班費記錄，使用自定義金額
        $this->total_phone_amount = 0; // 加班費不計電話獎金
        $this->total_receive_amount = $this->custom_amount ?? 0;
        $this->total_amount = $this->total_receive_amount;
    }

    /**
     * 取得夜間加成總金額
     */
    public function getNightTotalAttribute()
    {
        return $this->night_phone_amount + $this->night_receive_amount;
    }

    /**
     * 取得晚間加成總金額
     */
    public function getEveningTotalAttribute()
    {
        return $this->evening_phone_amount + $this->evening_receive_amount;
    }

    /**
     * 取得颱風加成總金額
     */
    public function getTyphoonTotalAttribute()
    {
        return $this->typhoon_phone_amount + $this->typhoon_receive_amount;
    }

    /**
     * 取得項目類型顯示名稱
     */
    public function getItemTypeNameAttribute()
    {
        return match($this->item_type) {
            'traditional' => '傳統加成',
            'furnace' => '夜間開爐',
            'overtime' => '加班費',
            default => '未知類型'
        };
    }

    /**
     * 檢查是否為傳統加成項目
     */
    public function isTraditional()
    {
        return $this->item_type === 'traditional';
    }

    /**
     * 檢查是否為夜間開爐項目
     */
    public function isFurnace()
    {
        return $this->item_type === 'furnace';
    }

    /**
     * 檢查是否為加班費項目
     */
    public function isOvertime()
    {
        return $this->item_type === 'overtime';
    }

    /**
     * 取得項目描述
     */
    public function getDescriptionAttribute()
    {
        if ($this->isTraditional()) {
            $categories = [];
            if ($this->night_phone_amount > 0 || $this->night_receive_amount > 0) {
                $categories[] = '夜間';
            }
            if ($this->evening_phone_amount > 0 || $this->evening_receive_amount > 0) {
                $categories[] = '晚間';
            }
            if ($this->typhoon_phone_amount > 0 || $this->typhoon_receive_amount > 0) {
                $categories[] = '颱風';
            }
            return implode('、', $categories) ?: '傳統加成';
        } elseif ($this->isFurnace()) {
            return '夜間開爐';
        } elseif ($this->isOvertime()) {
            return '加班費';
        }
        return '未知類型';
    }
}
