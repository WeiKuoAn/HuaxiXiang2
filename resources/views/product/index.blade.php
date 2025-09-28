@extends('layouts.vertical', ['page_title' => '商品列表'])

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
                            <li class="breadcrumb-item active">商品列表</li>
                        </ol>
                    </div>
                    <h4 class="page-title">商品列表</h4>
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
                                <form class="d-flex flex-wrap align-items-center" action="{{ route('product') }}"
                                    method="GET">
                                    <div class="me-3">
                                        <label for="start_date_start" class="form-label">商品名稱</label>
                                        <input type="text" class="form-control my-1 my-lg-0" id="inputPassword2"
                                            name="name" value="{{ $request->name }}">
                                    </div>
                                    <div class="me-3">
                                        <label for="start_date_start" class="form-label">商品類型</label>
                                        <select class="form-select my-1 my-lg-0" id="status-select" name="type"
                                            onchange="this.form.submit()">
                                            <option value="null" @if (!isset($request->type)) selected @endif>不限
                                            </option>
                                            <option value="normal" @if ($request->type == 'normal') selected @endif>一般
                                            </option>
                                            <option value="set" @if ($request->type == 'set') selected @endif>套組
                                            </option>
                                            {{-- <option value="combo" @if ($request->type == 'combo') selected @endif>組合
                                            </option> --}}
                                        </select>
                                    </div>
                                    <div class="me-3">
                                        <label for="start_date_start" class="form-label">商品類別</label>
                                        <select class="form-select my-1 my-lg-0" id="status-select" name="category_id"
                                            onchange="this.form.submit()">
                                            <option value="null" selected>不限</option>
                                            @foreach ($categorys as $category)
                                                <option value="{{ $category->id }}"
                                                    @if ($request->category_id == $category->id) selected @endif>{{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="me-3 mt-3">
                                        <button type="submit" class="btn btn-success waves-effect waves-light me-1"><i
                                                class="mdi mdi-cog me-1"></i>搜尋</button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-auto" style="margin-top: 26px;">
                                <div class="text-lg-end my-1 my-lg-0">
                                    {{-- <button type="button" class="btn btn-success waves-effect waves-light me-1"><i class="mdi mdi-cog"></i></button> --}}
                                    <a href="{{ route('product.create') }}"
                                        class="btn btn-danger waves-effect waves-light"><i
                                            class="mdi mdi-plus-circle me-1"></i>新增商品</a>
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
                        <div class="row mb-2">
                            <div class="col-sm-8">
                                {{-- <div class="mt-2 mt-sm-0">
                                <button type="button" class="btn btn-success mb-2 me-1"><i class="fe-search me-1"></i>搜尋</button>
                            </div> --}}
                            </div><!-- end col-->
                            <div class="col-sm-4 text-sm-end">
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-centered table-nowrap table-hover mb-0">
                                <thead class="table-light">
                                                                            <tr>
                                            <th>排序</th>
                                            <th>名稱</th>
                                            <th>類型</th>
                                            <th>產品類別</th>
                                            <th>後續處理類別/細項</th>
                                            <th>售價</th>
                                            <th>庫存</th>
                                            <th>細項</th>
                                            <th>計算佣金</th>
                                            <th>最近進貨日期</th>
                                            <th>排序</th>
                                            <th>狀態</th>
                                            <th>動作</th>
                                        </tr>
                                </thead>
                                <tbody>
                                    @foreach ($datas as $data)
                                        <tr>
                                            <td>{{ $data->seq }}</td>
                                            <td class="table-user">{{ $data->name }}</td>
                                            <td>
                                                @if ($data->type == 'normal')
                                                    一般
                                                @elseif($data->type == 'combo')
                                                    組合
                                                @elseif($data->type == 'set')
                                                    套組
                                                @endif
                                            </td>
                                            <td>
                                                @if (isset($data->category_id))
                                                    {{ $data->category_data->name }}
                                                @endif
                                            </td>
                                            <td>
                                                @if (isset($data->prom_data))
                                                    {{ $data->prom_data->prom_type->name }} / {{ $data->prom_data->name }}
                                                @endif
                                            </td>
                                            <td>
                                                @if ($data->has_variants)
                                                    <span class="badge bg-info">{{ $data->min_variant_price }} - {{ $data->max_variant_price }}</span>
                                                @else
                                                    {{ number_format($data->price) }}
                                                @endif
                                            </td>
                                            <td>
                                                @if ($data->has_variants)
                                                    <span class="badge bg-success">{{ $data->total_variants_stock }}</span>
                                                    <button class="btn btn-sm btn-outline-primary" onclick="showVariants({{ $data->id }})">
                                                        查看細項
                                                    </button>
                                                @else
                                                    @if ($data->stock == '1')
                                                        @if ($restocks[$data->id]['cur_num'] < 0)
                                                            <span class="text-danger">{{ $restocks[$data->id]['cur_num'] }}</span>
                                                        @else
                                                            {{ $restocks[$data->id]['cur_num'] }}
                                                        @endif
                                                    @else
                                                        -
                                                    @endif
                                                @endif
                                            </td>
                                            <td>
                                                @if ($data->has_variants)
                                                    <span class="badge bg-warning">{{ $data->variants_count }} 個細項</span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if ($data->commission == 0)
                                                    是
                                                @else
                                                    否
                                                @endif
                                            </td>
                                            <td>
                                                @if ($data->restock == 1)
                                                    @if ($data->restock_date() != null)
                                                        {{ $data->restock_date()->date }}
                                                    @endif
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ $data->seq }}</td>
                                            <td>
                                                @if ($data->status == 'up')
                                                    啟用
                                                @else
                                                    <b class="text-danger">停用</b>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group dropdown">
                                                    <a href="javascript: void(0);"
                                                        class="table-action-btn dropdown-toggle arrow-none btn btn-outline-secondary waves-effect"
                                                        data-bs-toggle="dropdown" aria-expanded="false">動作 <i
                                                            class="mdi mdi-arrow-down-drop-circle"></i></a>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        {{-- <a class="dropdown-item" href="#"><i class="mdi mdi-eye me-2 font-18 text-muted vertical-middle"></i>查看</a> --}}
                                                        <a class="dropdown-item"
                                                            href="{{ route('product.edit', $data->id) }}"><i
                                                                class="mdi mdi-pencil me-2 text-muted font-18 vertical-middle"></i>編輯</a>
                                                        <a class="dropdown-item" href="javascript:void(0);" onclick="showInventoryTraces({{ $data->id }})"><i class="mdi mdi-chart-line me-2 font-18 text-muted vertical-middle"></i>庫存軌跡</a>
                                                        {{-- <a class="dropdown-item" href="#"><i class="mdi mdi-delete me-2 text-muted font-18 vertical-middle"></i>刪除</a> --}}
                                                        <a class="dropdown-item"
                                                            href="{{ route('product.del', $data->id) }}"><i
                                                                class="mdi mdi-delete me-2 font-18 text-muted vertical-middle"></i>刪除</a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <ul class="pagination pagination-rounded justify-content-end mt-2 mb-0">
                            {{ $datas->appends($condition)->links('vendor.pagination.bootstrap-4') }}
                        </ul>
                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col -->
        </div>

    </div> <!-- container -->

    <!-- 細項詳情 Modal -->
    <div class="modal fade" id="variantsModal" tabindex="-1" aria-labelledby="variantsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="variantsModalLabel">商品細項詳情</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="variantsContent">
                        <!-- 細項內容將在這裡動態載入 -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">關閉</button>
                </div>
            </div>
        </div>
    </div>

    <!-- 庫存軌跡 Modal -->
    <div class="modal fade" id="inventoryTracesModal" tabindex="-1" aria-labelledby="inventoryTracesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="inventoryTracesModalLabel">庫存軌跡</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="tracesContent">
                        <!-- 軌跡內容將在這裡動態載入 -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">關閉</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
function showVariants(productId) {
    // 顯示載入中
    $('#variantsContent').html('<div class="text-center"><i class="mdi mdi-loading mdi-spin"></i> 載入中...</div>');
    $('#variantsModal').modal('show');
    
    // 發送 AJAX 請求獲取細項資料
    $.ajax({
        url: '{{ route("product.variants") }}',
        method: 'GET',
        data: { product_id: productId },
        success: function(response) {
            if (response.success) {
                var html = '<div class="table-responsive">';
                html += '<table class="table table-bordered">';
                html += '<thead class="table-light">';
                html += '<tr>';
                html += '<th>細項名稱</th>';
                html += '<th>顏色</th>';
                html += '<th>SKU</th>';
                html += '<th>價格</th>';
                html += '<th>成本</th>';
                html += '<th>庫存</th>';
                html += '<th>狀態</th>';
                html += '</tr>';
                html += '</thead>';
                html += '<tbody>';
                
                response.variants.forEach(function(variant) {
                    html += '<tr>';
                    html += '<td>' + variant.variant_name + '</td>';
                    html += '<td>' + (variant.color || '-') + '</td>';
                    html += '<td>' + (variant.sku || '-') + '</td>';
                    html += '<td>' + (variant.price ? 'NT$ ' + variant.price : '-') + '</td>';
                    html += '<td>' + (variant.cost ? 'NT$ ' + variant.cost : '-') + '</td>';
                    html += '<td>' + variant.stock_quantity + '</td>';
                    html += '<td>';
                    if (variant.status === 'active') {
                        html += '<span class="badge bg-success">啟用</span>';
                    } else {
                        html += '<span class="badge bg-danger">停用</span>';
                    }
                    html += '</td>';
                    html += '</tr>';
                });
                
                html += '</tbody>';
                html += '</table>';
                html += '</div>';
                
                $('#variantsContent').html(html);
            } else {
                $('#variantsContent').html('<div class="alert alert-danger">載入細項資料失敗</div>');
            }
        },
        error: function() {
            $('#variantsContent').html('<div class="alert alert-danger">載入細項資料失敗</div>');
        }
    });
}

function showInventoryTraces(productId) {
    // 顯示載入中
    $('#tracesContent').html('<div class="text-center"><i class="mdi mdi-loading mdi-spin"></i> 載入中...</div>');
    $('#inventoryTracesModal').modal('show');
    
    // 發送 AJAX 請求獲取庫存軌跡資料
    $.ajax({
        url: '{{ route("product.inventory-traces") }}',
        method: 'GET',
        data: { product_id: productId },
        success: function(response) {
            if (response.success) {
                var html = '';
                
                if (response.traces.length === 0) {
                    html = '<div class="alert alert-info">暫無庫存軌跡資料</div>';
                } else {
                    response.traces.forEach(function(productTraces) {
                        html += '<div class="mb-4">';
                        html += '<h6 class="text-primary">';
                        html += productTraces.product_name;
                        if (productTraces.variant_name) {
                            html += ' - ' + productTraces.variant_name;
                        }
                        html += '</h6>';
                        
                        if (productTraces.traces.length === 0) {
                            html += '<p class="text-muted">暫無軌跡記錄</p>';
                        } else {
                            html += '<div class="table-responsive">';
                            html += '<table class="table table-sm table-striped">';
                            html += '<thead class="table-light">';
                            html += '<tr><th>日期</th><th>類型</th><th>數量</th><th>說明</th></tr>';
                            html += '</thead>';
                            html += '<tbody>';
                            
                            productTraces.traces.forEach(function(trace) {
                                var typeClass = trace.type === 'inventory' ? 'text-success' : 'text-primary';
                                var typeIcon = trace.type === 'inventory' ? 'mdi mdi-clipboard-check' : 'mdi mdi-truck-delivery';
                                
                                html += '<tr>';
                                html += '<td>' + trace.date + '</td>';
                                html += '<td><i class="' + typeIcon + ' me-1 ' + typeClass + '"></i>' + trace.description + '</td>';
                                html += '<td class="text-end"><strong>' + trace.quantity + '</strong></td>';
                                html += '<td class="text-muted">' + trace.description + '記錄</td>';
                                html += '</tr>';
                            });
                            
                            html += '</tbody>';
                            html += '</table>';
                            html += '</div>';
                        }
                        
                        html += '</div>';
                    });
                }
                
                $('#tracesContent').html(html);
            } else {
                $('#tracesContent').html('<div class="alert alert-danger">載入庫存軌跡失敗</div>');
            }
        },
        error: function() {
            $('#tracesContent').html('<div class="alert alert-danger">載入庫存軌跡失敗</div>');
        }
    });
}
</script>
@endsection
