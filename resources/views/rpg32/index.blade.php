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
                        <li class="breadcrumb-item active">處理比較報表</li>
                    </ol>
                </div>
                <h4 class="page-title">處理比較報表</h4>
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
                            <form class="d-flex flex-wrap align-items-center" action="{{ route('rpg32') }}" method="GET">
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

    <!-- 成長率分析摘要 -->
    @if(isset($summary))
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">成長率分析摘要</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <h5>總數量成長率</h5>
                                <h3 class="text-{{ $summary['total_count_growth_rate'] >= 0 ? 'success' : 'danger' }}">
                                    {{ $summary['total_count_growth_rate'] }}%
                                </h3>
                                <p class="text-muted">
                                    <span class="badge bg-{{ $summary['total_count_growth_rate'] >= 0 ? 'success' : 'danger' }}">
                                        {{ $summary['total_count_performance']['description'] }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h5>總金額成長率</h5>
                                <h3 class="text-{{ $summary['total_amount_growth_rate'] >= 0 ? 'success' : 'danger' }}">
                                    {{ $summary['total_amount_growth_rate'] }}%
                                </h3>
                                <p class="text-muted">
                                    <span class="badge bg-{{ $summary['total_amount_growth_rate'] >= 0 ? 'success' : 'danger' }}">
                                        {{ $summary['total_amount_performance']['description'] }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h5>數量成長值</h5>
                                <h3 class="text-{{ $summary['total_count_growth_amount'] >= 0 ? 'success' : 'danger' }}">
                                    {{ $summary['total_count_growth_amount'] > 0 ? '+' : '' }}{{ $summary['total_count_growth_amount'] }}
                                </h3>
                                <p class="text-muted">案件數</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h5>金額成長值</h5>
                                <h3 class="text-{{ $summary['total_amount_growth_amount'] >= 0 ? 'success' : 'danger' }}">
                                    {{ $summary['total_amount_growth_amount'] > 0 ? '+' : '' }}{{ number_format($summary['total_amount_growth_amount']) }}
                                </h3>
                                <p class="text-muted">元</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- 詳細項目比較表格 -->
    @if(isset($datas))
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">安葬處理與後續處理項目成長率分析</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap table-hover mb-0">
                            <thead class="table-light">
                                <tr align="center">
                                    <th scope="col">項目名稱</th>
                                    <th scope="col" colspan="2" class="text-center"> ({{ $pastMonthStart->format('Y-m') }})</th>
                                    <th scope="col" colspan="2" class="text-center"> ({{ $currentMonthStart->format('Y-m') }})</th>
                                    <th scope="col" colspan="2" class="text-center">成長率分析</th>
                                    <th scope="col">表現評估</th>
                                </tr>
                                <tr align="center">
                                    <th></th>
                                    <th>數量</th>
                                    <th>金額</th>
                                    <th>數量</th>
                                    <th>金額</th>
                                    <th>數量成長率</th>
                                    <th>金額成長率</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datas as $promId => $data)
                                    <tr align="center">
                                        <td><strong>{{ $data['name'] }}</strong></td>
                                        <td>{{ number_format($data['past_count']) }}</td>
                                        <td>{{ number_format($data['past_amount']) }}</td>
                                        <td>{{ number_format($data['current_count']) }}</td>
                                        <td>{{ number_format($data['current_amount']) }}</td>
                                        <td class="text-{{ $data['count_growth_rate'] >= 0 ? 'success' : 'danger' }}">
                                            <strong>{{ $data['count_growth_rate'] }}%</strong>
                                            @if($data['count_growth_amount'] != 0)
                                                <br><small>({{ $data['count_growth_amount'] > 0 ? '+' : '' }}{{ $data['count_growth_amount'] }})</small>
                                            @endif
                                        </td>
                                        <td class="text-{{ $data['amount_growth_rate'] >= 0 ? 'success' : 'danger' }}">
                                            <strong>{{ $data['amount_growth_rate'] }}%</strong>
                                            @if($data['amount_growth_amount'] != 0)
                                                <br><small>({{ $data['amount_growth_amount'] > 0 ? '+' : '' }}{{ number_format($data['amount_growth_amount']) }})</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $data['count_growth_rate'] >= 0 ? 'success' : 'danger' }} me-1">
                                                {{ $data['count_performance']['description'] }}
                                            </span>
                                            <br>
                                            <span class="badge bg-{{ $data['amount_growth_rate'] >= 0 ? 'success' : 'danger' }}">
                                                {{ $data['amount_performance']['description'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- 成長率分析圖表 -->
    @if(isset($datas))
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">數量成長率比較</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>項目</th>
                                    <th>成長率</th>
                                    <th>狀態</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datas as $promId => $data)
                                    <tr>
                                        <td>{{ $data['name'] }}</td>
                                        <td>
                                            <span class="text-{{ $data['count_growth_rate'] >= 0 ? 'success' : 'danger' }}">
                                                {{ $data['count_growth_rate'] }}%
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $data['count_growth_rate'] >= 0 ? 'success' : 'danger' }}">
                                                {{ $data['count_performance']['description'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">金額成長率比較</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>項目</th>
                                    <th>成長率</th>
                                    <th>狀態</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datas as $promId => $data)
                                    <tr>
                                        <td>{{ $data['name'] }}</td>
                                        <td>
                                            <span class="text-{{ $data['amount_growth_rate'] >= 0 ? 'success' : 'danger' }}">
                                                {{ $data['amount_growth_rate'] }}%
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $data['amount_growth_rate'] >= 0 ? 'success' : 'danger' }}">
                                                {{ $data['amount_performance']['description'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- 原有的比較表格 -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row justify-content-between">
                        <h3 class="text-center text-danger">
                            後續比較差異
                        </h3>
                        <table class="table table-centered table-nowrap table-hover mb-0 mt-2">
                            <tbody>
                                <!-- 這裡可以加入其他比較數據 -->
                            </tbody>
                        </table>
                    </div>
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
                            後續處理
                        </h3>
                        <table class="table table-centered table-nowrap table-hover mb-0 mt-2">
                            <thead class="table-light">
                                <tr align="center">
                                    <th scope="col" width="33.3%">後續處理項目</th>
                                    <th scope="col" width="10%">數量</th>
                                    <th scope="col" width="10%">金額</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($datas))
                                    @foreach($datas as $promId => $data)
                                    <tr align="center">
                                        <td>{{ $data['name'] }}</td>
                                        <td align="right">{{ number_format($data['past_count']) }}</td>
                                        <td align="right">{{ number_format($data['past_amount']) }}</td>
                                    </tr>
                                    @endforeach
                                    <tr align="center" style="color:red;font-weight:500;">
                                        <td>總計</td>
                                        <td align="right">{{ number_format($summary['total_count_growth_amount'] + $summary['total_count_growth_amount']) }}</td>
                                        <td align="right">{{ number_format($summary['total_amount_growth_amount'] + $summary['total_amount_growth_amount']) }}</td>
                                    </tr>
                                @endif
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
                            後續處理
                        </h3>
                        <table class="table table-centered table-nowrap table-hover mb-0 mt-2">
                            <thead class="table-light">
                                <tr align="center">
                                    <th scope="col" width="33.3%">後續處理項目</th>
                                    <th scope="col" width="10%">數量</th>
                                    <th scope="col" width="10%">金額</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($datas))
                                    @foreach($datas as $promId => $data)
                                    <tr align="center">
                                        <td>{{ $data['name'] }}</td>
                                        <td align="right">{{ number_format($data['current_count']) }}</td>
                                        <td align="right">{{ number_format($data['current_amount']) }}</td>
                                    </tr>
                                    @endforeach
                                    <tr align="center" style="color:red;font-weight:500;">
                                        <td>總計</td>
                                        <td align="right">{{ number_format($summary['total_count_growth_amount'] + $summary['total_count_growth_amount']) }}</td>
                                        <td align="right">{{ number_format($summary['total_amount_growth_amount'] + $summary['total_amount_growth_amount']) }}</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table><br>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div> <!-- container -->
@endsection