@extends('layouts.vertical', ["page_title"=> "專員金紙獎金"])

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
                        <li class="breadcrumb-item active">專員金紙獎金</li>
                    </ol>
                </div>
                <h4 class="page-title">專員金紙獎金</h4>
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
                            <form class="d-flex flex-wrap align-items-center" id="myForm" action="{{ route('rpg10') }}" method="GET">
                                <label for="status-select" class="me-2">年度</label>
                                <div class="me-sm-3">
                                    <select class="form-select my-1 my-lg-0" id="status-select" name="year" onchange="this.form.submit()">
                                        @foreach($years as $year)
                                        <option value="{{$year}}" @if($request->year == $year) selected @endif >{{ $year }}年</option>
                                        @endforeach
                                    </select>
                                </div>
                                <label for="status-select" class="me-2">月份</label>
                                <div class="me-sm-3">
                                    <select class="form-select my-1 my-lg-0" id="status-select" name="month" onchange="this.form.submit()">
                                        <option value="" selected >請選擇</option>
                                        <option value="01" @if($request->month == "01" ) selected  @endif>一月</option>
                                        <option value="02" @if($request->month == "02" ) selected  @endif>二月</option>
                                        <option value="03" @if($request->month == "03" ) selected  @endif>三月</option>
                                        <option value="04" @if($request->month == "04" ) selected  @endif>四月</option>
                                        <option value="05" @if($request->month == "05" ) selected  @endif>五月</option>
                                        <option value="06" @if($request->month == "06" ) selected  @endif>六月</option>
                                        <option value="07" @if($request->month == "07" ) selected  @endif>七月</option>
                                        <option value="08" @if($request->month == "08" ) selected  @endif>八月</option>
                                        <option value="09" @if($request->month == "09" ) selected  @endif>九月</option>
                                        <option value="10" @if($request->month == "10" ) selected  @endif>十月</option>
                                        <option value="11" @if($request->month == "11" ) selected  @endif>十一月</option>
                                        <option value="12" @if($request->month == "12" ) selected  @endif>十二月</option>
                                    </select>
                                </div>
                                <label for="status-select" class="me-2">專員</label>
                                <div class="me-sm-3">
                                    <select class="form-select my-1 my-lg-0" id="status-select" name="user_id" onchange="this.form.submit()">
                                        <option value="NULL">不限</option>
                                        @foreach($users as $user)
                                            <option value="{{$user->id}}" @if($request->user_id == $user->id) selected @endif >{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="me-3">
                                    <button type="submit" onclick="CheckSearch(event)" class="btn btn-success waves-effect waves-light me-1"><i class="fe-search me-1"></i>搜尋</button>
                                </div>
                            </form>
                        </div>
                        {{-- <div class="col-auto">
                            <div class="text-lg-end my-1 my-lg-0">
                                <a href="{{ route('rpg07.export',request()->input()) }}" onclick="CheckForm(event)" class="btn btn-danger waves-effect waves-light">匯出</a>
                            </div>
                        </div><!-- end col--> --}}
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
                                    <th>No</th>
                                    <th>日期</th>
                                    <th>方案</th>
                                    <th>寶貝名稱</th>
                                    <th>金紙名稱</th>
                                    <th>金紙數量</th>
                                    <th>小計</th>
                                    <th>佣金</th>
                                </tr>
                            </thead>
                            <thead >
                                <tr style="color:red;" align="right">
                                    <th colspan="5"></th>
                                    <th>總共：{{ $sums['total_num'] }}份</th>
                                    <th>總計：{{ number_format($sums['total_price']) }}元</th>
                                    <th>佣金總計：{{ number_format($sums['total_comm_price']) }}元</th>
                                </tr>
                            </thead>
                            @foreach($datas as $user_name => $data)
                                <tbody>
                                    <tr>
                                        <td colspan="8">{{ $user_name }}</td>
                                    </tr>

                                    @foreach($data['sale_datas'] as $key=>$da)
                                    <tr>
                                        <td align="center">{{ $key+1 }}</td>
                                        <td align="center">{{ $da->sale_date }}</td>
                                        <td align="center">
                                            @if($da->pay_id == 'D')
                                            尾款
                                            @elseif($da->pay_id == 'E')
                                            追加
                                            @else
                                            {{ $da->plan_name }}
                                            @endif
                                        </td>
                                        <td align="center">{{ $da->pet_name }}</td>
                                        <td align="center">{{ $da->name }}</td>
                                        <td align="right">{{ $da->gdpaper_num }}</td>
                                        <td align="right">{{ $da->gdpaper_total }}</td>
                                        <td align="right">{{ $da->comm_price }}</td>
                                    </tr>
                                   @endforeach
                                   <tr>
                                       <td colspan="5"></td>
                                       <td align="right">共：{{ number_format($data['total_num']) }}份</td>
                                       <td align="right">小計：{{ number_format($data['total_price']) }}元</td>
                                       <td align="right">佣金小計：{{ number_format($data['total_comm_price']) }}元</td>
                                   </tr>
                                </tbody>
                            @endforeach
                        </table><br>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> <!-- container -->
@endsection