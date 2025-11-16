<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrematoriumEquipmentInstance extends Model
{
    use HasFactory;

    protected $table = 'crematorium_equipment_instances';

    protected $fillable = [
        'equipment_type_id',
        'category',
        'sub_category',
        'location',
        'status',
        'installed_date',
        'last_maintenance_date',
        'notes',
    ];

    protected $casts = [
        'installed_date' => 'date',
        'last_maintenance_date' => 'date',
    ];

    /**
     * 關聯到設備類型
     */
    public function equipmentType()
    {
        return $this->belongsTo(CrematoriumEquipmentType::class, 'equipment_type_id');
    }

    /**
     * 關聯到維護記錄
     */
    public function maintenanceRecords()
    {
        return $this->hasMany(CrematoriumMaintenance::class, 'equipment_instance_id');
    }

    /**
     * 關聯到報修明細
     */
    public function repairDetails()
    {
        return $this->hasMany(CrematoriumRepairDetail::class, 'equipment_instance_id');
    }

    /**
     * 狀態文字
     */
    public function getStatusTextAttribute()
    {
        $statuses = [
            'active' => '正常使用',
            'maintenance' => '維護中',
            'broken' => '故障',
            'inactive' => '停用',
        ];

        return $statuses[$this->status] ?? '未知狀態';
    }

    /**
     * 狀態顏色（用於 badge）
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            'active' => 'success',
            'maintenance' => 'warning',
            'broken' => 'danger',
            'inactive' => 'secondary',
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    /**
     * 類別文字
     */
    public function getCategoryTextAttribute()
    {
        $categories = [
            'furnace_1' => '一爐',
            'furnace_2' => '二爐',
            'ventilation' => '抽風',
            'furnace_1_ventilation' => '一爐抽風',
            'furnace_2_ventilation' => '二爐抽風',
        ];

        return $categories[$this->category] ?? '未知類別';
    }

    /**
     * 子類別文字
     */
    public function getSubCategoryTextAttribute()
    {
        if (in_array($this->category, ['ventilation', 'furnace_1_ventilation', 'furnace_2_ventilation'])) {
            return '';
        }

        $subCategories = [
            'fire_1' => '一火',
            'fire_2' => '二火',
            'fire_1a' => '一火A',
            'fire_1b' => '一火B',
        ];

        return $subCategories[$this->sub_category] ?? '';
    }

    /**
     * 完整位置文字
     */
    public function getFullLocationAttribute()
    {
        $text = $this->category_text;
        if ($this->sub_category_text) {
            $text .= '-' . $this->sub_category_text;
        }
        return $text;
    }

    /**
     * 完整名稱（設備名稱 + 位置）
     */
    public function getFullNameAttribute()
    {
        return $this->equipmentType->name . ' (' . $this->full_location . ')';
    }

    /**
     * 是否需要維護（超過30天未維護）
     */
    public function needsMaintenance($days = 30)
    {
        if (!$this->last_maintenance_date) {
            return true;
        }

        return $this->last_maintenance_date->diffInDays(now()) > $days;
    }

    /**
     * 標記為故障
     */
    public function markAsBroken($notes = null)
    {
        $this->status = 'broken';
        if ($notes) {
            $this->notes = $notes;
        }
        $this->save();
    }

    /**
     * 標記為維護中
     */
    public function markAsUnderMaintenance()
    {
        $this->status = 'maintenance';
        $this->save();
    }

    /**
     * 標記為正常（維護完成）
     */
    public function markAsActive()
    {
        $this->status = 'active';
        $this->last_maintenance_date = now();
        $this->save();
    }
}

