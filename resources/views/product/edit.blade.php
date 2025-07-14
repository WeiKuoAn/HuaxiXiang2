@extends('layouts.vertical', ['page_title' => 'Add & Edit Products'])

@section('css')
    <!-- third party css -->
    <link href="{{ asset('assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/dropzone/dropzone.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/quill/quill.min.css') }}" rel="stylesheet" type="text/css" />
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
                            <li class="breadcrumb-item active">編輯商品</li>
                        </ol>
                    </div>
                    <h4 class="page-title">編輯商品</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->


        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="text-uppercase bg-light p-2 mt-0 mb-3">商品資訊</h5>
                        <form action="{{ route('product.data.edit', $data->id) }}" method="POST" id="product-form"
                            enctype="multipart/form-data" data-plugin="dropzone" data-previews-container="#file-previews"
                            data-upload-preview-template="#uploadPreviewTemplate">
                            @csrf
                            <div class="row">
                                <div class="mb-3 col-md-4">
                                    <label for="type" class="form-label">商品類型<span class="text-danger">*</span></label>
                                    <select id="type" class="form-select" name="type" required>
                                        <option value="normal" @if ($data->type == 'normal') selected @endif>一般</option>
                                        <option value="set" @if ($data->type == 'set') selected @endif>套組</option>
                                        <option value="combo" @if ($data->type == 'combo') selected @endif>組合</option>
                                        {{-- <option value="online" @if ($data->type == 'online') selected @endif>數位</option>
                                <option value="service" @if ($data->type == 'service') selected @endif>服務</option> --}}
                                    </select>
                                    <input type="hidden" name="type_hidden" id="type_hidden" value="{{ $data->type }}">
                                </div>
                                <div class="mb-3 col-md-4">
                                    <label for="name" class="form-label">商品名稱<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ $data->name }}" required>
                                </div>

                                <div class="mb-3 col-md-4">
                                    <label for="on" class="form-label">商品代碼<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="number" name="number"
                                        value="{{ $data->number }}" readonly>
                                </div>
                            </div>

                            <div class="row mt-1">
                                <div class="mb-3 col-md-4">
                                    <label for="inputState" class="form-label">商品類別<span
                                            class="text-danger">*</span></label>
                                    <select id="inputState" class="form-select" name="category_id" required>
                                        <option value="" selected>請選擇...</option>
                                        @foreach ($categorys as $category)
                                            <option value="{{ $category->id }}"
                                                @if ($data->category_id == $category->id) selected @endif>{{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3 col-md-4">
                                    <label for="price" class="form-label">商品價格<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="price" name="price"
                                        value="{{ $data->price }}" required>
                                </div>

                                <div class="mb-3 col-md-4">
                                    <label for="seq" class="form-label">對應安葬處理/後續處理</label>
                                    <input type="number" class="form-control" id="seq" name="seq"
                                        value="{{ $data->seq }}">
                                </div>


                                {{-- <div class="mb-3 col-md-4">
                            <label for="alarm" class="form-label">警報數量</label>
                            <input type="number" class="form-control" id="alarm" name="alarm_num" value="{{ $data->alarm_num }}">
                        </div> --}}
                                <div class="mb-3 col-md-4">
                                    <label for="promTypes" class="form-label">安葬處理/後續處理類別</label>
                                    <select id="promTypes" class="form-select" name="promTypes">
                                        <option value="" selected>請選擇...</option>
                                        @foreach ($promTypes as $promType)
                                            <option value="{{ $promType->code }}"
                                                @if (isset($data->prom_id) && ($data->prom_data->type == $promType->code)) selected @endif>{{ $promType->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3 col-md-4">
                                    <label for="prom_id" class="form-label">安葬處理/後續處理細項</label>
                                    <select id="prom_id" class="form-select" name="prom_id">
                                        @foreach ($proms as $prom)
                                            <option value="{{ $prom->id }}"
                                                @if( $data->prom_id == $prom->id) selected @endif>{{ $prom->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3" id="combo">
                                <div class="mb-3 mt-1">
                                    <label for="lims_productcodeSearch" class="form-label">請輸入商品名稱或代碼<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="lims_productcodeSearch"
                                        name="product_code_name">
                                </div>

                                <table id="demo-foo-addrow"
                                    class="order-list table table-centered table-striped table-bordered mt-3 toggle-circle"
                                    data-page-size="7">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="min-width"></th>
                                            <th>商品名稱</th>
                                            <th>數量</th>
                                            <th>售價</th>
                                        </tr>
                                    </thead>
                                    @if (count($combo_datas) > 0)
                                        <tbody>
                                            @foreach ($combo_datas as $combo_data)
                                                <tr>
                                                    <td class="text-center"><button
                                                            class="ibtnDel demo-delete-row btn btn-danger btn-xs btn-icon"><i
                                                                class="fa fa-times"></i></button></td>
                                                    <td>{{ $combo_data->product_data->name }}</td>
                                                    <td><input type="number" class="form-control qty"
                                                            name="product_qty[]" value="{{ $combo_data->num }}"
                                                            step="any" /></td>
                                                    <td><input type="number" class="form-control unit_price"
                                                            name="unit_price[]" value="{{ $combo_data->price }}"
                                                            step="any" /></td>
                                                    <input type="hidden" class="product-id" name="product_id[]"
                                                        value="{{ $combo_data->include_product_id }}">
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    @else
                                        <tbody>
                                        </tbody>
                                    @endif
                                </table>
                            </div>
                            {{-- <div class="mb-3">
                        <label for="projectname" class="mb-1 form-label">商品照片</label>
                        <div class="my-3 mt-xl-0 dropzone">
                            <p class="text-muted font-14">Recommended thumbnail size 800x400 (px).</p>

                            
                                <div class="fallback">
                                    <input name="imagefile" type="file" />
                                </div>

                                <div class="dz-message needsclick">
                                    <i class="h3 text-muted dripicons-cloud-upload"></i>
                                    <h4>Drop files here or click to upload.</h4>
                                </div>


                            <!-- Preview -->
                            <div class="dropzone-previews mt-3" id="file-previews"></div>

                            <!-- file preview template -->
                            <div class="d-none" id="uploadPreviewTemplate">
                                <div class="card mt-1 mb-0 shadow-none border">
                                    <div class="p-2">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <img data-dz-thumbnail src="#" class="avatar-sm rounded bg-light" alt="">
                                            </div>
                                            <div class="col ps-0">
                                                <a href="javascript:void(0);" class="text-muted fw-bold" data-dz-name></a>
                                                <p class="mb-0" data-dz-size></p>
                                            </div>
                                            <div class="col-auto">
                                                <!-- Button -->
                                                <a href="" class="btn btn-link btn-lg text-muted" data-dz-remove>
                                                    <i class="mdi mdi-close"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- end file preview template -->
                        </div> --}}

                            <div class="mb-3 mt-1">
                                <label for="product-description" class="form-label">商品詳情<span
                                        class="text-danger">*</span></label>
                                <div id="snow-editor" style="height: 150px;"></div> <!-- end Snow-editor-->
                                <input name="description" id="description" type="hidden" value="">
                            </div>

                            <div class="mb-3 mt-3" id="check_cost_div">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="check_cost" name="check_cost">
                                    <label class="form-check-label" for="check_cost"><b>此產品成本是否固定？</b></label>
                                </div>
                                <div class="mt-2 col-md-4">
                                    <input type="text" class="form-control" id="cost" name="cost"
                                        placeholder="產品成本" value="{{ $data->cost }}">
                                    <input type="hidden" name="cost_hidden" id="cost_hidden"
                                        value="{{ $data->cost }}">
                                </div>
                            </div>

                            <div class="mb-3 mt-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="stock" name="stock"
                                        @if ($data->stock == 0) value="0" checked @endif>
                                    <label class="form-check-label" for="stock"><b>此產品不須紀錄至庫存盤點</b></label>
                                </div>
                            </div>

                            <div class="mb-3 mt-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="restock" name="restock"
                                        @if ($data->restock == 0) value="0"  checked @endif>
                                    <label class="form-check-label" for="restock"><b>此產品不須紀錄進貨</b></label>
                                </div>
                            </div>

                            <div class="mb-3 mt-3" id="check_commission_div">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="commission" name="commission"
                                        @if ($data->commission == 1) value="1"  checked @endif>
                                    <label class="form-check-label" for="commission"><b>此產品不列入專員獎金</b></label>
                                </div>
                            </div>

                            <div class="mb-3 mt-3">
                                <label class="mb-2">商品狀態<span class="text-danger">*</span></label>
                                <br />
                                <div class="radio form-check-inline">
                                    <input type="radio" id="inlineRadio1" value="up" name="status"
                                        @if ($data->status == 'up') checked @endif>
                                    <label for="inlineRadio1">上架</label>
                                </div>
                                <div class="radio form-check-inline">
                                    <input type="radio" id="inlineRadio2" value="down" name="status"
                                        @if ($data->status == 'down') checked @endif>
                                    <label for="inlineRadio2">下架</label>
                                </div>
                            </div>




                    </div>
                </div> <!-- end card -->
            </div> <!-- end col -->

        </div>
        <!-- end row -->

        <div class="row">
            <div class="col-12">
                <div class="text-center mb-3">
                    <button type="button" class="btn w-sm btn-light waves-effect" onclick="history.go(-1)">回上一頁</button>
                    <button type="submit" class="btn w-sm btn-success waves-effect waves-light">修改</button>
                    {{-- <button type="button" class="btn w-sm btn-danger waves-effect waves-light">Delete</button> --}}
                </div>
            </div> <!-- end col -->
        </div>
        </form>



    </div> <!-- container -->
@endsection

@section('script')
    <!-- third party js -->
    <script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/libs/dropzone/dropzone.min.js') }}"></script>
    <script src="{{ asset('assets/libs/quill/quill.min.js') }}"></script>
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
        $('#promTypes').on('change', function() {
            var promType = $(this).val();
            $.ajax({
                url: '{{ route('prom.search') }}',
                data: {
                    'select_prom': promType
                },
                success: function(data) {
                    $('#prom_id').html(data);
                    console.log(data);
                }
            });
        });

        //產生亂數產品代碼
        function generateRandomString(length) {
            var result = '';
            var characters = '0123456789';
            var charactersLength = characters.length;
            for (var i = 0; i < length; i++) {
                result += characters.charAt(Math.floor(Math.random() * charactersLength));
            }
            return result;
        }

        if ($("#number").val().trim() === '') {
            $("#number").val(generateRandomString(8));
        }
        // 顯示組合欄位
        $("#combo").hide();
        $("#cost").hide();
        $("#commission").hide();

        if ($("input[name='type_hidden']").val() == "combo" || $("input[name='type_hidden']").val() == "set") {
            $("#combo").show(300);
            $("#check_cost_div").hide(300);
            $("#price").prop('readonly', true);
        } else {
            $("#combo").hide(300);
            $("#check_cost_div").show(300);
            $("#price").prop('readonly', false);
        }

        console.log($("input[name='commission_hidden']").val());
        if ($("input[name='commission_hidden']").val() != "") {
            $("#check_commission").prop("checked", true);
            $("#commission").show();
        }

        if ($("input[name='cost_hidden']").val() != "") {
            $("#check_cost").prop("checked", true);
            $("#cost").show();
        }

        $('select[name="type"]').on('change', function() {
            if ($(this).val() == 'combo' || $(this).val() == 'set') {
                $("#combo").show(300);
                $("#check_cost_div").hide(300);
                $("#price").prop('readonly', true);
            } else {
                $("#combo").hide(300);
                $("#check_cost_div").show(300);
                $("#price").prop('readonly', false);
            }
        });

        $("#check_cost").on("change", function() {
            if ($(this).is(':checked')) {
                $("#cost").show(300);
            } else {
                $("#cost").hide(300);
            }
        });
        console.log($('#stock').val());

        $('#stock').change(function() {
            if ($(this).is(':checked')) {
                $(this).val(0);
            } else {
                $(this).val(1);
            }
        });

        $('#restock').change(function() {
            if ($(this).is(':checked')) {
                $(this).val(0);
            } else {
                $(this).val(1);
            }
        });

        $('#commission').change(function() {
            if ($(this).is(':checked')) {
                $(this).val(1);
            } else {
                $(this).val(0);
            }
        });


        //製作Ajax產品帶入表單
        var lims_product_code = {!! json_encode($products) !!};

        $("#lims_productcodeSearch").autocomplete({
            source: function(request, response) {
                var matcher = new RegExp(".?" + $.ui.autocomplete.escapeRegex(request.term), "i");
                response($.grep(lims_product_code, function(item) {
                    return matcher.test(item);
                }));
            },
            select: function(event, ui) {
                var value = ui.item.value;
                $.ajax({
                    url: '{{ route('product.product_search') }}',
                    data: {
                        data: value
                    },
                    success: function(data) {
                        console.log(data);
                        // console.log(data);
                        var flag = 1;
                        $(".product-id").each(function() {
                            if ($(this).val() == data['id']) {
                                alert('Duplicate input is not allowed!')
                                flag = 0;
                            }
                        });
                        $("input[name='product_code_name']").val('');
                        if (flag) {
                            var newRow = $("<tr>");
                            var cols = '';
                            cols +=
                                '<td class="text-center"><button class="ibtnDel demo-delete-row btn btn-danger btn-xs btn-icon"><i class="fa fa-times"></i></button></td>';
                            cols += '<td>' + data['name'] + '</td>';
                            cols +=
                                '<td><input type="number" class="form-control qty" name="product_qty[]" value="1" step="any"/></td>';
                            cols +=
                                '<td><input type="number" class="form-control unit_price" name="unit_price[]" value="' +
                                data['price'] + '" step="any"/></td>';
                            cols +=
                                '<input type="hidden" class="product-id" name="product_id[]" value="' +
                                data['id'] + '"';
                            newRow.append(cols);
                            $("table.order-list tbody").append(newRow);
                            calculate_price()
                        }
                    },
                    error: function() {
                        console.log('AJAX load did not work');
                    }
                });
            }
        });

        function calculate_price() {
            var price = 0;
            $(".qty").each(function() {
                rowindex = $(this).closest('tr').index();
                quantity = $(this).val();
                unit_price = $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ') .unit_price').val();
                price += quantity * unit_price;
            });
            $('input[name="price"]').val(price);
        }

        //Change quantity or unit price
        $("#demo-foo-addrow").on('input', '.qty , .unit_price', function() {
            calculate_price();
        });

        //Delete product
        $("table.order-list tbody").on("click", ".ibtnDel", function(event) {
            $(this).closest("tr").remove();
            calculate_price();
        });


        //將description元素放回去
        var edit_quill = new Quill('#snow-editor', {
            theme: 'snow'
        });
        //轉化為標籤格式
        var description = '{{ $data->description }}'.replace(/&lt;/g, '<').replace(/&gt;/g, '>');
        // console.log(aa);
        delta = edit_quill.clipboard.convert(description);

        edit_quill.setContents(delta);

        $("#product-form").submit(function(event) {

            // 获取Quill编辑器实例
            var quill = new Quill('#snow-editor', {
                theme: 'snow'
            });

            // 获取编辑器内容（HTML格式）
            var content = quill.root.innerHTML;
            $("#description").val(content);
            // alert($("#description").val());


            if ($("#type").val() == 'combo' || $("#type").val() == 'set') {
                var rownumber = $('table.order-list tbody tr:last').index();
                if (rownumber <= 0) {
                    alert("請新增2件（含）以上組合商品")
                    return false;
                }
            }
            // alert(rownumber);
        });
    </script>
    <!-- end demo js-->
@endsection
