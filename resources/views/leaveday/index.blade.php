@extends('layouts.vertical', ["page_title"=> "部門請假核准"])

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
                            <form class="d-flex flex-wrap align-items-center" action="{{ route('personnel.leave_days') }}" method="GET">
                                <div class="me-3">
                                    <label for="start_date_start" class="form-label">請假起始日期</label>
                                    <input type="date" class="form-control" id="start_date_start" name="start_date_start" value="{{ $request->start_date_start }}">
                                </div>
                                <div class="me-3">
                                    <label for="start_date" class="form-label">&nbsp;</label>
                                    <input type="date" class="form-control" id="start_date_end" name="start_date_end" value="{{ $request->start_date_end }}">
                                </div>
                                <div class="me-3">
                                    <label for="end_date_start" class="form-label">請假結束日期</label>
                                    <input type="date" class="form-control" id="end_date_start" name="end_date_start" value="{{ $request->end_date_start }}">
                                </div>
                                <div class="me-3">
                                    <label for="end_date_end" class="form-label">&nbsp;</label>
                                    <input type="date" class="form-control" id="end_date_end" name="end_date_end" value="{{ $request->end_date_end }}">
                                </div>
                                <div class="me-sm-3">
                                    <label class="form-label">假別</label>
                                    <select class="form-select my-1 my-lg-0" id="status-select" name="leave_day" onchange="this.form.submit()">
                                        <option value="null" selected>請選擇...</option>
                                        @foreach($leaves as $leave)
                                            <option value="{{ $leave->id }}" @if($request->leave_day ==  $leave->id) selected @endif>{{ $leave->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="me-sm-3">
                                    <label class="form-label">假別</label>
                                    <select class="form-select my-1 my-lg-0" id="status-select" name="state" onchange="this.form.submit()">
                                        <option value="2" @if(!isset($request->state) || $request->state == '2') selected @endif>待審核</option>
                                        <option value="9" @if($request->state == '9') selected @endif>已核准</option>
                                    </select>
                                </div>
                                <div class="me-3 mt-4">
                                    <button type="submit" class="btn btn-success waves-effect waves-light me-1"><i class="fe-search me-1"></i>搜尋</button>
                                </div>
                                <div class="col-auto text-sm-end mt-4">
                                    <a href="{{ route('leave_day.create') }}">
                                        <button type="button" class="btn btn-danger waves-effect waves-light"><i class="mdi mdi-plus-circle me-1"></i>新增專員假單</button>
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
                        <table class="table table-centered table-nowrap table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>編號</th>
                                    <th>申請人</th>
                                    <th>申請時間</th>
                                    <th>假別</th>
                                    <th width="12%">請假開始時間</th>
                                    <th width="12%">請假結束時間</th>
                                    <th>總時數</th>
                                    <th>備註</th>
                                    <th>狀態</th>
                                    <th>審核</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($datas as $key=>$data)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ $data->user_name->name }}</td>
                                    <td>{{ date('Y-m-d', strtotime($data->created_at)) }}</td>
                                    <td>{{ $data->leave_name->name }}</td>
                                    <td>{{ $data->start_datetime }}</td>
                                    <td>{{ $data->end_datetime }}</td>
                                    <td>
                                        {{ $data->total }}
                                        @if($data->unit == 'hour')小時
                                        @else 天
                                        @endif
                                    </td>
                                    <td>{{ $data->comment }}</td>
                                    <td>{{ $data->leave_status() }}</td>
                                    <td>
                                        @if($data->state == '2')
                                            <a href="{{ route('leave_day.check',$data->id) }}"><button type="button" class="btn btn-secondary waves-effect waves-light">審核</button></a>
                                        @elseif($data->state == '9')
                                            <a href="{{ route('leave_day.check',$data->id) }}"><button type="button" class="btn btn-secondary waves-effect waves-light">查看</button></a>
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