@extends('layouts.vertical', ["page_title"=> "例休假總覽"])

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
                        <li class="breadcrumb-item active">例休假總覽</li>
                    </ol>
                </div>
                <h4 class="page-title">例休假總覽</h4>
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
                            <form class="d-flex flex-wrap align-items-center" action="{{ route('personnel.holidays') }}" method="GET">
                                <div class="me-sm-3">
                                    <select class="form-select my-1 my-lg-0" id="status-select" name="year" onchange="this.form.submit()">
                                        @foreach($years as $year)
                                            <option value="{{ $year }}" @if($request->year == $year) selected @endif>{{ $year }}年</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="me-3">
                                    <button type="submit" class="btn btn-success waves-effect waves-light me-1"><i class="fe-search me-1"></i>搜尋</button>
                                </div>
                            </form>
                        </div>
                        <div class="col-auto">
                            <div class="text-lg-end my-1 my-lg-0">
                                {{-- <button type="button" class="btn btn-success waves-effect waves-light me-1"><i class="mdi mdi-cog"></i></button> --}}
                                <a href="{{ route('personnel.holidays.create') }}" class="btn btn-danger waves-effect waves-light"><i class="mdi mdi-plus-circle me-1"></i>新增專員每月休假</a>
                                <a href="{{ route('vacation.create') }}" style="margin-left: 20px;">
                                    <button type="button" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#custom-modal"><i class="mdi mdi-plus-circle me-1"></i>新增每月休假天數</button>
                                </a>
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
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap table-hover mb-0">
                            <thead class="table-light">
                                <tr align="center">
                                    <th>年份</th>
                                    <th>一月</th>
                                    <th>二月</th>
                                    <th>三月</th>
                                    <th>四月</th>
                                    <th>五月</th>
                                    <th>六月</th>
                                    <th>七月</th>
                                    <th>八月</th>
                                    <th>九月</th>
                                    <th>十月</th>
                                    <th>十一月</th>
                                    <th>十二月</th>
                                    <th>總休假天數</th>
                                    <th>動作</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($vdatas as $key=>$vdata)
                                <tr align="center">
                                    <td>{{ $vdata['year'] }}</td>
                                    @foreach($vdata['months'] as $month)
                                        <td>{{ $month->day }}天</td>
                                    @endforeach
                                    <td>{{ $vdata['total'] }}天</td>
                                    <td>
                                        <a href="{{ route('vacation.edit',$vdata['year']) }}" class="action-icon"> <i class="mdi mdi-square-edit-outline"></i></a>
                                        {{-- <a href="javascript:void(0);" class="action-icon"> <i class="mdi mdi-delete"></i></a> --}}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    

                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col -->
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                      <div class="table-responsive">
                        <table class="table table-centered table-nowrap table-hover mb-0">
                            <thead class="table-light">
                                <tr align="center">
                                    <th>姓名</th>
                                    @foreach ($months as $month)
                                        <th>{{ $month['name'] }}</th>
                                    @endforeach
                                    <th>已休假天數</th>
                                    <th>剩餘休假天數</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datas as $user_id=>$data)
                                {{-- {{ dd($datas) }} --}}
                                <tr align="center">
                                    <td>{{ $data['name'] }}</td>
                                    @foreach ($months as $key=>$month)
                                        <td>
                                            @if(isset($data['holidays'][$key]))
                                                <a href="{{ route('personnel.holidays.edit',[$user_id,$data['year'],$key]) }}">{{ $data['holidays'][$key] }}</a>天
                                            @else
                                            @endif
                                        </td>
                                    @endforeach
                                    <td>{{ $data['total_day'] }}天</td>
                                    <td>{{ $data['last_day'] }}天</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col -->
    </div>
    <!-- end row -->

</div> <!-- container -->
@endsection