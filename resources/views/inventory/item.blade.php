@extends('layouts.vertical', ["page_title"=> "編輯商品庫存數量"])

@section('css')
<!-- third party css -->
<link href="{{asset('assets/libs/dropzone/dropzone.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/libs/select2/select2.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/libs/flatpickr/flatpickr.min.css')}}" rel="stylesheet" type="text/css" />
<!-- third party css end -->
@endsection

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
                        <li class="breadcrumb-item active">編輯商品庫存數量</li>
                    </ol>
                </div>
                <h4 class="page-title">編輯商品庫存數量</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    
    <div class="row">
        <div class="col-12">
            <form class="row g-3  pb-1" action="{{ route('inventoryItem.edit.data',$inventory_no) }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">

                        <table class="table table-centered mb-0">
                            @php
                                $grouped = $datas->groupBy('product_id');
                                // 根據盤點狀態判斷是否顯示送出按鈕
                                $hasPending = $inventory_data->state == 0; // 只有未完成的盤點才顯示送出按鈕
                                $rowIndex = 1;
                            @endphp
                            <thead>
                                <tr align="center">
                                    <th scope="col">編號</th>
                                    <th scope="col">商品名稱</th>
                                    <th scope="col">商品原庫存數量</th>
                                    <th scope="col" width="25%">盤點新數量</th>
                                    <th scope="col" width="25%">備註</th>
                                </tr>
                            </thead>
                            <tbody align="center">
                                @foreach ($grouped as $productId => $items)
                                    @php
                                        $first = $items->first();
                                        $productName = optional($first->gdpaper_name)->name ?? '未知商品';
                                        $hasVariants = $items->contains(function($i){ return !empty($i->variant_id); });
                                    @endphp

                                    @if($hasVariants)
                                        <tr>
                                            <td colspan="5" style="text-align:left; font-weight:600; background:#f9f9f9;">
                                                {{ $productName }}
                                            </td>
                                        </tr>
                                        @foreach ($items as $item)
                                            @if(!empty($item->variant_id))
                                                <tr>
                                                    <td>{{ $rowIndex++ }}</td>
                                                    <td>
                                                        {{ $productName }} - {{ optional($item->variant)->variant_name ?? ('細項#'.$item->variant_id) }}
                                                    </td>
                                                    <td>{{ $item->old_num }}</td>
                                                    @if($inventory_data->state == 0)
                                                        <td>
                                                            <input type="text" class="form-control" name="variant[{{ $item->variant_id }}]" value="{{ $item->new_num }}" required>
                                                        </td>
                                                    @else
                                                        <td>{{ $item->new_num }}</td>
                                                    @endif
                                                    @if($inventory_data->state == 0)
                                                        <td>
                                                            <input type="text" class="form-control" name="comment_variant[{{ $item->variant_id }}]" value="{{ $item->comment }}">
                                                        </td>
                                                    @else
                                                        <td>{{ $item->comment }}</td>
                                                    @endif
                                                </tr>
                                            @endif
                                        @endforeach
                                    @else
                                        @foreach ($items as $item)
                                            <tr>
                                                <td>{{ $rowIndex++ }}</td>
                                                <td>{{ $productName }}</td>
                                                <td>{{ $item->old_num }}</td>
                                                @if($inventory_data->state == 0)
                                                    <td>
                                                        <input type="text" class="form-control" name="product[{{ $item->product_id }}]" value="{{ $item->new_num }}" required>
                                                    </td>
                                                @else
                                                    <td>{{ $item->new_num }}</td>
                                                @endif
                                                @if($inventory_data->state == 0)
                                                    <td>
                                                        <input type="text" class="form-control" name="comment[{{ $item->product_id }}]" value="{{ $item->comment }}">
                                                    </td>
                                                @else
                                                    <td>{{ $item->comment }}</td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div> <!-- end .table-responsive-->
                </div> <!-- end card-body -->
                <div class="row col-lg-12 mx-auto mb-4">
                    <div class="col-auto me-auto"></div>
                        @if($hasPending)
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary">送出盤點</button>
                            </div>
                        @endif
                        <div class="col-auto">
                            <button type="button" class="btn btn-secondary" onclick="history.go(-1)">回上一頁</button>
                        </div>
                </div>
            </div> <!-- end card -->
        </div> <!-- end col -->
    </div> <!-- end row -->
</form>

</div> <!-- container -->
@endsection

@section('script')
<!-- third party js -->
<script src="{{asset('assets/libs/jquery-tabledit/jquery-tabledit.min.js')}}"></script>
<!-- third party js ends -->

<!-- demo app -->
<script src="{{asset('assets/js/pages/tabledit.init.js')}}"></script>
<!-- end demo js-->
@endsection