@extends('layouts.vertical', ['page_title' => '編輯支出key單'])

@section('css')
    {{-- <link href="{{asset('assets/libs/select2/select2.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/libs/dropzone/dropzone.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/libs/quill/quill.min.css')}}" rel="stylesheet" type="text/css" /> --}}
@endsection

@section('content')

    <style>
        @media screen and (max-width:768px) {
            .mobile {
                width: 120px;
            }
        }
    </style>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Huaxixiang</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">支出管理</a></li>
                            <li class="breadcrumb-item active">編輯支出Key單</li>
                        </ol>
                    </div>
                    <h5 class="page-title">編輯支出Key單</h5>
                </div>
            </div>
        </div>

        <form action="{{ route('pay.edit.data', $data->id) }}" method="POST" id="your-form" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase bg-light p-2 mt-0 mb-3">支出總資訊</h5>
                            <div class="row">
                                <div class="mb-3 col-md-3">
                                    <label for="pay_on" class="form-label">支出單號<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="pay_on" name="pay_on"
                                        value="{{ $data->pay_on }}" readonly>
                                </div>
                                <div class="mb-3 col-md-3">
                                    <label for="price" class="form-label">總金額<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="price" name="price"
                                        value="{{ $data->price }}" required>
                                </div>
                                <div class="mb-3 col-md-3">
                                    <label for="comment" class="form-label">用途說明</label>
                                    <input type="text" class="form-control" id="comment" name="comment"
                                        value="{{ $data->comment }}">
                                </div>
                                <div class="mb-3 col-md-3">
                                    <label for="user_id" class="form-label">服務專員<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="user_id" name="user_id"
                                        value="{{ $data->user_name->name }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase bg-light p-2 mt-0 mb-3">發票清單</h5>
                            <div class="table-responsive">
                                <table id="cart" class="table cart-list">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>消費日期<span class="text-danger">*</span></th>
                                            <th>會計項目<span class="text-danger">*</span></th>
                                            <th>支出金額<span class="text-danger">*</span></th>
                                            <th>發票類型<span class="text-danger">*</span></th>
                                            <th>發票號碼 / 統編</th>
                                            <th>備註</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (isset($data->pay_items))
                                            @foreach ($data->pay_items as $key => $item)
                                                <tr id="row-{{ $key }}">
                                                    <td>
                                                        <button class="btn btn-primary del-row" alt="{{ $key }}"
                                                            type="button" onclick="del_row(this)">刪除</button>
                                                    </td>
                                                    <td><input type="date" class="form-control" name="pay_data_date[]"
                                                            id="pay_date-{{ $key }}" value="{{ $item->pay_date }}"
                                                            required></td>
                                                    <td>
                                                        <select class="form-select" name="pay_id[]"
                                                            id="pay_id-{{ $key }}" required>
                                                            <option value="" selected>請選擇...</option>
                                                            @foreach ($pays as $pay)
                                                                <option value="{{ $pay->id }}"
                                                                    @if ($pay->id == $item->pay_id) selected @endif>
                                                                    {{ $pay->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td><input type="text" class="form-control" name="pay_price[]"
                                                            id="pay_price-{{ $key }}" value="{{ $item->price }}"
                                                            required></td>
                                                    <td>
                                                        <select class="form-select" name="pay_invoice_type[]"
                                                            id="pay_invoice_type-{{ $key }}"
                                                            onchange="chgInvoice(this)" required>
                                                            <option value="" selected>請選擇</option>
                                                            <option value="FreeUniform"
                                                                @if ($item->invoice_type == 'FreeUniform') selected @endif>免用統一發票
                                                            </option>
                                                            <option value="Uniform"
                                                                @if ($item->invoice_type == 'Uniform') selected @endif>統一發票
                                                            </option>
                                                            <option value="Other"
                                                                @if ($item->invoice_type == 'Other') selected @endif>其他
                                                            </option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control invoice"
                                                            name="pay_invoice_number[]"
                                                            id="pay_invoice-{{ $key }}"
                                                            value="{{ $item->invoice_number }}">
                                                        <input type="text" class="form-control vendor"
                                                            name="vender_id[]" id="vendor-{{ $key }}"
                                                            value="{{ $item->vender_id }}">
                                                    </td>
                                                    <td><input type="text" class="form-control" name="pay_text[]"
                                                            value="{{ $item->comment }}"></td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" id="add_row" class="btn btn-secondary mt-2">新增筆數</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mb-3">
                <button type="button" class="btn btn-light" onclick="history.go(-1)">回上一頁</button>
                <button type="submit" class="btn btn-success">編輯</button>
            </div>

        </form>
    </div>

@endsection

@section('script')
    <!-- third party js -->
    <script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/libs/dropzone/dropzone.min.js') }}"></script>
    <script src="{{ asset('assets/libs/footable/footable.min.js') }}"></script>
    <!-- third party js ends -->

    <!-- demo app -->
    <script src="{{ asset('assets/js/pages/form-fileuploads.init.js') }}"></script>
    <script src="{{ asset('assets/js/pages/add-product.init.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.1/themes/smoothness/jquery-ui.css" />
    {{-- <script src="{{asset('assets/js/pages/foo-tables.init.js')}}"></script> --}}


    <script>
        function del_row(obj) {
            let number = $(obj).attr("alt");
            $('#row-' + number).remove();
            calculateTotal();
        }

        // Calculate total price
        function calculateTotal() {
            let total = 0;
            $('input[name="pay_price[]"]').each(function() {
                total += parseFloat($(this).val()) || 0;
            });
            $('#price').val(total);
        }

        // 當輸入框值改變時，重新計算總金額
        $(document).on('keyup change', '[id^=pay_price-]', function() {
            calculateTotal();
        });

        $(document).ready(function() {
            rowCount = $('#cart tr').length - 1;

            // Ensure the correct invoice visibility on page load
            for (let i = 0; i < rowCount; i++) {
                invoice_type = $("#pay_invoice_type-" + i).val();
                chgInvoice("#pay_invoice_type-" + i); // Update visibility based on the invoice type
            }

            // Add new row event
            $("#add_row").click(function() {
                let rowCount = $('#cart tr').length;
                console.log(rowCount);
                
                let lastRow = $("#cart tr:last");

                let newRow = `
                <tr id="row-${rowCount}">
                    <td>
                        <button class="mobile btn btn-primary del-row" alt="${rowCount}" type="button" onclick="del_row(this)">刪除</button>
                    </td>
                    <td scope="row">
                        <input id="pay_date-${rowCount}" class="mobile form-control" type="date" name="pay_data_date[]" value="" required>
                    </td>
                    <td>
                        <select id="pay_id-${rowCount}" class="form-select" aria-label="Default select example" name="pay_id[]" required>
                            @foreach ($pays as $pay)
                            <option value="{{ $pay->id }}">{{ $pay->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input id="pay_price-${rowCount}" class="mobile form-control" type="text" name="pay_price[]" required onkeyup="calculateTotal()" onchange="calculateTotal()">
                    </td>
                    <td>
                        <select id="pay_invoice_type-${rowCount}" alt="${rowCount}" class="form-select" name="pay_invoice_type[]" onchange="chgInvoice(this)" required>
                            <option value="" selected>請選擇</option>
                            <option value="FreeUniform">免用統一發票</option>
                            <option value="Uniform">統一發票</option>
                            <option value="Other">其他</option>
                        </select>
                    </td>
                    <td>
                        <input style="display: none;" id="pay_invoice-${rowCount}" class="invoice form-control" type="text" name="pay_invoice_number[]" value="" placeholder="請輸入發票號碼">
                        <input style="display: none;" list="vender_number_list_q" class="vendor form-control" id="vendor-${rowCount}" name="vender_id[]" placeholder="請輸入統編號碼">
                    </td>
                    <td>
                        <input id="pay_text-${rowCount}" class="form-control" type="text" name="pay_text[]" value="">
                    </td>
                </tr>`;
                lastRow.after(newRow); // Add new row at the end
            });

            // Form submission validation
            $("#btn_submit").click(function() {
                let total_price = $("#price").val();
                let pay_total = 0;
                rowCount = $('#cart tr').length - 1;

                for (var i = 0; i < rowCount; i++) {
                    pay_total += parseFloat($('#pay_price-' + i).val()) || 0;
                }

                if (total_price != pay_total) {
                    alert('金額錯誤！');
                    return false;
                }
            });

            $.ajaxSetup({
                headers: {
                    'csrftoken': '{{ csrf_token() }}'
                }
            });
        });

        function del_row(obj) {
            let number = $(obj).attr("alt");
            $('#row-' + number).remove();
            calculateTotal(); // Recalculate total after deleting a row
        }

        function chgInvoice(obj) {
            let number = $(obj).attr("alt");
            let invoice_type = $("#pay_invoice_type-" + number).val();

            if (invoice_type == 'FreeUniform') {
                $("#vendor-" + number).show(300).prop('required', true);
                $("#pay_invoice-" + number).hide(300);
            } else if (invoice_type == 'Uniform') {
                $("#vendor-" + number).show(300).prop('required', true);
                $("#pay_invoice-" + number).show(300).prop('required', true);
            } else {
                $("#vendor-" + number).hide(300).prop('required', false);
                $("#pay_invoice-" + number).hide(300).prop('required', false);
            }
        }
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            rowCount = $('#cart tr').length - 1;
            for (var i = 0; i < rowCount; i++) {
                $('#vendor-' + i).keydown(function() {
                    $value = $(this).val();
                    $.ajax({
                        type: 'get',
                        url: '{{ route('vender.number') }}',
                        data: {
                            'number': $value
                        },
                        success: function(data) {
                            $('#vender_number_list_q').html(data);
                        }
                    });
                });
            }
        });
    </script>
@endsection
