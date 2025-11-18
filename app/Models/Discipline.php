<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discipline extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'proposer_id',
        'reason',
        'severity',
        'amount',
        'bonus_deduction',
        'resolution',
        'status',
        'approved_at',
        'meeting_reviewed',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'meeting_reviewed' => 'boolean',
    ];

    /**
     * 關聯受懲處人
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * 關聯提出人
     */
    public function proposer()
    {
        return $this->belongsTo(User::class, 'proposer_id');
    }

    /**
     * 關聯發生日期
     */
    public function dates()
    {
        return $this->hasMany(DisciplineDate::class);
    }

    /**
     * 關聯審核人員
     */
    public function approvals()
    {
        return $this->hasMany(DisciplineApproval::class);
    }

    /**
     * 檢查是否所有審核人都已同意
     */
    public function isAllApproved()
    {
        $totalApprovers = $this->approvals()->count();
        $approvedCount = $this->approvals()->where('status', '同意')->count();
        
        return $totalApprovers > 0 && $totalApprovers === $approvedCount;
    }

    /**
     * 檢查是否有任何拒絕
     */
    public function hasRejection()
    {
        return $this->approvals()->where('status', '拒絕')->exists();
    }

    /**
     * 更新案件狀態
     */
    public function updateStatus()
    {
        if ($this->hasRejection()) {
            $this->status = '已拒絕';
            $this->approved_at = now();
        } elseif ($this->isAllApproved()) {
            $this->status = '已通過';
            $this->approved_at = now();
        } elseif ($this->approvals()->where('status', '同意')->exists()) {
            $this->status = '審核中';
        } else {
            $this->status = '待審核';
        }
        
        $this->save();
    }
}
