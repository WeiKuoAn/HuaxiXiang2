<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Increase extends Model
{
    use HasFactory;

    protected $fillable = [
        'increase_date',
        'comment',
        'evening_is_typhoon',
        'evening_is_newyear',
        'night_is_typhoon',
        'night_is_newyear',
        'created_by'
    ];

    protected $casts = [
        'increase_date' => 'date',
    ];

    /**
     * 取得加成項目
     */
    public function items()
    {
        return $this->hasMany(IncreaseItem::class);
    }

    /**
     * 取得建立者
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * 計算總金額
     */
    public function getTotalAmountAttribute()
    {
        return $this->items->sum('total_amount');
    }
}
