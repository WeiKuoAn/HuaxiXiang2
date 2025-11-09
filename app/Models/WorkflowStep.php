<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkflowStep extends Model
{
    use HasFactory;

    protected $table = 'workflow_steps';

    protected $fillable = [
        'workflow_id',
        'step_name',
        'approver_user_id',
        'step_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'step_order' => 'integer',
    ];

    /**
     * 取得所屬的流程
     */
    public function workflow()
    {
        return $this->belongsTo(Workflow::class, 'workflow_id');
    }

    /**
     * 取得審核人員
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_user_id');
    }

    /**
     * 取得此關卡的審核記錄
     */
    public function approvals()
    {
        return $this->hasMany(LeaveDayCheck::class, 'check_user_id', 'approver_user_id');
    }

    /**
     * 取得此關卡待審核的假單
     */
    public function pendingApprovals()
    {
        return $this->hasMany(LeaveDayCheck::class, 'check_user_id', 'approver_user_id')
            ->where('state', 2); // 待審核狀態
    }

    /**
     * 取得此關卡已審核的假單
     */
    public function completedApprovals()
    {
        return $this->hasMany(LeaveDayCheck::class, 'check_user_id', 'approver_user_id')
            ->whereIn('state', [9, 3]); // 已核准或已駁回
    }

    /**
     * 取得此關卡審核的假單數量統計
     */
    public function getApprovalStatsAttribute()
    {
        $total = $this->approvals()->count();
        $pending = $this->pendingApprovals()->count();
        $completed = $this->completedApprovals()->count();
        $approved = $this->approvals()->where('state', 9)->count();
        $rejected = $this->approvals()->where('state', 3)->count();

        return [
            'total' => $total,
            'pending' => $pending,
            'completed' => $completed,
            'approved' => $approved,
            'rejected' => $rejected,
            'approval_rate' => $completed > 0 ? round(($approved / $completed) * 100, 2) : 0,
        ];
    }

    /**
     * 取得關卡的狀態文字
     */
    public function getStatusTextAttribute()
    {
        return $this->is_active ? '啟用' : '停用';
    }

    /**
     * 取得關卡的狀態顏色
     */
    public function getStatusColorAttribute()
    {
        return $this->is_active ? 'success' : 'secondary';
    }

    /**
     * 取得關卡的完整資訊
     */
    public function getFullInfoAttribute()
    {
        return [
            'id' => $this->id,
            'workflow_id' => $this->workflow_id,
            'step_name' => $this->step_name,
            'approver_name' => $this->approver->name ?? '未指定',
            'approver_id' => $this->approver_user_id,
            'step_order' => $this->step_order,
            'status' => $this->status_text,
            'status_color' => $this->status_color,
            'approval_stats' => $this->approval_stats,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * 檢查此關卡是否為流程的第一個關卡
     */
    public function isFirstStep()
    {
        return $this->workflow()->first()->firstStep()->id === $this->id;
    }

    /**
     * 檢查此關卡是否為流程的最後一個關卡
     */
    public function isLastStep()
    {
        return $this->workflow()->first()->lastStep()->id === $this->id;
    }

    /**
     * 取得下一個關卡
     */
    public function getNextStep()
    {
        return $this->workflow()->first()->getNextStep($this->step_order);
    }

    /**
     * 取得上一個關卡
     */
    public function getPreviousStep()
    {
        return $this->workflow()->first()->getPreviousStep($this->step_order);
    }

    /**
     * 取得關卡在流程中的位置描述
     */
    public function getPositionDescriptionAttribute()
    {
        $workflow = $this->workflow;
        $totalSteps = $workflow->steps()->where('is_active', true)->count();
        $position = $workflow->steps()->where('is_active', true)->orderBy('step_order')->get()->search(function ($step) {
            return $step->id === $this->id;
        }) + 1;

        return "第 {$position} 關（共 {$totalSteps} 關）";
    }

    /**
     * 取得關卡的處理時間統計
     */
    public function getProcessingTimeStatsAttribute()
    {
        $approvals = $this->completedApprovals()
            ->whereNotNull('check_day')
            ->whereNotNull('created_at')
            ->get();

        if ($approvals->isEmpty()) {
            return [
                'average_hours' => 0,
                'fastest_hours' => 0,
                'slowest_hours' => 0,
                'total_processed' => 0,
            ];
        }

        $processingTimes = $approvals->map(function ($approval) {
            $created = \Carbon\Carbon::parse($approval->created_at);
            $checked = \Carbon\Carbon::parse($approval->check_day);
            return $created->diffInHours($checked);
        });

        return [
            'average_hours' => round($processingTimes->avg(), 2),
            'fastest_hours' => $processingTimes->min(),
            'slowest_hours' => $processingTimes->max(),
            'total_processed' => $processingTimes->count(),
        ];
    }
}