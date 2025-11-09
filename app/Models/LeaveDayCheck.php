<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveDayCheck extends Model
{
    use HasFactory;

    protected $table = "leave_day_check";

    protected $fillable = [
        'leave_day_id',
        'step_id',
        'check_day',
        'check_user_id',
        'comment',
        'state',
    ];

    /**
     * 取得審核狀態文字
     */
    public function leave_check_status(){
        $leave_name = [ 
            '1'=>'新增假單' , 
            '2'=>'待審核' , 
            '3'=>'已駁回' , 
            '9'=>'已核准',
            '10'=>'送出審核',  // 新增送出審核狀態
            '11'=>'編輯假單'   // 新增編輯假單狀態
        ];
        return $leave_name[$this->state] ?? '未知狀態';
    }

    /**
     * 取得審核人員資訊
     */
    public function user_name(){
        return $this->hasOne('App\Models\User','id','check_user_id');
    }

    /**
     * 取得審核人員資訊 (新關聯)
     */
    public function user(){
        return $this->belongsTo(User::class, 'check_user_id');
    }

    /**
     * 取得假單資訊
     */
    public function leaveDay(){
        return $this->belongsTo(LeaveDay::class, 'leave_day_id');
    }

    /**
     * 取得工作流程步驟資訊
     */
    public function step(){
        return $this->belongsTo(WorkflowStep::class, 'step_id');
    }

    /**
     * 取得審核狀態文字 (新方法)
     */
    public function getStatusTextAttribute(){
        $statusMap = [
            '1' => '新增假單',
            '2' => '待審核',
            '3' => '已駁回',
            '9' => '已核准',
            '10' => '送出審核',
            '11' => '編輯假單'
        ];
        return $statusMap[$this->state] ?? '未知狀態';
    }

    /**
     * 取得審核狀態顏色
     */
    public function getStatusColorAttribute(){
        $colorMap = [
            '1' => 'secondary',
            '2' => 'warning',
            '3' => 'danger',
            '9' => 'success',
            '10' => 'info',
            '11' => 'primary'
        ];
        return $colorMap[$this->state] ?? 'secondary';
    }

    /**
     * 取得審核記錄的完整資訊
     */
    public function getFullInfoAttribute(){
        return [
            'id' => $this->id,
            'leave_day_id' => $this->leave_day_id,
            'check_user_name' => $this->user->name ?? '未知',
            'check_user_id' => $this->check_user_id,
            'status' => $this->status_text,
            'status_color' => $this->status_color,
            'comment' => $this->comment,
            'check_day' => $this->check_day,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
