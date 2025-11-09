<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveWorkflowInstance extends Model
{
    use HasFactory;

    protected $table = 'leave_workflow_instances';

    protected $fillable = [
        'leave_day_id',
        'workflow_id',
        'status',
        'current_step_id',
        'started_by',
        'started_at',
        'completed_at',
        'final_comment',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * 假單
     */
    public function leaveDay()
    {
        return $this->belongsTo(LeaveDay::class, 'leave_day_id');
    }

    /**
     * 流程
     */
    public function workflow()
    {
        return $this->belongsTo(LeaveWorkflow::class, 'workflow_id');
    }

    /**
     * 目前步驟
     */
    public function currentStep()
    {
        return $this->belongsTo(LeaveWorkflowStep::class, 'current_step_id');
    }

    /**
     * 發起人
     */
    public function starter()
    {
        return $this->belongsTo(User::class, 'started_by');
    }

    /**
     * 審核記錄
     */
    public function approvals()
    {
        return $this->hasMany(LeaveWorkflowApproval::class, 'instance_id');
    }

    /**
     * 取得狀態文字
     */
    public function getStatusTextAttribute()
    {
        $statuses = [
            'pending' => '待開始',
            'in_progress' => '進行中',
            'approved' => '已核准',
            'rejected' => '已拒絕',
            'cancelled' => '已取消',
        ];
        return $statuses[$this->status] ?? '未知';
    }

    /**
     * 取得目前等待審核的人員
     */
    public function getCurrentApprovers()
    {
        if (!$this->currentStep) {
            return collect();
        }

        return $this->currentStep->getApprovers();
    }

    /**
     * 取得流程進度百分比
     */
    public function getProgressPercentageAttribute()
    {
        $totalSteps = $this->workflow->activeSteps()->count();
        $completedSteps = $this->approvals()->where('status', 'approved')->distinct('step_id')->count();
        
        if ($totalSteps == 0) return 0;
        
        return round(($completedSteps / $totalSteps) * 100);
    }
}
















