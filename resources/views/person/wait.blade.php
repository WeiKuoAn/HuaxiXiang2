@extends('layouts.vertical', ["page_title"=> "個人業務待確認對帳"])

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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">業務管理</a></li>
                        <li class="breadcrumb-item active">個人業務待確認對帳</li>
                    </ol>
                </div>
                <h4 class="page-title">個人業務待確認對帳</h4>
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
                                        <th>客戶</th>
                                        <th>寶貝名</th>
                                        <th>類別</th>
                                        <th>方案</th>
                                        <th>金紙</th>
                                        <th>後續處理A</th>
                                        <th>後續處理B</th>
                                        <th>付款方式</th>
                                        <th>實收價格</th>
                                        <th>動作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach ($sales as $sale)
                                    <tr>
                                        <td>{{ $sale->sale_on }}</td>
                                        <td>{{ $sale->user_name->name }}</td>
                                        <td>{{ $sale->sale_date }}</td>
                                        <td>
                                            @if (isset($sale->customer_id))
                                                @if(isset($sale->cust_name))
                                                    {{ $sale->cust_name->name }}
                                                @else
                                                    {{ $sale->customer_id }}<b style="color: red;">（客戶姓名須重新登入）</b>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @if (isset($sale->pet_name))
                                                {{ $sale->pet_name }}
                                            @endif
                                        </td>
                                        <td>
                                            @if (isset($sale->type))
                                                @if(isset($sale->source_type))
                                                    {{ $sale->source_type->name }}
                                                @else
                                                    {{$sale->type}}
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @if (isset($sale->plan_id))
                                                @if(isset($sale->plan_name))
                                                {{ $sale->plan_name->name }}
                                                @else
                                                {{$sale->plan_id}}
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @foreach ($sale->gdpapers as $gdpaper)
                                                @if (isset($gdpaper->gdpaper_id))
                                                    @if ($sale->plan_id != '4')
                                                        {{ $gdpaper->gdpaper_name->name }}({{ number_format($gdpaper->gdpaper_total) }})元<br>
                                                    @else
                                                        {{ $gdpaper->gdpaper_name->name }}({{ number_format($gdpaper->gdpaper_num) }})份<br>
                                                    @endif
                                                @else
                                                    無
                                                @endif
                                            @endforeach
                                        </td>
                                        <td>
                                            @if(isset($sale->before_prom_id))
                                                @if(isset($sale->PromA_name))
                                                {{ $sale->PromA_name->name }}-{{ number_format($sale->before_prom_price) }}
                                                @else
                                                {{$sale->before_prom_id}}
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
                                            @if (isset($sale->pay_id))
                                                {{ $sale->pay_type() }}
                                            @endif
                                        </td>
                                        <td>{{ number_format($sale->pay_price) }}</td>
                                        <td>
                                            <a href="{{ route('sale.check',$sale->id) }}"><button type="button" class="btn btn-secondary waves-effect waves-light">查看</button></a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>    
                        </div>
                    </div>
                </div>
                </div>
            </div>

                    

</div> <!-- container -->
@endsection