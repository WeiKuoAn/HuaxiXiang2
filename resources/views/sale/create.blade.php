@extends('layouts.vertical', ["page_title"=> "新增業務Key單"])

@section('css')
{{-- <link href="{{asset('assets/libs/select2/select2.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/libs/dropzone/dropzone.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/libs/quill/quill.min.css')}}" rel="stylesheet" type="text/css" /> --}}
<link href="{{ URL::asset('assets/css/customization.css') }}" id="app-style" rel="stylesheet" type="text/css" />
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
                        <li class="breadcrumb-item active">新增業務Key單</li>
                    </ol>
                </div>
                <h5 class="page-title">新增業務Key單</h5>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <form action="{{ route('sale.data.create') }}" method="POST" id="your-form"  enctype="multipart/form-data" data-plugin="dropzone" data-previews-container="#file-previews" data-upload-preview-template="#uploadPreviewTemplate">
        @csrf
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="text-uppercase bg-light  p-2 mt-0 mb-3">基本資訊</h5>
                    <div class="row">
                        <div class="mb-3 col-md-4">
                            <label for="type_list" class="form-label">案件類別選擇<span class="text-danger">*</span></label>
                            <select id="type_list" class="form-select" name="type_list" >
                                <option value="dispatch">派件單</option>
                                <option value="memorial">追思單</option>                            
                            </select>
                        </div>
                        <div class="mb-3 col-md-4">
                            <label for="pay_id" class="form-label">支付類別<span class="text-danger">*</span></label>
                            <select class="form-select" name="pay_id" required>
                                <option value="" selected>請選擇</option>
                                <option value="A">一次付清</option>
                                <option value="C">訂金</option>
                                <option value="E">追加</option>
                                <option value="D">尾款</option>
                            </select>
                        </div>
                        <div class="mb-3 col-md-4">
                            <label for="sale_on" class="form-label">單號<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="sale_on" name="sale_on" required >
                        </div>
                        <div class="mb-3 col-md-4">
                            <label for="sale_date" class="form-label">日期<span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="sale_date" name="sale_date" required>
                        </div>
                        <div class="mb-3 col-md-4 not_memorial_show">
                            <label for="customer_id" class="form-label">客戶名稱<span class="text-danger">*</span></label>
                            <input list="cust_name_list_q" class="form-control" id="cust_name_q" name="cust_name_q" placeholder="請輸入客戶姓名" required>
                            <datalist id="cust_name_list_q">
                            </datalist>
                        </div>
                        <div class="mb-3 col-md-4 not_memorial_show">
                            <label for="pet_name" class="form-label">寵物名稱<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="pet_name" name="pet_name" required >
                        </div>
                        <div class="mb-3 col-md-4 not_final_show not_memorial_show">
                            <label for="kg" class="form-label">公斤數<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="kg" name="kg" >
                        </div>
                        <div class="mb-3 col-md-4 not_final_show not_memorial_show">
                            <label for="type" class="form-label">案件來源<span class="text-danger">*</span></label>
                            <select id="type" class="form-select" name="type" >
                                <option value="">請選擇...</option>
                                @foreach($sources as $source)
                                    <option value="{{ $source->code }}">{{ $source->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 col-md-4" id="source_company">
                            <label for="source_company_id" class="form-label">來源公司名稱<span class="text-danger">*</span></label>
                            <input list="source_company_name_list_q" class="form-control" id="source_company_name_q" name="source_company_name_q" placeholder="請輸入醫院、禮儀社、美容院、繁殖場、狗園名稱">
                            <datalist id="source_company_name_list_q">
                            </datalist>
                        </div>
                        <div class="mb-3 col-md-4 not_final_show not_memorial_show plan">
                            <label for="plan_id" class="form-label">方案選擇<span class="text-danger">*</span></label>
                            <select id="plan_id" class="form-select" name="plan_id" >
                                <option value="">請選擇...</option>
                                @foreach($plans as $plan)
                                    <option value="{{ $plan->id }}">{{ $plan->name }}</option>
                                @endforeach                                
                            </select>
                        </div>
                        <div class="mb-3 col-md-4 not_final_show not_memorial_show plan_price">
                            <label for="plan_price" class="form-label">方案價格<span class="text-danger">*</span></label>
                            <input type="text" class="form-control total_number" id="plan_price" name="plan_price" >
                        </div>
                        <div class="mb-3 col-md-4" id="final_price">
                            <label for="plan_price" class="form-label">方案追加/尾款價格<span class="text-danger">*</span></label>
                            <input type="text" class="form-control total_number"  name="final_price" >
                        </div>
                        <div class="mb-3 col-md-4">
                            <label for="user_id" class="form-label">服務專員<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="user_id" name="user_id" readonly value="{{ Auth::user()->name }}">
                        </div>
                        {{-- <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="plan_price" class="form-label">接件縣市<span class="text-danger">*</span></label>
                                <div class="twzipcode mb-2">
                                    <select data-role="county"></select>
                                    <select data-role="district"></select>
                                    <select data-role="zipcode"></select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="AddNew-Phone">接件地址<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="address"  >
                            </div>
                            
                        </div> --}}
                        <div class="row">
                            <div class="mb-1 mt-1">
                                <div class="form-check" id="send_div">
                                    <input type="checkbox" class="form-check-input" id="send" name="send" @if(isset($sale_change)) checked value="1"  @endif >
                                    <label class="form-check-label" for="send"><b>親送</b></label>
                                </div>
                            </div>
                            <div class="mb-1 mt-1" id="connector_div">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="connector_address" name="connector_address" @if(isset($sale_split)) checked value="1" @endif >
                                    <label class="form-check-label" for="connector_address"><b>接體地址不為客戶地址</b></label>
                                </div>
                                <div class="mt-2 row" id="connector_address_div">
                                    <div class="col-md-4 mb-3">
                                        <label for="plan_price" class="form-label">接體縣市<span class="text-danger">*</span></label>
                                        <div class="twzipcode mb-2">
                                            <select data-role="county" required></select>
                                            <select data-role="district" required></select>
                                            <select data-role="zipcode" required></select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="AddNew-Phone">接體地址<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="address" name="address" >
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div> <!-- end card -->
        </div> <!-- end col -->
    </div>
    
    <div class="row not_memorial_show" id="prom_div">
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
                                            <th></th>
                                            <th>處理方式<span class="text-danger">*</span></th>
                                            <th>名稱<span class="text-danger">*</span></th>
                                            <th>售價<span class="text-danger">*</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $j = 0; @endphp
                                        @for ($i = 0; $i < 1; $i++)
                                            @php $j = $i+1; @endphp
                                            <tr id="row-{{ $i }}">
                                                <td class="text-center">
                                                    @if($j==1)
                                                    <button type="button" class="ibtnAdd_prom demo-delete-row btn btn-primary btn-sm btn-icon"><i class="fa fas fa-plus"></i></button>                                                    
                                                    @else
                                                    <button type="button" class="ibtnDel_prom demo-delete-row btn btn-danger btn-sm btn-icon"><i class="fa fa-times"></i></button>
                                                    @endif
                                                </td>
                                                <td>
                                                    <select id="select_prom_{{$i}}" alt="{{ $i }}" class="mobile form-select" name="select_proms[]" onchange="chgItems(this)">
                                                        <option value="" selected>請選擇</option>
                                                        <option value="A">安葬處理</option>
                                                        <option value="B">後續處理</option>
                                                        <option value="C">其他處理</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select id="prom_{{$i}}" class="mobile form-select" name="prom[]" >
                                                        <option value="">請選擇</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" class="mobile form-control total_number" id="prom_total_{{$i}}" name="prom_total[]" >
                                                </td>
                                            </tr>
                                        @endfor
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
                                            <th></th>
                                            <th>金紙名稱<span class="text-danger">*</span></th>
                                            <th>數量<span class="text-danger">*</span></th>
                                            <th>售價<span class="text-danger">*</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $j = 0; @endphp
                                        @for ($i = 0; $i < 1; $i++)
                                            @php $j = $i+1; @endphp
                                            <tr id="row-{{ $i }}">
                                                <td class="text-center">
                                                    @if($j==1)
                                                    <button type="button" class="ibtnAdd_gdpaper demo-delete-row btn btn-primary btn-sm btn-icon"><i class="fa fas fa-plus"></i></button>                                                    
                                                    @else
                                                    <button type="button" class="ibtnDel_gdpaper demo-delete-row btn btn-danger btn-sm btn-icon"><i class="fa fa-times"></i></button>
                                                    @endif
                                                </td>
                                            <td>
                                                <select id="gdpaper_id_{{$i}}" alt="{{ $i }}" class="mobile form-select" name="gdpaper_ids[]" onchange="chgPapers(this)" >
                                                    <option value="" selected>請選擇...</option>
                                                    @foreach($products as $product)
                                                        <option value="{{ $product->id }}">{{ $product->name }}({{ $product->price }})</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" alt="{{ $i }}"  class="mobile form-control" id="gdpaper_num_{{$i}}" name="gdpaper_num[]" onchange="chgNums(this)" onclick="chgNums(this)" onkeydown="chgNums(this)">
                                            </td>
                                            <td>
                                                <input type="text" class="mobile form-control total_number" id="gdpaper_total_{{$i}}" name="gdpaper_total[]" value="">
                                            </td>
                                        </tr>
                                        @endfor
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
                            <h2>應收金額<span id="total_text" class="text-danger">0</span>元</h2>
                            <input type="hidden" class="form-control" id="total" name="total" value="0" readonly>
                        </div>
                        <div class="mb-3 col-md-4">
                            <label for="pay_id" class="form-label">支付方式<span class="text-danger">*</span></label>
                            <select class="form-select" name="pay_method" required>
                                <option value="" selected>請選擇</option>
                                <option value="A">現金</option>
                                <option value="B">匯款</option>
                                <option value="C">現金與匯款</option>
                            </select>
                        </div>
                        <div class="mb-3 col-md-4" id="cash_price_div">
                            <label for="pay_price" class="form-label">現金收款<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="cash_price" name="cash_price">
                        </div>
                        <div class="mb-3 col-md-4" id="transfer_price_div">
                            <label for="pay_price" class="form-label">匯款收款<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="transfer_price" name="transfer_price">
                        </div>
                        <div class="mb-3 col-md-4" id="transfer_channel_div">
                            <label for="pay_id" class="form-label">匯款管道<span class="text-danger">*</span></label>
                            <select class="form-select" name="transfer_channel">
                                <option value="" selected>請選擇</option>
                                <option value="銀行轉帳">銀行轉帳</option>
                                <option value="Line Pay">Line Pay</option>
                            </select>
                        </div>
                        <div class="mb-3 col-md-4" id="transfer_number_div">
                            <label for="pay_price" class="form-label">匯款後四碼</label>
                            <input type="text" class="form-control" id="transfer_number" name="transfer_number">
                        </div>
                        <div class="mb-3 col-md-4" id="this_price_div">
                            <label for="pay_price" class="form-label">本次收款<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="pay_price" name="pay_price" required>
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

    <div class="row">
        <div class="col-12">
            <div class="text-center mb-3">
                <button type="button" class="btn w-sm btn-light waves-effect" onclick="history.go(-1)">回上一頁</button>
                <button type="submit" class="btn w-sm btn-success waves-effect waves-light">新增</button>
                {{-- <button type="button" class="btn w-sm btn-danger waves-effect waves-light">Delete</button> --}}
            </div>
        </div> <!-- end col -->
    </div>
    <input type="hidden" id="row_id" name="row_id" value="">

</form>


</div> <!-- container -->

@endsection

@section('script')
<!-- third party js -->
<script src="{{ asset('assets/js/twzipcode-1.4.1-min.js') }}"></script>
<script src="{{ asset('assets/js/twzipcode.js') }}"></script>
<!-- third party js ends -->

<!-- demo app -->   


<script>
   $(".twzipcode").twzipcode({
       css: ["twzipcode-select", "twzipcode-select" , "twzipcode-select"], // 自訂 "城市"、"地區" class 名稱 
       countyName: "county", // 自訂城市 select 標籤的 name 值
       districtName: "district", // 自訂地區 select 標籤的 name 值
       zipcodeName: "zipcode", // 自訂地區 select 標籤的 name 值
    });


    //親送
    send = $('input[name="send"]').val();
    if(send == 1){
        $("#connector_address_div").show();

    }else{
        $("#connector_address_div").hide();
    }
    $("#send").on("change", function() {
        if ($(this).is(':checked')) {
            $(this).val(1);
            $("#connector_div").hide(300);

        }
        else {
            $(this).val(0);
            $("#connector_div").show(300);
        }
    });
    //地址
    connector_address = $('input[name="connector_address"]').val();
    $("#connector_address").on("change", function() {
        if ($(this).is(':checked')) {
            $("#connector_address_div").show(300);
            $("#send_div").hide(300);
            $(this).val(1);
            $('#your-form').submit(function(event){
                var county = $('select[name="county"]').val();
                if (county == '') {
                    alert('接體縣市不得為空！');
                    event.preventDefault();
                }
            });
            $("#address").prop('required', true);
        }
        else {
            $("#connector_address_div").hide(300);
            $("#send_div").show(300);
            $(this).val(0);
            $('#your-form').off('submit');
            // Remove pet name required attribute
            $("#address").prop('required', false);
        }
    });
    if(connector_address == 1){
        $("#connector_address_div").show();
    }else{
        $("#connector_address_div").hide();
    }


    $("#final_price").hide();

    $("#source_company").hide();
    $('select[name="type"]').on('change', function() {
        if($(this).val() == 'H' || $(this).val() == 'B' || $(this).val() == 'Salon' || $(this).val() == 'dogpark' || $(this).val() == 'G' || $(this).val() == 'other'){
            $("#source_company").show(300);
            $("#source_company_name_q").prop('required', true);
        }else{
            $("#source_company").hide(300);
            $("#source_company_name_q").prop('required', false);
        }
    });

    
    //案件單類別
    $('select[name="type_list"]').on('change', function() {
        payIdValue = $('select[name="pay_id"]').val();
        if($(this).val() == 'memorial'){
            $(".not_memorial_show").hide(300);
            $("#final_price").hide(300);
            $("#cust_name_q").prop('required', false);
            $("#pet_name").prop('required', false);
            $("#kg").prop('required', false);
            $("#type").prop('required', false);
            $("#plan_id").prop('required', false);
            $("#plan_price").prop('required', false);
            $("#send_div").hide(300);
            $("#connector_div").hide(300);
        }else if($(this).val() == 'dispatch'){
            $(".not_memorial_show").show(300);
            $("#send_div").show(300);
            $("#connector_div").show(300);
            if(payIdValue == 'D' || payIdValue =='E'){
                // $("#final_price").show(300);
                $(".not_final_show").hide();
                $("#kg").prop('required', false);
                $("#type").prop('required', false);
                $("#plan_id").prop('required', false);
                $("#plan_price").prop('required', false);
                $("#send_div").hide();
                $("#connector_div").hide();
            }else{
                $("#final_price").hide(300);
                $(".not_final_show").show(300);
                $("#pet_name").prop('required', true);
                $("#kg").prop('required', true);
                $("#type").prop('required', true);
                $("#plan_id").prop('required', true);
                $("#plan_price").prop('required', true);
                $("#send_div").show(300);
                $("#connector_div").show(300);
            }
        }
    });

    $('select[name="pay_id"]').on('change', function() {
        type_list = $('select[name="type_list"]').val();
        console.log(type_list);
        if($(this).val() == 'D' || $(this).val() =='E'){
            $(".not_final_show").hide(300);
            $("#kg").prop('required', false);
            $("#type").prop('required', false);
            $("#plan_id").prop('required', false);
            $("#plan_price").prop('required', false);
            if(type_list == 'memorial'){
                $("#final_price").hide();
                $(".not_memorial_show").hide();
                $("#send_div").hide(300);
                $("#connector_div").hide(300);
            }
            $("#send_div").hide();
            $("#connector_div").hide();
        }else{
            $("#final_price").hide(300);
            $("#send_div").show(300);
            $("#connector_div").show(300);
            if(type_list == 'memorial'){
                $("#final_price").hide();
                $(".not_memorial_show").hide();
                $("#send_div").hide();
                $("#connector_div").hide();
            }else{
                $(".not_memorial_show").show();
                $("#pet_name").prop('required', true);
                $("#kg").prop('required', true);
                $("#type").prop('required', true);
                $("#plan_id").prop('required', true);
                $("#plan_price").prop('required', true);
                $("#send_div").show();
                $("#connector_div").show();
            }
            
        }
    });
    
    $("#cash_price_div").hide();
    $("#transfer_price_div").hide();
    $("#transfer_channel_div").hide();
    $("#transfer_number_div").hide();

    $('select[name="pay_method"]').on('change', function() {
        console.log($(this).val());
        if($(this).val() == 'C'){
            $("#cash_price_div").show(300);
            $("#transfer_price_div").show(300);
            $("#transfer_number_div").show(300);
            $("#transfer_channel_div").show(300);
            $("#pay_price").prop('required', false);
            $("#cash_price").prop('required', true);
            $("#transfer_price").prop('required', true);
            $("#transfer_channel").prop('required', true);
        }else if($(this).val() == 'B'){
            $("#cash_price_div").hide(300);
            $("#transfer_price_div").hide(300);
            $("#transfer_number_div").show(300);
            $("#transfer_channel_div").show(300);
            $("#pay_price").prop('required', true);
            $("#cash_price").prop('required', false);
            $("#transfer_price").prop('required', false);
            $("#transfer_channel").prop('required', true);
        }else{
            $("#cash_price_div").hide(300);
            $("#transfer_price_div").hide(300);
            $("#transfer_channel_div").hide(300);
            $("#transfer_number_div").hide(300);
            $("#pay_price").prop('required', true);
            $("#cash_price").prop('required', false);
            $("#transfer_price").prop('required', false);
            $("#transfer_channel").prop('required', false);
        }
    });

    

    $("#plan_id").on('change', function(){
        // var plan_id = $(this).val();
        // if(plan_id == '3'){
        //     var total = $("#total").val();
        //     total = total - 100;
        //     $("#total").val(total);
        //     $("#total_text").html(total);
        // }else{
            calculate_price();
        // }
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

    function chgPapers(obj){
        $("#row_id").val($("#"+ obj.id).attr('alt'));
        row_id = $("#row_id").val();
        console.log(row_id);
        $.ajax({
            url : '{{ route('gdpaper.search') }}',
            data:{'gdpaper_id':$("#gdpaper_id_"+row_id).val()},
            success:function(data){
                if($("#gdpaper_num_"+row_id).val()){
                    var gdpaper_num = $("#gdpaper_num_"+row_id).val();
                    $("#gdpaper_total_"+row_id).val(gdpaper_num*data);
                    calculate_price();
                }else{
                    $("#gdpaper_num_"+row_id).on('change', function(){
                        var gdpaper_num = $("#gdpaper_num_"+row_id).val();
                        $("#gdpaper_total_"+row_id).val(gdpaper_num*data);
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
            url : '{{ route('gdpaper.search') }}',
            data:{'gdpaper_id':$("#gdpaper_id_"+row_id).val()},
            success:function(data){
                $("#gdpaper_num_"+row_id).on('change', function(){
                    console.log("#gdpaper_total_"+row_id);
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
        cols += '<input type="number"  alt="'+rowCount+'"  class="mobile form-control" id="gdpaper_num_'+rowCount+'" name="gdpaper_num[]" value="" onchange="chgNums(this)" onmousedown="chgNums(this)" onkeydown="chgNums(this)">';
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
        plan_id = $('select[name="plan_id"]').val();
        // if(plan_id == '3'){
        //     total = total - 100;
        // }
        $("#total").val(total);
        $("#total_text").html(total);
        console.log(plan_id);
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
            console.log($value);
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
            cols += '<option value="C">其他處理</option>';
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

        $("#not_cust_adress").hide();
        
        $("#in_preson").on("change", function() {
                            if ($(this).is(':checked')) {
                                $("#not_cust_adress").hide(300);
                                $(this).val(0);
                            }
                            else {
                                $("#not_cust_adress").show(300);
                                $(this).val(1);
                            }
                        });
                        $("#not_cust_adress").on("change", function() {
                            if ($(this).is(':checked')) {
                                $("#connector_afdress_div").show(300);
                                $(this).val(1);
                            }
                            else {
                                $("#connector_afdress_div").hide(300);
                                $(this).val(0);
                            }
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