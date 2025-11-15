<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrematoriumPurchase extends Model
{
    use HasFactory;

    protected $table = 'crematorium_purchases';

    protected $fillable = [
        'purchase_number',
        'purchase_date',
        'total_price',
        'supplier',
        'invoice_number',
        'purchaser_id',
        'notes',
        'status',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'total_price' => 'decimal:2',
    ];

    /**
     * 關聯到進貨明細
     */
    public function items()
    {
        return $this->hasMany(CrematoriumPurchaseItem::class, 'purchase_id');
    }

    /**
     * 關聯到進貨人員
     */
    public function purchaser()
    {
        return $this->belongsTo(User::class, 'purchaser_id');
    }

    /**
     * 取得狀態文字
     */
    public function getStatusTextAttribute()
    {
        $statuses = [
            'pending' => '待確認',
            'confirmed' => '已確認',
            'cancelled' => '已取消',
        ];

        return $statuses[$this->status] ?? '未知';
    }

    /**
     * 取得狀態顏色
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            'pending' => 'warning',
            'confirmed' => 'success',
            'cancelled' => 'danger',
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    /**
     * 生成進貨單號
     */
    public static function generatePurchaseNumber()
    {
        $date = date('Ymd');
        $lastPurchase = self::where('purchase_number', 'like', "PUR{$date}%")
            ->orderBy('purchase_number', 'desc')
            ->first();

        if ($lastPurchase) {
            $lastNumber = intval(substr($lastPurchase->purchase_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "PUR{$date}{$newNumber}";
    }
}
