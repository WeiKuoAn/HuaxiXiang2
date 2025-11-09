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
                        <form action="{{ route('person.leave_day.check.data', $data->id) }}" method="POST">
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
                                        <select class="form-control" data-toggle="select" data-width="100%"
                                            name="leave_day" disabled>
                                            @foreach ($leaves as $leave)
                                                <option value="{{ $leave->id }}" @if($data->leave_day == $leave->id) selected @endif>{{ $leave->name }}</option>
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
                                        <div class="input-group clockpicker" data-placement="top" data-align="top"
                                            data-autoclose="true">
                                            <input type="text" class="form-control" name="start_time"
                                                value="{{ date('H:i', strtotime($data->start_datetime)) }}" readonly>
                                            <span class="input-group-text"><i class="mdi mdi-clock-outline"></i></span>
                                        </div>
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
                                            <div class="input-group clockpicker" data-placement="top" data-align="top"
                                                data-autoclose="true">
                                                <input type="text" class="form-control" name="end_time"
                                                    value="{{ date('H:i', strtotime($data->end_datetime)) }}" readonly>
                                                <span class="input-group-text"><i
                                                        class="mdi mdi-clock-outline"></i></span>
                                            </div>
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
                                        <a href="{{ $data->file }}" id="filePreview" target="_blank">點我預覽</a>
                                    </div>
                                @endif
                                <div class="mb-3">
                                    <label class="form-label">備註</label>
                                    <textarea class="form-control" rows="3" placeholder="" name="comm" readonly>{{ $data->comment }}</textarea>
                                </div>
                                
                                @if ($data->state == 1)
                                    {{-- 未送出狀態：隱藏工作流程選擇，由控制器自動設定 --}}
                                    <input type="hidden" name="workflow_id" value="">
                                @endif
                            </div>
                            <div class="row mt-3">
                                <div class="col-12 text-center">
                                    @if ($data->state == 1)
                                        {{-- 未審核 --}}
                                        <button type="submit" class="btn btn-success waves-effect waves-light m-1"
                                            id="btn_submit"
                                            onclick="return confirmSubmit()"><i
                                                class="fe-check-circle me-1"></i>送出審核</button>
                                        <button type="reset" class="btn btn-secondary waves-effect waves-light m-1"
                                            onclick="history.go(-1)"><i class="fe-x me-1"></i>回上一頁</button>
                                    @else
                                        <button type="reset" class="btn btn-secondary waves-effect waves-light m-1"
                                            onclick="history.go(-1)"><i class="fe-x me-1"></i>回上一頁</button>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div> <!-- end card-body -->
                </div> <!-- end card-->
            </div> <!-- end col-->
            @if ($data->state != 1 || $data->checks()->where('state', 3)->exists())
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
                                                    if ($check->state == 1) { // 編輯假單
                                                        $isEdit = false;
                                                        if ($rejectedChecks->count() > 0) {
                                                            $firstRejectionTime = $rejectedChecks->first()->created_at;
                                                            $isEdit = $check->created_at > $firstRejectionTime;
                                                        }
                                                        
                                                        $allRecords->push([
                                                            'check' => $check,
                                                            'stepText' => $isEdit ? '編輯' : '申請',
                                                            'stepNumber' => ($isEdit ? '編輯' : '申請') . ($draftChecks->count() > 1 ? ' ' . $draftIndex : ''),
                                                            'userName' => $data->user_name->name ?? '未知',
                                                            'statusText' => $isEdit ? '編輯假單' : '新增假單',
                                                            'badgeClass' => $isEdit ? 'bg-warning' : 'bg-secondary',
                                                            'rowClass' => 'table-secondary',
                                                            'hasDate' => true
                                                        ]);
                                                        $draftIndex++;
                                                    } elseif ($check->state == 10) { // 送出審核
                                                        $allRecords->push([
                                                            'check' => $check,
                                                            'stepText' => '送出',
                                                            'stepNumber' => '送出' . ($submitChecks->count() > 1 ? ' ' . $submitIndex : ''),
                                                            'userName' => $data->user_name->name ?? '未知',
                                                            'statusText' => '送出審核',
                                                            'badgeClass' => 'bg-info',
                                                            'rowClass' => 'table-info',
                                                            'hasDate' => true
                                                        ]);
                                                        $submitIndex++;
                                                    } elseif (in_array($check->state, [2, 3, 9])) { // 審核記錄
                                                        // 找到對應的審核人
                                                        $approver = \App\Models\User::find($check->check_user_id);
                                                        $approverName = $approver ? $approver->name : '未知';
                                                        
                                                        // 判斷狀態
                                                        if ($check->state == 9) {
                                                            $statusText = '已核准';
                                                            $badgeClass = 'bg-success';
                                                            $rowClass = 'table-success';
                                                        } elseif ($check->state == 3) {
                                                            $statusText = '已駁回';
                                                            $badgeClass = 'bg-danger';
                                                            $rowClass = 'table-danger';
                                                        } else { // state == 2
                                                            $statusText = '待審核';
                                                            $badgeClass = 'bg-warning';
                                                            $rowClass = 'table-warning';
                                                        }
                                                        
                                                        $allRecords->push([
                                                            'check' => $check,
                                                            'stepText' => '審核',
                                                            'stepNumber' => '審核',
                                                            'userName' => $approverName,
                                                            'statusText' => $statusText,
                                                            'badgeClass' => $badgeClass,
                                                            'rowClass' => $rowClass,
                                                            'hasDate' => in_array($check->state, [9, 3])
                                                        ]);
                                                    }
                                                }
                                                
                                                // 按時間排序
                                                $allRecords = $allRecords->sortBy('check.created_at');
                                            @endphp
                                            
                                            @foreach($allRecords as $record)
                                                <tr align="center" class="{{ $record['rowClass'] }}">
                                                    <td>{{ $record['stepNumber'] }}</td>
                                                    <td>{{ $record['userName'] }}</td>
                                                    <td><span class="badge {{ $record['badgeClass'] }}">{{ $record['statusText'] }}</span></td>
                                                    <td>
                                                        @if ($record['hasDate'] && $record['check']->created_at)
                                                            {{ date('Y-m-d H:i', strtotime($record['check']->created_at)) }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
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
                                                            {{ $item->check_day ? date('Y-m-d H:i', strtotime($item->check_day)) : date('Y-m-d H:i', strtotime($item->created_at)) }}
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
                                    foreach ($data->workflow->steps->sortBy('step_order') as $index => $step) {
                                        $stepCheck = $data->checks()->where('check_user_id', $step->approver_user_id)->first();
                                        if (!$stepCheck) {
                                            $pendingSteps->push([
                                                'step_number' => $index + 1,
                                                'approver' => $step->approver->name ?? '未設定'
                                            ]);
                                        }
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
    
    <script>
    function confirmSubmit() {
        // 確認送出
        if (!confirm('是否確定送出審核？送出後將無法修改。')) {
            return false;
        }
        
        return true;
    }
    </script>
@endsection
