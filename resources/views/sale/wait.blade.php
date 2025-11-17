@extends('layouts.vertical', ['page_title' => '業務待確認對帳'])

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
                            <li class="breadcrumb-item active">業務待確認對帳</li>
                        </ol>
                    </div>
                    <h4 class="page-title">業務待確認對帳</h4>
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
                                <form class="d-flex flex-wrap align-items-center" action="{{ route('wait.sales') }}"
                                    method="GET">

                                    <div class="me-2">
                                        <label for="after_date" class="form-label">單號日期</label>
                                        <input type="date" class="form-control" id="after_date" name="after_date"
                                            value="{{ $request->after_date }}">
                                    </div>
                                    <div class="me-2">
                                        <label for="before_date" class="form-label">&nbsp;</label>
                                        <input type="date" class="form-control" id="before_date" name="before_date"
                                            value="{{ $request->before_date }}">
                                    </div>
                                    <div class="me-2">
                                        <label for="before_date" class="form-label">業務</label>
                                        <select id="inputState" class="form-select" name="user"
                                            onchange="this.form.submit()">
                                            <option value="null" @if (isset($request->user) || $request->user == '') selected @endif>請選擇
                                            </option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}"
                                                    @if ($request->user == $user->id) selected @endif>
                                                    {{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="me-3 mt-4">
                                        <button type="submit" class="btn btn-success waves-effect waves-light me-1"><i
                                                class="fe-search me-1"></i>搜尋</button>
                                    </div>
                                </form>

                            </div><!-- end col-->
                            <div class="col-auto mt-3">
                                <div class=" text-lg-end my-1 my-lg-0">
                                    <h3><span class="text-danger">共計：{{ number_format($total) }}元</span></h3>
                                </div>
                            </div>

                            <!-- end col-->
                        </div> <!-- end row -->
                    </div>
                </div> <!-- end card -->
            </div> <!-- end col-->
        </div>
        @foreach ($datas as $user_id => $data)
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"> {{ $data['name'] }}</h5>
                            @if (isset($data['items']))
                                <div class="table-responsive ">
                                    <table class="table table-centered table-nowrap table-hover mb-0 mt-2">
                                        <thead class="table-light">
                                            <tr>
                                                <th>單號</th>
                                                <th>日期</th>
                                                <th>客戶</th>
                                                <th>寶貝名</th>
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
                                            @foreach ($data['items'] as $sale)
                                                <tr>
                                                    <td>
                                                        @if ($sale->type_list == 'scrapped')
                                                            <span class="badge bg-danger">報廢</span> {{ $sale->sale_on }}
                                                        @else
                                                            {{ $sale->sale_on }}
                                                        @endif
                                                    </td>
                                                    <td>{{ $sale->sale_date }}</td>
                                                    <td>
                                                        @if (isset($sale->customer_id))
                                                            @if (isset($sale->cust_name))
                                                                {{ $sale->cust_name->name }}
                                                                @if($sale->type_list =='memorial')
                                                                    -追思
                                                                @endif
                                                            @else
                                                                {{ $sale->customer_id }}<b
                                                                    style="color: red;">（客戶姓名須重新登入）</b>
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (isset($sale->pet_name))
                                                            {{ $sale->pet_name }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (isset($sale->plan_id))
                                                            @if (isset($sale->plan_name))
                                                                {{ $sale->plan_name->name }}
                                                            @else
                                                                {{ $sale->plan_id }}
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
                                                            @else
                                                                {{ $sale->before_prom_id }}
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
                                                        @if (isset($sale->pay_id))
                                                            {{ $sale->pay_type() }}
                                                        @endif
                                                    </td>
                                                    <td>{{ number_format($sale->pay_price) }}</td>
                                                    <td>
                                                        @if ($sale->type_list == 'scrapped')
                                                            <button type="button"
                                                                onclick="openCheckModal({{ $sale->id }}, 'scrapped')"
                                                                class="btn btn-danger waves-effect waves-light">確認對帳</button>
                                                        @else
                                                            <button type="button"
                                                                onclick="openCheckModal({{ $sale->id }}, 'normal')"
                                                                class="btn btn-danger waves-effect waves-light">確認對帳</button>
                                                        @endif
                                                    </td>
                                                    {{-- <td>
                                                @if ($sale->type_list == 'scrapped')
                                                <a href="{{ route('sale.scrapped.check',$sale->id) }}">
                                                    <button type="button" 
                                                        class="btn btn-danger waves-effect waves-light">確認對帳</button>
                                                    </a>
                                                @else
                                                <a href="{{ route('sale.check',$sale->id) }}">
                                                    <button type="button" 
                                                        class="btn btn-danger waves-effect waves-light">確認對帳</button>
                                                    </a>
                                                @endif
                                            </td> --}}
                                                </tr>
                                            @endforeach
                                            <tr class="mb-3">
                                                <td colspan="6"></td>
                                                <td align="right"><b>共計：{{ number_format($data['count']) }}單</b></td>
                                                <td align="right"><b>現金：{{ number_format($data['cash_total']) }}元</b></td>
                                                <td align="right"><b>匯款：{{ number_format($data['transfer_total']) }}元</b>
                                                </td>
                                                <td align="right"><b>小計：{{ number_format($data['price']) }}元</b></td>
                                                <td align="right"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                            @if (isset($data['pay_datas']))
                                <div class="table-responsive mt-3">
                                    <table class="table table-centered table-nowrap table-hover mb-0 mt-2">
                                        <thead class="table-light">
                                            <tr>
                                                <th>No</th>
                                                <th>key單日期</th>
                                                <th>key單單號</th>
                                                <th>支出日期</th>
                                                <th>支出科目</th>
                                                <th width="20%">發票號碼</th>
                                                <th>支出總價格</th>
                                                <th width="15%">備註</th>
                                                <th width="10%">動作</th>
                                            </tr>
                                        </thead>
                                        @foreach ($data['pay_datas'] as $pay_key => $pay_data)
                                            <tbody>
                                                <tr>
                                                    <td>{{ $pay_key + 1 }}</td>
                                                    <td>{{ $pay_data->pay_date }}</td>
                                                    <td>{{ $pay_data->pay_on }}</td>
                                                    <td>
                                                        @if (isset($pay_data->pay_items))
                                                            @foreach ($pay_data->pay_items as $item)
                                                                {{ $item->pay_date }}<br>
                                                            @endforeach
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (isset($pay_data->pay_items))
                                                            @foreach ($pay_data->pay_items as $item)
                                                                @if (!empty($item->pay_id))
                                                                    {{ $item->pay_name->name }}<br>
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (isset($pay_data->pay_items))
                                                            @foreach ($pay_data->pay_items as $item)
                                                                @if (isset($item->pay_id))
                                                                    <b>{{ $item->invoice_number }}</b> -
                                                                    ${{ number_format($item->price) }}<br>
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    </td>
                                                    <td>{{ number_format($pay_data->price) }}</td>
                                                    <td>{{ $pay_data->comment }}</td>
                                                    <td>
                                                        <button type="button"
                                                            onclick="openPayModal({{ $pay_data->id }})"
                                                            class="btn btn-info btn-sm waves-effect waves-light">
                                                            <i class="fe-eye"></i> 審核
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        @endforeach
                                        <tr class="mb-3">
                                            <td colspan="5"></td>
                                            <td align="center">
                                                <b>共計：{{ number_format($data['pay_count']) }}單</b>
                                            </td>
                                            <td align="center">
                                                <b>小計：{{ number_format($data['pay_price']) }}元</b>
                                            </td>
                                            <td align="center"></td>
                                            <td align="center"></td>
                                        </tr>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div> <!-- container -->

    <!-- 確認對帳 Modal -->
    <div class="modal fade" id="checkModal" tabindex="-1" role="dialog" aria-labelledby="checkModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="checkModalLabel">確認對帳</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="checkModalBody" style="overflow-y: auto; max-height: calc(80vh);">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">載入中...</span>
                        </div>
                        <p class="mt-2">載入中，請稍候...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 查看支出 Modal -->
    <div class="modal fade" id="payModal" tabindex="-1" role="dialog" aria-labelledby="payModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="payModalLabel">查看支出Key單</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="payModalBody" style="overflow-y: auto; max-height: calc(80vh);">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">載入中...</span>
                        </div>
                        <p class="mt-2">載入中，請稍候...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function openCheckModal(saleId, type) {
            // 顯示 modal
            var checkModal = new bootstrap.Modal(document.getElementById('checkModal'));
            checkModal.show();

            // 重置 modal 內容為載入中
            $('#checkModalBody').html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">載入中...</span>
                </div>
                <p class="mt-2">載入中，請稍候...</p>
            </div>
        `);

            // 發送 Ajax 請求獲取對帳資料
            var url = type === 'scrapped' ?
                '{{ route('sale.scrapped.check.ajax', ':id') }}'.replace(':id', saleId) :
                '{{ route('sale.check.ajax', ':id') }}'.replace(':id', saleId);

            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    // 將返回的 HTML 填入 modal
                    $('#checkModalBody').html(response);

                    // 重新初始化表單內的 JavaScript（如果需要）
                    initializeModalForm();
                },
                error: function(xhr, status, error) {
                    $('#checkModalBody').html(`
                    <div class="alert alert-danger" role="alert">
                        <h4 class="alert-heading">載入失敗</h4>
                        <p>無法載入對帳資料，請稍後再試。</p>
                        <hr>
                        <p class="mb-0">錯誤訊息：${error}</p>
                    </div>
                `);
                    console.error('Ajax error:', error);
                }
            });
        }

        function initializeModalForm() {
            // 這裡初始化 modal 內表單需要的 JavaScript
            console.log('Modal form initialized');

            // 動態載入 select2 和其他必要的 CSS/JS
            if (!$('script[src*="select2.min.js"]').length) {
                $('<script>').attr('src', '{{ asset('assets/libs/select2/select2.min.js') }}').appendTo('head');
            }

            // 等待腳本載入後初始化
            setTimeout(function() {
                // 初始化 select2（如果有的話）
                if (typeof $.fn.select2 !== 'undefined') {
                    $('#checkModalBody select.select2').select2({
                        dropdownParent: $('#checkModal')
                    });
                }

                // 注意：不要使用 disabled，因為 disabled 的欄位不會被提交
                // 所有欄位已經在 modal 視圖中設置為 readonly

            }, 300);
        }

        // 處理 modal 內的表單提交（使用 click 事件而非 submit 事件）
        $(document).on('click', '#checkModalBody button[type="submit"]', function(e) {
            e.preventDefault();

            var clickedButton = $(this);
            var form = clickedButton.closest('form');
            var formData = new FormData(form[0]);

            // 手動添加被點擊按鈕的 name 和 value
            if (clickedButton.attr('name')) {
                formData.append(clickedButton.attr('name'), clickedButton.attr('value'));
            }

            // 禁用提交按鈕防止重複提交
            clickedButton.prop('disabled', true);

            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || form.find('input[name="_token"]').val()
                },
                success: function(response) {
                    if (response.success) {
                        // 關閉 modal
                        bootstrap.Modal.getInstance(document.getElementById('checkModal')).hide();

                        // 重新載入頁面
                        location.reload();
                    } else {
                        alert(response.message || '操作失敗，請稍後再試。');
                        clickedButton.prop('disabled', false);
                    }
                },
                error: function(xhr, status, error) {
                    var errorMessage = '提交失敗：' + error;
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    alert(errorMessage);
                    clickedButton.prop('disabled', false);
                }
            });
        });

        // 開啟支出視窗
        function openPayModal(payId) {
            // 顯示 modal
            var payModal = new bootstrap.Modal(document.getElementById('payModal'));
            payModal.show();

            // 重置 modal 內容為載入中
            $('#payModalBody').html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">載入中...</span>
                </div>
                <p class="mt-2">載入中，請稍候...</p>
            </div>
        `);

            // 發送 Ajax 請求獲取支出資料
            var url = '{{ route('pay.check.ajax', ':id') }}'.replace(':id', payId);

            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    // 將返回的 HTML 填入 modal
                    $('#payModalBody').html(response);

                    // 重新初始化表單內的 JavaScript（如果需要）
                    initializePayModalForm();
                },
                error: function(xhr, status, error) {
                    $('#payModalBody').html(`
                    <div class="alert alert-danger" role="alert">
                        <h4 class="alert-heading">載入失敗</h4>
                        <p>無法載入支出資料，請稍後再試。</p>
                        <hr>
                        <p class="mb-0">錯誤訊息：${error}</p>
                    </div>
                `);
                    console.error('Ajax error:', error);
                }
            });
        }

        // 初始化支出視窗表單
        function initializePayModalForm() {
            console.log('Pay modal form initialized');

            // 等待腳本載入後初始化
            setTimeout(function() {
                // 初始化 select2（如果有的話）
                if (typeof $.fn.select2 !== 'undefined') {
                    $('#payModalBody select.select2').select2({
                        dropdownParent: $('#payModal')
                    });
                }

                // 處理發票類型切換顯示
                var rowCount = $('#payModalBody #cart tr').length - 1;
                for (var i = 0; i < rowCount; i++) {
                    (function(index) {
                        var invoiceType = $("#pay_invoice_type-" + index).val();
                        updateInvoiceFields(index, invoiceType);

                        // 綁定發票類型改變事件
                        $("#pay_invoice_type-" + index).on('change', function() {
                            updateInvoiceFields(index, $(this).val());
                        });
                    })(i);
                }
            }, 300);
        }

        // 更新發票欄位顯示
        function updateInvoiceFields(index, invoiceType) {
            if (invoiceType == 'FreeUniform') {
                $("#vendor-" + index).show(300);
                $("#pay_invoice-" + index).hide(300);
            } else if (invoiceType == 'Uniform') {
                $("#pay_invoice-" + index).show(300);
                $("#vendor-" + index).show(300);
            } else if (invoiceType == 'Other') {
                $("#pay_invoice-" + index).hide(300);
                $("#vendor-" + index).hide(300);
            }
        }

        // 處理支出 modal 內的表單提交
        $(document).on('click', '#payModalBody button[type="submit"]', function(e) {
            e.preventDefault();

            var clickedButton = $(this);
            var form = clickedButton.closest('form');
            var formData = new FormData(form[0]);

            // 手動添加被點擊按鈕的 name 和 value
            if (clickedButton.attr('name')) {
                formData.append(clickedButton.attr('name'), clickedButton.attr('value'));
            }

            // 禁用提交按鈕防止重複提交
            clickedButton.prop('disabled', true);

            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || form.find('input[name="_token"]').val()
                },
                success: function(response) {
                    if (response.success) {
                        // 關閉 modal
                        bootstrap.Modal.getInstance(document.getElementById('payModal')).hide();

                        // 重新載入頁面
                        location.reload();
                    } else {
                        alert(response.message || '操作失敗，請稍後再試。');
                        clickedButton.prop('disabled', false);
                    }
                },
                error: function(xhr, status, error) {
                    var errorMessage = '提交失敗：' + error;
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    alert(errorMessage);
                    clickedButton.prop('disabled', false);
                }
            });
        });
    </script>
@endsection
