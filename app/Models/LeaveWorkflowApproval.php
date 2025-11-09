<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveWorkflowApproval extends Model
{
    use HasFactory;

    protected $table = 'leave_workflow_approvals';

    protected $fillable = [
        'instance_id',
        'step_id',
        'approver_id',
        'status',
        'comment',
        'action_at',
        'order_in_step',
        'is_delegated',
        'delegated_from',
    ];

    protected $casts = [
        'action_at' => 'datetime',
        'is_delegated' => 'boolean',
    ];

    /**
     * 流程實例
     */
    public function instance()
    {
        return $this->belongsTo(LeaveWorkflowInstance::class, 'instance_id');
    }

    /**
     * 步驟
     */
    public function step()
    {
        return $this->belongsTo(LeaveWorkflowStep::class, 'step_id');
    }

    /**
     * 審核人
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    /**
     * 委派來源
     */
    public function delegatedFrom()
    {
        return $this->belongsTo(User::class, 'delegated_from');
    }

    /**
     * 取得狀態文字
     */
    public function getStatusTextAttribute()
    {
        $statuses = [
            'pending' => '待審核',
            'approved' => '已核准',
            'rejected' => '已拒絕',
            'returned' => '已退回',
            'skipped' => '已跳過',
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
            'approved' => 'success',
            'rejected' => 'danger',
            'returned' => 'info',
            'skipped' => 'secondary',
        ];
        return $colors[$this->status] ?? 'secondary';
    }

    /**
     * 是否已完成
     */
    public function isCompleted()
    {
        return in_array($this->status, ['approved', 'rejected', 'returned', 'skipped']);
    }

    /**
     * 是否待處理
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }
}
















