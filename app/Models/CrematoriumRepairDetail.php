<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrematoriumRepairDetail extends Model
{
    use HasFactory;

    protected $table = 'crematorium_repair_details';

    protected $fillable = [
        'repair_id',
        'equipment_id',
        'equipment_instance_id',
        'problem_description',
        'action',
        'quantity',
        'replacement_type',
        'notes',
    ];

    /**
     * 關聯到報修單
     */
    public function repair()
    {
        return $this->belongsTo(CrematoriumRepair::class, 'repair_id');
    }

    /**
     * 關聯到設備（舊）
     */
    public function equipment()
    {
        return $this->belongsTo(CrematoriumEquipment::class, 'equipment_id');
    }

    /**
     * 關聯到設備實例（新）
     */
    public function equipmentInstance()
    {
        return $this->belongsTo(CrematoriumEquipmentInstance::class, 'equipment_instance_id');
    }

    /**
     * 關聯到設備類型（透過設備實例）
     */
    public function equipmentType()
    {
        return $this->hasOneThrough(
            CrematoriumEquipmentType::class,
            CrematoriumEquipmentInstance::class,
            'id',
            'id',
            'equipment_instance_id',
            'equipment_type_id'
        );
    }

    /**
     * 取得處理方式文字
     */
    public function getActionTextAttribute()
    {
        $actions = [
            'repair' => '維修',
            'replace' => '更換',
        ];

        return $actions[$this->action] ?? '未處理';
    }

    /**
     * 取得更換類型文字
     */
    public function getReplacementTypeTextAttribute()
    {
        $types = [
            'new' => '全新',
            'usable' => '堪用',
        ];

        return $types[$this->replacement_type] ?? '';
    }
}
