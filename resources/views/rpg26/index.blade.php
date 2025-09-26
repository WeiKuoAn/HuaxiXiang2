@extends('layouts.vertical', ['page_title' => '營收總表'])

@section('content')
<style>
.tooltip-container {
    position: relative;
    display: inline-block;
    cursor: pointer;
}

.tooltip {
    visibility: hidden;
    width: 300px;
    background-color: #333;
    color: #fff;
    text-align: left;
    border-radius: 6px;
    padding: 10px;
    position: absolute;
    z-index: 1000;
    bottom: 125%;
    left: 50%;
    margin-left: -150px;
    opacity: 0;
    transition: opacity 0.3s;
    font-size: 12px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.3);
}

.tooltip::after {
    content: "";
    position: absolute;
    top: 100%;
    left: 50%;
    margin-left: -5px;
    border-width: 5px;
    border-style: solid;
    border-color: #333 transparent transparent transparent;
}

.tooltip-container:hover .tooltip {
    visibility: visible;
    opacity: 1;
}

.tooltip-item {
    margin: 3px 0;
    padding: 2px 0;
    border-bottom: 1px solid #555;
}

.tooltip-item:last-child {
    border-bottom: none;
}

.tooltip-label {
    display: inline-block;
    width: 120px;
    font-weight: bold;
}

.tooltip-value {
    color: #ffeb3b;
    font-weight: bold;
}
</style>


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
                            <li class="breadcrumb-item active">營收總表</li>
                        </ol>
                    </div>
                    <h4 class="page-title">營收總表</h4>
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
                                <form class="d-flex flex-wrap align-items-center" action="{{ route('rpg26') }}"
                                    method="GET">
                                    <label for="status-select" class="me-2">年度</label>
                                    <div class="me-sm-3">
                                        <select class="form-select my-1 my-lg-0" id="status-select" name="year"
                                            onchange="this.form.submit()">
                                            @foreach ($years as $year)
                                                <option value="{{ $year }}"
                                                    @if ($request->year == $year) selected @endif>{{ $year }}
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
                                        <th scope="col">項目名稱</th>
                                        @foreach ($datas as $data)
                                            <th scope="col">{{ $data['month'] }}</th>
                                        @endforeach
                                        <th scope="col">總金額</th>
                                    </tr>
                                </thead>
                                <tbody align="center">
                                    <tr>
                                        <td>淨利</td>
                                        @foreach ($datas as $data)
                                            <td>
                                                <b style="color: red;">{{ number_format($data['cur_month_total']) }}</b>
                                            </td>
                                        @endforeach
                                        <td>
                                            <b style="color: red;">{{ number_format($sums['total_month_total']) }}</b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>營收</td>
                                        @foreach ($datas as $data)
                                            <td>
                                                <div class="tooltip-container">
                                                    {{ number_format($data['cur_price_amount']) }}
                                                    <div class="tooltip">
                                                        <div class="tooltip-item">
                                                            <span class="tooltip-label">銷售金額：</span>
                                                            <span class="tooltip-value">{{ number_format($data['tooltip_data']['sale_price']) }}</span>
                                                        </div>
                                                        <div class="tooltip-item">
                                                            <span class="tooltip-label">收入金額：</span>
                                                            <span class="tooltip-value">{{ number_format($data['tooltip_data']['income_price']) }}</span>
                                                        </div>
                                                        <div class="tooltip-item">
                                                            <span class="tooltip-label">法會金額：</span>
                                                            <span class="tooltip-value">{{ number_format($data['tooltip_data']['puja_price']) }}</span>
                                                        </div>
                                                        <div class="tooltip-item">
                                                            <span class="tooltip-label">營收總額：</span>
                                                            <span class="tooltip-value">{{ number_format($data['cur_price_amount']) }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        @endforeach
                                        <td>{{ number_format($sums['total_price_amount']) }}</td>
                                    </tr>
                                    <tr>
                                        <td>營業費用</td>
                                        @foreach ($datas as $data)
                                            <td>{{ number_format($data['cur_pay_item_price_0']) }}</td>
                                        @endforeach
                                        <td>{{ number_format($sums['total_pay_item_price_0']) }}</td>
                                    </tr>
                                    <tr>
                                        <td>營業成本</td>
                                        @foreach ($datas as $data)
                                            <td>{{ number_format($data['cur_pay_item_price_1']) }}</td>
                                        @endforeach
                                        <td>{{ number_format($sums['total_pay_item_price_1']) }}</td>
                                    </tr>
                                    <tr>
                                        <td>其他費用</td>
                                        @foreach ($datas as $data)
                                            <td>{{ number_format($data['cur_pay_item_price_2']) }}</td>
                                        @endforeach
                                        <td>{{ number_format($sums['total_pay_item_price_2']) }}</td>
                                    </tr>
                                    <tr>
                                        <td scope="col">業務單量</td>
                                        @foreach ($datas as $data)
                                            <td>{{ $data['count'] }}單</td>
                                        @endforeach
                                        <td>{{ number_format($sums['total_count']) }}單</td>
                                    </tr>
                                    <tr>
                                        <td scope="col">方案價格</td>
                                        @foreach ($datas as $data)
                                            <td>{{ number_format($data['plan_price']) }}</td>
                                        @endforeach
                                        <td>{{ number_format($sums['total_plan_price']) }}</td>
                                    </tr>
                                    <tr>
                                        <td scope="col">金紙金額</td>
                                        @foreach ($datas as $data)
                                            <td>{{ number_format($data['gdpaper_price']) }}</td>
                                        @endforeach
                                        <td>{{ number_format($sums['total_gdpaper_price']) }}</td>
                                    </tr>
                                    <tr>
                                        <td scope="col">安葬服務金額</td>
                                        @foreach ($datas as $data)
                                            <td>{{ number_format($data['sale_promA']) }}</td>
                                        @endforeach
                                        <td>{{ number_format($sums['total_sale_promA']) }}</td>
                                    </tr>
                                    <tr>
                                        <td scope="col">後續服務金額</td>
                                        @foreach ($datas as $data)
                                            <td>{{ number_format($data['sale_promB']) }}</td>
                                        @endforeach
                                        <td>{{ number_format($sums['total_sale_promB']) }}</td>
                                    </tr>
                                    <tr>
                                        <td scope="col">其他服務金額</td>
                                        @foreach ($datas as $data)
                                            <td>{{ number_format($data['sale_promC']) }}</td>
                                        @endforeach
                                        <td>{{ number_format($sums['total_sale_promC']) }}</td>
                                    </tr>
                                </tbody>
                            </table><br>
                        </div>
                    </div>
                </div>
            </div>
        </div>



    </div> <!-- container -->
@endsection
