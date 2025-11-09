<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Workflow;

class LeaveDay extends Model
{
    use HasFactory;

    protected $table = "leave_day";

    protected $fillable = [
        'user_id',
        'leave_day',
        'start_datetime',
        'end_datetime',
        'unit',
        'total',
        'comment',
        'director_id',
        'file',
        'state',
        'workflow_id',
    ];

    public function leave_check()
    {
        return $this->hasOne('App\Models\LeaveDayCheck', 'leave_day_id', 'id')->orderBy('check_day', 'desc')->where('state', 9);
    }

    public function leave_name(){
        return $this->hasOne('App\Models\Leaves','id','leave_day');
    }

    public function leave_status(){
        $leave_name = [ '1'=>'未送出' , '2'=>'待審核' , '3'=>'已駁回' , '9'=>'已核准'];
        return $leave_name[$this->state];
    }

    public function user_name(){
        return $this->hasOne('App\Models\User','id','user_id');
    }

    /**
     * 取得假別資訊
     */
    public function leave()
    {
        return $this->belongsTo(Leaves::class, 'leave_day');
    }

    /**
     * 取得申請人資訊
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * 取得簽核流程
     */
    public function workflow()
    {
        return $this->belongsTo(Workflow::class, 'workflow_id');
    }

    /**
     * 取得所有審核記錄
     */
    public function checks()
    {
        return $this->hasMany(LeaveDayCheck::class, 'leave_day_id');
    }

    /**
     * 取得目前的審核關卡
     */
    public function currentStep()
    {
        // 這是一個計算屬性，不是關聯
        // 在控制器中不應該使用 with() 載入
        return null;
    }

    /**
     * 取得目前的審核關卡（關聯版本）
     */
    public function currentStepRelation()
    {
        return $this->hasOne(WorkflowStep::class, 'workflow_id', 'workflow_id')
            ->whereHas('checks', function($query) {
                $query->where('state', 2);
            });
    }

    /**
     * 取得最後一個審核記錄
     */
    public function lastCheck()
    {
        return $this->hasOne(LeaveDayCheck::class, 'leave_day_id')
            ->orderBy('check_day', 'desc');
    }

    /**
     * 是否有簽核流程
     */
    public function hasWorkflow()
    {
        return !is_null($this->workflow_id);
    }

    /**
     * 取得簽核狀態
     */
    public function getWorkflowStatusAttribute()
    {
        if (!$this->hasWorkflow()) {
            return '無流程';
        }

        switch ($this->state) {
            case 1:
                return '草稿';
            case 2:
                return '待審核';
            case 9:
                return '已核准';
            case 3:
                return '已駁回';
            case 4:
                return '已撤銷';
            default:
                return '未知狀態';
        }
    }

    /**
     * 取得流程進度百分比
     */
    public function getProgressPercentageAttribute()
    {
        if (!$this->hasWorkflow()) {
            return 0;
        }

        $totalSteps = $this->workflow->steps()->where('is_active', true)->count();
        $completedSteps = $this->checks()->where('state', 9)->count();

        return $totalSteps > 0 ? round(($completedSteps / $totalSteps) * 100, 2) : 0;
    }

    /**
     * 取得流程進度文字
     */
    public function getProgressTextAttribute()
    {
        if (!$this->hasWorkflow()) {
            return '無流程';
        }

        $totalSteps = $this->workflow->steps()->where('is_active', true)->count();
        $completedSteps = $this->checks()->where('state', 9)->count();

        return "{$completedSteps}/{$totalSteps}";
    }

    /**
     * 取得假單的完整資訊
     */
    public function getFullInfoAttribute()
    {
        return [
            'id' => $this->id,
            'user_name' => $this->user->name ?? '未知',
            'leave_name' => $this->leave->name ?? '未知假別',
            'start_datetime' => $this->start_datetime,
            'end_datetime' => $this->end_datetime,
            'total' => $this->total,
            'unit' => $this->unit,
            'comment' => $this->comment,
            'state' => $this->state,
            'workflow_status' => $this->workflow_status,
            'progress_percentage' => $this->progress_percentage,
            'progress_text' => $this->progress_text,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
