@extends('layouts.vertical', ['page_title' => '部門請假核准'])

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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">人事管理</a></li>
                            <li class="breadcrumb-item active">部門請假核准</li>
                        </ol>
                    </div>
                    <h4 class="page-title">部門請假核准</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row justify-content-between">
                            <div class="col-auto">
                                <form class="d-flex flex-wrap align-items-center"
                                    action="{{ route('personnel.leave_days') }}" method="GET">
                                    <div class="me-3">
                                        <label for="start_date_start" class="form-label">請假起始日期</label>
                                        <input type="date" class="form-control" id="start_date_start"
                                            name="start_date_start" value="{{ $request->start_date_start }}">
                                    </div>
                                    <div class="me-3">
                                        <label for="start_date" class="form-label">&nbsp;</label>
                                        <input type="date" class="form-control" id="start_date_end" name="start_date_end"
                                            value="{{ $request->start_date_end }}">
                                    </div>
                                    <div class="me-3">
                                        <label for="end_date_start" class="form-label">請假結束日期</label>
                                        <input type="date" class="form-control" id="end_date_start" name="end_date_start"
                                            value="{{ $request->end_date_start }}">
                                    </div>
                                    <div class="me-3">
                                        <label for="end_date_end" class="form-label">&nbsp;</label>
                                        <input type="date" class="form-control" id="end_date_end" name="end_date_end"
                                            value="{{ $request->end_date_end }}">
                                    </div>
                                    <div class="me-sm-3">
                                        <label class="form-label">假別</label>
                                        <select class="form-select my-1 my-lg-0" id="status-select" name="leave_day"
                                            onchange="this.form.submit()">
                                            <option value="null" selected>請選擇...</option>
                                            @foreach ($leaves as $leave)
                                                <option value="{{ $leave->id }}"
                                                    @if ($request->leave_day == $leave->id) selected @endif>{{ $leave->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="me-sm-3">
                                        <label class="form-label">狀態</label>
                                        <select class="form-select my-1 my-lg-0" id="status-select" name="state"
                                            onchange="this.form.submit()">
                                            <option value="2" @if (!isset($request->state) || $request->state == '2') selected @endif>待審核
                                            </option>
                                            <option value="9" @if ($request->state == '9') selected @endif>已核准
                                            </option>
                                            <option value="3" @if ($request->state == '3') selected @endif>已駁回
                                            </option>
                                        </select>
                                    </div>
                                    <div class="me-3 mt-4">
                                        <button type="submit" class="btn btn-success waves-effect waves-light me-1"><i
                                                class="fe-search me-1"></i>搜尋</button>
                                    </div>
                                    <div class="col-auto text-sm-end mt-4">
                                        <a href="{{ route('leave_day.create') }}">
                                            <button type="button" class="btn btn-danger waves-effect waves-light"><i
                                                    class="mdi mdi-plus-circle me-1"></i>新增專員假單</button>
                                        </a>
                                    </div>
                                </form>

                            </div>
                        </div> <!-- end row -->
                    </div>
                </div> <!-- end card -->
            </div> <!-- end col-->
        </div>


        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-centered  table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">編號</th>
                                        <th width="8%">申請人</th>
                                        @if ($request->state == '9')
                                            <th width="8%">審核時間</th>
                                        @else
                                            <th width="8%">申請時間</th>
                                        @endif
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
                                <tbody>
                                    @foreach ($datas as $key => $data)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $data->user_name->name }}</td>
                                            @if ($request->state == '9')
                                                <td>
                                                    @if ($data->leave_check)
                                                        {{ $data->leave_check->check_day }}
                                                    @endif
                                                </td>
                                            @else
                                                <td>{{ date('Y-m-d', strtotime($data->created_at)) }}</td>
                                            @endif
                                            <td>{{ $data->leave_name->name }}</td>
                                            <td>{{ $data->start_datetime }}</td>
                                            <td>{{ $data->end_datetime }}</td>
                                            <td>
                                                {{ $data->total }}
                                                @if ($data->unit == 'hour')
                                                    小時
                                                @elseif($data->unit == 'week')
                                                    週
                                                @elseif($data->unit == 'month')
                                                    月
                                                @else
                                                    天
                                                @endif
                                            </td>
                                            <td>{{ $data->comment }}</td>
                                            <td><a href="{{ $data->file }}" target="_blank" class="action-icon">
                                                    <i class="mdi mdi-file-document"></i>
                                                </a></td>
                                            <td>{{ $data->leave_status() }}</td>
                                            <td>
                                                @if ($data->state == '1')
                                                    {{-- 未送出狀態：顯示送出按鈕 --}}
                                                    <a href="{{ route('leave_day.select_workflow', $data->id) }}" class="btn btn-success waves-effect waves-light btn-sm">
                                                        送出
                                                    </a>
                                                    <a href="{{ route('leave_day.edit', $data->id) }}" class="btn btn-primary waves-effect waves-light btn-sm">編輯</a>
                                                @elseif ($data->state == '2')
                                                    {{-- 待審核狀態：檢查是否為當前審核人 --}}
                                                    @php
                                                        // $currentCheck = $data->checks()->where('state', 2)->first();
                                                        // $canApprove = $currentCheck && $currentCheck->check_user_id == Auth::user()->id;
                                                        $canApprove = false; // 暫時設為 false，等待 LeaveDay::checks() 方法修復
                                                    @endphp
                                                    @if ($canApprove)
                                                        <a href="{{ route('leave_day.check', $data->id) }}">
                                                            <button type="button" class="btn btn-warning waves-effect waves-light btn-sm">審核</button>
                                                        </a>
                                                    @else
                                                        <a href="{{ route('leave_day.check', $data->id) }}">
                                                            <button type="button" class="btn btn-warning waves-effect waves-light btn-sm">
                                                                {{-- {{ $currentCheck->user->name ?? '審核中' }} --}}
                                                                審核
                                                            </button>
                                                        </a>
                                                    @endif
                                                @elseif($data->state == '3')
                                                    {{-- 已駁回狀態 --}}
                                                    <span class="badge bg-danger">已駁回</span>
                                                @elseif($data->state == '9')
                                                    {{-- 已核准狀態 --}}
                                                    <a href="{{ route('leave_day.check', $data->id) }}">
                                                        <button type="button" class="btn btn-secondary waves-effect waves-light btn-sm">查看</button>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <br>
                            <ul class="pagination pagination-rounded justify-content-end mb-0">
                                {{ $datas->appends($condition)->links('vendor.pagination.bootstrap-4') }}
                            </ul>
                        </div>
                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col -->
        </div>
        <!-- end row -->

    </div> <!-- container -->
@endsection