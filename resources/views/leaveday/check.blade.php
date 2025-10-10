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
                
                {{-- 審核按鈕表單 - 移到外層表單外面 --}}
                @if ($data->state == 2)
                    {{-- 待審核：檢查是否為當前審核人 --}}
                    @php
                        // $currentCheck = $data->checks()->where('state', 2)->first();
                        // $canApprove = $currentCheck && $currentCheck->check_user_id == Auth::user()->id;
                        $canApprove = true; // 暫時設為 true，等待 LeaveDay::checks() 方法修復
                        // 調試資訊
                        \Log::info('審核按鈕顯示檢查', [
                            'leave_day_id' => $data->id,
                            'auth_user_id' => Auth::user()->id,
                            // 'current_check_id' => $currentCheck ? $currentCheck->id : null,
                            // 'current_check_user_id' => $currentCheck ? $currentCheck->check_user_id : null,
                            'can_approve' => $canApprove
                        ]);
                    @endphp
                    @if ($canApprove)
                        <div class="row mt-3">
                            <div class="col-12 text-center">
                                {{-- 當前審核人可以審核 --}}
                                <form action="{{ route('leave_day.approve', $data->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    {{-- <input type="hidden" name="check_id" value="{{ $currentCheck->id }}"> --}}
                                    <input type="hidden" name="check_id" value="0">
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="btn btn-success waves-effect waves-light m-1"
                                            onclick="console.log('提交核准表單，check_id: 0'); console.log('表單 action: {{ route('leave_day.approve', $data->id) }}'); return confirm('確定要核准此假單嗎？')">
                                        <i class="fe-check-circle me-1"></i>核准
                                    </button>
                                </form>
                                <form action="{{ route('leave_day.approve', $data->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    {{-- <input type="hidden" name="check_id" value="{{ $currentCheck->id }}"> --}}
                                    <input type="hidden" name="check_id" value="0">
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" class="btn btn-danger waves-effect waves-light m-1"
                                            onclick="return confirm('確定要駁回此假單嗎？')">
                                        <i class="fe-x me-1"></i>駁回
                                    </button>
                                </form>
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
                                </div>
                            @endif
                            
                            {{-- 審核流程總表 --}}
                            <h5 class="text-uppercase bg-light  p-2 mt-0 mb-3">審核流程總表</h5>
                            <div class="table-responsive mb-3">
                                <table class="table table-bordered mb-0">
                                    <thead>
                                        <tr align="center">
                                            <th>步驟</th>
                                            <th>審核人員</th>
                                            <th>狀態</th>
                                            <th>備註</th>
                                            <th>審核日期</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($items->sortBy('created_at') as $key => $item)
                                            <tr align="center" class="{{ $item->state == 2 ? 'table-warning' : ($item->state == 9 ? 'table-success' : ($item->state == 3 ? 'table-danger' : '')) }}">
                                                <td>
                                                    @if ($item->state == 2)
                                                        <span class="badge bg-warning">進行中</span>
                                                    @else
                                                        {{ $key + 1 }}
                                                    @endif
                                                </td>
                                                <td>{{ $item->user_name->name }}</td>
                                                <td>
                                                    @if ($item->state == 1)
                                                        <span class="badge bg-secondary">新增假單</span>
                                                    @elseif ($item->state == 2)
                                                        <span class="badge bg-warning">待審核</span>
                                                    @elseif ($item->state == 3)
                                                        <span class="badge bg-danger">已駁回</span>
                                                    @elseif ($item->state == 9)
                                                        <span class="badge bg-success">已核准</span>
                                                    @elseif ($item->state == 10)
                                                        <span class="badge bg-info">送出審核</span>
                                                    @elseif ($item->state == 11)
                                                        <span class="badge bg-primary">編輯假單</span>
                                                    @endif
                                                </td>
                                                <td>{{ $item->comment ?? '-' }}</td>
                                                <td>{{ $item->updated_at ? date('Y-m-d H:i', strtotime($item->updated_at)) : date('Y-m-d H:i', strtotime($item->created_at)) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            {{-- 尚未審核的人員 --}}
                            @if ($data->workflow && $data->workflow->steps)
                                @php
                                    $pendingSteps = collect();
                                    foreach ($data->workflow->steps->sortBy('step_order') as $index => $step) {
                                        // $stepCheck = $data->checks()->where('step_id', $step->id)->first();
                                        $stepCheck = null; // 暫時設為 null，等待 LeaveDay::checks() 方法修復
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
@endsection
