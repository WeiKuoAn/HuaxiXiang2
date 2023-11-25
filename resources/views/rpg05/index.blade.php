@extends('layouts.vertical', ["page_title"=> "日營收報表"])

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
                        <li class="breadcrumb-item active">日營收報表</li>
                    </ol>
                </div>
                <h4 class="page-title">日營收報表</h4>
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
                            <form class="d-flex flex-wrap align-items-center" action="{{ route('rpg05') }}" method="GET">
                                <label for="status-select" class="me-2">日期區間</label>
                                <div class="me-2">
                                    <input type="date" class="form-control my-1 my-lg-0" id="inputPassword2" name="after_date" @if(!isset($request->after_date)) value="{{ $first_date->format("Y-m-d") }}" @endif value="{{ $request->after_date }}">
                                </div>
                                <label for="status-select" class="me-2">至</label>
                                <div class="me-3">
                                    <input type="date" class="form-control my-1 my-lg-0" id="inputPassword2" name="before_date" @if(!isset($request->before_date)) value="{{ $last_date->format("Y-m-d") }}" @endif value="{{ $request->before_date }}">
                                </div>
                                <div class="me-3">
                                    <button type="submit" class="btn btn-success waves-effect waves-light me-1"><i class="fe-search me-1"></i>搜尋</button>
                                </div>
                            </form>
                        </div>
                        <div class="col-auto">
                            {{-- <div class="text-lg-end my-1 my-lg-0">
                                <h3><span class="text-danger">共計：{{ number_format($totals['nums']) }}份，{{ number_format($totals['total']) }}元</span></h3>
                            </div> --}}
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
                            <thead>
                                <tr align="center">
                                    <th scope="col" width="10%">日期</th>
                                    <th scope="col" width="">業務收入</th>
                                    <th scope="col" width="">其他收入</th>
                                    <th scope="col" width="">支出</th>
                                    <th scope="col" width="">當日營收</th>
                                    <th scope="col" width="">累積淨利</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr align="center" style="font-weight:bold;">
                                    <td>當月個別總計</td>
                                    <td>{{  number_format($sums['sum_total']) }}</td>
                                    <td>{{  number_format($sums['income_total']) }}</td>
                                    <td>{{  number_format($sums['pay_total']) }}</td>
                                    <td>{{ number_format($sums['all_income_total']) }}</td>
                                    @if( number_format($sums['total']) < 0)
                                        <td>{{ number_format($sums['total']) }}</td>
                                    @else
                                        <td>{{  number_format($sums['total']) }}</td>
                                    @endif
                                </tr>
                                @foreach($periods as $period)
                                    <tr align="center" >
                                        <td>{{ $period->format("Y-m-d") }}</td>
                                        @if(isset($datas[$period->format("Y-m-d")]))
                                            @foreach($datas[$period->format("Y-m-d")] as $data)
                                                <td>{{ number_format($data) }}</td>
                                            @endforeach
                                                <td>{{ number_format($sums[$period->format("Y-m-d")]['day_income']) }}</td>
                                                @if(number_format($sums[$period->format("Y-m-d")]['day_total']) < 0)
                                                <td style="color: red;">{{ number_format($sums[$period->format("Y-m-d")]['day_total']) }}</td>
                                                @else
                                                    <td>{{ number_format($sums[$period->format("Y-m-d")]['day_total']) }}</td>
                                                @endif
                                        @else
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        @endif
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