<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workflow extends Model
{
    use HasFactory;

    protected $table = 'workflows';

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'category',
        'job_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * 取得流程的關卡
     */
    public function steps()
    {
        return $this->hasMany(WorkflowStep::class, 'workflow_id')->orderBy('step_order');
    }

    /**
     * 取得使用此流程的假單
     */
    public function leaveDays()
    {
        return $this->hasMany(LeaveDay::class, 'workflow_id');
    }

    /**
     * 取得關聯的職稱
     */
    public function job()
    {
        return $this->belongsTo(\App\Models\Job::class, 'job_id');
    }

    /**
     * 取得啟用的關卡
     */
    public function activeSteps()
    {
        return $this->hasMany(WorkflowStep::class, 'workflow_id')
            ->where('is_active', true)
            ->orderBy('step_order');
    }

    /**
     * 檢查流程是否可用
     */
    public function isAvailable()
    {
        return $this->is_active && $this->steps()->where('is_active', true)->count() > 0;
    }

    /**
     * 取得流程的第一個關卡
     */
    public function firstStep()
    {
        return $this->hasOne(WorkflowStep::class, 'workflow_id')
            ->where('is_active', true)
            ->orderBy('step_order');
    }

    /**
     * 取得流程的最後一個關卡
     */
    public function lastStep()
    {
        return $this->hasOne(WorkflowStep::class, 'workflow_id')
            ->where('is_active', true)
            ->orderBy('step_order', 'desc');
    }

    /**
     * 取得指定順序的下一個關卡
     */
    public function getNextStep($currentOrder)
    {
        return $this->steps()
            ->where('is_active', true)
            ->where('step_order', '>', $currentOrder)
            ->orderBy('step_order')
            ->first();
    }

    /**
     * 取得指定順序的上一個關卡
     */
    public function getPreviousStep($currentOrder)
    {
        return $this->steps()
            ->where('is_active', true)
            ->where('step_order', '<', $currentOrder)
            ->orderBy('step_order', 'desc')
            ->first();
    }

    /**
     * 取得流程統計資訊
     */
    public function getStatsAttribute()
    {
        $totalRequests = $this->leaveDays()->count();
        $pendingRequests = $this->leaveDays()->where('state', 2)->count();
        $approvedRequests = $this->leaveDays()->where('state', 9)->count();
        $rejectedRequests = $this->leaveDays()->where('state', 3)->count();

        return [
            'total' => $totalRequests,
            'pending' => $pendingRequests,
            'approved' => $approvedRequests,
            'rejected' => $rejectedRequests,
            'completion_rate' => $totalRequests > 0 ? round(($approvedRequests / $totalRequests) * 100, 2) : 0,
        ];
    }

    /**
     * 取得流程的狀態文字
     */
    public function getStatusTextAttribute()
    {
        return $this->is_active ? '啟用中' : '已停用';
    }

    /**
     * 取得流程的狀態顏色
     */
    public function getStatusColorAttribute()
    {
        return $this->is_active ? 'success' : 'secondary';
    }

    /**
     * 取得流程的完整資訊
     */
    public function getFullInfoAttribute()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status_text,
            'status_color' => $this->status_color,
            'steps_count' => $this->steps()->count(),
            'active_steps_count' => $this->steps()->where('is_active', true)->count(),
            'stats' => $this->stats,
            'job_name' => $this->job->name ?? '全部職稱',
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * 根據用戶職稱取得適合的流程
     */
    public static function getWorkflowForUser($userId)
    {
        $user = \App\Models\User::with('job')->find($userId);
        
        if (!$user || !$user->job) {
            // 如果用戶沒有職稱，使用預設流程（沒有指定職稱的流程）
            return self::where('is_active', true)
                ->whereNull('job_id')
                ->first();
        }

        // 先尋找該職稱專用的流程
        $workflow = self::where('is_active', true)
            ->where('job_id', $user->job_id)
            ->first();

        // 如果沒有專用流程，使用預設流程
        if (!$workflow) {
            $workflow = self::where('is_active', true)
                ->whereNull('job_id')
                ->first();
        }

        return $workflow;
    }

    /**
     * 取得所有可用的流程（按職稱分組）
     */
    public static function getAvailableWorkflows()
    {
        return self::with(['job', 'steps'])
            ->where('is_active', true)
            ->orderBy('job_id')
            ->orderBy('created_at')
            ->get()
            ->groupBy(function ($workflow) {
                return $workflow->job ? $workflow->job->name : '預設流程';
            });
    }

    /**
     * 取得所有可用的流程（簡單列表）
     */
    public static function getAllAvailableWorkflows()
    {
        return self::with(['job', 'steps'])
            ->where('is_active', true)
            ->orderBy('job_id')
            ->orderBy('created_at')
            ->get();
    }
}