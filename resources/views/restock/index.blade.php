@extends('layouts.vertical', ["page_title"=> "商品進貨"])

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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">商品管理</a></li>
                        <li class="breadcrumb-item active">商品進貨</li>
                    </ol>
                </div>
                <h4 class="page-title">商品進貨</h4>
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
                            <form class="d-flex flex-wrap align-items-center" action="{{ route('product.restock') }}" method="GET">
                                <div class="me-3">
                                    <label for="start_date_start" class="form-label">進貨日期</label>
                                    <input type="date" class="form-control" id="after_date" name="after_date" value="{{ $request->after_date }}">
                                </div>
                                <div class="me-3">
                                    <label for="start_date" class="form-label">&nbsp;</label>
                                    <input type="date" class="form-control" id="before_date" name="before_date" value="{{ $request->before_date }}">
                                </div>
                                <div class="me-3 mt-3">
                                    <button type="submit" class="btn btn-success waves-effect waves-light me-1"><i class="fe-search me-1"></i>搜尋</button>
                                </div>
                            </form>
                        </div>
                        <div class="col-auto"  style="margin-top: 26px;">
                            <div class="text-lg-end my-1 my-lg-0">
                                {{-- <button type="button" class="btn btn-success waves-effect waves-light me-1"><i class="mdi mdi-cog"></i></button> --}}
                                <a href="{{ route('product.restock.create') }}">
                                    <button type="button" class="btn btn-danger waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#custom-modal"><i class="mdi mdi-plus-circle me-1"></i>新增進貨</button>
                                </a>
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
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>進貨日期</th>
                                    <th>名稱與進貨數量</th>
                                    <th>總價格</th>
                                    <th>支付價格</th>
                                    <th>付款方式</th>
                                    <th>備註</th>
                                    <th>動作</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($datas as $key=>$data)
                                <tr>
                                    <td>{{ $data->date }}</td>
                                    <td>
                                        @if(isset($data->product_id))
                                            {{ $data->product_data->name }}（{{ $data->num }}個）
                                        @else
                                            @foreach($data->restock_items as $restock_item)
                                                {{ $restock_item->product_data->name }}
                                                @if($restock_item->variant_id && $restock_item->variant)
                                                    - {{ $restock_item->variant->variant_name }}
                                                @endif
                                                （{{ $restock_item->product_num }}個）<br>
                                            @endforeach
                                        @endif
                                    </td>
                                    <td>
                                        {{ number_format($data->total) }}元
                                    </td>
                                    <td>
                                        @if(isset($data->pay_price))
                                        {{ number_format($data->restock_pay_price()) }}元
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($data->pay_id))
                                        {{ $data->pay_type() }}
                                        @endif
                                    </td>
                                    <td>{{ $data->comment }}</td>
                                    <td>
                                        <div class="btn-group dropdown">
                                            <a href="javascript: void(0);" class="table-action-btn dropdown-toggle arrow-none btn btn-outline-secondary waves-effect" data-bs-toggle="dropdown" aria-expanded="false">動作 <i class="mdi mdi-arrow-down-drop-circle"></i></a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item" href="{{ route('product.restock.edit',$data->id) }}"><i class="mdi mdi-pencil me-2 text-muted font-18 vertical-middle"></i>編輯</a>
                                                {{-- <a class="dropdown-item" href="#"><i class="mdi mdi-delete me-2 text-muted font-18 vertical-middle"></i>刪除</a> --}}
                                                <a class="dropdown-item" href="{{ route('product.restock.pay.create',$data->id) }}"><i class="mdi mdi-cash-plus me-2 font-18 text-muted vertical-middle"></i>新增付款</a>
                                                <a class="dropdown-item" href="{{ route('product.restock.pay',$data->id) }}"><i class="mdi mdi-cash me-2 font-18 text-muted vertical-middle"></i>檢視付款</a>
                                                <a class="dropdown-item" href="{{ route('product.restock.del',$data->id) }}"><i class="mdi mdi-delete me-2 font-18 text-muted vertical-middle"></i>刪除</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <br>
                        <ul class="pagination pagination-rounded justify-content-end mb-0">
                            {{ $datas->links('vendor.pagination.bootstrap-4') }}
                        </ul>
                    </div>

                    

                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col -->
    </div>
    <!-- end row -->

</div> <!-- container -->
@endsection