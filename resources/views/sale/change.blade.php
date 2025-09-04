@extends('layouts.vertical', ["page_title"=> "轉單/對拆業務Key單"])

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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">業務管理</a></li>
                        <li class="breadcrumb-item active">轉單/對拆業務Key單</li>
                    </ol>
                </div>
                <h5 class="page-title">轉單/對拆業務Key單</h5>
            </div>
        </div>
    </div>
    <!-- end page title -->


    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="text-uppercase bg-light  p-2 mt-0 mb-3">轉單/對拆</h5>
                    <form action="{{ route('sale.data.change',$data->id) }}" method="POST">
                        @csrf
                    <div class="row">
                        <div class="mb-1 mt-1">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="check_change" name="check_change" @if(isset($sale_change)) checked value="1"  @endif >
                                <label class="form-check-label" for="check_change"><b>轉單</b></label>
                            </div>
                            <div class="mt-2 row" id="change_user_div">
                                <div class="mb-1">
                                    <h4>原服務人員：{{ $data->user_data }}</h4>
                                </div>
                                <div class="col-auto">
                                    <h4><label class="form-check-label" for="check_change">轉單至</label></h4>
                                </div>
                                <input type="hidden" name="user_id" value="{{ $data->user_id }}">
                                <div class="col-4">
                                    <select class="form-control" data-toggle="select2" data-width="100%" name="change_user_id" required>
                                        @foreach($users as $user)
                                        <option value="{{ $user->id }}" @if(isset($sale_change) && $sale_change->change_user_id == $user->id) selected  @endif>{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-4">
                                    <input type="text" class="form-control" name="change_comm" @if(isset($sale_change))  value="{{ $sale_change->comm }}"  @endif placeholder="備註">
                                </div>
                            </div>
                        </div>
                        <div class="mb-1 mt-1">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="check_split" name="check_split" @if(isset($sale_split)) checked value="1" @endif >
                                <label class="form-check-label" for="check_split"><b>對拆</b></label>
                            </div>
                            <div class="mt-2 row" id="check_split_div">
                                <div class="col-auto">
                                    <h4><label class="form-check-label" for="check_split">專員1</label></h4>
                                </div>
                                <div class="col-3">
                                    <select class="form-control" data-toggle="select2" data-width="100%" name="split_user_id_1" required>
                                        @foreach($users as $user)
                                        <option value="{{ $user->id }}" @if($data->user_id == $user->id) selected @endif>{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-auto">
                                    <h4><label class="form-check-label" for="check_split">專員2</label></h4>
                                </div>
                                <div class="col-3">
                                    <select class="form-control" data-toggle="select2" data-width="100%" name="split_user_id_2" required>
                                        @foreach($users as $user)
                                        <option value="{{ $user->id }}" @if(isset($sale_split) && $sale_split->split_user_id == $user->id) selected  @endif>{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-3">
                                    <input type="text" class="form-control"  name="split_comm" @if(isset($sale_split))  value="{{ $sale_split->comm }}"  @endif  placeholder="備註">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- end card -->
        
            <div class="col-12">
                <div class="text-center mb-3">
                        <button type="button" class="btn w-sm btn-light waves-effect" onclick="history.go(-1)">回上一頁</button>
                        <button type="submit" class="btn w-sm btn-success waves-effect waves-light" onclick="if(!confirm('是否已確定更新轉單/對拆資訊？')){event.returnValue=false;return false;}">確定更新</button>
                </div>

        </div> <!-- end col -->   
    </div> <!-- end col -->
</form>


    

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="text-uppercase bg-light  p-2 mt-0 mb-3">基本資訊</h5>
                    <div class="row">
                        <div class="mb-3 col-md-4">
                            <label for="type_list" class="form-label">案件類別選擇<span class="text-danger">*</span></label>
                            <select id="type_list" class="form-select" name="type_list" disabled>
                                <option value="dispatch" @if($data->type_list == 'dispatch') selected @endif>派件單</option>
                                <option value="memorial" @if($data->type_list == 'memorial') selected @endif>追思單</option>                            
                            </select>
                        </div>
                        <div class="mb-3 col-md-4">
                            <label for="sale_on" class="form-label">單號<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="sale_on" name="sale_on" value="{{ $data->sale_on }}" required  readonly >
                        </div>
                        <div class="mb-3 col-md-4">
                            <label for="sale_date" class="form-label">日期<span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="sale_date" name="sale_date" value="{{ $data->sale_date }}" required readonly>
                        </div>
                        <div class="mb-3 col-md-4">
                            <label for="user_id" class="form-label">服務專員<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="user_id" name="user_id" value="{{ $data->user_name->name }}" readonly>
                        </div>
                        <div class="mb-3 col-md-4 not_memorial_show">
                            <label for="customer_id" class="form-label">客戶名稱<span class="text-danger">*</span></label>
                            <select id="type" class="form-select" name="customer_id" disabled >
                                <option value="">請選擇...</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}" @if($data->customer_id == $customer->id) selected @endif readonly>{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 col-md-4 not_final_show not_memorial_show">
                            <label for="pet_name" class="form-label">寵物名稱<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="pet_name" name="pet_name" value="{{ $data->pet_name }}" readonly>
                        </div>
                        <div class="mb-3 col-md-4 not_final_show not_memorial_show">
                            <label for="kg" class="form-label">公斤數<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="kg" name="kg" value="{{ $data->kg }}" readonly>
                        </div>
                        <div class="mb-3 col-md-4 not_final_show not_memorial_show">
                            <label for="type" class="form-label">案件來源<span class="text-danger">*</span></label>
                            <select id="type" class="form-select" name="type" disabled >
                                <option value="">請選擇...</option>
                                @foreach($sources as $source)
                                    <option value="{{ $source->code }}" @if($source->code == $data->type) selected @endif readonly>{{ $source->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 col-md-4" id="source_company">
                            <label for="source_company_id" class="form-label">來源公司名稱<span class="text-danger">*</span>@if(isset($sale_company))（{{ $sale_company->company_name->name }}）@endif</label>
                            <input list="source_company_name_list_q" class="form-control" id="source_company_name_q" 
                                    name="source_company_name_q" placeholder="請輸入醫院、禮儀社、美容院、繁殖場、狗園名稱" @if(isset($sale_company)) value="{{ $sale_company->company_id }}" @endif readonly>
                            <datalist id="source_company_name_list_q">
                            </datalist>
                        </div>
                        <div class="mb-3 col-md-4 not_final_show not_memorial_show">
                            <label for="plan_id" class="form-label">方案選擇<span class="text-danger">*</span></label>
                            <select id="plan_id" class="form-select" name="plan_id" disabled>
                                <option value="">請選擇...</option>
                                @foreach ($plans as $plan)
                                    <option value="{{ $plan->id }}" @if($data->plan_id == $plan->id) selected @endif>{{ $plan->name }}</option>
                                @endforeach                             
                            </select>
                        </div>
                        <div class="mb-3 col-md-4 not_final_show not_memorial_show">
                            <label for="plan_price" class="form-label">方案價格<span class="text-danger">*</span></label>
                            <input type="text" class="form-control total_number" id="plan_price" name="plan_price" value="{{ $data->plan_price }}" readonly >
                        </div>
                        <div class="mb-3 col-md-4 not_memorial_show" id="final_price">
                            <label for="plan_price" class="form-label">收款金額<span class="text-danger">*</span></label>
                            <input type="text" class="form-control total_number"  name="final_price" value="{{ $data->pay_price }}" readonly >
                        </div>
                    </div>
                </div>
            </div> <!-- end card -->
        </div> <!-- end col -->
    </div>
    
    <div class="row" id="prom_div">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="text-uppercase bg-light p-2 mt-0 mb-3">後續處理</h5>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="tech-companies-1" class="table prom-list">
                                    <thead>
                                        <tr>
                                            <th>處理方式<span class="text-danger">*</span></th>
                                            <th>名稱<span class="text-danger">*</span></th>
                                            <th>售價<span class="text-danger">*</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($sale_proms as $key=>$sale_prom)
                                            <tr id="row-{{ $key }}">
                                                <td>
                                                    <select id="select_prom_{{$key}}" alt="{{ $key }}" class="mobile form-select" name="select_proms[]" onchange="chgItems(this)" disabled>
                                                        <option value="" selected>請選擇</option>
                                                        <option value="A" @if($sale_prom->prom_type == 'A') selected @endif>安葬處理</option>
                                                        <option value="B" @if($sale_prom->prom_type == 'B') selected @endif>後續處理</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select id="prom_{{$key}}" class="mobile form-select" name="prom[]" disabled>
                                                        @foreach($proms as $prom)
                                                            <option value="{{ $prom->id }}" @if($sale_prom->prom_id == $prom->id) selected @endif >{{ $prom->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" class="mobile form-control total_number" id="prom_total_{{$key}}" name="prom_total[]" value="{{ $sale_prom->prom_total }}" readonly>
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

    <div class="row" id="gdpaper_div">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="text-uppercase bg-light p-2 mt-0 mb-3">金紙選購</h5>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="tech-companies-1" class="table gdpaper-list">
                                    <thead>
                                        <tr>
                                            <th>金紙名稱<span class="text-danger">*</span></th>
                                            <th>數量<span class="text-danger">*</span></th>
                                            <th>售價<span class="text-danger">*</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($sale_gdpapers as $key=>$sale_gdpaper)
                                            <tr id="row-{{ $key }}">
                                            <td>
                                                <select id="gdpaper_id_{{$key}}" alt="{{ $key }}" class="mobile form-select" name="gdpaper_ids[]" onchange="chgPapers(this)" disabled >
                                                    @foreach($products as $product)
                                                        <option value="{{ $product->id }}" @if($product->id == $sale_gdpaper->gdpaper_id) selected @endif>{{ $product->name }}({{ $product->price }})</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" class="mobile form-control" id="gdpaper_num_{{$key}}" alt="{{ $key }}"  name="gdpaper_num[]" value="{{ $sale_gdpaper->gdpaper_num }}" onchange="chgNums(this)" readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="mobile form-control total_number" id="gdpaper_total_{{$key}}" name="gdpaper_total[]" value="{{ $sale_gdpaper->gdpaper_total }}" readonly>
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
                        <div class="mb-3 col-md-3">
                            <label for="pay_id" class="form-label">支付類別<span class="text-danger">*</span></label>
                            <select class="form-select" name="pay_id" required disabled>
                                <option value="" selected>請選擇</option>
                                <option value="A" @if($data->pay_id == 'A') selected @endif>一次付清</option>
                                <option value="C" @if($data->pay_id == 'C') selected @endif>訂金</option>
                                <option value="E" @if($data->pay_id == 'E') selected @endif>追加</option>
                                <option value="D" @if($data->pay_id == 'D') selected @endif>尾款</option>
                            </select>
                        </div>
                        <div class="mb-3 col-md-3">
                            <label for="pay_method" class="form-label">收款方式<span class="text-danger">*</span></label>
                            <select class="form-select" id="pay_method" name="pay_method" disabled required >
                                <option value="" selected>請選擇</option>
                                <option value="A" @if($data->pay_method == 'A') selected @endif>現金</option>
                                <option value="B" @if($data->pay_method == 'B') selected @endif>匯款</option>
                                <option value="C" @if($data->pay_method == 'C') selected @endif>現金與匯款</option>
                            </select>
                        </div>
                        <div class="mb-3 col-md-3" id="cash_price_div">
                            <label for="pay_price" class="form-label">現金收款<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="cash_price" name="cash_price" value="{{ $data->cash_price }}" readonly>
                        </div>
                        <div class="mb-3 col-md-3" id="transfer_price_div">
                            <label for="pay_price" class="form-label">匯款收款<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="transfer_price" name="transfer_price" value="{{ $data->transfer_price }}" readonly>
                        </div>
                        <div class="mb-3 col-md-3" id="transfer_number_div">
                            <label for="pay_price" class="form-label">匯款後四碼<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="transfer_number" name="transfer_number" value="{{ $data->transfer_number }}" readonly>
                        </div>
                        <div class="mb-3 col-md-3">
                            <label for="pay_price" class="form-label">本次收款<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="pay_price" name="pay_price" value="{{ $data->pay_price }}" required readonly>
                        </div>
                        <div class="mb-3 col-md-3">
                            <label for="total" class="form-label">應收金額<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="total" name="total" value="{{ $data->total }}" readonly>
                        </div>
                    </div>
                    <div>
                        <label class="form-label">備註</label>
                        <textarea class="form-control" rows="3" placeholder="" name="comm"></textarea>
                    </div>
                </div>
            </div> <!-- end card -->
        </div> <!-- end col -->
        
    </div>
    <!-- end row -->
    <input type="hidden" id="row_id" name="row_id" value="">



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
    check_change = $('input[name="check_change"]').val();
    if(check_change == 1){
        $("#change_user_div").show();

    }else{
        $("#change_user_div").hide();
    }

    check_split = $('input[name="check_split"]').val();
    if(check_split == 1){
        $("#check_split_div").show();

    }else{
        $("#check_split_div").hide();
    }

    
    $("#check_change").on("change", function() {
        if ($(this).is(':checked')) {
            $("#change_user_div").show(300);
            $(this).val(1);
        }
        else {
            $("#change_user_div").hide(300);
        }
    });

    $("#check_split").on("change", function() {
        if ($(this).is(':checked')) {
            $("#check_split_div").show(300);
            $(this).val(1);
        }
        else {
            $("#check_split_div").hide(300);
            $(this).val(0);
        }
    });


    type_list = $('select[name="type_list"]').val();

    //案件單類別
    if(type_list == 'memorial'){
        $(".not_memorial_show").hide(300);
        $("#cust_name_q").prop('required', false);
        $("#pet_name").prop('required', false);
        $("#kg").prop('required', false);
        $("#type").prop('required', false);
        $("#plan_id").prop('required', false);
    }

    type = $('select[name="type"]').val();
    if(type == 'H' || type == 'B' || type == 'Salon' || type == 'G' || type == 'dogpark'){
        $("#source_company").show(300);
        $("#source_company_name_q").prop('required', true);
    }else{
        $("#source_company").hide(300);
        $("#source_company_name_q").prop('required', false);
    }

    $('select[name="type"]').on('change', function() {
        if($(this).val() == 'H' || $(this).val() == 'B' || $(this).val() == 'Salon' || $(this).val() == 'G' || $(this).val() == 'dogpark'){
            $("#source_company").show(300);
            $("#source_company_name_q").prop('required', true);
        }else{
            $("#source_company").hide(300);
            $("#source_company_name_q").prop('required', false);
            $("#source_company_name_q").val('null');
        }
    });




    $("#cash_price_div").hide();
    $("#transfer_price_div").hide();
    $("#transfer_number_div").hide();
    payMethod = $('select[name="pay_method"]').val();
    if(payMethod == 'C'){
            $("#cash_price_div").show(300);
            $("#transfer_price_div").show(300);
            $("#transfer_number_div").show(300);
            $("#pay_price").prop('required', false);
            $("#cash_price").prop('required', true);
            $("#transfer_price").prop('required', true);
            $("#transfer_number").prop('required', true);
        }else if(payMethod == 'B'){
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

    
    $("#final_price").on('input', function(){
        calculate_price();
    });
    
    $("#plan_price").on('input', function(){
        calculate_price();
    });

    $(".total_number").on('input', function(){
        calculate_price();
    });
    

    function chgItems(obj){
        $("#row_id").val($("#"+ obj.id).attr('alt'));
        row_id = $("#row_id").val();
        $.ajax({
            url : '{{ route('prom.search') }}',
            data:{'select_prom':$("#select_prom_"+row_id).val()},
            success:function(data){
                $("#prom_"+row_id).html(data);
                $("#prom_total_"+row_id).on('input', function(){
                    calculate_price();
                });
            }
        });
    }

    function chgNums(obj){
        $("#row_id").val($("#"+ obj.id).attr('alt'));
        row_id = $("#row_id").val();
        $.ajax({
            url : '{{ route('gdpaper.search') }}',
            data:{'gdpaper_id':$("#gdpaper_id_"+row_id).val()},
            success:function(data){
                $("#gdpaper_num_"+row_id).on('change', function(){
                    var gdpaper_num = $("#gdpaper_num_"+row_id).val();
                    $("#gdpaper_total_"+row_id).val(gdpaper_num*data);
                    calculate_price();
                });
            }
        });
    }

    function chgPapers(obj){
        $("#row_id").val($("#"+ obj.id).attr('alt'));
        row_id = $("#row_id").val();
        $.ajax({
            url : '{{ route('gdpaper.search') }}',
            data:{'gdpaper_id':$("#gdpaper_id_"+row_id).val()},
            success:function(data){
                if($("#gdpaper_num_"+row_id).val()){
                    var gdpaper_num = $("#gdpaper_num_"+row_id).val();
                    $("#gdpaper_total_"+row_id).val(gdpaper_num*data);
                    calculate_price();
                }
                $("#gdpaper_num_"+row_id).on('change', function(){
                    var gdpaper_num = $("#gdpaper_num_"+row_id).val();
                    $("#gdpaper_total_"+row_id).val(gdpaper_num*data);
                    calculate_price();
                });
            }
        });
    }
    

    $("table.prom-list tbody").on("click", ".ibtnDel_prom", function() {
        $(this).closest('tr').remove();
    });  

    $("table.gdpaper-list tbody").on("click", ".ibtnDel_gdpaper", function() {
        $(this).closest('tr').remove();
    });

    $("table.gdpaper-list tbody").on("click", ".ibtnAdd_gdpaper", function() {
        rowCount = $('table.gdpaper-list tr').length - 1;
        var newRow = $("<tr>");
        var cols = '';
        cols += '<td class="text-center"><button type="button" class="ibtnDel_gdpaper demo-delete-row btn btn-danger btn-sm btn-icon"><i class="fa fa-times"></i></button></td>';
        cols += '<td>';
        cols += '<select id="gdpaper_id_'+rowCount+'" alt="'+rowCount+'" class="mobile form-select" name="gdpaper_ids[]" onchange="chgPapers(this)">';
        cols += '<option value="" selected>請選擇...</option>';
            @foreach($products as $product)
                cols += '<option value="{{ $product->id }}">{{ $product->name }}({{ $product->price }})</option>';
            @endforeach
        cols += '</select>';
        cols += '</td>';
        cols += '<td>';
        cols += '<input type="number" class="mobile form-control" id="gdpaper_num_'+rowCount+'" name="gdpaper_num[]" value="">';
        cols += '</td>';
        cols += '<td>';
        cols += '<input type="text" class="mobile form-control total_number" id="gdpaper_total_'+rowCount+'" name="gdpaper_total[]">';
        cols += '</td>';
        cols += '</tr>';
        newRow.append(cols);
        $("table.gdpaper-list tbody").append(newRow);
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
    }

   


    $( "#cust_name_q" ).keydown(function() {
            $value=$(this).val();
            $.ajax({
            type : 'get',
            url : '{{ route('customer.search') }}',
            data:{'cust_name':$value},
            success:function(data){
                $('#cust_name_list_q').html(data);
            }
            });
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
</script>

{{-- <script type="text/javascript">
    
    $(document).ready(function() {
  $("#your-form").submit(function(event) {
    event.preventDefault(); // 阻止預設的表單提交行為
    var formData = $(this).serialize(); // 將表單數據序列化為字串
    
    // 使用AJAX發送表單數據
    $.ajax({
      url: '{{ route('sale.data.create') }}',
      type: "POST",
      data: formData,
      success: function(response) {
        // 請求成功的處理邏輯
      },
      error: function(xhr, status, error) {
        // 請求失敗的處理邏輯
      }
    });
  });
});

</script> --}}
<!-- end demo js-->
@endsection