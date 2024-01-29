@extends('layouts.vertical', ["page_title"=> "支出比較報表"])

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
                        <li class="breadcrumb-item active">支出比較報表</li>
                    </ol>
                </div>
                <h4 class="page-title">支出比較報表</h4>
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
                            <form class="d-flex flex-wrap align-items-center" action="{{ route('rpg20') }}" method="GET">
                                <label for="status-select" class="me-2">年度月份比較</label>
                                <div class="me-sm-0">
                                    <select class="form-select my-1 my-lg-0" id="status-select" name="past_year">
                                        @foreach($years as $year)
                                            <option value="{{ $year }}" 
                                                @if($request->past_year == $year) selected 
                                                @elseif($pastMonthStart->format("Y") == $year) selected 
                                                @endif>{{ $year }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <label for="status-select" class="me-1"></label>
                                <div class="me-sm-2">
                                    <select class="form-select my-1 my-lg-0" id="status-select" name="past_month">
                                        @foreach($months as $key=>$month)
                                            <option value="{{ $key }}" 
                                                @if($key == $request->past_month) selected 
                                                @elseif($key == $pastMonthStart->format("m")) selected 
                                                @endif>
                                                {{ $month['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <label for="status-select" class="me-2">vs</label>
                                <div class="me-sm-0">
                                    <select class="form-select my-1 my-lg-0" id="status-select" name="current_year">
                                        @foreach($years as $year)
                                            <option value="{{ $year }}" 
                                                @if($request->current_year == $year) selected 
                                                @elseif($currentMonthStart->format("Y") == $year) selected 
                                                @endif>{{ $year }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <label for="status-select" class="me-1"></label>
                                <div class="me-sm-2">
                                    <select class="form-select my-1 my-lg-0" id="status-select" name="current_month">
                                        @foreach($months as $key=>$month)
                                            <option value="{{ $key }}" 
                                                @if($key == $request->current_month) selected 
                                                @elseif($key == $currentMonthStart->format("m")) selected 
                                                @endif>
                                                {{ $month['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <label for="status-select" class="me-2">支出科目</label>
                                <div class="me-sm-3">
                                    <select class="form-select my-1 my-lg-0" id="status-select" name="pay_id" onchange="this.form.submit()">
                                        <option value="NULL" selected>不限</option>
                                        @foreach($pays as $pay)
                                            <option value="{{ $pay->id }}" @if($request->pay_id == $pay->id) selected @endif>{{ $pay->name }}</option>
                                        @endforeach
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
                    <div class="row justify-content-between">
                        <h3 class="text-center text-danger">
                            支出比較差異
                        </h3>
                        <table class="table table-centered table-nowrap table-hover mb-0 mt-2">
                            <tbody>
                                 {{-- <tr align="center" style="color:red;font-weight:500;">
                                      <td>總支出</td>
                                      <td align="right">{{ number_format( $past_sums['total_amount']) }}</td>
                                      <td align="center">@if(isset($past_sums['percent'])){{ $past_sums['percent'] }}% @endif</td>
                                 </tr> --}}
                                @foreach($differences as $key => $difference)
                                    @if(($difference['key'])%6 == 1)
                                    <tr>
                                    @endif
                                    <td>{{ $difference['pay_name'] }}</td>
                                    <td @if($difference['difference']<0) class="text-danger" @endif>
                                        {{ number_format($difference['difference']) }}
                                    </td>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <h3 class="text-end text-danger">
                        @if($sums_difference['total_difference']>0)
                            淨利{{ number_format($sums_difference['total_difference']) }}元
                        @else
                            虧損{{ number_format($sums_difference['total_difference']) }}元
                        @endif
                    </h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!----第一個---->
        <div class="col-6">
            <div class="card">
                <div class="card-body row">
                    <div class="table-responsive">
                        <h3 class="text-center">
                            @if(!isset($request->past_year))
                                {{ $pastMonthStart->format("Y") }}年
                            @else
                                {{ $request->past_year }}年
                            @endif
                            @if(!isset($request->past_month))
                                {{ $pastMonthStart->format("m") }}月
                            @else
                                {{ $request->past_month }}月
                            @endif
                            支出
                        </h3>
                        <table class="table table-centered table-nowrap table-hover mb-0 mt-2">
                            <thead class="table-light">
                                    <tr align="center">
                                        <th scope="col" width="33.3%">科目</th>
                                        <th scope="col" width="10%">支出金額</th>
                                        <th scope="col" width="33.3%">百分比</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($past_datas as $key=>$past_data)
                                    <tr align="center">
                                        <td>{{ $past_data['pay_name'] }}</td>
                                        <td align="right">{{ number_format($past_data['total_price']) }}</td>
                                        <td>{{ $past_data['percent'] }}%</td>
                                    </tr>
                                    @endforeach
                                    <tr align="center" style="color:red;font-weight:500;">
                                        <td>總支出</td>
                                        <td align="right">{{ number_format( $past_sums['total_amount']) }}</td>
                                        {{-- <td align="right"></td> --}}
                                        <td align="center">@if(isset($past_sums['percent'])){{ $past_sums['percent'] }}% @endif</td>
                                    </tr>
                                </tbody>
                        </table><br>
                    </div>
                </div>
            </div>
        </div>
        
        <!----第二個---->
        <div class="col-6">
            <div class="card">
                <div class="card-body row">
                    <div class="table-responsive">
                        <h3 class="text-center">
                            @if(!isset($request->current_year))
                                {{ $currentMonthStart->format("Y") }}年
                            @else
                                {{ $request->current_year }}年
                            @endif
                            @if(!isset($request->current_month))
                                {{ $currentMonthStart->format("m") }}月
                            @else
                                {{ $request->current_month }}月
                            @endif
                            支出
                        </h3>
                        <table class="table table-centered table-nowrap table-hover mb-0 mt-2">
                            <thead class="table-light">
                                    <tr align="center">
                                        <th scope="col" width="33.3%">科目</th>
                                        <th scope="col" width="10%">支出金額</th>
                                        <th scope="col" width="33.3%">百分比</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($current_datas as $key=>$current_data)
                                    <tr align="center">
                                        <td>{{ $current_data['pay_name'] }}</td>
                                        <td align="right">{{ number_format($current_data['total_price']) }}</td>
                                        <td>{{ $current_data['percent'] }}%</td>
                                    </tr>
                                    @endforeach
                                    <tr align="center" style="color:red;font-weight:500;">
                                        <td>總支出</td>
                                        <td align="right">{{ number_format( $current_sums['total_amount']) }}</td>
                                        {{-- <td align="right"></td> --}}
                                        <td align="center">@if(isset($current_sums['percent'])){{ $current_sums['percent'] }}% @endif</td>
                                    </tr>
                                </tbody>
                        </table><br>
                    </div>
                     <!----第二個---->
                </div>
            </div>
        </div>
    </div>

                    

</div> <!-- container -->
@endsection