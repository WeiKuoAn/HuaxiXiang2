@extends('layouts.vertical', ["page_title"=> "專員年度業務金額統計"])

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
                        <li class="breadcrumb-item active">專員年度業務金額統計</li>
                    </ol>
                </div>
                <h4 class="page-title">專員年度業務金額統計</h4>
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
                            <form class="d-flex flex-wrap align-items-center" action="{{ route('rpg21') }}" method="GET">
                                <div class="me-sm-3">
                                    <select class="form-select my-1 my-lg-0" id="status-select" name="year" onchange="this.form.submit()">
                                        @foreach($years as $year)
                                            <option value="{{ $year }}" @if($request->year == $year) selected @endif>{{ $year }}年</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="me-3">
                                    <button type="submit" class="btn btn-success waves-effect waves-light me-1"><i class="fe-search me-1"></i>搜尋</button>
                                </div>
                            </form>
                        </div>
                        <div class="col-auto">
                            <div class="text-lg-end my-1 my-lg-0">
                                {{-- <button type="button" class="btn btn-success waves-effect waves-light me-1"><i class="mdi mdi-cog"></i></button> --}}
                            </div>
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
                    <div class="row mb-2">
                      <div class="table-responsive">
                        <table class="table table-centered table-nowrap table-hover mb-0">
                            <thead class="table-light">
                                <tr align="left">
                                    <th>姓名</th>
                                    @foreach ($months as $key=>$month)
                                        <th>{{ $month['monthName'] }}</th>
                                    @endforeach
                                    <th>總單量</th>
                                    <th>總金額</th>
                                    <th>每單平均</th>
                                    <th>每月總平均</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($datas as $data)
                                <tr align="left">
                                    <td>{{ $data['name'] }}</td>
                                    @foreach ($months as $key=>$month)
                                        <td>
                                            <span class="bg-soft-light p-1" style="line-height: 35px;">{{ number_format($data['months'][$key]['price']) }}</span><br>
                                        </td>
                                    @endforeach
                                    <td>
                                        <span class="p-1" style="line-height: 35px;">{{ number_format($data['count']) }}單</span>
                                    </td>
                                    <td>
                                        <span class="p-1" style="line-height: 35px;">{{ number_format($data['total_price']) }}</span>
                                    </td>
                                    <td>
                                        <span class="p-1" style="line-height: 35px;">{{ number_format($data['sale_average']) }}</span>
                                    </td>
                                    <td>
                                        <span class="p-1" style="line-height: 35px;">{{ number_format($data['month_average']) }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col -->
    </div>
    <!-- end row -->

</div> <!-- container -->
@endsection