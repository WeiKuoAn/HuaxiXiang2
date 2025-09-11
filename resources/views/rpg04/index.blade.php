@extends('layouts.vertical', ["page_title"=> "每月金紙銷售報表"])

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
                        <li class="breadcrumb-item active">每月金紙銷售報表</li>
                    </ol>
                </div>
                <h4 class="page-title">每月金紙銷售報表</h4>
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
                            <form class="d-flex flex-wrap align-items-center" action="{{ route('rpg04') }}" method="GET">
                                <label for="status-select" class="me-2">日期區間</label>
                                <div class="me-2">
                                    <input type="date" class="form-control my-1 my-lg-0" id="inputPassword2" name="after_date" @if(!isset($request->after_date)) value="{{ $first_date->format("Y-m-d") }}" @endif value="{{ $request->after_date }}">
                                </div>
                                <label for="status-select" class="me-2">至</label>
                                <div class="me-3">
                                    <input type="date" class="form-control my-1 my-lg-0" id="inputPassword2" name="before_date" @if(!isset($request->before_date)) value="{{ $last_date->format("Y-m-d") }}" @endif value="{{ $request->before_date }}">
                                </div>
                                <label for="status-select" class="me-2">產品類型</label>
                                <div class="me-sm-3">
                                    <select class="form-select my-1 my-lg-0" id="status-select" name="type" onchange="this.form.submit()">
                                        <option value="null" @if(!isset($request->type)) selected @endif>不限</option>
                                        <option value="normal" @if($request->type == 'normal') selected @endif>一般</option>
                                        <option value="set"  @if($request->type == 'set') selected @endif>套組</option>
                                        <option value="combo"  @if($request->type == 'combo') selected @endif>組合</option>
                                    </select>
                                </div>
                                <div class="me-3">
                                    <button type="submit" class="btn btn-success waves-effect waves-light me-1"><i class="fe-search me-1"></i>搜尋</button>
                                </div>
                            </form>
                        </div>
                        @if(Auth::user()->job_id == 1 || Auth::user()->job_id== 7)
                        <div class="col-auto">
                            <div class="text-lg-end my-1 my-lg-0">
                                <h3><span class="text-danger">共計：{{ number_format($totals['nums']) }}份，{{ number_format($totals['total']) }}元</span></h3>
                            </div>
                        </div><!-- end col-->
                        @endif
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
                                    <th scope="col" width="10%">日期</th>
                                    @foreach ($products as $product)
                                        <th>{{ $product->name }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                <tr align="center" style="color:red;font-weight:500;">
                                    <td>總計</td>
                                    @foreach ($products as $product)
                                        @if(isset($sums[$product->id]))
                                            <td>{{ number_format($sums[$product->id]['nums']) }}</td>
                                        @else
                                            <td></td>
                                        @endif
                                    @endforeach
                                    </tr>
                                    @foreach($periods as $period)
                                        <tr align="center">
                                            <td>{{ $period->format("Y-m-d") }}</td>
                                            @foreach ($products as $product)
                                                @if(isset($datas[$period->format("Y-m-d")][$product->id]))
                                                    <td>{{ number_format($datas[$period->format("Y-m-d")][$product->id]['nums']) }}</td>
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