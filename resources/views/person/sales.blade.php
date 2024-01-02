@extends('layouts.vertical', ["page_title"=> "個人業務列表"])

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
                        <li class="breadcrumb-item active">個人業務列表</li>
                    </ol>
                </div>
                <h4 class="page-title">個人業務列表</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

                    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row justify-content-between">
                        <form action="{{ route('person.sales') }}" method="GET">
                                <div class="col-auto d-flex flex-wrap align-items-center">
                                        <div class="me-2">
                                            <label for="after_date" class="form-label">單號日期</label>
                                            <input type="date" class="form-control" id="after_date" name="after_date" value="{{ $request->after_date }}">
                                        </div>
                                        <div class="me-2">
                                            <label for="before_date" class="form-label">&nbsp;</label>
                                            <input type="date" class="form-control" id="before_date" name="before_date" value="{{ $request->before_date }}">
                                        </div>
                                        <div class="me-2">
                                            <label for="cust_mobile" class="form-label">客戶電話</label>
                                            <input type="text" class="form-control" id="cust_mobile" name="cust_mobile" value="{{ $request->cust_mobile }}">
                                        </div>
                                        <div class="me-2">
                                            <label for="sale_on" class="form-label">單號</label>
                                            <input type="text" class="form-control" id="sale_on" name="sale_on" value="{{ $request->sale_on }}">
                                        </div>
                                        <div class="me-2">
                                            <label for="pet_name" class="form-label">寶貝名稱</label>
                                            <input type="text" class="form-control" id="pet_name" name="pet_name" value="{{ $request->pet_name }}">
                                        </div>
                                        <div class="me-2">
                                            <label for="after_date">付款方式</label>
                                            <select id="inputState" class="form-select" name="pay_id" onchange="this.form.submit()">
                                                <option value="" @if (!isset($request->pay_id)) selected @endif>請選擇</option>
                                                <option value="A" @if($request->pay_id == 'A') selected @endif>一次付清</option>
                                                <option value="C" @if($request->pay_id == 'C') selected @endif>訂金</option>
                                                <option value="E" @if($request->pay_id == 'E') selected @endif>追加</option>
                                                <option value="D" @if($request->pay_id == 'D') selected @endif>尾款</option>
                                            </select>
                                        </div>
                                        <div class="me-2">
                                            <label for="after_date">狀態</label>
                                            <select id="inputState" class="form-select" name="status" onchange="this.form.submit()">
                                                <option value="not_check" @if (isset($request->status) || $request->status == 'not_check') selected @endif>未對帳</option>
                                                <option value="check" @if ($request->status == 'check') selected @endif>已對帳</option>
                                            </select>
                                        </div>
                                        <div class="me-3 mt-3">
                                            <button type="submit" class="btn btn-success waves-effect waves-light me-1"><i class="fe-search me-1"></i>搜尋</button>
                                        </div>
                                        <div class="col mt-3" style="text-align: right;">
                                            {{-- <button type="button" class="btn btn-success waves-effect waves-light me-1"><i class="mdi mdi-cog"></i></button> --}}
                                            <a href="{{ route('sale.create') }}" class="btn btn-danger waves-effect waves-light"><i class="mdi mdi-plus-circle me-1"></i>新增業務</a>
                                        </div>
                                </div>
                        </form>
                        <!-- end col-->
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
                                            @if ($sale->status != '9')
                                                <div class="btn-group dropdown">
                                                    <a href="javascript: void(0);" class="table-action-btn dropdown-toggle arrow-none btn btn-outline-secondary waves-effect" data-bs-toggle="dropdown" aria-expanded="false">動作 <i class="mdi mdi-arrow-down-drop-circle"></i></a>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a class="dropdown-item" href="{{ route('sale.edit',$sale->id) }}"><i class="mdi mdi-pencil me-2 text-muted font-18 vertical-middle"></i>編輯</a>
                                                        {{-- <a class="dropdown-item" href="#"><i class="mdi mdi-delete me-2 text-muted font-18 vertical-middle"></i>刪除</a> --}}
                                                        <a class="dropdown-item" href="{{ route('sale.del',$sale->id) }}"><i class="mdi mdi-delete me-2 font-18 text-muted vertical-middle"></i>刪除</a>
                                                        <a class="dropdown-item" href="{{ route('sale.check',$sale->id) }}"><i class="mdi mdi-send me-2 font-18 text-muted vertical-middle"></i>送出對帳</a>
                                                    </div>
                                                </div>
                                            @else
                                                <a href="{{ route('sale.check',$sale->id) }}"><button type="button" class="btn btn-secondary waves-effect waves-light">查看</button></a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <br>
                            <ul class="pagination pagination-rounded justify-content-end mb-0">
                                {{ $sales->appends($condition)->links('vendor.pagination.bootstrap-4') }}
                            </ul>
                        </div>
                    </div>
                </div>
                </div>
            </div>

                    

</div> <!-- container -->
@endsection