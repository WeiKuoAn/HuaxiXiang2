@extends('layouts.vertical', ["page_title"=> "年度後續服務統計"])

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
                        <li class="breadcrumb-item active">年度後續服務統計</li>
                    </ol>
                </div>
                <h4 class="page-title">年度後續服務統計</h4>
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
                            <form class="d-flex flex-wrap align-items-center" action="{{ route('rpg16') }}" method="GET">
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
                                        <th scope="col">後續服務</th>
                                        @foreach($months as $key=>$month)
                                            <th scope="col">{{ $month['month'] }}</th>
                                        @endforeach
                                        <th scope="col">總次數</th>
                                    </tr>
                                </thead>
                                {{-- <tr align="center" style="font-weight:bold;" class="text-danger">
                                    <td>當年總計</td>
                                    <td>{{  number_format($sums['total_count']) }}</td>
                                    <td>{{  number_format($sums['total_price_amount']) }}</td>
                                    @if(Auth::user()->job_id == '1' || Auth::user()->job_id == '6' || Auth::user()->job_id == '7')
                                        <td>{{  number_format($sums['total_pay_price']) }}</td>
                                        <td>{{  number_format($sums['total_month_total']) }}</td>
                                    @endif
                                </tr> --}}
                                <tbody align="center">
                                    @foreach($proms as $prom)
                                        <tr>
                                            <td>{{ $prom['name'] }}</td>
                                            @foreach($datas as $key=>$data)
                                                <td><a href="{{ route('rpg16.detail',['year'=>$request->year,'month'=>$key,'prom_id'=>$prom->id]) }}">{{ $data['proms'][$prom->id]['count'] }} </a></td>
                                            @endforeach
                                            <td>{{ $sums[$prom->id] }}</td>
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