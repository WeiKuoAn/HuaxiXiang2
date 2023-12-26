@extends('layouts.vertical', ["page_title"=> "法會收入統計"])

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
                        <li class="breadcrumb-item active">法會收入統計</li>
                    </ol>
                </div>
                <h4 class="page-title">法會收入統計</h4>
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
                            <form class="d-flex flex-wrap align-items-center" action="{{ route('rpg18') }}" method="GET">
                                <label for="status-select" class="me-2">年度</label>
                                <div class="me-sm-3">
                                    <select class="form-select my-1 my-lg-0" id="status-select" name="year" onchange="this.form.submit()">
                                        @foreach($years as $year)
                                            <option value="{{ $year }}" @if($request->year == $year) selected @endif>{{ $year }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <label for="status-select" class="me-2">法會</label>
                                <div class="me-sm-3">
                                    <select class="form-select my-1 my-lg-0" id="status-select" name="type" onchange="this.form.submit()">
                                        <option value="null">請選擇</option>
                                        @foreach($puja_types as $puja_type)
                                            <option value="{{ $puja_type->id }}" @if($request->type == $puja_type->id) selected @endif>{{ $puja_type->name }}</option>
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
                    <div class="table-responsive ">
                        <table class="table table-centered table-nowrap table-hover mb-0 mt-2">
                            <thead class="table-light">
                                <tr align="center">
                                    <th>法會名稱</th>
                                    <th>報名人數</th>
                                    <th>報名收入（含雜項）</th>
                                    <th>金紙收入</th>
                                    <th>實收金額</th>
                                </tr>
                            </thead>
                                <tbody>
                                    @foreach($datas as $data)
                                    <tr align="center">
                                        <td>{{ $data['name'] }}</td>
                                        <td width="15%">{{ $data['count'] }}（贈與{{ $data['gift_count'] }}人、套組{{ $data['suit_count'] }}人）</td>
                                        <td>{{ number_format($data['apply_price']) }}</td>
                                        <td>{{ number_format($data['monty_price']) }}</td>
                                        <td>{{ number_format($data['total_price']) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                {{-- <tr style="color:red;" >
                                    <td colspan="3"></td>
                                    <td align="center">總共：{{ $data['count_total'] }}份</td>
                                    <td align="right">總計：{{ number_format($data['plan_total']) }}元</td>
                                    <td align="right">佣金總計：{{ number_format($data['commission_total']) }}元</td>
                                </tr> --}}
                        </table><br>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div> <!-- container -->
@endsection