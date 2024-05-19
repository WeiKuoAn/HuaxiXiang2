@extends('layouts.vertical', ["page_title"=> "人事列表"])

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
                        <li class="breadcrumb-item active">人事列表</li>
                    </ol>
                </div>
                <h4 class="page-title">人事列表</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->


    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-sm-8">
                            {{-- <div class="mt-2 mt-sm-0">
                                <button type="button" class="btn btn-success mb-2 me-1"><i class="fe-search me-1"></i>搜尋</button>
                            </div> --}}
                        </div><!-- end col-->
                        {{-- <div class="col-sm-4 text-sm-end">
                            <a href="{{ route('user.create') }}">
                                <button type="button" class="btn btn-danger waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#custom-modal"><i class="mdi mdi-plus-circle me-1"></i>新增人事</button>
                            </a>
                        </div> --}}
                    </div>

                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>姓名</th>
                                    {{-- <th>職稱</th> --}}
                                    <th>入職時間</th>
                                    <th>電話</th>
                                    <th>年資</th>
                                    {{-- <th>餘額</th> --}}
                                    <th>特休天數</th>
                                    {{-- <th>剩餘休假天數（含特休）</th> --}}
                                    <th>動作</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td class="table-user"><img src="{{asset('assets/images/users/user-4.jpg')}}" alt="table-user" class="me-2 rounded-circle">{{ $user->name }}</td>
                                    {{-- <td>
                                        @if(isset($user->job_data))
                                        {{ $user->job_data->name }}
                                        @endif
                                    </td> --}}
                                    <td>{{ $user->entry_date }}</td>    
                                    <td>{{ $user->mobile }}</td>
                                        <td>{{ $datas[$user->id]['seniority'] }}年</td>
                                        {{-- <td>
                                            @if($datas[$user->id]['balance'] <= 0)
                                                <span style="color:red;">{{ number_format($datas[$user->id]['balance']) }}</span>
                                            @else
                                                {{ number_format($datas[$user->id]['balance']) }}
                                            @endif
                                            元
                                        </td> --}}
                                        <td>{{ $datas[$user->id]['specil_vacation'] }}天</td>
                                        {{-- <td>{{ $datas[$user->id]['remain_specil_vacation'] }}天</td> --}}
                                        <td>
                                            <div class="btn-group dropdown">
                                                <a href="javascript: void(0);" class="table-action-btn dropdown-toggle arrow-none btn btn-outline-secondary waves-effect" data-bs-toggle="dropdown" aria-expanded="false">動作 <i class="mdi mdi-arrow-down-drop-circle"></i></a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a class="dropdown-item" href="{{ route('user.edit',$user->id) }}"><i class="mdi mdi-clipboard-text-search me-2 font-18 text-muted vertical-middle"></i>查看個資</a>
                                                    <a class="dropdown-item" href="{{ route('user.work.index',$user->id) }}"><i class="mdi mdi-clock me-2 font-18 text-muted vertical-middle"></i>出勤紀錄</a>
                                                    <a class="dropdown-item" href="{{ route('user.leave_day',$user->id) }}"><i class="mdi mdi-clipboard-text-search me-2 font-18 text-muted vertical-middle"></i>查看假單</a>
                                                    <a class="dropdown-item" href="{{ route('user.pay',$user->id) }}"><i class="mdi mdi-clipboard-text-search me-2 font-18 text-muted vertical-middle"></i>查看收支紀錄</a>
                                                </div>
                                            </div>
                                        </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{ $users->links() }}

                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col -->
    </div>
    <!-- end row -->

</div> <!-- container -->
@endsection