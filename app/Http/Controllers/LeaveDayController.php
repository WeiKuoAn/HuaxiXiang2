<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveDay;
use App\Models\Job;
use App\Models\LeaveDayCheck;
use App\Models\Leaves;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Services\LeaveWorkflowService;


//個別價單控制
class LeaveDayController extends Controller
{
    public function index(Request $request)
    {
        $leaves = Leaves::where('status', 0)->orderby('seq')->get();
        $datas = LeaveDay::orderby('start_datetime', 'desc')->orderby('created_at', 'desc');
        if ($request) {
            $state = $request->state;
            if ($state) {
                $datas = $datas->where('state', $state);
            } else {
                // 根據使用者職稱設定預設篩選
                if (Auth::user()->job_id == 2) {
                    // 主管：顯示所有待審核的假單
                    $datas = $datas->where('state', 2);
                } else {
                    // 其他人：顯示流程中正要由他們審核的假單
                    // 需要檢查該使用者的最新審核記錄是否為待審核狀態
                    $datas = $datas->where('state', 2)
                        ->whereExists(function($query) {
                            $query->select(\DB::raw(1))
                                  ->from('leave_day_check as ldc1')
                                  ->whereRaw('ldc1.leave_day_id = leave_day.id')
                                  ->where('ldc1.check_user_id', Auth::user()->id)
                                  ->where('ldc1.state', 2)
                                  ->whereRaw('ldc1.created_at = (
                                      SELECT MAX(ldc2.created_at) 
                                      FROM leave_day_check as ldc2 
                                      WHERE ldc2.leave_day_id = leave_day.id 
                                      AND ldc2.check_user_id = ?)', [Auth::user()->id]);
                        });
                }
            }
            $start_date_start = $request->start_date_start;
            if ($start_date_start) {
                $start_date_start = $request->start_date_start . ' 00:00:00';
                $datas = $datas->where('start_datetime', '>=', $start_date_start);
            }
            $start_date_end = $request->start_date_end;
            if ($start_date_end) {
                $start_date_end = $request->start_date_end . ' 11:59:59';
                $datas = $datas->where('start_datetime', '<=', $start_date_end);
            }
            $end_date_start = $request->end_date_start;
            if ($end_date_start) {
                $end_date_start = $request->end_date_start . ' 00:00:00';
                $datas = $datas->where('end_datetime', '>=', $end_date_start);
            }
            $end_date_end = $request->end_date_end;
            if ($end_date_end) {
                $end_date_end = $request->end_date_end . ' 11:59:59';
                $datas = $datas->where('end_datetime', '<=', $end_date_end);
            }
            $leave_day = $request->leave_day;
            if ($leave_day != "null") {
                if (isset($leave_day)) {
                    $datas = $datas->where('leave_day', $leave_day);
                } else {
                    $datas = $datas;
                }
            }
            $condition = $condition = $request->all();
            $datas = $datas->paginate(50);
        } else {
            $datas = $datas->paginate(50);
            $condition = '';
        }
        return view('leaveday.index')->with('datas', $datas)->with('request', $request)->with('condition', $condition)->with('leaves', $leaves);
    }

    public function create()
    {
        $users = User::where('status', '0')->get();
        $leaves = Leaves::where('status', 0)->orderby('seq')->get();
        return view('leaveday.create')->with('users', $users)->with('leaves', $leaves);
    }

    public function store(Request $request)
    {
        // dd($request->file);
        $job = Job::where('id', Auth::user()->job_id)->first();
        $data = new LeaveDay;

        if (Auth::user()->job_id == 2) {
            $data->user_id = $request->auth_name;
            if (isset($job->director_id)) {
                $data->director_id = $job->director_id;
            } else {
                $data->director_id = '1'; //主管直接顯示老闆
            }
            $data->state = 9;
        } else {
            $data->user_id = Auth::user()->id;
            if (isset($job->director_id)) {
                $data->director_id = $job->director_id;
            } else {
                $data->director_id = '1'; //主管直接顯示老闆
            }
            $data->state = 1;
        }
        $data->leave_day = $request->leave_day;
        $data->start_datetime = $request->start_date . ' ' . $request->start_time;
        $data->end_datetime = $request->end_date . ' ' . $request->end_time;
        $data->unit = $request->unit;
        $data->total = $request->total;
        $data->comment = $request->comment;
        $data->file = $request->filename;
        
        // 根據使用者職稱自動關聯審核流程
        if (Auth::user()->job_id) {
            $workflow = \App\Models\Workflow::where('is_active', true)
                ->where('category', 'leave')
                ->where('job_id', Auth::user()->job_id)
                ->first();
            
            if ($workflow) {
                $data->workflow_id = $workflow->id;
            }
        }
        
        $data->save();


        $leave_data = LeaveDay::orderby('id', 'desc')->first();
        $item = new LeaveDayCheck;
        $item->leave_day_id = $leave_data->id;
        $item->check_day = Carbon::now()->locale('zh-tw')->format('Y-m-d');
        $item->check_user_id = Auth::user()->id;
        $item->created_at = Carbon::now()->locale('zh-tw');
        if (Auth::user()->job_id == 2) {
            $item->state = 9;
        } else {
            $item->state = 1;
        }
        $item->save();

        if (Auth::user()->job_id == 2) {
            return redirect()->route('personnel.leave_days');
        } else {
            return redirect()->route('person.leave_days');
        }
    }

    public function uploadFile(Request $request)
    {
        // 驗證檔案格式與大小
        $request->validate([
            'file' => 'required|mimes:pdf,jpg,png|max:2048', // 限制檔案類型與大小
        ]);

        if ($request->hasFile('file')) {
            // 獲取檔案
            $file = $request->file('file');

            // 生成檔案名稱（時間戳 + 原始檔名）
            $fileName = time() . '_' . $file->getClientOriginalName();

            // 定義存放路徑為 public/assets/uploads
            $destinationPath = public_path('assets/uploads');

            // 如果目錄不存在，則建立目錄
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            // 將檔案移動到指定目錄
            $file->move($destinationPath, $fileName);

            // 生成檔案的公開 URL
            $fileUrl = asset('assets/uploads/' . $fileName);

            // 返回 JSON 回應，包含檔案 URL
            return response()->json([
                'success' => true,
                'file_url' => $fileUrl,
            ]);
        }

        // 如果沒有檔案
        return response()->json([
            'success' => false,
            'message' => '檔案上傳失敗，請確認是否選擇了檔案。',
        ]);
    }





    public function show($id)
    {
        $data = LeaveDay::where('id', $id)->first();
        $leaves = Leaves::where('status', 0)->orderby('seq')->get();
        return view('leaveday.edit')->with('data', $data)->with('leaves', $leaves);
    }

    public function update($id, Request $request)
    {
        $data = LeaveDay::where('id', $id)->first();
        $originalState = $data->state;
        
        $data->leave_day = $request->leave_day;
        $data->start_datetime = $request->start_date . ' ' . $request->start_time;
        $data->end_datetime = $request->end_date . ' ' . $request->end_time;
        $data->unit = $request->unit;
        $data->total = $request->total;
        $data->comment = $request->comment;
        $data->file = $request->filename;
        $data->state = 1;
        $data->save();

        // 如果是已駁回的假單，創建新的編輯記錄
        if ($originalState == 3) {
            // 創建編輯記錄（state = 1）
            $editCheck = new LeaveDayCheck;
            $editCheck->leave_day_id = $data->id;
            $editCheck->check_user_id = $data->user_id; // 申請人自己編輯
            $editCheck->state = 1; // 編輯狀態
            $editCheck->check_day = Carbon::now()->locale('zh-tw')->format('Y-m-d');
            $editCheck->created_at = Carbon::now()->locale('zh-tw');
            $editCheck->save();
        } else {
            // 一般編輯，更新現有記錄
            $item = LeaveDayCheck::where('leave_day_id', $data->id)->first();
            if ($item) {
                $item->state = 1;
                $item->check_day = Carbon::now()->locale('zh-tw')->format('Y-m-d');
                $item->updated_at = Carbon::now()->locale('zh-tw');
                $item->save();
            }
        }

        return redirect()->route('person.leave_days');
    }

    public function check($id)
    {
        $data = LeaveDay::with(['user.job_data', 'workflow.steps.approver'])->where('id', $id)->first();
        $items = LeaveDayCheck::where('leave_day_id', $data->id)->get();
        $leaves = Leaves::where('status', 0)->orderby('seq')->get();
        $users = User::where('status', '0')->whereIn('job_id', [1, 2, 3, 10])->get();
        
        // 如果假單還沒有關聯到流程，根據申請人的職稱自動關聯
        if (!$data->workflow_id && $data->user && $data->user->job_id) {
            $workflow = \App\Models\Workflow::where('is_active', true)
                ->where('category', 'leave')
                ->where('job_id', $data->user->job_id)
                ->first();
            
            if ($workflow) {
                $data->workflow_id = $workflow->id;
                $data->save();
                // 重新載入資料
                $data = LeaveDay::with(['user.job_data', 'workflow.steps.approver'])->where('id', $id)->first();
            }
        }
        
        return view('leaveday.check')->with('data', $data)->with('items', $items)->with('leaves', $leaves)->with('users', $users);
    }

    public function check_data($id, Request $request) //主管確認
    {
        $data = LeaveDay::with('workflow.steps')->find($id);
        
        if ($request->btn_submit == 'check') {
            // 核准假單
            $this->approveLeaveDay($data, $request);
        } elseif ($request->btn_submit == 'not_check') {
            // 駁回假單
            $this->rejectLeaveDay($data, $request);
        }
        
        return redirect()->route('personnel.leave_days');
    }
    
    /**
     * 審核假單（流程中的審核）
     */
    public function approve(Request $request, $id)
    {
        $leaveDay = LeaveDay::with(['workflow.steps', 'checks'])->findOrFail($id);
        $action = $request->input('action', 'approve');
        $comment = $request->input('comment');

        $currentCheck = $leaveDay->checks()
            ->where('state', 2)
            ->where('check_user_id', Auth::id())
            ->orderByDesc('created_at')
            ->first();

        if (!$currentCheck) {
            return redirect()->route('leave_day.check', $id)
                ->with('error', '您沒有權限審核此假單');
        }

        if ($comment) {
            $currentCheck->comment = $comment;
            $currentCheck->save();
        }

        $workflowService = new LeaveWorkflowService();

        try {
            $workflowService->processApproval($leaveDay, $currentCheck, $action === 'reject' ? 'reject' : 'approve');

            $message = $action === 'reject' ? '假單已駁回' : '假單已核准';

            return redirect()->route('leave_day.check', $id)
                ->with('success', $message);
        } catch (\Exception $e) {
            Log::error('假單審核失敗', [
                'leave_day_id' => $id,
                'action' => $action,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('leave_day.check', $id)
                ->with('error', '處理失敗：' . $e->getMessage());
        }
    }
    
    /**
     * 處理核准邏輯
     */
    private function processApproval($leaveDay, $isApproved)
    {
        if (!$leaveDay->workflow || !$leaveDay->workflow->steps->count()) {
            // 沒有流程：直接核准
            $leaveDay->state = 9;
            $leaveDay->save();
            
            $check = new LeaveDayCheck;
            $check->leave_day_id = $leaveDay->id;
            $check->check_user_id = Auth::user()->id;
            $check->state = 9;
            $check->check_day = Carbon::now()->locale('zh-tw')->format('Y-m-d');
            $check->created_at = Carbon::now()->locale('zh-tw');
            $check->save();
            return;
        }
        
        // 創建當前審核記錄
        $check = new LeaveDayCheck;
        $check->leave_day_id = $leaveDay->id;
        $check->check_user_id = Auth::user()->id;
        $check->state = 9; // 已核准
        $check->check_day = Carbon::now()->locale('zh-tw')->format('Y-m-d');
        $check->created_at = Carbon::now()->locale('zh-tw');
        $check->save();
        
        // 按照工作流程步驟順序，檢查是否所有關卡都已完成
        $workflowSteps = $leaveDay->workflow->steps->sortBy('step_order');
        $completedSteps = 0;
        
        // 檢查是否有駁回記錄，如果有，則需要找到最後一次重新送出的時間點
        $allChecks = $leaveDay->checks()->orderBy('created_at')->get();
        $rejectedChecks = $allChecks->where('state', 3);
        $lastRejectionTime = $rejectedChecks->isNotEmpty() ? $rejectedChecks->max('created_at') : null;
        
        if ($lastRejectionTime) {
            // 有駁回記錄：只計算駁回後重新送出的審核記錄
            $resubmitChecks = $allChecks->where('created_at', '>', $lastRejectionTime);
            
            // 取得所有已核准的記錄，按時間排序
            $approvedChecks = $resubmitChecks->where('state', 9)->sortBy('created_at');
            
            // 按照工作流程步驟順序，逐一比對已核准記錄
            foreach ($workflowSteps as $stepIndex => $step) {
                // 檢查是否有對應的核准記錄
                // 關鍵：需要按照順序來比對，不能跳過
                $foundApproval = false;
                foreach ($approvedChecks as $approval) {
                    if ($approval->check_user_id == $step->approver_user_id) {
                        // 找到對應的核准記錄，標記為已完成並移除這筆記錄
                        $foundApproval = true;
                        $approvedChecks = $approvedChecks->reject(function($item) use ($approval) {
                            return $item->id == $approval->id;
                        });
                        break;
                    }
                }
                
                if ($foundApproval) {
                    $completedSteps++;
                } else {
                    // 這個步驟還沒有完成，停止計算
                    break;
                }
            }
        } else {
            // 沒有駁回記錄：按照工作流程步驟順序，計算已完成的步驟數量
            $approvedChecks = $allChecks->whereIn('state', [9, 3])->sortBy('created_at');
            
            foreach ($workflowSteps as $stepIndex => $step) {
                $foundApproval = false;
                foreach ($approvedChecks as $approval) {
                    if ($approval->check_user_id == $step->approver_user_id) {
                        $foundApproval = true;
                        $approvedChecks = $approvedChecks->reject(function($item) use ($approval) {
                            return $item->id == $approval->id;
                        });
                        break;
                    }
                }
                
                if ($foundApproval) {
                    $completedSteps++;
                } else {
                    break;
                }
            }
        }
        
        // 檢查是否所有關卡都已完成
        if ($completedSteps >= $workflowSteps->count()) {
            // 所有關卡都完成：核准假單
            $leaveDay->state = 9;
            $leaveDay->save();
        } else {
            // 還有下一關：創建下一關的審核記錄
            $nextStep = $workflowSteps->skip($completedSteps)->first();
            
            if ($nextStep) {
                $nextCheck = new LeaveDayCheck;
                $nextCheck->leave_day_id = $leaveDay->id;
                $nextCheck->check_user_id = $nextStep->approver_user_id;
                $nextCheck->state = 2; // 待審核
                $nextCheck->check_day = Carbon::now()->locale('zh-tw')->format('Y-m-d');
                $nextCheck->created_at = Carbon::now()->locale('zh-tw');
                $nextCheck->save();
            }
            
            // 保持待審核狀態
            $leaveDay->state = 2;
            $leaveDay->save();
        }
    }
    
    /**
     * 核准假單
     */
    private function approveLeaveDay($leaveDay, $request)
    {
        DB::beginTransaction();
        try {
            // 檢查是否有審核流程
            if ($leaveDay->workflow && $leaveDay->workflow->steps->count() > 0) {
                // 有流程：進入審核流程
                $leaveDay->state = 2; // 待審核
                $leaveDay->save();
                
                // 創建審核記錄
                $check = new LeaveDayCheck;
                $check->leave_day_id = $leaveDay->id;
                $check->check_user_id = Auth::user()->id;
                $check->state = 2; // 待審核
                $check->check_day = Carbon::now()->locale('zh-tw')->format('Y-m-d');
                $check->created_at = Carbon::now()->locale('zh-tw');
                $check->save();
            } else {
                // 沒有流程：直接核准
                $leaveDay->state = 9; // 已核准
                $leaveDay->save();
                
                // 創建核准記錄
                $check = new LeaveDayCheck;
                $check->leave_day_id = $leaveDay->id;
                $check->check_user_id = Auth::user()->id;
                $check->state = 9; // 已核准
                $check->check_day = Carbon::now()->locale('zh-tw')->format('Y-m-d');
                $check->created_at = Carbon::now()->locale('zh-tw');
                $check->save();
            }
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
    
    /**
     * 駁回假單
     */
    private function rejectLeaveDay($leaveDay, $request)
    {
        DB::beginTransaction();
        try {
            $leaveDay->state = 3; // 已駁回
            $leaveDay->save();
            
            // 創建駁回記錄
            $check = new LeaveDayCheck;
            $check->leave_day_id = $leaveDay->id;
            $check->check_user_id = Auth::user()->id;
            $check->state = 3; // 已駁回
            $check->check_day = Carbon::now()->locale('zh-tw')->format('Y-m-d');
            $check->created_at = Carbon::now()->locale('zh-tw');
            $check->save();
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function delete($id)
    {
        $data = LeaveDay::where('id', $id)->first();
        $leaves = Leaves::where('status', 0)->orderby('seq')->get();
        return view('leaveday.del')->with('data', $data)->with('leaves', $leaves);
    }

    public function destroy($id, Request $request)
    {
        $data = LeaveDay::where('id', $id)->first();
        $data->delete();

        $item = LeaveDayCheck::where('leave_day_id', $id)->first();
        $item->delete();

        return redirect()->route('person.leave_days');
    }


    public function user_index($id, Request $request)
    {
        $leaves = Leaves::where('status',)->orderby('seq')->get();
        $datas = LeaveDay::orderby('created_at', 'desc')->where('user_id', $id);
        $user = User::where('id', $id)->first();

        if ($request) {
            $state = $request->state;
            if ($state) {
                $datas = $datas->where('state', $state);
            } else {
                $datas = $datas->where('state', 1);
            }
            $start_date_start = $request->start_date_start;
            if ($start_date_start) {
                $start_date_start = $request->start_date_start . ' 00:00:00';
                $datas = $datas->where('start_datetime', '>=', $start_date_start);
            }
            $start_date_end = $request->start_date_end;
            if ($start_date_end) {
                $start_date_end = $request->start_date_end . ' 11:59:59';
                $datas = $datas->where('start_datetime', '<=', $start_date_end);
            }
            $end_date_start = $request->end_date_start;
            if ($end_date_start) {
                $end_date_start = $request->end_date_start . ' 00:00:00';
                $datas = $datas->where('end_datetime', '>=', $end_date_start);
            }
            $end_date_end = $request->end_date_end;
            if ($end_date_end) {
                $end_date_end = $request->end_date_end . ' 11:59:59';
                $datas = $datas->where('end_datetime', '<=', $end_date_end);
            }
            $leave_day = $request->leave_day;
            if ($leave_day != "null") {
                if (isset($leave_day)) {
                    $datas = $datas->where('leave_day', $leave_day);
                } else {
                    $datas = $datas;
                }
            }
            $condition = $condition = $request->all();
            $datas = $datas->paginate(50);
        } else {
            $datas = $datas->paginate(50);
            $condition = '';
        }
        return view('leaveday.user_index')->with('user', $user)->with('datas', $datas)->with('request', $request)->with('condition', $condition)->with('leaves', $leaves);
    }
}
