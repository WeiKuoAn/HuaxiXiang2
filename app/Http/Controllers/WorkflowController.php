<?php

namespace App\Http\Controllers;

use App\Models\LeaveDay;
use App\Models\LeaveDayCheck;
use App\Models\Workflow;
use App\Models\WorkflowStep;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class WorkflowController extends Controller
{
    /**
     * 顯示流程列表
     */
    public function index()
    {
        // 取得所有職稱審核流程
        $workflows = Workflow::with(['steps.approver', 'job'])
            ->withCount(['steps', 'leaveDays'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('flow.index', compact('workflows'));
    }

    /**
     * 顯示新增流程頁面
     */
    public function create()
    {
        $users = User::with('job_data')->where('status', '0')->orderBy('name')->get();
        
        // 取得所有職稱
        $jobs = \App\Models\Job::orderBy('name')->get();
        
        return view('flow.create', compact('users', 'jobs'));
    }

    /**
     * 儲存新流程
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
            'category' => 'required|in:leave,discipline',
            'job_id' => 'required|exists:job,id',
            'steps' => 'required|array|min:1',
            'steps.*.approver_user_id' => 'required|exists:users,id',
            'steps.*.step_order' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            \Log::error('流程表單驗證失敗', [
                'errors' => $validator->errors()->toArray(),
                'input' => $request->all()
            ]);
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // 檢查該職稱的該類別是否已有流程
            $existingWorkflow = Workflow::where('job_id', $request->job_id)
                ->where('category', $request->category)
                ->first();
            
            if ($existingWorkflow) {
                // 如果已有流程，更新現有流程
                $workflow = $existingWorkflow;
                $workflow->update([
                    'name' => $request->name,
                    'description' => $request->description,
                    'is_active' => $request->is_active,
                ]);
                
                // 刪除現有的關卡
                $workflow->steps()->delete();
            } else {
                // 創建新流程
                $workflow = Workflow::create([
                    'name' => $request->name,
                    'description' => $request->description,
                    'is_active' => $request->is_active,
                    'category' => $request->category,
                    'job_id' => $request->job_id,
                ]);
            }

            // 創建關卡
            foreach ($request->steps as $stepData) {
                WorkflowStep::create([
                    'workflow_id' => $workflow->id,
                    'step_name' => '審核關卡', // 預設關卡名稱
                    'approver_user_id' => $stepData['approver_user_id'],
                    'step_order' => $stepData['step_order'],
                    'is_active' => true,
                ]);
            }

            DB::commit();
            $message = $existingWorkflow ? '流程更新成功！' : '流程創建成功！';
            return redirect()
                ->route('flow.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->with('error', '創建流程時發生錯誤：' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * 顯示編輯流程頁面
     */
    public function edit($id)
    {
        $workflow = Workflow::with('steps.approver', 'job')->findOrFail($id);
        $users = User::with('job_data')->where('status', '0')->orderBy('name')->get();
        
        // 取得所有職稱
        $jobs = \App\Models\Job::orderBy('name')->get();

        return view('flow.edit', compact('workflow', 'users', 'jobs'));
    }

    /**
     * 更新流程
     */
    public function update(Request $request, $id)
    {
        $workflow = Workflow::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
            'category' => 'required|in:leave,discipline',
            'job_id' => 'required|exists:job,id',
            'steps' => 'required|array|min:1',
            'steps.*.approver_user_id' => 'required|exists:users,id',
            'steps.*.step_order' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // 更新流程
            $workflow->update([
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => $request->is_active,
                'category' => $request->category,
                'job_id' => $request->job_id,
            ]);

            // 更新關卡
            $existingStepIds = [];
            foreach ($request->steps as $stepData) {
                if (isset($stepData['id'])) {
                    // 更新現有關卡
                    $step = WorkflowStep::find($stepData['id']);
                    if ($step) {
                        $step->update([
                            'approver_user_id' => $stepData['approver_user_id'],
                            'step_order' => $stepData['step_order'],
                        ]);
                        $existingStepIds[] = $step->id;
                    }
                } else {
                    // 新增關卡
                    $newStep = WorkflowStep::create([
                        'workflow_id' => $workflow->id,
                        'step_name' => '審核關卡', // 預設關卡名稱
                        'approver_user_id' => $stepData['approver_user_id'],
                        'step_order' => $stepData['step_order'],
                        'is_active' => true,
                    ]);
                    $existingStepIds[] = $newStep->id;
                }
            }

            // 刪除不在新列表中的關卡
            WorkflowStep::where('workflow_id', $workflow->id)
                ->whereNotIn('id', $existingStepIds)
                ->delete();

            DB::commit();
            return redirect()
                ->route('flow.index')
                ->with('success', '流程更新成功！');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->with('error', '更新流程時發生錯誤：' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * 切換流程狀態
     */
    public function toggleStatus(Request $request, $workflowId)
    {
        $workflow = Workflow::find($workflowId);
        
        if (!$workflow) {
            return response()->json([
                'success' => false,
                'message' => '流程不存在'
            ]);
        }

        $workflow->update([
            'is_active' => $request->status
        ]);

        return response()->json([
            'success' => true,
            'message' => '狀態更新成功'
        ]);
    }

    /**
     * 顯示流程狀態總覽
     */
    public function status(Request $request)
    {
        $workflows = Workflow::where('is_active', true)->get();

        // 統計數據
        $stats = [
            'pending' => LeaveDay::where('state', 2)->count(),
            'approved' => LeaveDay::where('state', 9)->count(),
            'rejected' => LeaveDay::where('state', 3)->count(),
            'cancelled' => LeaveDay::where('state', 4)->count(),
        ];

        // 查詢假單申請
        $query = LeaveDay::with(['user', 'leave', 'workflow', 'checks'])
            ->orderBy('created_at', 'desc');

        // 篩選條件
        if ($request->status) {
            switch ($request->status) {
                case 'pending':
                    $query->where('state', 2);
                    break;
                case 'approved':
                    $query->where('state', 9);
                    break;
                case 'rejected':
                    $query->where('state', 3);
                    break;
                case 'cancelled':
                    $query->where('state', 4);
                    break;
            }
        }

        if ($request->workflow_id) {
            $query->where('workflow_id', $request->workflow_id);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $leaveRequests = $query->paginate(20);

        // 流程統計
        $workflowStats = [
            'labels' => $workflows->pluck('name')->toArray(),
            'data' => $workflows->map(function ($workflow) {
                return LeaveDay::where('workflow_id', $workflow->id)->count();
            })->toArray()
        ];

        return view('flow.status', compact(
            'leaveRequests',
            'workflows',
            'stats',
            'workflowStats'
        ));
    }

    /**
     * 顯示流程詳情
     */
    public function detail($id)
    {
        $leaveRequest = LeaveDay::with([
            'user',
            'leave',
            'workflow.steps.approver',
            'checks.user',
            'currentStep.approver'
        ])->findOrFail($id);

        $workflowSteps = $leaveRequest->workflow
            ? $leaveRequest->workflow->steps->sortBy('step_order')
            : collect();

        return view('flow.detail', compact('leaveRequest', 'workflowSteps'));
    }

    /**
     * 顯示關卡設定頁面
     */
    public function steps($id)
    {
        $workflow = Workflow::with('steps.approver')->findOrFail($id);
        $users = User::with('job_data')->orderBy('name')->get();

        return view('flow.steps', compact('workflow', 'users'));
    }

    /**
     * 新增關卡
     */
    public function storeStep(Request $request, $workflowId)
    {
        $validator = Validator::make($request->all(), [
            'step_name' => 'required|string|max:255',
            'approver_user_id' => 'required|exists:users,id',
            'step_order' => 'required|integer|min:1',
            'is_active' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        WorkflowStep::create([
            'workflow_id' => $workflowId,
            'step_name' => $request->step_name,
            'approver_user_id' => $request->approver_user_id,
            'step_order' => $request->step_order,
            'is_active' => $request->is_active,
        ]);

        return redirect()
            ->route('flow.steps', $workflowId)
            ->with('success', '關卡新增成功！');
    }

    /**
     * 編輯關卡（AJAX）
     */
    public function editStep($id)
    {
        $step = WorkflowStep::with('approver')->findOrFail($id);

        return response()->json([
            'success' => true,
            'step' => $step
        ]);
    }

    /**
     * 更新關卡
     */
    public function updateStep(Request $request, $id)
    {
        $step = WorkflowStep::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'step_name' => 'required|string|max:255',
            'approver_user_id' => 'required|exists:users,id',
            'step_order' => 'required|integer|min:1',
            'is_active' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $step->update([
            'step_name' => $request->step_name,
            'approver_user_id' => $request->approver_user_id,
            'step_order' => $request->step_order,
            'is_active' => $request->is_active,
        ]);

        return redirect()
            ->route('flow.steps', $step->workflow_id)
            ->with('success', '關卡更新成功！');
    }

    /**
     * 切換關卡狀態
     */
    public function toggleStepStatus(Request $request, $id)
    {
        $step = WorkflowStep::findOrFail($id);

        $step->update([
            'is_active' => $request->status
        ]);

        return response()->json([
            'success' => true,
            'message' => '關卡狀態更新成功'
        ]);
    }

    /**
     * 刪除關卡
     */
    public function deleteStep($id)
    {
        $step = WorkflowStep::findOrFail($id);
        $workflowId = $step->workflow_id;

        // 檢查是否有進行中的假單使用此關卡
        $hasActiveRequests = LeaveDayCheck::where('check_user_id', $step->approver_user_id)
            ->whereHas('leaveDay', function ($query) use ($workflowId) {
                $query
                    ->where('workflow_id', $workflowId)
                    ->whereIn('state', [2]);  // 待審核狀態
            })
            ->exists();

        if ($hasActiveRequests) {
            return response()->json([
                'success' => false,
                'message' => '無法刪除：此關卡有進行中的假單申請'
            ]);
        }

        $step->delete();

        return response()->json([
            'success' => true,
            'message' => '關卡刪除成功'
        ]);
    }

    /**
     * 顯示刪除確認頁面
     */
    public function delete($id)
    {
        $workflow = Workflow::with(['steps.approver', 'job'])->findOrFail($id);
        
        // 計算使用此流程的申請數量
        $totalRequests = LeaveDay::where('workflow_id', $workflow->id)->count();
        $activeRequests = LeaveDay::where('workflow_id', $workflow->id)
            ->whereIn('state', [2])  // 待審核狀態
            ->count();
        
        return view('flow.delete', compact('workflow', 'totalRequests', 'activeRequests'));
    }

    /**
     * 刪除流程
     */
    public function destroy($id)
    {
        try {
            $workflow = Workflow::findOrFail($id);

            // 檢查是否有進行中的申請使用此流程
            $hasActiveRequests = LeaveDay::where('workflow_id', $workflow->id)
                ->whereIn('state', [2])  // 待審核狀態
                ->exists();

            if ($hasActiveRequests) {
                return response()->json([
                    'success' => false,
                    'message' => '無法刪除：此流程有進行中的申請，請先處理完畢'
                ], 400);
            }

            // 刪除流程的所有關卡
            $workflow->steps()->delete();

            // 刪除流程
            $workflow->delete();

            // 判斷是否為 AJAX 請求
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => '流程刪除成功'
                ]);
            }

            return redirect()
                ->route('flow.index')
                ->with('success', '流程刪除成功');

        } catch (\Exception $e) {
            \Log::error('刪除流程失敗', [
                'workflow_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            // 判斷是否為 AJAX 請求
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => '刪除失敗：' . $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->with('error', '刪除失敗：' . $e->getMessage());
        }
    }
}
