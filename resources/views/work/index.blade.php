@extends('layouts.vertical', ["page_title"=> "CRM Customers"])

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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">用戶管理</a></li>
                        <li class="breadcrumb-item active">出勤列表</li>
                    </ol>
                </div>
                <h4 class="page-title">{{ $user->name }}出勤列表</h4>
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
                            <h4 class="page-title">{{ $user->name }}出勤列表</h4>
                        </div>
                        <div class="col-auto">
                            <div class="text-lg-end my-1 my-lg-0">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item"><a href="javascript: void(0);">Huaxixiang</a></li>
                                    <li class="breadcrumb-item"><a href="javascript: void(0);">用戶管理</a></li>
                                    <li class="breadcrumb-item active">出勤列表</li>
                                </ol>
                            </div>
                        </div><!-- end col-->
                    </div> <!-- end row -->
                </div>
            </div> <!-- end card -->
        </div> <!-- end col-->
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row justify-content-between">
                        <div class="col-auto">
                            <form class="d-flex flex-wrap align-items-center" action="{{ route('user.work.index',$user->id) }}" method="GET">
                                <div class="me-3">
                                    <input type="date" class="form-control my-1 my-lg-0" id="inputPassword2" name="startdate" value="{{$request->startdate}}" >
                                </div>
                                <div class="me-3">
                                    <input type="date" class="form-control my-1 my-lg-0" id="inputPassword2" name="enddate" value="{{$request->enddate}}" >
                                </div>
                                <div class="me-3">
                                    <button type="submit" class="btn btn-success waves-effect waves-light me-1"><i class="fe-search me-1"></i>搜尋</button>
                                </div>
                                <div class="me-3">
                                    <a href="{{ route('user.work.export', array_merge(['id' => $user->id], request()->input())) }}" class="btn btn-danger waves-effect waves-light">匯出</a>
                                </div>
                            </form>
                        </div>
                        <div class="col-auto">
                            <div class="text-lg-end my-1 my-lg-0">
                                <h3>總時數：<span class="text-danger">{{ $work_sum }}</span> 小時</h3>
                            </div>
                        </div><!-- end col-->
                    </div> <!-- end row -->
                </div>
            </div> <!-- end card -->
        </div> <!-- end col-->
    </div>
                    
                    <div class="row">
                        <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                        <div class="table-responsive ">
                            <table class="table table-centered table-nowrap table-hover mb-0 mt-2">
                                <thead class="table-light">
                                    <tr>
                                        <th>日期</th>
                                        <th>上班時間</th>
                                        <th>下班時間</th>
                                        <th>時間</th>
                                        <th>備註</th>
                                        <th>狀態</th>
                                        {{-- @if(Auth::user()->level == 0) --}}
                                        <th>操作</th>
                                        {{-- @endif --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($works as $work)
                                    <tr>
                                        <td>{{ date('Y-m-d', strtotime($work->worktime)) }}</td>
                                        <td>{{ date('H:i', strtotime($work->worktime)) }}</td>
                                        <td>
                                            @if ($work->dutytime != null)
                                                {{ date('H:i', strtotime($work->dutytime)) }}
                                            @else
                                                <b><span style="color: red;">尚未下班</span></b>
                                            @endif
                                        </td>
                                        <td>{{ $work->total }}</td>
                                        <td>{{ $work->remark }}</td>
                                        <td>
                                            @if ($work->status == '0')
                                                值班
                                            @else
                                                <b style="color:red;">補簽</b>
                                            @endif
                                        </td>
                                        {{-- @if(Auth::user()->level == 0) --}}
                                        <td>
                                            <a href="{{ route('user.work.edit',$work->id) }}" class="action-icon"> <i class="mdi mdi-square-edit-outline"></i></a>
                                            <a href="{{ route('user.work.del',$work->id) }}" class="action-icon"> <i class="mdi mdi-trash-can-outline"></i></a>
                                        </td>
                                        {{-- @endif --}}
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <br>
                            <ul class="pagination pagination-rounded justify-content-end mb-0">
                                {{ $works->appends($condition)->links('vendor.pagination.bootstrap-4') }}
                            </ul>
                        </div>
                    </div>
                </div>
                </div>
            </div>

                    

</div> <!-- container -->
@endsection