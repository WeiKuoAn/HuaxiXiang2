@extends('layouts.vertical', ["page_title"=> "每月客戶新增數量"])

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
                        <li class="breadcrumb-item active">每月客戶新增數量</li>
                    </ol>
                </div>
                <h4 class="page-title">每月客戶新增數量</h4>
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
                            <form class="d-flex flex-wrap align-items-center" action="{{ route('rpg24') }}" method="GET">
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

    @foreach($datas as $county_name => $district_datas)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4>{{ $county_name }}</h4>
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap table-hover mb-0 mt-2">
                            @foreach(collect($district_datas)->chunk(5) as $district_count => $chunkedDistricts)
                                    <tr>
                                        @foreach($chunkedDistricts as $districtname => $district)
                                            <td>{{ $districtname }}</td>
                                            <td>{{ $district['count'] }}</td>
                                        @endforeach
                                    </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach


</div> <!-- container -->
@endsection