@extends('layouts.vertical', ['page_title' => 'Dashboard', 'mode' => $mode ?? '', 'demo' => $demo ?? ''])

@section('css')
    <!-- third party css -->
    <link href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/selectize/selectize.min.css') }}" rel="stylesheet" type="text/css" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- third party css end -->

    <style>
        .selectize-control {
            min-height: 80px !important;
            height: auto !important;
        }

        .selectize-input {
            min-height: 80px !important;
            height: auto !important;
        }
    </style>
@endsection

@section('content')
    <!-- Start Content-->
    <div class="container-fluid">

        <!-- 顯示錯誤訊息 -->
        @if (session('error'))
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        @endif

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                    </div>
                    <h4 class="page-title">資訊總覽</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->


        @if (Auth::user()->job_id != 5)
            <div class="row">
                <div class="col-12">
                    <div class="widget-rounded-circle card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-auto">
                                    <h4 class="header-title mb-3">線上打卡</h4>
                                </div>
                                <form action="{{ route('index.worktime') }}" method="POST">
                                    <div class="alert alert-primary" role="alert">
                                        目前時間為 <b>{{ $now }}</b>
                                    </div>
                                    @csrf
                                    @if (!isset($work->worktime))
                                        <button type="Submit" class="btn btn-primary" name="work_time"
                                            value="0">上班</button>
                                        <button type="button" class="btn btn-success" name="overtime" value="1"
                                            id="overtime">補簽</button>
                                        <div id="overtimecontent">
                                            <br>
                                            <div class="mb-3">
                                                <label for="exampleFormControlTextarea1" class="form-label">上班時間</label>
                                                <input type="datetime-local" class="form-control" id="name"
                                                    name="worktime" value="">
                                            </div>
                                            <div class="mb-3">
                                                <label for="exampleFormControlTextarea1" class="form-label">下班時間</label>
                                                <input type="datetime-local" class="form-control" id="name"
                                                    name="dutytime" value="">
                                            </div>
                                            <div class="mb-3">
                                                <label for="exampleFormControlTextarea1" class="form-label">補簽原因</label>
                                                <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" name="remark"></textarea><br>
                                                <button type="Submit" class="btn btn-danger" name="overtime"
                                                    value="1">送出</button>
                                            </div>
                                        </div>
                                    @elseif($work->dutytime != null)
                                        <button type="Submit" class="btn btn-primary" name="work_time"
                                            value="0">上班</button>
                                        <button type="button" class="btn btn-success" value="1"
                                            id="overtime">補簽</button>
                                        <div id="overtimecontent">
                                            <br>
                                            <div class="mb-3">
                                                <label for="exampleFormControlTextarea1" class="form-label">上班時間</label>
                                                <input type="datetime-local" class="form-control" id="name"
                                                    name="worktime" value="">
                                            </div>
                                            <div class="mb-3">
                                                <label for="exampleFormControlTextarea1" class="form-label">下班時間</label>
                                                <input type="datetime-local" class="form-control" id="name"
                                                    name="dutytime" value="">
                                            </div>
                                            <div class="mb-3">
                                                <label for="exampleFormControlTextarea1" class="form-label">補簽原因</label>
                                                <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" name="remark"></textarea><br>
                                                <button type="Submit" class="btn btn-danger" name="overtime"
                                                    value="1">送出</button>
                                            </div>
                                        </div>
                                    @elseif($work->dutytime == null)
                                        <button type="Submit" class="btn btn-danger" name="dutytime"
                                            value="2">下班</button>
                                    @endif
                            </div>
                            </form>
                        </div> <!-- end row-->
                    </div>
                </div> <!-- end widget-rounded-circle-->
            </div> <!-- end col-->
    </div> <!-- container -->
    @endif

    <div class="row p-2">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="row justify-content-between">
                        <div class="col-auto">
                            <h4 class="header-title mb-3">待辦提醒</h4>
                        </div>
                        <div class="col-auto mb-2">
                            <div class="text-lg-end my-1 my-lg-0">
                                <a href="javascript:;" class="btn-sm btn-secondary waves-effect waves-light"
                                    data-bs-toggle="modal" data-bs-target="#taskModal">
                                    <i class="mdi mdi-plus-circle me-1"></i> 新增待辦
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-borderless table-hover table-nowrap table-centered m-0">

                            <thead class="table-light">
                                <tr align="center">
                                    <th>立案人</th>
                                    <th>待辦事項</th>
                                    <th>指派給</th>
                                    <th>預計結束日期</th>
                                    <th>待辦事項說明</th>
                                    <th>狀態</th>
                                    <th>動作</th>
                                </tr>
                            </thead>
                            <tbody id="tasksTableBody">
                                @forelse($tasks as $key => $task)
                                    <tr align="center" style="border-bottom: 1px solid #dee2e6;">
                                        <td>{{ $task->created_users->name ?? '' }}</td>
                                        <td>{{ $task->title }}</td>
                                        <td>
                                            @foreach ($task->items as $item)
                                                <div class="d-flex align-items-center mb-1">
                                                    <span class="me-2">
                                                        @if ($item->user_id == null)
                                                            不指定（大家都可以完成）
                                                        @else
                                                            {{ $item->user->name ?? '' }}
                                                        @endif
                                                    </span>
                                                    @if ($item->status == 1)
                                                        <span class="badge bg-success">已完成</span>
                                                    @else
                                                        <span class="badge bg-warning">未完成</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </td>
                                        <td>{{ optional($task->end_date)->format('Y-m-d') }}</td>
                                        <td style="white-space: pre-line;">{{ $task->description }}</td>
                                        <td>
                                            @php
                                                $completedCount = $task->items->where('status', 1)->count();
                                                $totalCount = $task->items->count();
                                            @endphp
                                            @if ($totalCount > 0)
                                                @if ($completedCount == $totalCount)
                                                    <span class="badge bg-success">全部完成</span>
                                                @else
                                                    <span
                                                        class="badge bg-warning">{{ $completedCount }}/{{ $totalCount }}</span>
                                                @endif
                                            @else
                                                <span class="badge bg-secondary">無指派</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $myItem = $task->items->where('user_id', Auth::id())->first();
                                                $hasUnassignedItem = $task->items->where('user_id', null)->first();
                                                $isCreator = Auth::user()->id == $task->created_by;
                                            @endphp
                                            @if ($isCreator)
                                                <button
                                                    class="btn-sm btn btn-primary waves-effect waves-light mt-1 btn-edit-task"
                                                    data-id="{{ $task->id }}">
                                                    <i class="mdi mdi-pencil me-1"></i>編輯
                                                </button>
                                            @endif
                                            @if ($myItem)
                                                @if ($myItem->status == 0)
                                                    <button
                                                        class="btn-sm btn btn-danger waves-effect waves-light mt-1 btn-complete"
                                                        data-id="{{ $myItem->id }}">
                                                        確認完成
                                                    </button>
                                                @else
                                                    <span class="badge bg-success">已完成</span>
                                                @endif
                                            @elseif($hasUnassignedItem && $hasUnassignedItem->status == 0)
                                                <button
                                                    class="btn-sm btn btn-danger waves-effect waves-light mt-1 btn-complete"
                                                    data-id="{{ $hasUnassignedItem->id }}">
                                                    確認完成
                                                </button>
                                            @elseif($hasUnassignedItem && $hasUnassignedItem->status == 1)
                                                <span class="badge bg-success">已完成</span>
                                            @elseif(!$isCreator)
                                                <span class="text-muted">非指派人員</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">沒有待辦事項</td>
                                    </tr>
                                @endforelse
                        </table>
                    </div>
                </div>
            </div>
        </div> <!-- end col -->
    </div>

    <div class="row p-2">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="row justify-content-between">
                        <div class="col-auto">
                            <h4 class="header-title mb-3">假單待審核</h4>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-borderless table-hover table-nowrap table-centered m-0">

                            <thead class="table-light">
                                <tr align="center">
                                    <th width="5%">編號</th>
                                    <th width="8%">申請人</th>
                                    <th width="8%">申請時間</th>
                                    <th width="8%">假別</th>
                                    <th width="15%">請假開始時間</th>
                                    <th width="15%">請假結束時間</th>
                                    <th width="8%">總時數</th>
                                    <th width="15%">備註</th>
                                    <th width="5%">附件</th>
                                    <th width="5%">狀態</th>
                                    <th width="8%">審核</th>
                                </tr>
                            </thead>
                            <tbody id="leavesTableBody">
                                @forelse($leaves_datas as $key => $leaves_data)
                                <tr align="center">
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $leaves_data->user_name->name }}</td>
                                    <td>{{ date('Y-m-d', strtotime($leaves_data->created_at)) }}</td>
                                    <td>{{ $leaves_data->leave_name->name }}</td>
                                    <td>{{ $leaves_data->start_datetime }}</td>
                                    <td>{{ $leaves_data->end_datetime }}</td>
                                    <td>
                                        {{ $leaves_data->total }}
                                        @if ($leaves_data->unit == 'hour')
                                            小時
                                        @elseif($leaves_data->unit == 'week')
                                            週
                                        @elseif($leaves_data->unit == 'month')
                                            月
                                        @else
                                            天
                                        @endif
                                    </td>
                                    <td>{{ $leaves_data->comment }}</td>
                                    <td><a href="{{ $leaves_data->file }}" target="_blank" class="action-icon">
                                            <i class="mdi mdi-file-document"></i>
                                        </a></td>
                                    <td>{{ $leaves_data->leave_status() }}</td>
                                    <td>
                                        @if ($leaves_data->state == '2')
                                            <a href="{{ route('leave_day.check', $leaves_data->id) }}"><button
                                                    type="button"
                                                    class="btn btn-secondary waves-effect waves-light">審核</button></a>
                                        @elseif($leaves_data->state == '9')
                                            <a href="{{ route('leave_day.check', $leaves_data->id) }}"><button
                                                    type="button"
                                                    class="btn btn-secondary waves-effect waves-light">查看</button></a>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center">尚無假單需要審核</td>
                                    </tr>
                                @endforelse
                        </table>
                    </div>
                </div>
            </div>
        </div> <!-- end col -->
    </div>

    <div class="row p-2">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">待提醒合約清單（兩個月內）</h4>

                    <div class="table-responsive">
                        <table class="table table-borderless table-hover table-nowrap table-centered m-0">

                            <thead class="table-light">
                                <tr>
                                    <th>編號</th>
                                    <th>合約類別</th>
                                    <th>顧客名稱</th>
                                    <th>電話</th>
                                    <th>寶貝名稱</th>
                                    <th>目前簽約年份</th>
                                    <th>開始日期</th>
                                    <th>結束日期</th>
                                    <th>金額</th>
                                    <th>續約</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($contract_datas as $key => $contract_data)
                                    <tr>
                                        <td>{{ $contract_data->number }}</td>
                                        <td>
                                            <span
                                                @if ($contract_data->type == '1') class=" bg-soft-success text-success p-1" 
                                        @elseif($contract_data->type == '2') class=" bg-soft-danger text-danger p-1"
                                        @elseif($contract_data->type == '4') class=" bg-soft-warning text-warning p-1"
                                        @else class=" bg-soft-blue text-blue p-1" @endif>
                                                @if (isset($contract_data->type_data))
                                                    {{ $contract_data->type_data->name }}
                                                @endif
                                            </span>
                                        </td>
                                        <td>{{ $contract_data->cust_name->name }}</td>
                                        <td>{{ $contract_data->mobile }}</td>
                                        <td>{{ $contract_data->pet_name }}</td>
                                        <td>
                                            @if ($contract_data->type == '4')
                                                {{ $contract_data->year }}天
                                            @else
                                                第{{ $contract_data->year }}年
                                            @endif
                                        </td>
                                        <td>{{ $contract_data->getRocStartDateAttribute() }}</td>
                                        <td>{{ $contract_data->getRocEndDateAttribute() }}</td>
                                        <td>{{ number_format($contract_data->price) }}</td>
                                        <td>
                                            @if ($contract_data->renew == '1')
                                                是（{{ $contract_data->renew_year }}年）
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> <!-- end col -->
    </div>

    @if (Auth::user()->job_id == 1 || Auth::user()->job_id == 2 || Auth::user()->job_id == 3 || Auth::user()->job_id == 7 || Auth::user()->job_id == 10)
    <div class="row p-2">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">待提醒平安燈清單（一個月內）</h4>

                    <div class="table-responsive">
                        <table class="table table-borderless table-hover table-nowrap table-centered m-0">

                            <thead class="table-light">
                                <tr>
                                    <th>編號</th>
                                    <th>合約類別</th>
                                    <th>顧客名稱</th>
                                    <th>電話</th>
                                    <th>寶貝名稱</th>
                                    <th>目前簽約年份</th>
                                    <th>開始日期</th>
                                    <th>結束日期</th>
                                    <th>金額</th>
                                    <th>續約</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($lamp_datas as $key => $lamp_data)
                                    <tr>
                                        <td>{{ $lamp_data->number }}</td>
                                        <td>
                                            <span
                                                @if ($lamp_data->type == '1') class=" bg-soft-success text-success p-1" 
                                        @elseif($lamp_data->type == '2') class=" bg-soft-danger text-danger p-1"
                                        @elseif($lamp_data->type == '4') class=" bg-soft-warning text-warning p-1"
                                        @else class=" bg-soft-blue text-blue p-1" @endif>
                                                @if (isset($lamp_data->type_data))
                                                    {{ $lamp_data->type_data->name }}
                                                @endif
                                            </span>
                                        </td>
                                        <td>{{ $lamp_data->cust_name->name }}</td>
                                        <td>{{ $lamp_data->mobile }}</td>
                                        <td>{{ $lamp_data->pet_name }}</td>
                                        <td>第{{ $lamp_data->year }}年</td>
                                        <td>{{ $lamp_data->getRocStartDateAttribute() }}</td>
                                        <td>{{ $lamp_data->getRocEndDateAttribute() }}</td>
                                        <td>{{ number_format($lamp_data->price) }}</td>
                                        <td>
                                            @if ($lamp_data->renew == '1')
                                                是（{{ $lamp_data->renew_year }}年）
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> <!-- end col -->
    </div>
    @endif
    
    {{-- 放在最下面 --}}
    <div class="modal fade" id="taskModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="taskModalTitle">新增待辦</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="taskForm">
                        @csrf
                        <input type="hidden" name="task_id" id="task_id" value="">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label>待辦事項<span class="text-danger">*</span></label>
                                <input type="text" name="title" id="task_title" class="form-control" required>
                            </div>
                            <input type="hidden" name="start_date" class="form-control" value="{{ date('Y-m-d') }}"
                                required>
                            <input type="hidden" name="start_time" class="form-control" value="09:00" required>
                            <div class="col-md-12 mb-3">
                                <label>預計結束日期<span class="text-danger">*</span></label>
                                <div class="row g-2">
                                    <div class="col-md">
                                        <input type="date" name="end_date" id="task_end_date" class="form-control" required>
                                    </div>
                                    <div class="col-md">
                                        <input type="time" name="end_time" id="task_end_time" class="form-control" value="18:00">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <label>待辦事項說明</label>
                                <textarea name="description" id="task_description" class="form-control" rows="4"></textarea>
                            </div>
                            <div class="col-md-12 mb-3 d-none">
                                <label>狀態</label>
                                <select name="status" class="form-select">
                                    <option value="0">待辦</option>
                                    <option value="1">已完成</option>
                                </select>
                            </div>
                            {{-- 指派給（可多選） --}}
                            <div class="col-md-12 mb-3">
                                <label>指派給</label>
                                <select name="assigned_to[]" id="assigned_to_select" class="form-select" multiple
                                    style="min-height: 150px; height: 150px;">
                                    <option value="0">不指定（大家都可以完成）</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>
                </div><!-- /.modal-body -->
                <div class="modal-footer">
                    <button type="submit" form="taskForm" class="btn btn-success waves-effect waves-light" id="taskSubmitBtn">
                        <i class="fe-check-circle me-1"></i> <span id="taskSubmitText">新增</span>
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fe-x me-1"></i> 取消
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- third party js -->
    <script src="{{ asset('assets/js/overtime.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/libs/selectize/selectize.min.js') }}"></script>
    <!-- third party js ends -->

    <!-- demo app -->
    <script src="{{ asset('assets/js/pages/dashboard-1.init.js') }}"></script>
    <!-- end demo js-->
    {{-- 在 Blade 最底部，確定已經載入 jQuery + Bootstrap.js --}}

    <script>
        $(function() {
            // 多選 select 初始化（Selectize）
            if ($('#assigned_to_select').length && typeof $.fn.selectize === 'function') {
                $('#assigned_to_select').selectize({
                    plugins: ['remove_button'],
                    create: false,
                    sortField: 'text',
                    placeholder: '可多選被指派者'
                });

                // 確保 Selectize 容器也有正確的高度
                setTimeout(function() {
                    $('#assigned_to_select').next('.selectize-control').css({
                        'min-height': '150px',
                        'height': 'auto'
                    });
                    $('.selectize-dropdown').css('max-height', '200px');
                }, 100);
            } else {
                // 後備方案：啟用原生多選提示
                $('#assigned_to_select').attr('multiple', 'multiple');
            }
            // 編輯按鈕點擊事件
            $(document).on('click', '.btn-edit-task', function(e) {
                e.preventDefault();
                let taskId = $(this).data('id');
                
                // 載入待辦事項資料
                $.ajax({
                    url: "{{ route('task.ajax.edit', ':id') }}".replace(':id', taskId),
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        let task = res.task;
                        
                        // 填充表單
                        $('#task_id').val(task.id);
                        $('#task_title').val(task.title);
                        $('#task_description').val(task.description || '');
                        $('#task_end_date').val(task.end_date || '');
                        $('#task_end_time').val(task.end_time || '18:00');
                        
                        // 設定指派人員
                        let selectize = $('#assigned_to_select')[0].selectize;
                        if (selectize) {
                            selectize.clear();
                            if (task.assigned_to && task.assigned_to.length > 0) {
                                selectize.setValue(task.assigned_to);
                            }
                        } else {
                            // 如果 Selectize 未初始化，使用原生方式
                            $('#assigned_to_select').val(task.assigned_to || []);
                        }
                        
                        // 更新 modal 標題和按鈕文字
                        $('#taskModalTitle').text('編輯待辦');
                        $('#taskSubmitText').text('更新');
                        
                        // 顯示 modal
                        $('#taskModal').modal('show');
                    },
                    error: function(xhr) {
                        if (xhr.status === 403) {
                            alert('無權限編輯此待辦事項');
                        } else {
                            alert('載入資料失敗，請稍後再試。');
                        }
                    }
                });
            });
            
            // 新增按鈕點擊時重置表單
            $('[data-bs-target="#taskModal"]').on('click', function() {
                $('#taskForm')[0].reset();
                $('#task_id').val('');
                $('#taskModalTitle').text('新增待辦');
                $('#taskSubmitText').text('新增');
                
                // 清除 Selectize 選擇
                let selectize = $('#assigned_to_select')[0].selectize;
                if (selectize) {
                    selectize.clear();
                } else {
                    $('#assigned_to_select').val([]);
                }
            });
            
            $('#taskForm').on('submit', function(e) {
                e.preventDefault();
                let $form = $(this);
                let taskId = $('#task_id').val();
                let url, method;
                
                if (taskId) {
                    // 編輯模式
                    url = "{{ route('task.ajax.edit.data', ':id') }}".replace(':id', taskId);
                    method = 'POST';
                } else {
                    // 新增模式
                    url = "{{ route('task.ajax.create.data') }}";
                    method = 'POST';
                }
                
                $.ajax({
                    url: url,
                    type: method,
                    data: $form.serialize(),
                    success: function(res) {
                        // 成功後，重新載入頁面
                        $('#taskModal').modal('hide');
                        $form[0].reset();
                        location.reload();
                    },
                    error: function(xhr) {
                        // 錯誤處理
                        let errs = xhr.responseJSON?.errors || {};
                        let errorMsg = xhr.responseJSON?.error || '操作失敗，請稍後再試。';
                        
                        if (Object.keys(errs).length > 0) {
                            $form.find('.is-invalid').removeClass('is-invalid');
                            $form.find('.invalid-feedback').remove();
                            $.each(errs, function(k, msgs) {
                                let $inp = $form.find(`[name="${k}"]`);
                                $inp.addClass('is-invalid');
                                $inp.after(
                                    `<div class="invalid-feedback">${msgs[0]}</div>`);
                            });
                        } else {
                            alert(errorMsg);
                        }
                    }
                });
            });
        });


        $(document).on('click', '.btn-complete', function(e) {
            e.preventDefault();
            let $btn = $(this);
            let taskItemId = $btn.data('id');

            if (!confirm('確定這項待辦為「已完成」嗎？')) {
                return;
            }

            // 使用 TaskItem 完成 API
            let apiUrl = "{{ route('task.item.complete') }}";
            let data = {
                id: taskItemId
            };

            $.ajax({
                url: apiUrl,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: data,
                success: function(res) {
                    if (res.success) {
                        // 重新載入頁面以更新所有狀態
                        location.reload();
                    } else {
                        alert('更新失敗，請稍後再試。');
                    }
                },
                error: function() {
                    alert('伺服器錯誤，無法完成操作。');
                }
            });
        });
    </script>
@endsection
