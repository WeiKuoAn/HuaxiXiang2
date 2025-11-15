<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrematoriumPurchaseItem extends Model
{
    use HasFactory;

    protected $table = 'crematorium_purchase_items';

    protected $fillable = [
        'purchase_id',
        'equipment_type_id',
        'quantity',
        'unit_price',
        'subtotal',
        'notes',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /**
     * 關聯到進貨單
     */
    public function purchase()
    {
        return $this->belongsTo(CrematoriumPurchase::class, 'purchase_id');
    }

    /**
     * 關聯到設備類型
     */
    public function equipmentType()
    {
        return $this->belongsTo(CrematoriumEquipmentType::class, 'equipment_type_id');
    }
}
