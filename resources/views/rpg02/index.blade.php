@extends('layouts.vertical', ['page_title' => '支出報表'])

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
                            <li class="breadcrumb-item active">支出報表</li>
                        </ol>
                    </div>
                    <h4 class="page-title">支出報表</h4>
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
                                <form class="d-flex flex-wrap align-items-center" action="{{ route('rpg02') }}"
                                    method="GET">
                                    <label for="status-select" class="me-2">日期區間</label>
                                    <div class="me-2">
                                        <input type="date" class="form-control my-1 my-lg-0" id="inputPassword2"
                                            name="after_date"
                                            @if (!isset($request->after_date)) value="{{ $first_date->format('Y-m-d') }}" @endif
                                            value="{{ $request->after_date }}">
                                    </div>
                                    <label for="status-select" class="me-2">至</label>
                                    <div class="me-3">
                                        <input type="date" class="form-control my-1 my-lg-0" id="inputPassword2"
                                            name="before_date"
                                            @if (!isset($request->before_date)) value="{{ $last_date->format('Y-m-d') }}" @endif
                                            value="{{ $request->before_date }}">
                                    </div>
                                    <label for="status-select" class="me-2">支出科目</label>
                                    <div class="me-sm-3">
                                        <select class="form-select my-1 my-lg-0" id="status-select" name="pay_id"
                                            onchange="this.form.submit()">
                                            <option value="NULL" selected>不限</option>
                                            @foreach ($pays as $pay)
                                                <option value="{{ $pay->id }}"
                                                    @if ($request->pay_id == $pay->id) selected @endif>{{ $pay->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="me-3">
                                        <button type="submit" class="btn btn-success waves-effect waves-light me-1"><i
                                                class="fe-search me-1"></i>搜尋</button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-auto">
                                <div class="text-lg-end my-1 my-lg-0">
                                    <h3 class="text-end text-danger">
                                        @if ($sums['total_amount'] > 0)
                                            淨利{{ number_format($sums['total_amount']) }}元
                                        @else
                                            虧損{{ number_format($sums['total_amount']) }}元
                                        @endif
                                    </h3>
                                </div>
                            </div><!-- end col-->
                        </div> <!-- end row -->
                    </div>
                </div> <!-- end card -->
            </div> <!-- end col-->
        </div>


        <div class="row">
            <!----第一個---->
            @foreach ($groupedDatas as $key => $data)
                <div class="col-4">
                    <div class="card">
                        <div class="card-body row">
                            <div class="table-responsive">
                                <h3 class="text-center">
                                    {{ $data['group_name'] }}
                                </h3>
                                <table class="table table-centered table-nowrap table-hover mb-0 mt-2">
                                    <thead class="table-light">
                                        <tr align="center">
                                            <th scope="col" width="33.3%">科目</th>
                                            <th scope="col" width="10%">支出金額</th>
                                            <th scope="col" width="33.3%">百分比</th>
                                            <th scope="col" width="10%">檢視細項</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data['details'] as $detail)
                                            <tr align="center">
                                                <td>{{ $detail['pay_name'] }}</td>
                                                <td align="right">{{ number_format($detail['total_price']) }}</td>
                                                <td>{{ $detail['percent'] }}%</td>
                                                <td><a
                                                        href="{{ route('rpg02.detail', [$after_date, $before_date, $detail['pay_id']]) }}">
                                                        <i class="mdi mdi-eye me-2 font-18 text-muted vertical-middle"></i>
                                                    </a></td>
                                            </tr>
                                        @endforeach
                                        <tr align="center" style="color:red;font-weight:500;">
                                            <td>總支出</td>
                                            <td align="right">{{ number_format($data['total_price_sum']) }}</td>
                                            {{-- <td align="right"></td> --}}
                                            <td align="center">
                                                @if (isset($data['total_price_percent']))
                                                    {{ $data['total_price_percent'] }}%
                                                @endif
                                            </td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table><br>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>



    </div> <!-- container -->
@endsection
