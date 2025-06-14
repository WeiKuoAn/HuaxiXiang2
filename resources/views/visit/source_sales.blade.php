@extends('layouts.vertical', ['page_title' => 'CRM Customers'])

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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">拜訪管理</a></li>
                            <li class="breadcrumb-item active">客戶【{{ $customer->name }}】叫件紀錄</li>
                        </ol>
                    </div>
                    <h4 class="page-title">客戶【{{ $customer->name }}】叫件紀錄</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">叫件紀錄</h4>
                        <div class="table-responsive ">
                            <table class="table table-centered table-nowrap table-hover mb-0 mt-2">
                                <thead class="table-light">
                                    <tr>
                                        <th>單號</th>
                                        <th>日期</th>
                                        <th>方案</th>
                                        <th>客戶名</th>
                                        <th>寶貝名</th>
                                        <th>金紙</th>
                                        <th>安葬方式</th>
                                        <th>後續處理</th>
                                        <th>其他處理</th>
                                        <th>付款方式</th>
                                        <th>付款金額</th>
                                        <th>佣金金額</th>
                                        <th width="25%">備註</th>
                                        <th>Key單人員</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sales as $sale)
                                        <tr>
                                            <td>{{ $sale->sale_on }}</td>
                                            <td>{{ $sale->sale_date }}</td>
                                            <td>
                                                @if (isset($sale->plan_id))
                                                    @if (isset($sale->plan_name))
                                                        {{ $sale->plan_name }}
                                                    @endif
                                                @endif
                                            </td>
                                            <td>{{ $sale->customer_name }} </td>
                                            <td>
                                                @if (isset($sale->pet_name))
                                                    {{ $sale->pet_name }}
                                                @endif
                                            </td>
                                            <td>
                                                @if (isset($sale->gdpapers))
                                                    @foreach ($sale->gdpapers as $gdpaper)
                                                        @if (isset($gdpaper->gdpaper_id))
                                                            @if (isset($gdpaper->gdpaper_name))
                                                                {{ $gdpaper->gdpaper_name->name }}({{ number_format($gdpaper->gdpaper_total) }})元<br>
                                                            @endif
                                                        @else
                                                            無
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td>
                                                @if (isset($sale->before_prom_id))
                                                    @if (isset($sale->PromA_name))
                                                        {{ $sale->PromA_name->name }}-{{ number_format($sale->before_prom_price) }}
                                                    @endif
                                                @endif
                                                @foreach ($sale->proms as $prom)
                                                    @if ($prom->prom_type == 'A')
                                                        @if (isset($prom->prom_id))
                                                            {{ $prom->prom_name->name }}-{{ number_format($prom->prom_total) }}<br>
                                                        @else
                                                            無
                                                        @endif
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td>
                                                @foreach ($sale->proms as $prom)
                                                    @if ($prom->prom_type == 'B')
                                                        @if (isset($prom->prom_id))
                                                            {{ $prom->prom_name->name }}-{{ number_format($prom->prom_total) }}<br>
                                                        @else
                                                            無
                                                        @endif
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td>
                                                @foreach ($sale->proms as $prom)
                                                    @if ($prom->prom_type == 'C')
                                                        @if (isset($prom->prom_id))
                                                            {{ $prom->prom_name->name }}-{{ number_format($prom->prom_total) }}<br>
                                                        @endif
                                                    @else
                                                        無
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td>{{ $sale->pay_type() }}</td>
                                            <td>{{ number_format($sale->plan_price) }}</td>
                                            <td>{{ number_format($sale->commission) }}</td>
                                            <td>{{ $sale->comm }}</td>
                                            <td>{{ $sale->user_name }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>





        <div class="row">
            <div class="col-12">
                <div class="text-center mb-3">
                    <button type="button" class="btn w-sm btn-light waves-effect" onclick="history.go(-1)">回上一頁</button>
                </div>
            </div> <!-- end col -->
        </div>

    </div> <!-- container -->
@endsection
