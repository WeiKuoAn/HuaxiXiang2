<?php

namespace App\Services;

use App\Models\LeaveDay;
use App\Models\LeaveDayCheck;
use App\Models\Workflow;
use App\Models\WorkflowStep;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LeaveWorkflowService
{
    /**
     * 為假單啟動工作流程
     */
    public function startWorkflow(LeaveDay $leaveDay, $workflowId = null, $createSubmissionRecord = true, $resumeStepId = null)
    {
        DB::beginTransaction();
        try {
            // 1. 如果有指定工作流程ID，使用指定的工作流程；否則使用請假管理審核流程
            if ($workflowId) {
                $workflow = Workflow::find($workflowId);
            } else {
                $workflow = Workflow::where('category', 'leave')->first();
            }
            
            if (!$workflow) {
                // 如果沒有找到工作流程，使用預設流程（直接核准）
                $this->createDefaultApproval($leaveDay);
                DB::commit();
                return;
            }
            
            // 2. 將工作流程ID關聯到假單
            $leaveDay->workflow_id = $workflow->id;
            $leaveDay->state = 2; // 設為待審核狀態
            $leaveDay->save();
            
            // 3. 取得工作流程的步驟
            $steps = $workflow->steps()->orderBy('step_order')->get();
            
            if ($steps->isEmpty()) {
                // 如果沒有步驟，直接核准
                $this->createDefaultApproval($leaveDay);
                DB::commit();
                return;
            }
            
            // 4. 創建「送出審核」記錄（記錄送出的人員）- 只有當需要時才創建
            if ($createSubmissionRecord) {
                $this->createSubmissionRecord($leaveDay);
            }
            
            // 5. 創建第一關的審核記錄
            $startStep = null;
            if ($resumeStepId) {
                $startStep = $steps->firstWhere('id', $resumeStepId);
            }
            if (!$startStep) {
                $startStep = $steps->first();
            }

            if ($resumeStepId) {
                // 清除舊的待審核紀錄避免重複
                LeaveDayCheck::where('leave_day_id', $leaveDay->id)
                    ->where('state', 2)
                    ->delete();
            }

            $this->createCheckRecord($leaveDay, $startStep, 2); // 狀態2表示待審核
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * 處理審核結果
     */
    public function processApproval(LeaveDay $leaveDay, LeaveDayCheck $check, $action)
    {
        DB::beginTransaction();
        try {
            // 更新審核記錄
            $check->state = $action === 'approve' ? 9 : 3; // 9=核准, 3=駁回
            
            // 如果 step_id 為空，嘗試找到對應的步驟
            if (empty($check->step_id)) {
                $currentStep = $this->getCurrentStep($leaveDay, $check);
                if ($currentStep) {
                    $check->step_id = $currentStep->id;
                }
            }
            
            $check->save();
            
            if ($action === 'reject') {
                // 駁回：假單設為未送出狀態，讓申請人可以重新送出
                $leaveDay->state = 1; // 1=未送出
                $leaveDay->save();
                DB::commit();
                return;
            }
            
            // 核准：檢查是否還有下一關
            $workflow = $leaveDay->workflow;
            $currentStep = $this->getCurrentStep($leaveDay, $check);
            
            if (!$currentStep) {
                // 找不到當前步驟，直接核准
                $leaveDay->state = 9;
                $leaveDay->save();
                DB::commit();
                return;
            }
            
            $nextStep = $this->getNextStep($workflow, $currentStep);
            
            if ($nextStep) {
                // 還有下一關，創建下一關的審核記錄
                $this->createCheckRecord($leaveDay, $nextStep, 2);
                // 假單狀態保持待審核
            } else {
                // 沒有下一關了，核准假單
                $leaveDay->state = 9;
                $leaveDay->save();
            }
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * 取得當前審核步驟
     */
    private function getCurrentStep(LeaveDay $leaveDay, LeaveDayCheck $check)
    {
        $workflow = $leaveDay->workflow;
        if (!$workflow) {
            return null;
        }
        
        $steps = $workflow->steps()->orderBy('step_order')->get();
        
        // 找到與當前審核人員對應的步驟
        foreach ($steps as $step) {
            if ($step->approver_user_id == $check->check_user_id) {
                return $step;
            }
        }
        
        return null;
    }
    
    /**
     * 取得下一個審核步驟
     */
    private function getNextStep(Workflow $workflow, WorkflowStep $currentStep)
    {
        return $workflow->steps()
            ->where('step_order', '>', $currentStep->step_order)
            ->orderBy('step_order')
            ->first();
    }
    
    /**
     * 創建「送出審核」記錄（記錄送出的人員）
     */
    private function createSubmissionRecord(LeaveDay $leaveDay)
    {
        return LeaveDayCheck::create([
            'leave_day_id' => $leaveDay->id,
            'check_day' => Carbon::now()->format('Y-m-d'),
            'check_user_id' => $leaveDay->user_id, // 送出的人員
            'state' => 10, // 狀態10表示送出審核
            'comment' => null,
        ]);
    }

    /**
     * 創建審核記錄
     */
    private function createCheckRecord(LeaveDay $leaveDay, WorkflowStep $step, $state)
    {
        return LeaveDayCheck::create([
            'leave_day_id' => $leaveDay->id,
            'step_id' => $step->id,
            'check_day' => Carbon::now()->format('Y-m-d'),
            'check_user_id' => $step->approver_user_id,
            'state' => $state,
            'comment' => null,
        ]);
    }
    
    /**
     * 創建預設核准記錄（當沒有工作流程時）
     */
    private function createDefaultApproval(LeaveDay $leaveDay)
    {
        $leaveDay->state = 9; // 直接核准
        $leaveDay->save();
        
        // 創建核准記錄
        LeaveDayCheck::create([
            'leave_day_id' => $leaveDay->id,
            'check_day' => Carbon::now()->format('Y-m-d'),
            'check_user_id' => $leaveDay->user_id, // 申請人自己核准
            'state' => 9,
            'comment' => '系統自動核准',
        ]);
    }
    
    /**
     * 取得假單的當前審核狀態
     */
    public function getCurrentStatus(LeaveDay $leaveDay)
    {
        if ($leaveDay->state == 9) {
            return [
                'status' => 'approved',
                'message' => '已核准',
                'current_step' => null,
                'next_step' => null
            ];
        }
        
        if ($leaveDay->state == 3) {
            return [
                'status' => 'rejected',
                'message' => '已駁回',
                'current_step' => null,
                'next_step' => null
            ];
        }
        
        // 找到當前的審核記錄
        $currentCheck = LeaveDayCheck::where('leave_day_id', $leaveDay->id)
            ->where('state', 2) // 待審核
            ->orderBy('created_at', 'desc')
            ->first();
            
        if (!$currentCheck) {
            return [
                'status' => 'unknown',
                'message' => '狀態不明',
                'current_step' => null,
                'next_step' => null
            ];
        }
        
        $workflow = $leaveDay->workflow;
        $currentStep = $this->getCurrentStep($leaveDay, $currentCheck);
        $nextStep = $currentStep ? $this->getNextStep($workflow, $currentStep) : null;
        
        return [
            'status' => 'pending',
            'message' => '待審核',
            'current_step' => $currentStep,
            'next_step' => $nextStep,
            'current_approver' => $currentCheck->user
        ];
    }
}
