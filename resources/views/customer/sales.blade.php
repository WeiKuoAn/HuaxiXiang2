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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">客戶管理</a></li>
                            <li class="breadcrumb-item active">客戶【{{ $customer->name }}】業務列表</li>
                        </ol>
                    </div>
                    <h4 class="page-title">客戶【{{ $customer->name }}】業務列表</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">業務紀錄</h4>
                        <div class="table-responsive ">
                            <table class="table table-centered table-nowrap table-hover mb-0 mt-2">
                                <thead class="table-light">
                                    <tr>
                                        <th>單號</th>
                                        <th>Key單人員</th>
                                        <th>日期</th>
                                        {{-- <th>客戶</th> --}}
                                        <th>寶貝名</th>
                                        {{-- <th>類別</th> --}}
                                        <th>方案</th>
                                        <th>金紙</th>
                                        <th>安葬方式</th>
                                        <th>後續處理</th>
                                        <th>其他處理</th>
                                        <th>付款方式</th>
                                        {{-- <th>實收價格</th> --}}
                                        {{-- @if ($request->status == 'check')
                                            <th>轉單</th>
                                            <th>對拆</th>
                                        @endif
                                        <th>動作</th> --}}
                                        <th width="25%">備註</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sales as $sale)
                                        <tr>
                                            <td>
                                                <a href="{{ route('sale.check', $sale->id) }}">
                                                    {{ $sale->sale_on }}
                                                </a>
                                            </td>
                                            <td>{{ $sale->user_name->name }}</td>
                                            <td>{{ $sale->sale_date }}</td>
                                            {{-- <td>
                                            @if (isset($sale->customer_id))
                                                @if (isset($sale->cust_name))
                                                    {{ $sale->cust_name->name }}
                                                @else
                                                    {{ $sale->customer_id }}<b style="color: red;">（客戶姓名須重新登入）</b>
                                                @endif
                                            @elseif($sale->type_list == 'memorial')
                                                追思
                                            @endif
                                        </td> --}}
                                            <td>
                                                @if (isset($sale->pet_name))
                                                    {{ $sale->pet_name }}
                                                @endif
                                            </td>
                                            {{-- <td>
                                            @if (isset($sale->type))
                                                @if (isset($sale->source_type))
                                                    {{ $sale->source_type->name }}
                                                @else
                                                    {{$sale->type}}
                                                @endif
                                            @endif
                                        </td> --}}
                                            <td>
                                                @if (isset($sale->plan_id))
                                                    @if (isset($sale->plan_name))
                                                        {{ $sale->plan_name->name }}
                                                    @endif
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
                                            <td>
                                                @if (isset($sale->pay_id))
                                                    {{ $sale->pay_type() }}
                                                @endif
                                            </td>
                                            {{-- <td>{{ number_format($sale->pay_price) }}</td> --}}

                                            {{-- <td> --}}
                                            {{-- @if ($sale->status != '9')
                                                <a href="{{ route('edit-sale', $sale->id) }}"><button type="button"
                                                        class="btn btn-secondary btn-sm">修改</button></a>
                                                        <a href="{{ route('del-sale', $sale->id) }}"><button type="button"
                                                            class="btn btn-secondary btn-sm">刪除</button></a>
                                                <a href="{{ route('check-sale', $sale->id) }}"><button type="button"
                                                        class="btn btn-success btn-sm">送出對帳</button></a>
                                            @else
                                                <a href="{{ route('check-sale', $sale->id) }}"><button type="button"
                                                        class="btn btn-danger btn-sm">查看</button></a>
                                            @endif --}}
                                            {{-- @if ($sale->status != '9')
                                                <div class="btn-group dropdown">
                                                    <a href="javascript: void(0);" class="table-action-btn dropdown-toggle arrow-none btn btn-outline-secondary waves-effect" data-bs-toggle="dropdown" aria-expanded="false">動作 <i class="mdi mdi-arrow-down-drop-circle"></i></a>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a class="dropdown-item" href="{{ route('sale.edit',$sale->id) }}"><i class="mdi mdi-pencil me-2 text-muted font-18 vertical-middle"></i>編輯</a>
                                                        <a class="dropdown-item" href="{{ route('sale.del',$sale->id) }}"><i class="mdi mdi-delete me-2 font-18 text-muted vertical-middle"></i>刪除</a>
                                                        <a class="dropdown-item" href="{{ route('sale.check',$sale->id) }}"><i class="mdi mdi-send me-2 font-18 text-muted vertical-middle"></i>送出對帳</a>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="btn-group dropdown">
                                                    <a href="javascript: void(0);" class="table-action-btn dropdown-toggle arrow-none btn btn-outline-secondary waves-effect" data-bs-toggle="dropdown" aria-expanded="false">動作 <i class="mdi mdi-arrow-down-drop-circle"></i></a>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a class="dropdown-item" href="{{ route('sale.check',$sale->id) }}"><i class="mdi mdi-eye me-2 font-18 text-muted vertical-middle"></i>查看</a>
                                                        <a class="dropdown-item" href="{{ route('sale.change',$sale->id) }}"><i class="mdi mdi-autorenew me-2 text-muted font-18 vertical-middle"></i>轉單/對拆</a>
                                                        <a class="dropdown-item" href="{{ route('sale.change.record',$sale->id) }}"><i class="mdi mdi-cash me-2 text-muted font-18 vertical-middle"></i>轉單/對拆紀錄</a>
                                                    </div>
                                                </div>
                                            @endif --}}
                                            {{-- </td> --}}
                                            <td>{{ $sale->comm }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">合約紀錄</h4>
                        <div class="table-responsive ">
                            <table class="table table-centered table-nowrap table-hover mb-0 mt-2">
                                <thead class="table-light">
                                    <tr>
                                        <th>編號</th>
                                        <th>合約類別</th>
                                        <th>寶貝名稱</th>
                                        <th>目前簽約年份</th>
                                        <th>開始日期</th>
                                        <th>結束日期</th>
                                        <th>金額</th>
                                        <th>續約</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (count($contract_datas) == 0)
                                        <tr>
                                            <td colspan="8" class="text-center">無資料</td>
                                        </tr>
                                    @else
                                        @foreach ($contract_datas as $contract_data)
                                            <tr>
                                                <td>{{ $contract_data->number }}</td>
                                                <td>
                                                    <span
                                                        @if ($contract_data->type == '1') class=" bg-soft-success text-success p-1" 
                                            @elseif($contract_data->type == '2') class=" bg-soft-danger text-danger p-1"
                                            @elseif($contract_data->type == '4') class=" bg-soft-warning text-warning p-1"
                                            @else class=" bg-soft-blue text-blue p-1" @endif>
                                                        {{ $contract_data->type_data->name }}
                                                    </span>
                                                </td>
                                                <td>{{ $contract_data->pet_name }}</td>
                                                <td>
                                                    @if ($contract_data->type == '4')
                                                        {{ $contract_data->year }}天
                                                    @else
                                                        第{{ $contract_data->year }}年
                                                    @endif
                                                </td>
                                                <td>{{ $contract_data->getRocStartDateAttribute() }}</td>
                                                @if (!isset($request->check_close) || $request->check_close == '1')
                                                    <td>{{ $contract_data->getRocEndDateAttribute() }}</td>
                                                @else
                                                    <td>{{ $contract_data->getRocCloseDateAttribute() }}</td>
                                                @endif
                                                <td>{{ number_format($contract_data->price) }}</td>
                                                <td>
                                                    @if ($contract_data->renew == '1')
                                                        是（{{ $contract_data->renew_year }}年）
                                                    @endif
                                                    @if (isset($contract_data->close_date))
                                                        已結案
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">平安燈紀錄</h4>
                        <div class="table-responsive ">
                            <table class="table table-centered table-nowrap table-hover mb-0 mt-2">
                                <thead class="table-light">
                                    <tr>
                                        <th>編號</th>
                                        <th>平安燈類別</th>
                                        <th>寶貝名稱</th>
                                        <th>目前簽約年份</th>
                                        <th>開始日期</th>
                                        <th>結束日期</th>
                                        <th>金額</th>
                                        <th>續約</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (count($lamp_datas) == 0)
                                        <tr>
                                            <td colspan="8" class="text-center">無資料</td>
                                        </tr>
                                    @else
                                        @foreach ($lamp_datas as $key => $lamp_data)
                                            <tr>
                                                <td>{{ $lamp_data->number }}</td>
                                                <td>
                                                    <span
                                                        @if ($lamp_data->type == '1') class=" bg-soft-success text-success p-1" 
                                            @elseif($lamp_data->type == '2') class=" bg-soft-danger text-danger p-1"
                                            @elseif($lamp_data->type == '4') class=" bg-soft-warning text-warning p-1"
                                            @else class=" bg-soft-blue text-blue p-1" @endif>
                                                        {{ $lamp_data->type_data->name }}
                                                    </span>
                                                </td>
                                                <td>{{ $lamp_data->pet_name }}</td>
                                                <td>第{{ $lamp_data->year }}年</td>
                                                <td>{{ $lamp_data->getRocStartDateAttribute() }}</td>
                                                @if (!isset($request->check_close) || $request->check_close == '1')
                                                    <td>{{ $lamp_data->getRocEndDateAttribute() }}</td>
                                                @else
                                                    <td>{{ $lamp_data->getRocCloseDateAttribute() }}</td>
                                                @endif
                                                <td>{{ number_format($lamp_data->price) }}</td>
                                                <td>
                                                    @if ($lamp_data->renew == '1')
                                                        是（{{ $lamp_data->renew_year }}年）
                                                    @endif
                                                    @if (isset($lamp_data->close_date))
                                                        已結案
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">法會紀錄</h4>
                        <div class="table-responsive ">
                            <table class="table table-centered table-nowrap table-hover mb-0 mt-2">
                                <thead class="table-light">
                                    <tr>
                                        <th>編號</th>
                                        <th>報名類別</th>
                                        <th>報名日期</th>
                                        <th>法會名稱</th>
                                        <th>寶貝名稱</th>
                                        <th>附加商品</th>
                                        <th>付款方式</th>
                                        <th>支付金額</th>
                                        <th>備註</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (count($puja_datas) == 0)
                                        <tr>
                                            <td colspan="10" class="text-center">無資料</td>
                                        </tr>
                                    @else
                                        @foreach ($puja_datas as $key => $puja_data)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $puja_data->type() }}</td>
                                                <td>{{ $puja_data->date }}</td>
                                                <td>{{ $puja_data->puja_name->name }}</td>
                                                <td>
                                                    {{ $puja_data->pet_name }}<br>
                                                </td>
                                                <td>
                                                    @if (isset($puja_data->products))
                                                        @foreach ($puja_data->products as $puja_data->product)
                                                            {{ $product_name[$puja_data->product->product_id] }}-{{ $puja_data->product->product_num }}份<br>
                                                        @endforeach
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (isset($puja_data->pay_id))
                                                        {{ $puja_data->pay_type() }}
                                                    @endif
                                                </td>
                                                <td>{{ number_format($puja_data->pay_price) }}</td>
                                                <td>{{ $puja_data->comment }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
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
