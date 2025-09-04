<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncreaseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'increase_id',
        'phone_person_id',
        'receive_person_id',
        'phone_exclude_bonus',
        'night_phone_amount',
        'night_receive_amount',
        'evening_phone_amount',
        'evening_receive_amount',
        'typhoon_phone_amount',
        'typhoon_receive_amount',
        'total_phone_amount',
        'total_receive_amount',
        'total_amount'
    ];

    protected $casts = [
        'phone_exclude_bonus' => 'boolean',
        'night_phone_amount' => 'decimal:2',
        'night_receive_amount' => 'decimal:2',
        'evening_phone_amount' => 'decimal:2',
        'evening_receive_amount' => 'decimal:2',
        'typhoon_phone_amount' => 'decimal:2',
        'typhoon_receive_amount' => 'decimal:2',
        'total_phone_amount' => 'decimal:2',
        'total_receive_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
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
     * 計算總金額
     */
    public function calculateTotalAmount()
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
        
        return $this->total_amount;
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
}
