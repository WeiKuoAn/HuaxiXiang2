@extends('layouts.vertical', ['page_title' => 'Dashboard', 'mode' => $mode ?? '', 'demo' => $demo ?? ''])

@section('css')
    <!-- third party css -->
    <link href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/selectize/selectize.min.css') }}" rel="stylesheet" type="text/css" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
                                    <th>編號</th>
                                    <th>待辦事項</th>
                                    <th>預計結束日期</th>
                                    <th>待辦事項說明</th>
                                    <th>狀態</th>
                                    <th>動作</th>
                                </tr>
                            </thead>
                            <tbody id="tasksTableBody">
                                @forelse($tasks as $key => $task)
                                    <tr align="center">
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $task->title }}</td>
                                        <td>{{ optional($task->end_date)->format('Y-m-d H:i') }}</td>
                                        <td>{{ $task->note }}</td>
                                        <td>
                                            @if ($task->status == 1)
                                                已完成
                                            @else
                                                <b style="color:red;">未完成</b>
                                            @endif
                                        </td>
                                        <td>
                                            <button
                                                class="btn-sm btn btn-danger waves-effect waves-light mt-1 btn-complete"
                                                data-id="{{ $task->id }}">
                                                確認完成
                                            </button>
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
                                        <td>第{{ $contract_data->year }}年</td>
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

    {{-- 放在最下面 --}}
    <div class="modal fade" id="taskModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">新增待辦</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="taskForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label>待辦事項<span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            <input type="hidden" name="start_date" class="form-control" value="{{ date('Y-m-d') }}"
                                required>
                            <input type="hidden" name="start_time" class="form-control" value="09:00" required>
                            <div class="col-md-12 mb-3">
                                <label>預計結束日期<span class="text-danger">*</span></label>
                                <div class="row g-2">
                                    <div class="col-md">
                                        <input type="date" name="end_date" class="form-control" required>
                                    </div>
                                    <div class="col-md">
                                        <input type="time" name="end_time" class="form-control" value="18:00">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <label>待辦事項說明</label>
                                <textarea name="description" class="form-control" rows="4"></textarea>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label>備註</label>
                                <input type="text" name="note" class="form-control">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label>狀態</label>
                                <select name="status" class="form-select">
                                    <option value="0">待辦</option>
                                    <option value="1">已完成</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div><!-- /.modal-body -->
                <div class="modal-footer">
                    <button type="submit" form="taskForm" class="btn btn-success waves-effect waves-light">
                        <i class="fe-check-circle me-1"></i> 新增
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
            $('#taskForm').on('submit', function(e) {
                e.preventDefault();
                let $form = $(this);
                $.ajax({
                    url: "{{ route('task.ajax.create.data') }}",
                    type: 'POST',
                    data: $form.serialize(),
                    success: function(res) {
                        let t = res.task;

                        // 新列的 index
                        let idx = $('#tasksTableBody tr').length + 1;

                        // ✅ 使用字串處理，避免 new Date() 時區錯誤
                        let end = '';
                        if (t.end_date && typeof t.end_date === 'string') {
                            end = t.end_date.substr(0, 16); // 取出 "YYYY-MM-DD HH:mm"
                        }

                        console.log(t);

                        let statusLabel = t.status == 1 ?
                            '已完成' :
                            '<b style="color:red;">未完成</b>';

                        let newRow = `
                    <tr align="center">
                      <td>${idx}</td>
                      <td>${t.title}</td>
                      <td>${end}</td>
                      <td>${t.note || ''}</td>
                      <td>${statusLabel}</td>
                      <td>
                        <button class="btn-sm btn btn-danger waves-effect waves-light mt-1 btn-complete"
                                data-id="${t.id}">
                          確認完成
                        </button>
                      </td>
                    </tr>`;

                        $('#tasksTableBody').append(newRow);
                        $('#taskModal').modal('hide');
                        $form[0].reset();
                    },
                    error: function(xhr) {
                        // 錯誤處理
                        let errs = xhr.responseJSON.errors || {};
                        $form.find('.is-invalid').removeClass('is-invalid');
                        $form.find('.invalid-feedback').remove();
                        $.each(errs, function(k, msgs) {
                            let $inp = $form.find(`[name="${k}"]`);
                            $inp.addClass('is-invalid');
                            $inp.after(
                                `<div class="invalid-feedback">${msgs[0]}</div>`);
                        });
                    }
                });
            });
        });


        $(document).on('click', '.btn-complete', function(e) {
            e.preventDefault();
            let $btn = $(this);
            let taskId = $btn.data('id');

            if (!confirm('確定這項待辦為「已完成」嗎？')) {
                return;
            }

            $.ajax({
                url: "{{ route('task.create.check') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    id: taskId
                },
                success: function(res) {
                    if (res.success) {
                        let $row = $btn.closest('tr');
                        // 把「狀態」那格換文字
                        $row.find('td').eq(4).html('已完成');
                        // 移除「確認完成」按鈕
                        $btn.remove();
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
