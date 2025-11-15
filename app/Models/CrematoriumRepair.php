<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrematoriumRepair extends Model
{
    use HasFactory;

    protected $table = 'crematorium_repairs';

    protected $fillable = [
        'repair_number',
        'reporter_id',
        'report_date',
        'problem_description',
        'status',
        'processor_id',
        'processed_at',
        'notes',
    ];

    protected $casts = [
        'report_date' => 'date',
        'processed_at' => 'datetime',
    ];

    /**
     * 關聯到報修人員
     */
    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    /**
     * 關聯到處理人員
     */
    public function processor()
    {
        return $this->belongsTo(User::class, 'processor_id');
    }

    /**
     * 關聯到報修明細
     */
    public function repairDetails()
    {
        return $this->hasMany(CrematoriumRepairDetail::class, 'repair_id');
    }

    /**
     * 取得狀態文字
     */
    public function getStatusTextAttribute()
    {
        $statuses = [
            'pending' => '待處理',
            'processing' => '處理中',
            'completed' => '已完成',
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
            'processing' => 'info',
            'completed' => 'success',
            'cancelled' => 'secondary',
        ];

        return $colors[$this->status] ?? 'secondary';
    }
}
