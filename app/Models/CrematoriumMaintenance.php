<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrematoriumMaintenance extends Model
{
    use HasFactory;

    protected $table = 'crematorium_maintenance';

    protected $fillable = [
        'maintenance_number', // 檢查單號
        'maintenance_date', // 檢查日期
        'inspector', // 檢查人員
        'maintainer', // 保養人
        'notes', // 備註
        'status', // 狀態：0=未檢查, 3=送審, 9=已檢查
        // 供電系統檢查
        'power_system_status', // 供電系統狀態
        'power_system_problem', // 供電系統問題描述
        'high_voltage_wire_status', // 220v高壓電線狀態
        'high_voltage_wire_problem', // 220v高壓電線問題描述
    ];

    /**
     * 關聯到維護明細
     */
    public function maintenanceDetails()
    {
        return $this->hasMany(CrematoriumMaintenanceDetail::class, 'maintenance_id');
    }

    /**
     * 關聯到檢查人員
     */
    public function inspectorUser()
    {
        return $this->belongsTo(User::class, 'inspector');
    }

    /**
     * 關聯到保養人員
     */
    public function maintainerUser()
    {
        return $this->belongsTo(User::class, 'maintainer');
    }

    /**
     * 取得狀態文字
     */
    public function getStatusTextAttribute()
    {
        $statuses = [
            0 => '未檢查',
            3 => '送審',
            9 => '已檢查',
        ];

        return $statuses[$this->status] ?? '未知';
    }

    /**
     * 取得狀態顏色
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            0 => 'warning',
            3 => 'info',
            9 => 'success',
        ];

        return $colors[$this->status] ?? 'secondary';
    }


    public function getOverallStatusAttribute()
    {
        // 根據設備類別獲取相關的檢查項目
        $statuses = $this->getRelevantStatuses();

        if (in_array('bad', $statuses)) {
            return 'bad';
        } elseif (in_array('warning', $statuses)) {
            return 'warning';
        } else {
            return 'good';
        }
    }

    public function getRelevantStatuses()
    {
        $statuses = [];
        
        if (!$this->equipment) {
            return $statuses;
        }

        $category = $this->equipment->category;
        $subCategory = $this->equipment->sub_category;

        // 根據設備類別和子類別決定要檢查的項目
        if ($category === 'furnace_1' || $category === 'furnace_2') {
            // 一爐和二爐的基本檢查項目
            $statuses[] = $this->sensor_status;
            $statuses[] = $this->relay_status;
            $statuses[] = $this->transformer_status;
            $statuses[] = $this->ignition_rod_status;
            $statuses[] = $this->nozzle_status;
            $statuses[] = $this->gasket_status;
            $statuses[] = $this->oil_pipe_status;
            $statuses[] = $this->oil_pump_status;

            // 二爐的額外檢查項目
            if ($category === 'furnace_2') {
                $statuses[] = $this->photosensor_status;
                $statuses[] = $this->controller_status;
                $statuses[] = $this->support_rod_status;
            }
        } elseif ($category === 'ventilation') {
            // 抽風設備的檢查項目（可以根據實際需要調整）
            $statuses[] = $this->sensor_status;
            $statuses[] = $this->relay_status;
            $statuses[] = $this->transformer_status;
        }

        return array_filter($statuses); // 過濾掉空值
    }

    public function getCheckItemsForEquipment()
    {
        if (!$this->equipment) {
            return [];
        }

        $category = $this->equipment->category;
        $subCategory = $this->equipment->sub_category;

        $items = [];

        if ($category === 'furnace_1' || $category === 'furnace_2') {
            $items = [
                'sensor_status' => '感知器',
                'relay_status' => '繼電器',
                'transformer_status' => '變壓器',
                'ignition_rod_status' => '點火棒',
                'nozzle_status' => '噴油嘴',
                'gasket_status' => '固定墊片',
                'oil_pipe_status' => '油管',
                'oil_pump_status' => '油泵浦',
            ];

            if ($category === 'furnace_2') {
                $items['photosensor_status'] = '光敏電阻（感光器）';
                $items['controller_status'] = '控制器';
                $items['support_rod_status'] = '支撐桿';
            }
        } elseif ($category === 'ventilation') {
            $items = [
                'sensor_status' => '感知器',
                'relay_status' => '繼電器',
                'transformer_status' => '變壓器',
            ];
        }

        return $items;
    }
}
