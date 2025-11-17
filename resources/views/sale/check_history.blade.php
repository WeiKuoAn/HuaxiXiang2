@extends('layouts.vertical', ['page_title' => '查看對帳明細'])

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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">報表管理</a></li>
                            <li class="breadcrumb-item active">查看對帳明細</li>
                        </ol>
                    </div>
                    <h4 class="page-title">查看對帳明細</h4>
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
                                <form class="d-flex flex-wrap align-items-center" id="myForm"
                                    action="{{ route('sales.checkHistory') }}" method="GET">
                                    <label for="status-select" class="me-2">日期區間</label>
                                    <div class="me-2">
                                        <input type="date" class="form-control my-1 my-lg-0" id="inputPassword2"
                                            name="after_date"
                                            @if (!isset($request->after_date)) value="{{ $firstDay->format('Y-m-d') }}" @endif
                                            value="{{ $request->after_date }}">
                                    </div>
                                    <label for="status-select" class="me-2">至</label>
                                    <div class="me-3">
                                        <input type="date" class="form-control my-1 my-lg-0" id="inputPassword2"
                                            name="before_date"
                                            @if (!isset($request->before_date)) value="{{ $lastDay->format('Y-m-d') }}" @endif
                                            value="{{ $request->before_date }}">
                                    </div>
                                    <label for="status-select" class="me-2">對帳人員</label>
                                    <div class="me-4">
                                        <select id="inputState" class="form-select" name="check_user_id"
                                            onchange="this.form.submit()">
                                            <option value="null" @if (isset($request->check_user_id) || $request->check_user_id == '') selected @endif>請選擇
                                            </option>
                                            @foreach ($check_users as $check_user)
                                                <option value="{{ $check_user->id }}"
                                                    @if ($request->check_user_id == $check_user->id) selected @endif>
                                                    {{ $check_user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="me-3">
                                        <button type="submit" onclick="CheckSearch(event)"
                                            class="btn btn-success waves-effect waves-light me-1"><i
                                                class="fe-search me-1"></i>搜尋</button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-auto">
                                <div class="text-lg-end my-1 my-lg-0">
                                    <h3><span
                                            class="text-danger">業務共{{ number_format($sums['count']) }}單，支出共{{ number_format($sums['pay_count']) }}單，總計：{{ number_format($sums['actual_price']) }}元</span>
                                    </h3>
                                </div>
                            </div><!-- end col-->
                        </div> <!-- end row -->
                    </div>
                </div> <!-- end card -->
            </div> <!-- end col-->
        </div>

        <div class="row">
            <div class="col-12">
                @foreach ($datas as $date => $data)
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"> {{ $data['name'] }}</h5>
                            @if (isset($data['items']))
                                <div class="table-responsive ">
                                    <table class="table table-centered table-nowrap table-hover mb-0 mt-2">
                                        <thead class="table-light">
                                            <tr align="center">
                                                <th width="5%">No</th>
                                                <th width="10%">日期</th>
                                                <th width="10%">單號</th>
                                                <th width="15%">客戶名稱</th>
                                                <th width="10%">寶貝名</th>
                                                <th width="10%">方案</th>
                                                <th width="10%">業務價格</th>
                                                <th width="10%">對帳人員</th>
                                                <th width="10%">業務詳情</th>
                                            </tr>
                                        </thead>
                                        @foreach ($data['items'] as $key => $item)
                                            <tbody>
                                                <tr>
                                                    <td align="center">{{ $key + 1 }}</td>
                                                    <td align="center">{{ $item->sale_date }}</td>
                                                    <td align="center">
                                                        @if ($item->type_list == 'scrapped')
                                                            <span class="badge bg-danger">報廢</span> {{ $item->sale_on }}
                                                        @else
                                                            {{ $item->sale_on }}
                                                        @endif
                                                    </td>
                                                    <td align="center">
                                                        @if (isset($item->cust_name))
                                                            {{ $item->cust_name->name }}
                                                        @endif
                                                    </td>
                                                    <td align="center">{{ $item->pet_name }}</td>
                                                    <td align="center">
                                                        @if (isset($item->plan_name))
                                                            {{ $item->plan_name->name }}
                                                        @elseif($item->pay_id == 'D')
                                                            尾款
                                                        @elseif($item->pay_id == 'E')
                                                            追加
                                                        @endif
                                                    </td>
                                                    <td align="center">{{ number_format($item->pay_price) }}</td>
                                                    <td align="center">{{ $item->check_user_name->name }}</td>
                                                    <td align="center">
                                                        <a href="javascript:void(0)"
                                                            onclick="openCheckModal({{ $item->id }}, 'normal')">
                                                            <i class="mdi mdi-eye me-2 text-muted font-18 vertical-middle"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                        @endforeach
                                        </tbody>
                                        <tr class="mb-3">
                                            <td colspan="5"></td>
                                            <td align="center"><b>共計：{{ number_format($data['count']) }}單</b></td>
                                            <td align="center"><b>現金：{{ number_format($data['cash_total']) }}元</b></td>
                                            <td align="center"><b>匯款：{{ number_format($data['transfer_total']) }}元</b></td>
                                            <td align="center"><b>小計：{{ number_format($data['price']) }}元</b></td>
                                        </tr>
                                    </table><br>
                                </div>
                            @endif

                            @if (isset($data['pay_items']))
                                <div class="table-responsive ">
                                    <table class="table table-centered table-nowrap table-hover mb-0 mt-2">
                                        <thead class="table-light">
                                            <tr align="center">
                                                <th width="5%">No</th>
                                                <th width="10%">key單日期</th>
                                                <th width="10%">單號</th>
                                                <th width="15%">支出日期</th>
                                                <th width="10%">支出科目</th>
                                                <th width="10%">發票號碼</th>
                                                <th width="10%">支出總價格</th>
                                                <th width="10%">備註</th>
                                                <th width="10%">支出詳情</th>
                                            </tr>
                                        </thead>
                                        @foreach ($data['pay_items'] as $pay_key => $pay_item)
                                            <tbody>
                                                <tr>
                                                    <td align="center">{{ $pay_key + 1 }}</td>
                                                    <td align="center">{{ $pay_item->pay_data_date }}</td>
                                                    <td align="center">{{ $pay_item->pay_on }}</td>
                                                    <td align="center">{{ $pay_item->pay_date }}</td>
                                                    <td align="center">{{ $pay_item->pay_name }}</td>
                                                    <td align="center">{{ $pay_item->invoice_number }}</td>
                                                    <td align="center">{{ number_format($pay_item->price) }}</td>
                                                    <td align="center">{{ $pay_item->comment }}</td>
                                                    <td align="center">
                                                        <a href="javascript:void(0)"
                                                            onclick="openPayModal({{ $pay_item->pay_data_id }})">
                                                            <i class="mdi mdi-eye me-2 text-muted font-18 vertical-middle"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                        @endforeach
                                        </tbody>
                                        <tr class="mb-3">
                                            <td colspan="5"></td>
                                            <td align="center"><b>共計：{{ number_format($data['pay_count']) }}單</b></td>
                                            <td align="center"></td>
                                            <td align="center"></td>
                                            <td align="center"><b>小計：{{ number_format($data['pay_price']) }}元</b></td>
                                        </tr>
                                    </table><br>
                                </div>
                            @endif
                            <div class="row">
                                <div class="card mb-0">
                                    <div class="card-body">
                                        <div class="col-12 text-end">
                                            <h4 class="card-title text-danger">
                                                現金實收：{{ number_format($data['cash_actual_price'] ?? 0) }}元</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div> <!-- container -->

    <!-- 確認對帳 Modal -->
    <div class="modal fade" id="checkModal" tabindex="-1" role="dialog" aria-labelledby="checkModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="checkModalLabel">業務詳情</h5>
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
        function CheckSearch(event) {
            // 檢查日期是否有效
            var afterDate = document.querySelector('input[name="after_date"]').value;
            var beforeDate = document.querySelector('input[name="before_date"]').value;

            if (afterDate && beforeDate && afterDate > beforeDate) {
                event.preventDefault();
                alert('開始日期不能大於結束日期');
                return false;
            }

            return true;
        }

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
                            <p>無法載入業務詳情，請稍後再試。</p>
                            <hr>
                            <p class="mb-0">錯誤訊息：${error}</p>
                        </div>
                    `);
                    console.error('Ajax error:', error);
                }
            });
        }

        function initializeModalForm() {
            // 重新初始化 Select2（如果需要）
            setTimeout(function() {
                if ($.fn.select2) {
                    $('#checkModalBody select.select2').select2({
                        dropdownParent: $('#checkModal')
                    });
                }
            }, 300);
        }

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
            // 重新初始化 Select2（如果需要）
            setTimeout(function() {
                if ($.fn.select2) {
                    $('#payModalBody select.select2').select2({
                        dropdownParent: $('#payModal')
                    });
                }
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

        // 處理支出 modal 內的表單提交（使用 click 事件而非 submit 事件）
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
