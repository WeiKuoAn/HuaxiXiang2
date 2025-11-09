@extends('layouts.vertical', ['page_title' => '請假確認'])

@section('css')
    <!-- third party css -->
    <link href="{{ asset('assets/libs/spectrum-colorpicker2/spectrum-colorpicker2.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/clockpicker/clockpicker.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet"
        type="text/css" />
    <!-- third party css end -->
@endsection

@section('content')
    <!-- Start Content-->
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Huaxixiang</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">請假管理</a></li>
                            <li class="breadcrumb-item active">請假確認</li>
                        </ol>
                    </div>
                    <h4 class="page-title">請假確認</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('leave_day.check.data', $data->id) }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="mb-3">
                                        <div class="mb-3">
                                            <label class="form-label">姓名<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="name"
                                                value="{{ $data->user_name->name }}" readonly>
                                            <input type="hidden" class="form-control" name="user_id"
                                                value="{{ $data->user_id }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="mb-3">
                                        <label for="project-priority" class="form-label">假別<span
                                                class="text-danger">*</span></label>
                                        <select class="form-control" data-toggle="select" data-width="100%" name="leave_day"
                                            disabled>
                                            @foreach ($leaves as $leave)
                                                <option value="{{ $leave->id }}"
                                                    @if ($data->leave_day == $leave->id) selected @endif>{{ $leave->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="mb-3">
                                        <label class="form-label">請假起始時間<span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="start_date" placeholder="起始日期"
                                            value="{{ date('Y-m-d', strtotime($data->start_datetime)) }}" readonly>
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="mb-3">
                                        <label class="form-label">&nbsp;<span class="text-danger"></span></label>
                                        <input type="text" class="form-control" name="start_time"
                                            value="{{ date('H:i', strtotime($data->start_datetime)) }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="mb-3">
                                        <div class="mb-3">
                                            <label class="form-label">請假結束時間<span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" name="end_date" placeholder="結束時間"
                                                value="{{ date('Y-m-d', strtotime($data->end_datetime)) }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="mb-3">
                                        <div class="mb-3">
                                            <label class="form-label">&nbsp;<span class="text-danger"></span></label>
                                            <input type="text" class="form-control" name="end_time"
                                                value="{{ date('H:i', strtotime($data->end_datetime)) }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="mb-3">
                                        <label for="project-priority" class="form-label">請假單位<span
                                                class="text-danger">*</span></label>
                                        <select class="form-control" data-toggle="select" data-width="100%"
                                            name="unit" disabled>
                                            <option value="day" @if ($data->unit == 'day') selected @endif>天
                                            </option>
                                            <option value="hour" @if ($data->unit == 'hour') selected @endif>小時
                                            </option>
                                            <option value="week" @if ($data->unit == 'week') selected @endif>週
                                            </option>
                                            <option value="month" @if ($data->unit == 'month') selected @endif>月
                                            </option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <div class="mb-3">
                                            <label class="form-label">總請假數量<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="total"
                                                value="{{ $data->total }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                @if (isset($data->file))
                                    <div class="mb-3">
                                        <label class="form-label">檔案預覽：</label>
                                        <a href="{{ $data->file }}"
                                            id="filePreview" target="_blank">點我預覽</a>
                                    </div>
                                @endif
                                <div class="mb-3">
                                    <label class="form-label">備註</label>
                                    <textarea class="form-control" rows="3" placeholder="" name="comment" readonly>{{ $data->comment }}</textarea>
                                </div>
                            </div>
                        </form>
                    </div> <!-- end card-body -->
                </div> <!-- end card-->
                
                {{-- 送出審核按鈕 - 只在假單狀態為1（草稿）時顯示 --}}
                @if ($data->state == 1 && $data->user_id == Auth::id())
                    <div class="row mt-3">
                        <div class="col-12 text-center">
                            <div class="alert alert-info">
                                <strong>假單已建立完成</strong><br>
                                請點擊下方按鈕送出審核
                            </div>
                            <form action="{{ route('person.leave_day.check.data', $data->id) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-success waves-effect waves-light"
                                        onclick="return confirm('確定要送出此假單進行審核嗎？')">
                                    <i class="mdi mdi-check-circle me-1"></i>送出審核
                                </button>
                            </form>
                        </div>
                    </div>
                @elseif($data->state == 1)
                    <div class="row mt-3">
                        <div class="col-12 text-center">
                            <div class="alert alert-warning">
                                <strong>僅申請人本人可以送出此假單</strong>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- 審核按鈕表單 - 移到外層表單外面 --}}
                @if ($data->state == 2)
                    {{-- 待審核：檢查是否為當前審核人 --}}
                    @php
                        $pendingCheckForUser = $data->checks()
                            ->where('state', 2)
                            ->where('check_user_id', Auth::user()->id)
                            ->orderByDesc('created_at')
                            ->first();

                        $canApprove = (bool) $pendingCheckForUser;
                        $currentStep = null;
                        if ($canApprove) {
                            $currentStep = $pendingCheckForUser->step;
                            if (!$currentStep && $data->workflow && $data->workflow->steps) {
                                $currentStep = $data->workflow->steps->sortBy('step_order')->firstWhere('approver_user_id', Auth::user()->id);
                            }
                        }
                    @endphp
                    @if ($canApprove)
                        <div class="row mt-3">
                            <div class="col-12 text-center">
                                <div class="alert alert-info">
                                    <strong>您有權限審核此假單</strong>
                                    @if($currentStep)
                                        <br>當前關卡：第 {{ $currentStep->step_order }} 關
                                    @endif
                                </div>
                                {{-- 當前審核人可以審核 --}}
                                <form action="{{ route('leave_day.approve', $data->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <input type="hidden" name="check_id" value="{{ $pendingCheckForUser->id ?? '' }}">
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="btn btn-success waves-effect waves-light m-1"
                                            onclick="return confirm('確定要核准此假單嗎？')">
                                        <i class="fe-check-circle me-1"></i>核准
                                    </button>
                                </form>
                                <form action="{{ route('leave_day.approve', $data->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <input type="hidden" name="check_id" value="{{ $pendingCheckForUser->id ?? '' }}">
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" class="btn btn-danger waves-effect waves-light m-1"
                                            onclick="return confirm('確定要駁回此假單嗎？')">
                                        <i class="fe-x me-1"></i>駁回
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="row mt-3">
                            <div class="col-12 text-center">
                                <div class="alert alert-warning">
                                    <strong>您目前沒有權限審核此假單</strong>
                                    @if($data->workflow && $data->workflow->steps)
                                        <br>請等待其他審核人員處理
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            </div> <!-- end col-->
            @if ($data->state != 1 || false) {{-- $data->checks()->where('state', 3)->exists() --}}
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-body">
                            @if ($data->workflow)
                                <h5 class="text-uppercase bg-light  p-2 mt-0 mb-3">使用流程：{{ $data->workflow->name }}</h5>
                                <div class="alert alert-info mb-3">
                                    <strong>流程說明：</strong>{{ $data->workflow->description ?? '無' }}<br>
                                    <strong>適用職稱：</strong>
                                    @if($data->workflow->job)
                                        {{ $data->workflow->job->name }}
                                    @else
                                        全部職稱
                                    @endif
                        <br>
                        <strong>流程狀態：</strong>
                        @if($data->state == 1)
                            <span class="badge bg-secondary">草稿</span>
                        @elseif($data->state == 2)
                            <span class="badge bg-warning">待審核</span>
                        @elseif($data->state == 9)
                            <span class="badge bg-success">已核准</span>
                        @elseif($data->state == 3)
                            <span class="badge bg-danger">已駁回</span>
                        @endif
                    </div>
                @else
                    <div class="alert alert-warning mb-3">
                        <strong>注意：</strong>此假單尚未關聯到審核流程
                                </div>
                            @endif
                            
                            {{-- 審核流程總表 --}}
                            @if ($data->workflow && $data->workflow->steps)
                            <h5 class="text-uppercase bg-light  p-2 mt-0 mb-3">審核流程總表</h5>
                            <div class="table-responsive mb-3">
                                <table class="table table-bordered mb-0">
                                    <thead>
                                        <tr align="center">
                                            <th>步驟</th>
                                            <th>審核人員</th>
                                            <th>狀態</th>
                                            <th>審核日期</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                            {{-- 顯示所有記錄 - 按時間排序 --}}
                                            @php
                                                // 取得所有記錄
                                                $allChecks = $data->checks()->orderBy('created_at')->get();
                                                $rejectedChecks = $allChecks->where('state', 3);
                                                $approvedChecks = $allChecks->where('state', 9);
                                                $pendingChecks = $allChecks->where('state', 2);
                                                $draftChecks = $allChecks->where('state', 1);
                                                $submitChecks = $allChecks->where('state', 10);
                                                
                                                // 計算已完成的步驟數量（包括已核准和已駁回）
                                                $completedSteps = $approvedChecks->count() + $rejectedChecks->count();
                                                
                                                // 建立所有記錄的統一列表
                                                $allRecords = collect();
                                                
                                                // 處理申請人記錄（編輯假單和送出審核）
                                                $draftIndex = 1;
                                                $submitIndex = 1;
                                                
                                                foreach($allChecks as $check) {
                                                    $stepNumber = '步驟 -';
                                                    if (!empty($check->step_id) && $data->workflow) {
                                                        $step = $data->workflow->steps->firstWhere('id', $check->step_id);
                                                        if ($step) {
                                                            $stepNumber = '步驟 ' . $step->step_order;
                                                        }
                                                    } elseif (empty($check->step_id) && $data->workflow) {
                                                        $step = $data->workflow->steps->firstWhere('approver_user_id', $check->check_user_id);
                                                        if ($step) {
                                                            $stepNumber = '步驟 ' . $step->step_order;
                                                        }
                                                    }

                                                    $badgeClass = 'bg-secondary';
                                                    $rowClass = 'table-secondary';
                                                    if ($check->state == 10) {
                                                        $badgeClass = 'bg-info';
                                                        $rowClass = 'table-info';
                                                    } elseif ($check->state == 9) {
                                                        $badgeClass = 'bg-success';
                                                        $rowClass = 'table-success';
                                                    } elseif ($check->state == 3) {
                                                        $badgeClass = 'bg-danger';
                                                        $rowClass = 'table-danger';
                                                    } elseif ($check->state == 2) {
                                                        $badgeClass = 'bg-warning';
                                                        $rowClass = 'table-warning';
                                                    }

                                                    $userName = $check->user_name->name
                                                        ?? (($check->check_user_id ? (\App\Models\User::find($check->check_user_id)->name ?? null) : null)
                                                        ?? ($data->user_name->name ?? '未知'));

                                                    $allRecords->push([
                                                        'check' => $check,
                                                        'stepText' => $stepNumber,
                                                        'stepNumber' => $stepNumber,
                                                        'userName' => $userName,
                                                        'statusText' => $check->leave_check_status(),
                                                        'badgeClass' => $badgeClass,
                                                        'rowClass' => $rowClass,
                                                        'hasDate' => in_array($check->state, [9, 3])
                                                    ]);
                                                }
                                                
                                                // 按審核時間排序
                                                $allRecords = $allRecords->sortBy(function($record) {
                                                    return $record['check']->updated_at ?? $record['check']->created_at;
                                                });
                                            @endphp
                                            
                                            @foreach($allRecords as $record)
                                                @php
                                                    $recordTimestamp = $record['check']->updated_at ?? $record['check']->created_at;
                                                @endphp
                                                <tr align="center" class="{{ $record['rowClass'] }}">
                                                    <td>{{ $record['stepNumber'] }}</td>
                                                    <td>{{ $record['userName'] }}</td>
                                                    <td><span class="badge {{ $record['badgeClass'] }}">{{ $record['statusText'] }}</span></td>
                                                    <td>{{ $recordTimestamp ? $recordTimestamp->format('Y-m-d H:i') : '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <h5 class="text-uppercase bg-light  p-2 mt-0 mb-3">審核記錄</h5>
                                <div class="table-responsive mb-3">
                                    <table class="table table-bordered mb-0">
                                        <thead>
                                            <tr align="center">
                                                <th>審核人員</th>
                                                <th>狀態</th>
                                                <th>審核日期</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($items->sortBy('created_at') as $item)
                                                <tr align="center" class="{{ $item->state == 9 ? 'table-success' : ($item->state == 3 ? 'table-danger' : 'table-warning') }}">
                                                <td>{{ $item->user_name->name }}</td>
                                                <td>
                                                        @if ($item->state == 9)
                                                            <span class="badge bg-success">已核准</span>
                                                        @elseif ($item->state == 3)
                                                            <span class="badge bg-danger">已駁回</span>
                                                    @elseif ($item->state == 2)
                                                        <span class="badge bg-warning">待審核</span>
                                                        @else
                                                            <span class="badge bg-secondary">未知狀態</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($item->state == 9 || $item->state == 3)
                                                            {{ $item->check_day ? date('Y-m-d H:i', strtotime($item->check_day)) : date('Y-m-d H:i', strtotime($item->updated_at ?? $item->created_at)) }}
                                                        @else
                                                            -
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @endif
                            
                            {{-- 尚未審核的人員 --}}
                            @if ($data->workflow && $data->workflow->steps)
                                @php
                                    $pendingSteps = collect();
                                    
                                    // 取得所有審核記錄，按時間排序
                                    $allChecks = $data->checks()->orderBy('created_at')->get();
                                    
                                    // 檢查是否有駁回記錄，如果有，則需要找到最後一次重新送出的時間點與關卡
                                    $rejectedChecks = $allChecks->where('state', 3)->sortByDesc('created_at');
                                    $lastRejectedCheck = $rejectedChecks->first();
                                    $lastRejectionTime = $lastRejectedCheck ? \Carbon\Carbon::parse($lastRejectedCheck->created_at) : null;
                                    
                                    $workflowSteps = $data->workflow->steps->sortBy('step_order');
                                    $lastRejectedStepOrder = null;
                                    if ($lastRejectedCheck) {
                                        $rejectedStep = $workflowSteps->firstWhere('id', $lastRejectedCheck->step_id);
                                        if (!$rejectedStep) {
                                            $rejectedStep = $workflowSteps->firstWhere('approver_user_id', $lastRejectedCheck->check_user_id);
                                        }
                                        $lastRejectedStepOrder = optional($rejectedStep)->step_order;
                                    }
                                    
                                    $approvedChecks = $allChecks->where('state', 9)->sortBy('created_at');
                                    
                                    $completedSteps = 0;
                                    foreach ($workflowSteps as $step) {
                                        $isCompleted = false;
                                        
                                        if ($lastRejectedStepOrder && $step->step_order < $lastRejectedStepOrder) {
                                            // 駁回之前的關卡視為已完成
                                            $isCompleted = true;
                                        } else {
                                            $stepApprovals = $approvedChecks->filter(function ($approval) use ($step, $lastRejectionTime) {
                                                if ($lastRejectionTime && \Carbon\Carbon::parse($approval->created_at)->lte($lastRejectionTime)) {
                                                    return false;
                                                }
                                                if (!empty($approval->step_id)) {
                                                    return $approval->step_id == $step->id;
                                                }
                                                return $approval->check_user_id == $step->approver_user_id;
                                            });
                                            
                                            if ($stepApprovals->isNotEmpty()) {
                                                $isCompleted = true;
                                            }
                                        }
                                        
                                        if ($isCompleted) {
                                            $completedSteps++;
                                        } else {
                                            break;
                                        }
                                    }
                                    
                                    // 從下一個待審核關卡開始顯示
                                    $remainingSteps = $workflowSteps->skip($completedSteps);
                                    
                                    foreach ($remainingSteps as $step) {
                                        $pendingSteps->push([
                                            'step_number' => $step->step_order,
                                            'approver' => $step->approver->name ?? '未設定'
                                        ]);
                                    }
                                @endphp
                                
                                @if ($pendingSteps->count() > 0)
                                    <h5 class="text-uppercase bg-light  p-2 mt-0 mb-3">尚未審核的人員</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                                <tr align="center">
                                                    <th>步驟</th>
                                                    <th>審核人員</th>
                                                    <th>狀態</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($pendingSteps as $step)
                                                    <tr align="center">
                                                        <td>{{ $step['step_number'] }}</td>
                                                        <td>{{ $step['approver'] }}</td>
                                                        <td>
                                                            <span class="badge bg-light text-dark">等待中</span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            @endif
                        </div> <!-- end card-body -->
                    </div> <!-- end card-->
                </div> <!-- end col-->
            @endif
        </div>
        <!-- end row-->

        <div class="row mt-3">
            <div class="col-12 text-center">
                <button type="button" class="btn btn-secondary waves-effect waves-light m-1" onclick="history.back()">回上一頁</button>
            </div>
        </div>

    </div> <!-- container -->
@endsection

@section('script')
    <!-- third party js -->
    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('assets/libs/spectrum-colorpicker2/spectrum-colorpicker2.min.js') }}"></script>
    <script src="{{ asset('assets/libs/clockpicker/clockpicker.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <!-- third party js ends -->

    <!-- demo app -->
    <script src="{{ asset('assets/js/pages/form-pickers.init.js') }}"></script>
    <!-- end demo js-->
@endsection
