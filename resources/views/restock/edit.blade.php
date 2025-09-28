@extends('layouts.vertical', ["page_title"=> "編輯商品進貨"])

@section('css')
{{-- <link href="{{asset('assets/libs/select2/select2.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/libs/dropzone/dropzone.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/libs/quill/quill.min.css')}}" rel="stylesheet" type="text/css" /> --}}
{{-- <meta name="csrf-token" content="{{ csrf_token() }}"> --}}
@endsection

@section('content')

<style>
    @media screen and (max-width:768px) { 
        .mobile{
            width: 180px;
        }
    }
    /* .bg-light {
        background-color: rgba(0,0,0,0.08) !important;
    } */
</style>

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
                        <li class="breadcrumb-item active">編輯商品進貨</li>
                    </ol>
                </div>
                <h5 class="page-title">編輯商品進貨</h5>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <form action="{{ route('product.restock.edit.data',$data->id) }}" method="POST" id="your-form"  enctype="multipart/form-data" data-plugin="dropzone" data-previews-container="#file-previews" data-upload-preview-template="#uploadPreviewTemplate">
    @csrf
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="text-uppercase bg-light  p-2 mt-0 mb-3">基本設定</h5>
                    <div class="row">
                        <div class="mb-3 col-md-4">
                            <label for="date" class="form-label">進貨日期<span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="date" name="date" value="{{ $data->date }}" required>
                        </div>
                        <div class="mb-3 col-md-4">
                            <label for="user_id" class="form-label">進貨人<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="user_id" name="user_id" readonly value="{{ $data->user_name->name }}">
                        </div>
                    </div>
                </div>
            </div> <!-- end card -->
        </div> <!-- end col -->
    </div>

    <div class="row" id="gdpaper_div">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="text-uppercase bg-light p-2 mt-0 mb-3">進貨品項</h5>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="tech-companies-1" class="table gdpaper-list">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>產品名稱<span class="text-danger">*</span></th>
                                            <th>成本<span class="text-danger">*</span></th>
                                            <th>數量<span class="text-danger">*</span></th>
                                            <th>小計<span class="text-danger">*</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($items as $key=>$item)
                                            <tr id="row-{{ $key }}">
                                                <td class="text-center">
                                                    @if($key==0)
                                                    <button type="button" class="ibtnAdd_gdpaper demo-delete-row btn btn-primary btn-sm btn-icon"><i class="fa fas fa-plus"></i></button>                                                    
                                                    @else
                                                    <button type="button" class="ibtnDel_gdpaper demo-delete-row btn btn-danger btn-sm btn-icon"><i class="fa fa-times"></i></button>
                                                    @endif
                                                </td>
                                            <td>
                                                <select id="gdpaper_id_{{$key}}" alt="{{ $key }}" class="mobile form-select" name="gdpaper_ids[]" onchange="chgPapers(this)" >
                                                    <option value="" selected>請選擇...</option>
                                                    @foreach($products as $product)
                                                        <option value="{{ $product->id }}" data-has-variants="{{ $product->has_variants ? '1' : '0' }}" @if($item->product_id == $product->id) selected @endif>{{ $product->name }}
                                                            @if(isset($product->cost))
                                                            (成本：{{ $product->cost }}元)
                                                            @else
                                                            (無固定成本)
                                                            @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <br>
                                                <select id="variant_id_{{$key}}" alt="{{ $key }}" class="mobile form-select variant-select" name="variant_ids[]" style="display:none;" onchange="chgVariantCost(this)">
                                                    <option value="" selected>請選擇細項...</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number"  alt="{{ $key }}" class="mobile form-control" id="gdpaper_cost_{{$key}}" name="gdpaper_cost[]" onchange="chgCosts(this)" value="{{ $item->product_cost }}">
                                            </td>
                                            <td>
                                                <input type="number"  alt="{{ $key }}" class="mobile form-control" id="gdpaper_num_{{$key}}" name="gdpaper_num[]" onchange="chgNums(this)" value="{{ $item->product_num }}">
                                            </td>
                                            <td>
                                                <input type="text" class="mobile form-control total_number" id="gdpaper_total_{{$key}}" name="gdpaper_total[]" value="{{ $item->product_total }}">
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div> <!-- end .table-responsive -->
                        </div>
                    </div>
                </div>
            </div> <!-- end card -->
        </div> <!-- end col -->
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="text-uppercase bg-light  p-2 mt-0 mb-3">付款方式</h5>
                    <div class="row">
                        <div class="mb-3 col-md-12">
                            <h2>應付金額<span id="total_text" class="text-danger">{{ $data->total }}</span>元</h2>
                            <input type="hidden" class="form-control" id="total" name="total" value="{{ $data->total }}" readonly>
                        </div>
                        <div class="mb-3 col-md-4">
                            <label for="pay_id" class="form-label">支付類別<span class="text-danger">*</span></label>
                            <select class="form-select" name="pay_id" required>
                                <option value="" selected>請選擇</option>
                                <option value="A" @if($data->pay_id == 'A') selected @endif>一次付清</option>
                                <option value="C" @if($data->pay_id == 'C') selected @endif>訂金</option>
                                <option value="E" @if($data->pay_id == 'E') selected @endif>追加</option>
                                <option value="D" @if($data->pay_id == 'D') selected @endif>尾款</option>
                            </select>
                        </div>
                        <div class="mb-3 col-md-4">
                            <label for="pay_id" class="form-label">支付方式<span class="text-danger">*</span></label>
                            <select class="form-select" name="pay_method" required>
                                <option value="" selected>請選擇</option>
                                <option value="A" @if($data->pay_method == 'A') selected @endif>現金</option>
                                <option value="B" @if($data->pay_method == 'B') selected @endif>匯款</option>
                                <option value="C" @if($data->pay_method == 'C') selected @endif>現金與匯款</option>
                            </select>
                        </div>
                        <div class="mb-3 col-md-4" id="cash_price_div">
                            <label for="pay_price" class="form-label">現金付款<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="cash_price" name="cash_price" value="{{ $data->pay_method }}">
                        </div>
                        <div class="mb-3 col-md-4" id="transfer_price_div">
                            <label for="pay_price" class="form-label">匯款付款<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="transfer_price" name="transfer_price" value="{{ $data->transfer_price }}">
                        </div>
                        <div class="mb-3 col-md-4" id="this_price_div">
                            <label for="pay_price" class="form-label">本次付款<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="pay_price" name="pay_price" value="{{ $data->pay_price }}" required>
                        </div>
                    </div>
                    <div>
                        <label class="form-label">備註</label>
                        <textarea class="form-control" rows="3" placeholder="" name="comm">{{ $data->comm }}</textarea>
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
                <button type="submit" class="btn w-sm btn-success waves-effect waves-light">編輯</button>
                {{-- <button type="button" class="btn w-sm btn-danger waves-effect waves-light">Delete</button> --}}
            </div>
        </div> <!-- end col -->
    </div>
    <input class="total_number" type="hidden" id="puja_price" name="puja_price" value="">
    <input type="hidden" id="row_id" name="row_id" value="">

</form>


</div> <!-- container -->

@endsection

@section('script')
<!-- third party js -->
<script src="{{asset('assets/libs/select2/select2.min.js')}}"></script>
<script src="{{asset('assets/libs/dropzone/dropzone.min.js')}}"></script>
<script src="{{asset('assets/libs/quill/quill.min.js')}}"></script>
<script src="{{asset('assets/libs/footable/footable.min.js')}}"></script>
<!-- third party js ends -->

<!-- demo app -->
<script src="{{asset('assets/js/pages/form-fileuploads.init.js')}}"></script>
<script src="{{asset('assets/js/pages/add-product.init.js')}}"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.1/themes/smoothness/jquery-ui.css" />
{{-- <script src="{{asset('assets/js/pages/foo-tables.init.js')}}"></script> --}}


<script>
    $("#final_price").on('input', function(){
        calculate_price();
    });
    
    $("#plan_price").on('input', function(){
        calculate_price();
    });

    $(".total_number").on('input', function(){
        calculate_price();
    });

    $("#cash_price_div").hide();
    $("#transfer_price_div").hide();
    $("#transfer_number_div").hide();

    $('select[name="pay_method"]').on('change', function() {
        if($(this).val() == 'C'){
            $("#cash_price_div").show(300);
            $("#transfer_price_div").show(300);
            $("#transfer_number_div").show(300);
            $("#pay_price").prop('required', false);
            $("#cash_price").prop('required', true);
            $("#transfer_price").prop('required', true);
            $("#transfer_number").prop('required', true);
        }else if($(this).val() == 'B'){
            $("#transfer_number_div").show(300);
            $("#pay_price").prop('required', true);
            $("#cash_price").prop('required', false);
            $("#transfer_price").prop('required', false);
            $("#transfer_number").prop('required', true);
        }else{
            $("#cash_price_div").hide(300);
            $("#transfer_price_div").hide(300);
            $("#transfer_number_div").hide(300);
            $("#pay_price").prop('required', true);
            $("#cash_price").prop('required', false);
            $("#transfer_price").prop('required', false);
            $("#transfer_number").prop('required', false);
        }
    });

    $('select[name="puja_id"]').on('change', function() {
        puja_id = $(this).val();
        // console.log(puja_id);
        $.ajax({
            url : '{{ route('puja.search') }}',
            data:{'puja_id':puja_id},
            success:function(data){
                console.log(data);
                $("#puja_price").val(data);
                calculate_price();
            }
        });
    });

    function calculate_price() {
        var total = 0;
        $(".total_number").each(function(){
            var value = parseFloat($(this).val());
            if(!isNaN(value)) {
                total += value;
            }
        });
        $("#total").val(total);
        $("#total_text").html(total);
        console.log(plan_id);
    }

    function chgPapers(obj){
        $("#row_id").val($("#"+ obj.id).attr('alt'));
        row_id = $("#row_id").val();
        
        // 檢查選中的商品是否有細項
        var selectedOption = $("#gdpaper_id_"+row_id + " option:selected");
        var hasVariants = selectedOption.data('has-variants');
        var variantSelect = $("#variant_id_"+row_id);
        
        if (hasVariants == '1') {
            // 顯示細項選擇
            variantSelect.show();
            
            // 載入細項選項
            var productId = $("#gdpaper_id_"+row_id).val();
            variantSelect.html('<option value="" selected>請選擇細項...</option>');
            
            // 使用 JavaScript 物件來載入細項
            var variantsData = {
                @foreach($productsWithVariants as $productId => $variants)
                    {{ $productId }}: [
                        @foreach($variants as $variant)
                            {id: {{ $variant->id }}, name: '{{ $variant->variant_name }}', cost: {{ $variant->cost ?? 0 }}},
                        @endforeach
                    ],
                @endforeach
            };
            
            if (variantsData[productId]) {
                variantsData[productId].forEach(function(variant) {
                    variantSelect.append('<option value="' + variant.id + '" data-cost="' + variant.cost + '">' + variant.name + '</option>');
                });
            }
        } else {
            // 隱藏細項選擇
            variantSelect.hide();
            variantSelect.val('');
        }
        
        $.ajax({
            url : '{{ route('gdpaper.cost.search') }}',
            data:{'gdpaper_id':$("#gdpaper_id_"+row_id).val()},
            success:function(data){
                if(data != 0){//如果成本不等於0
                    if($("#gdpaper_num_"+row_id).val()){
                    var gdpaper_num = $("#gdpaper_num_"+row_id).val();
                    $("#gdpaper_total_"+row_id).val(gdpaper_num*data);
                    calculate_price();
                    }
                    $("#gdpaper_num_"+row_id).on('input', function(){
                        var gdpaper_num = $("#gdpaper_num_"+row_id).val();
                        $("#gdpaper_total_"+row_id).val(gdpaper_num*data);
                        calculate_price();
                    });
                }else{
                    if($("#gdpaper_num_"+row_id).val()){
                    var gdpaper_num = $("#gdpaper_num_"+row_id).val();
                    var gdpaper_cost = $("#gdpaper_cost_"+row_id).val();
                    $("#gdpaper_total_"+row_id).val(gdpaper_num*gdpaper_cost);
                    calculate_price();
                    }
                    $("#gdpaper_num_"+row_id).on('input', function(){
                        var gdpaper_num = $("#gdpaper_num_"+row_id).val();
                        var gdpaper_cost = $("#gdpaper_cost_"+row_id).val();
                        $("#gdpaper_total_"+row_id).val(gdpaper_num*gdpaper_cost);
                        calculate_price();
                    });
                }
                $("#gdpaper_cost_"+row_id).val(data);
            }
        });
    }

    function chgVariantCost(obj){
        $("#row_id").val($("#"+ obj.id).attr('alt'));
        row_id = $("#row_id").val();
        
        var selectedVariant = $("#variant_id_"+row_id + " option:selected");
        var variantCost = selectedVariant.data('cost');
        
        if (variantCost && variantCost > 0) {
            $("#gdpaper_cost_"+row_id).val(variantCost);
            
            if($("#gdpaper_num_"+row_id).val()){
                var gdpaper_num = $("#gdpaper_num_"+row_id).val();
                $("#gdpaper_total_"+row_id).val(gdpaper_num*variantCost);
                calculate_price();
            }
        }
    }

    function chgCosts(obj){
        $("#row_id").val($("#"+ obj.id).attr('alt'));
        row_id = $("#row_id").val();
        $.ajax({
            url : '{{ route('gdpaper.cost.search') }}',
            data:{'gdpaper_id':$("#gdpaper_id_"+row_id).val()},
            success:function(data){
                if(data != 0){//如果成本不等於0
                    if($("#gdpaper_num_"+row_id).val()){
                    var gdpaper_num = $("#gdpaper_num_"+row_id).val();
                    $("#gdpaper_total_"+row_id).val(gdpaper_num*data);
                    calculate_price();
                    }
                    $("#gdpaper_cost_"+row_id).on('input', function(){
                        var gdpaper_num = $("#gdpaper_num_"+row_id).val();
                        $("#gdpaper_total_"+row_id).val(gdpaper_num*data);
                        calculate_price();
                    });
                }else{
                    if($("#gdpaper_num_"+row_id).val()){
                    var gdpaper_num = $("#gdpaper_num_"+row_id).val();
                    var gdpaper_cost = $("#gdpaper_cost_"+row_id).val();
                    $("#gdpaper_total_"+row_id).val(gdpaper_num*gdpaper_cost);
                    calculate_price();
                    }
                    $("#gdpaper_cost_"+row_id).on('input', function(){
                        var gdpaper_num = $("#gdpaper_num_"+row_id).val();
                        var gdpaper_cost = $("#gdpaper_cost_"+row_id).val();
                        $("#gdpaper_total_"+row_id).val(gdpaper_num*gdpaper_cost);
                        calculate_price();
                    });
                }
            }
        });
    }

    function chgNums(obj){
        $("#row_id").val($("#"+ obj.id).attr('alt'));
        row_id = $("#row_id").val();
        $.ajax({
            url : '{{ route('gdpaper.cost.search') }}',
            data:{'gdpaper_id':$("#gdpaper_id_"+row_id).val()},
            success:function(data){
                if(data != 0){//如果成本不等於0
                    if($("#gdpaper_num_"+row_id).val()){
                    var gdpaper_num = $("#gdpaper_num_"+row_id).val();
                    $("#gdpaper_total_"+row_id).val(gdpaper_num*data);
                    calculate_price();
                    }
                    $("#gdpaper_num_"+row_id).on('input', function(){
                        var gdpaper_num = $("#gdpaper_num_"+row_id).val();
                        $("#gdpaper_total_"+row_id).val(gdpaper_num*data);
                        calculate_price();
                    });
                }else{
                    if($("#gdpaper_num_"+row_id).val()){
                    var gdpaper_num = $("#gdpaper_num_"+row_id).val();
                    var gdpaper_cost = $("#gdpaper_cost_"+row_id).val();
                    $("#gdpaper_total_"+row_id).val(gdpaper_num*gdpaper_cost);
                    calculate_price();
                    }
                    $("#gdpaper_num_"+row_id).on('input', function(){
                        var gdpaper_num = $("#gdpaper_num_"+row_id).val();
                        var gdpaper_cost = $("#gdpaper_cost_"+row_id).val();
                        $("#gdpaper_total_"+row_id).val(gdpaper_num*gdpaper_cost);
                        calculate_price();
                    });
                }
            }
        });
    }
    

    $("table.pet-list tbody").on("click", ".ibtnDel_pet", function() {
        $(this).closest('tr').remove();
    });  

    $("table.gdpaper-list tbody").on("click", ".ibtnDel_gdpaper", function() {
        $(this).closest('tr').remove();
    });

    $("table.pet-list tbody").on("click", ".ibtnAdd_pet", function() {
        rowCount = $('table.pet-list tr').length - 1;
        var newRow = $("<tr>");
        var cols = '';
        cols += '<td class="text-center"><button type="button" class="ibtnDel_pet demo-delete-row btn btn-danger btn-sm btn-icon"><i class="fa fa-times"></i></button></td>';
        cols += '<td>';
        cols += '<select id="pet_id_'+rowCount+'" alt="'+rowCount+'" class="mobile form-select" name="pet_ids[]" required>請選擇...</select>';
        cols += '</td>';
        cols += '</tr>';
        newRow.append(cols);
        $("table.pet-list tbody").append(newRow);

        var value1 = $("#cust_name_q").val();
        $.ajax({
            type: 'get',
            url: '{{ route('customer.pet.search') }}',
            data: { 'cust_id': value1 },
            success: function(data) {
                $("#pet_id_"+rowCount).html(data[1]);
            }
        });
    });

    function chgItems(obj){
        $("#row_id").val($("#"+ obj.id).attr('alt'));
        row_id = $("#row_id").val();
        var value1 = $("#cust_name_q").val();
        $.ajax({
            type: 'get',
            url: '{{ route('customer.pet.search') }}',
            data: { 'cust_id': value1 },
            success: function(data) {
                console.log(row_id);
                $("#pet_id_"+row_id).html(data[1]);
                // 在這裡處理第二個 AJAX 請求的成功回應
            }
        });
    }

    $("table.gdpaper-list tbody").on("click", ".ibtnAdd_gdpaper", function() {
        rowCount = $('table.gdpaper-list tr').length - 1;
        var newRow = $("<tr>");
        var cols = '';
        cols += '<td class="text-center"><button type="button" class="ibtnDel_gdpaper demo-delete-row btn btn-danger btn-sm btn-icon"><i class="fa fa-times"></i></button></td>';
        cols += '<td>';
        cols += '<select id="gdpaper_id_'+rowCount+'" alt="'+rowCount+'" class="mobile form-select" name="gdpaper_ids[]" onchange="chgPapers(this)">';
        cols += '<option value="" selected>請選擇...</option>';
            @foreach($products as $product)
                cols += '<option value="{{ $product->id }}" data-has-variants="{{ $product->has_variants ? "1" : "0" }}">{{ $product->name }}';
                @if(isset($product->cost))
                cols += '(成本：{{ $product->cost }}元)';
                @else
                cols += '(無固定成本)';
                @endif
                cols += '</option>';
            @endforeach
        cols += '</select>';
        cols += '<br>';
        cols += '<select id="variant_id_'+rowCount+'" alt="'+rowCount+'" class="mobile form-select variant-select" name="variant_ids[]" style="display:none;" onchange="chgVariantCost(this)">';
        cols += '<option value="" selected>請選擇細項...</option>';
        cols += '</select>';
        cols += '</td>';
        cols += '<td>';
        cols += '<input type="number"  alt="'+rowCount+'"  class="mobile form-control" id="gdpaper_cost_'+rowCount+'" name="gdpaper_cost[]" value="" onchange="chgCosts(this)">';
        cols += '</td>';
        cols += '<td>';
        cols += '<input type="number"  alt="'+rowCount+'"  class="mobile form-control" id="gdpaper_num_'+rowCount+'" name="gdpaper_num[]" value="" onchange="chgNums(this)">';
        cols += '</td>';
        cols += '<td>';
        cols += '<input type="text" class="mobile form-control total_number" id="gdpaper_total_'+rowCount+'" name="gdpaper_total[]">';
        cols += '</td>';
        cols += '</tr>';
        newRow.append(cols);
        $("table.gdpaper-list tbody").append(newRow);
    });


    $( "#source_company_name_q" ).keydown(function() {
            $value=$(this).val();
            $.ajax({
            type : 'get',
            url : '{{ route('company.search') }}',
            data:{'cust_name':$value},
            success:function(data){
                $('#source_company_name_list_q').html(data);
            }
            });
            console.log($value);
        });

        $(".ibtnAdd_prom").click(function(){
            $rowCount = $('table.prom-list tr').length - 1;
            var newRow = $("<tr>");
            var cols = '';
            cols += '<td class="text-center"><button type="button" class="ibtnDel_prom demo-delete-row btn btn-danger btn-sm btn-icon"><i class="fa fa-times"></i></button></td>';
            cols += '<td>';
            cols += '<select id="select_prom_'+$rowCount+'" alt="'+$rowCount+'" class="mobile form-select" name="select_proms[]" onchange="chgItems(this)">';
            cols += '<option value="" selected>請選擇...</option>';
            cols += '<option value="A">安葬處理</option>';
            cols += '<option value="B">後續處理</option>';
            cols += '</select>';
            cols += '</td>';
            cols += '<td>';
            cols += '<select id="prom_'+$rowCount+'" class="mobile form-select" name="prom[]">';
            cols += '<option value="">請選擇...</option>';
            cols += '</select>';
            cols += '</td>';
            cols += '<td>';
            cols += '<input type="text" class="mobile form-control total_number" id="prom_total_'+$rowCount+'" name="prom_total[]">';
            cols += '</td>';
            cols += '</tr>';
            newRow.append(cols);
            $("table.prom-list tbody").append(newRow);
        });
        $.ajaxSetup({ headers: { 'csrftoken' : '{{ csrf_token() }}' } });
        
        // 頁面載入時初始化細項選擇
        $(document).ready(function() {
            $('select[name="gdpaper_ids[]"]').each(function() {
                var rowId = $(this).attr('alt');
                var selectedProductId = $(this).val();
                var variantSelect = $("#variant_id_" + rowId);
                
                if (selectedProductId) {
                    var selectedOption = $(this).find('option:selected');
                    var hasVariants = selectedOption.data('has-variants');
                    
                    if (hasVariants == '1') {
                        variantSelect.show();
                        
                        // 載入細項選項
                        var variantsData = {
                            @foreach($productsWithVariants as $productId => $variants)
                                {{ $productId }}: [
                                    @foreach($variants as $variant)
                                        {id: {{ $variant->id }}, name: '{{ $variant->variant_name }}', cost: {{ $variant->cost ?? 0 }}},
                                    @endforeach
                                ],
                            @endforeach
                        };
                        
                        if (variantsData[selectedProductId]) {
                            variantsData[selectedProductId].forEach(function(variant) {
                                variantSelect.append('<option value="' + variant.id + '" data-cost="' + variant.cost + '">' + variant.name + '</option>');
                            });
                        }
                    }
                }
            });
            
            // 設定已選擇的細項
            @foreach($items as $key => $item)
                @if($item->variant_id)
                    var variantSelect_{{ $key }} = $("#variant_id_{{ $key }}");
                    if (variantSelect_{{ $key }}.length > 0) {
                        variantSelect_{{ $key }}.val({{ $item->variant_id }});
                    }
                @endif
            @endforeach
        });
</script>
<!-- end demo js-->
@endsection