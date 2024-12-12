@extends('layouts.vertical', ["page_title"=> "方案報表"])

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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">報表管理</a></li>
                        <li class="breadcrumb-item active">每月方案報表</li>
                    </ol>
                </div>
                <h4 class="page-title">每月方案報表</h4>
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
                            <form class="d-flex flex-wrap align-items-center" action="{{ route('rpg01') }}" method="GET">
                                <label for="status-select" class="me-2">年度</label>
                                <div class="me-sm-3">
                                    <select class="form-select my-1 my-lg-0" id="status-select" name="year" onchange="this.form.submit()">
                                        <option value="" selected>不限</option>
                                        @foreach($years as $year)
                                            <option value="{{ $year }}" @if($request->year == $year) selected @endif>{{ $year }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <label for="status-select" class="me-2">月份</label>
                                <div class="me-sm-3">
                                    <select class="form-select my-1 my-lg-0" id="status-select" name="month" onchange="this.form.submit()">
                                        <option value="" selected >請選擇</option>
                                        <option value="1" @if($request->month == "1" ) selected  @endif>一月</option>
                                        <option value="2" @if($request->month == "2" ) selected  @endif>二月</option>
                                        <option value="3" @if($request->month == "3" ) selected  @endif>三月</option>
                                        <option value="4" @if($request->month == "4" ) selected  @endif>四月</option>
                                        <option value="5" @if($request->month == "5" ) selected  @endif>五月</option>
                                        <option value="6" @if($request->month == "6" ) selected  @endif>六月</option>
                                        <option value="7" @if($request->month == "7" ) selected  @endif>七月</option>
                                        <option value="8" @if($request->month == "8" ) selected  @endif>八月</option>
                                        <option value="9" @if($request->month == "9" ) selected  @endif>九月</option>
                                        <option value="10" @if($request->month == "10" ) selected  @endif>十月</option>
                                        <option value="11" @if($request->month == "11" ) selected  @endif>十一月</option>
                                        <option value="12" @if($request->month == "12" ) selected  @endif>十二月</option>
                                    </select>
                                </div>
                                <div class="me-3">
                                    <button type="submit" class="btn btn-success waves-effect waves-light me-1"><i class="fe-search me-1"></i>搜尋</button>
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
                    <div class="table-responsive ">
                        <table class="table table-centered table-nowrap table-hover mb-0 mt-2">
                            <thead class="table-light">
                                    <tr align="center">
                                        <th scope="col" width="15%">日期</th>
                                        @foreach ($plans as $key => $plan)
                                            <th scope="col">{{ $plan->name }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr align="center" style="color:red;font-weight:500;">
                                        <td>總單量</td>
                                        @foreach ($plans as $key => $plan)
                                            @if (isset($sums[$plan->id]['count']) && $sums[$plan->id]['count'] != 0)
                                                <td>{{ $sums[$plan->id]['count'] }}單</td>
                                            @else
                                                <td>0單</td>
                                            @endif
                                        @endforeach
                                    </tr>
                                    @foreach ($datas as $key => $data)
                                        <tr align="center">
                                            <td>{{ $key }}</td>
                                            @foreach ($plans as  $plan)
                                                @if (isset($data[$plan->id]['count']) && $data[$plan->id]['count'] != 0)
                                                    <td><a href="{{ route('rpg01.detail',['date'=>$key,'plan_id'=>$plan->id]) }}">{{ $data[$plan->id]['count'] }}</a></td>
                                                @else
                                                    <td></td>
                                                @endif
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                        </table><br>
                    </div>
                </div>
            </div>
        </div>
    </div>

                    

</div> <!-- container -->
@endsection