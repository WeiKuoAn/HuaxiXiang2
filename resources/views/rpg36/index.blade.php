@extends('layouts.vertical', ['page_title' => '員工佣金抽成'])

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
                            <li class="breadcrumb-item active">員工佣金抽成</li>
                        </ol>
                    </div>
                    <h4 class="page-title">員工佣金抽成</h4>
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
                                <form class="d-flex flex-wrap align-items-center" id="myForm"
                                    action="{{ route('rpg36') }}" method="GET">
                                    <label for="status-select" class="me-2">日期區間</label>
                                    <div class="me-2">
                                        <input type="date" class="form-control my-1 my-lg-0" id="inputPassword2"
                                            name="after_date"
                                            @if (!isset($request->after_date)) value="{{ $firstDay->format('Y-m-d') }}" @endif
                                            value="{{ $request->after_date }}">
                                    </div>
                                    <label for="status-select" class="me-2">至</label>
                                    <div class="me-3">
                                        <input type="date" class="form-control my-1 my-lg-0" id="inputPassword2"
                                            name="before_date"
                                            @if (!isset($request->before_date)) value="{{ $lastDay->format('Y-m-d') }}" @endif
                                            value="{{ $request->before_date }}">
                                    </div>
                                    <label for="status-select" class="me-2">來源類別</label>
                                    <div class="me-sm-3">
                                        <select class="form-select my-1 my-lg-0" id="status-select" name="source"
                                            onchange="this.form.submit()">
                                            <option value="NULL">不限</option>
                                            @foreach ($sources as $source)
                                                <option value="{{ $source->code }}"
                                                    @if ($request->source == $source->code) selected @endif>{{ $source->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="me-3">
                                        <button type="submit" onclick="CheckSearch(event)"
                                            class="btn btn-success waves-effect waves-light me-1"><i
                                                class="fe-search me-1"></i>搜尋</button>
                                    </div>
                                    <div class="me-3">
                                        @php
                                            $exportParams = [
                                                'after_date' => $request->after_date ?? $firstDay->format('Y-m-d'),
                                                'before_date' => $request->before_date ?? $lastDay->format('Y-m-d'),
                                            ];
                                            if (!empty($request->source) && $request->source !== 'NULL') {
                                                $exportParams['source'] = $request->source;
                                            }
                                        @endphp
                                        <a href="{{ route('rpg36.export', $exportParams) }}"
                                            class="btn btn-outline-primary waves-effect me-1"><i
                                                class="fe-download me-1"></i>匯出</a>
                                    </div>
                                </form>
                            </div>
                            <div class="col-auto">
                                <div class="text-lg-end my-1 my-lg-0">
                                    <h3><span
                                            class="text-danger">共計：{{ number_format($sums['count']) }}單，佣金共{{ number_format($sums['commission']) }}元</span>
                                    </h3>
                                </div>
                            </div><!-- end col-->
                        </div> <!-- end row -->
                    </div>
                </div> <!-- end card -->
            </div> <!-- end col-->
        </div>

        @foreach ($datas as $type => $data)
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4>來源：{{ $data['name'] }}</h4>
                            <div class="table-responsive ">
                                <table class="table table-centered table-nowrap table-hover mb-0 mt-2">
                                    <thead class="table-light">
                                        <tr align="center">
                                            <th>No</th>
                                            <th>日期</th>
                                            <th>客戶名稱</th>
                                            <th>寶貝名</th>
                                            <th>方案</th>
                                            <th>方案價格</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data['companys'] as $company_data)
                                            <tr>
                                                <td colspan="6">{{ $company_data['name'] }}</td>
                                            </tr>
                                            @foreach ($company_data['items'] as $key => $item)
                                                <tr>
                                                    <td align="center">{{ $key + 1 }}</td>
                                                    <td align="center">{{ $item->sale_date }}</td>
                                                    <td align="center">{{ $item->name }}</td>
                                                    <td align="center">{{ $item->pet_name }}</td>
                                                    <td align="center">{{ $item->plan_name }}</td>
                                                    <td align="right">{{ number_format($item->plan_price) }}</td>
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <td colspan="4"></td>
                                                <td align="center">共計：{{ number_format($company_data['count']) }}單</td>
                                                <td align="right">小計：{{ number_format($company_data['plan_amount']) }}元
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tr style="color:red;">
                                        <td colspan="4"></td>
                                        <td align="center">總共：{{ $data['count_total'] }}份</td>
                                        <td align="right">總計：{{ number_format($data['plan_total']) }}元</td>
                                    </tr>
                                </table><br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

    </div> <!-- container -->
@endsection
