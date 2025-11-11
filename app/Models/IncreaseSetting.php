<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncreaseSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'phone_bonus',
        'receive_bonus',
        'status'
    ];

    protected $casts = [
        'phone_bonus' => 'decimal:2',
        'receive_bonus' => 'decimal:2',
    ];

    /**
     * 取得有效的加成設定
     */
    public static function getActiveSettings()
    {
        return self::where('status', 'active')->get()->keyBy('type');
    }

    /**
     * 取得特定類型的加成設定
     */
    public static function getSettingByType($type)
    {
        return self::where('type', $type)->where('status', 'active')->first();
    }

    /**
     * 取得加成類型的中文名稱
     */
    public function getTypeNameAttribute()
    {
        $typeNames = [
            'night' => '夜間加成',
            'evening' => '晚間加成',
            'typhoon' => '颱風',
            'newyear' => '過年'
        ];

        return $typeNames[$this->type] ?? $this->type;
    }
}
