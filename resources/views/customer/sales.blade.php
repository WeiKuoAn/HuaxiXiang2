@extends('layouts.vertical', ["page_title"=> "CRM Customers"])

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
                                        {{-- @if($request->status == 'check')
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
                                        <td>{{ $sale->sale_on }}</td>
                                        <td>{{ $sale->user_name->name }}</td>
                                        <td>{{ $sale->sale_date }}</td>
                                        {{-- <td>
                                            @if (isset($sale->customer_id))
                                                @if(isset($sale->cust_name))
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
                                                @if(isset($sale->source_type))
                                                    {{ $sale->source_type->name }}
                                                @else
                                                    {{$sale->type}}
                                                @endif
                                            @endif
                                        </td> --}}
                                        <td>
                                            @if (isset($sale->plan_id))
                                                @if(isset($sale->plan_name))
                                                    {{ $sale->plan_name->name }}
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @if(isset($sale->gdpapers))
                                                @foreach ($sale->gdpapers as $gdpaper)
                                                    @if (isset($gdpaper->gdpaper_id))
                                                        @if(isset($gdpaper->gdpaper_name))
                                                            @if ($sale->plan_id != '4')
                                                                {{ $gdpaper->gdpaper_name->name }}({{ number_format($gdpaper->gdpaper_total) }})元<br>
                                                            @else
                                                                {{ $gdpaper->gdpaper_name->name }}({{ number_format($gdpaper->gdpaper_num) }})份<br>
                                                            @endif
                                                        @endif
                                                    @else
                                                        無
                                                    @endif
                                                @endforeach
                                            @endif
                                        </td>
                                        <td>
                                            @if(isset($sale->before_prom_id))
                                                @if(isset($sale->PromA_name))
                                                    {{ $sale->PromA_name->name }}-{{ number_format($sale->before_prom_price) }}
                                                @endif
                                            @endif
                                            @foreach ($sale->proms as $prom)
                                                @if ($prom->prom_type == 'A')
                                                    @if(isset($prom->prom_id))
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
                                                    @if(isset($prom->prom_id))
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
                                                    @if(isset($prom->prom_id))
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
                <div class="row">
                    <div class="col-12">
                        <div class="text-center mb-3">
                            <button type="button" class="btn w-sm btn-light waves-effect" onclick="history.go(-1)">回上一頁</button>
                        </div>
                    </div> <!-- end col -->
                </div>
                </div>
            </div>

                    

</div> <!-- container -->
@endsection