@extends('layouts.vertical', ["page_title"=> "平安燈收入統計"])

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
                        <li class="breadcrumb-item active">平安燈收入統計</li>
                    </ol>
                </div>
                <h4 class="page-title">平安燈收入統計</h4>
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
                            <form class="d-flex flex-wrap align-items-center" action="{{ route('rpg28') }}" method="GET">
                                <label for="status-select" class="me-2">年度</label>
                                <div class="me-sm-3">
                                    <select class="form-select my-1 my-lg-0" id="status-select" name="year" onchange="this.form.submit()">
                                        @foreach($years as $year)
                                            <option value="{{ $year }}" @if($request->year == $year) selected @endif>{{ $year }}</option>
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
                                    <th>平安燈名稱</th>
                                    <th>點燈人數</th>
                                    <th>點燈收入</th>
                                </tr>
                            </thead>
                                <tbody>
                                    @foreach($datas as $data)
                                    <tr align="center">
                                        <td>{{ $data['name'] }}</td>
                                        <td>{{ number_format($data['count']) }}</td>
                                        <td>{{ number_format($data['total_price']) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tr style="color:red;" >
                                    <td></td>
                                    <td align="center">紅區共：{{ $sums['red_count'] }}人、黃區共：{{ $sums['yellow_count'] }}人</td>
                                    <td align="center">總計：{{ number_format($sums['total_price']) }}元</td>
                                </tr>
                        </table><br>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div> <!-- container -->
@endsection