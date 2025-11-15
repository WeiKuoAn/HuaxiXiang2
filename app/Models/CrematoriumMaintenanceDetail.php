<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrematoriumMaintenanceDetail extends Model
{
    use HasFactory;

    protected $table = 'crematorium_maintenance_details';

    protected $fillable = [
        'maintenance_id',
        'equipment_id',
        'equipment_instance_id',
        'status',
        'problem_description',
        'action',
        'quantity',
        'replacement_type',
    ];

    /**
     * 關聯到維護記錄
     */
    public function maintenance()
    {
        return $this->belongsTo(CrematoriumMaintenance::class, 'maintenance_id');
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
     * 取得狀態文字
     */
    public function getStatusTextAttribute()
    {
        $statuses = [
            'good' => '正常',
            'problem' => '有問題',
            'not_checked' => '未檢查',
        ];

        return $statuses[$this->status] ?? '未知';
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
}
