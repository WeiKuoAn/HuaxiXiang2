@extends('layouts.vertical', ["page_title"=> "查看支出key單"])

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
            width: 120px;
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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">支出管理</a></li>
                        <li class="breadcrumb-item active">查看支出Key單</li>
                    </ol>
                </div>
                <h5 class="page-title">查看支出Key單</h5>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="text-uppercase bg-light  p-2 mt-0 mb-3">支出總資訊</h5>
                    <div class="row">
                        <div class="mb-3 col-md-3">
                            <label for="sale_on" class="form-label">支出單號<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="pay_on" name="pay_on" value="{{ $data->pay_on }}" readonly >
                        </div>
                        <div class="mb-3 col-md-3">
                            <label for="sale_date" class="form-label">總金額<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="price" name="price" value="{{ $data->price }}" required>
                        </div>
                        <div class="mb-3 col-md-3">
                            <label for="sale_date" class="form-label">用途說明</label>
                            <input type="text" class="form-control" id="comment" name="comment" value="{{ $data->comment }}">
                        </div>
                        <div class="mb-3 col-md-3">
                            <label for="user_id" class="form-label">服務專員<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="user_id" name="user_id" value="{{ $data->user_name->name }}" readonly>
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
                    <h5 class="text-uppercase bg-light p-2 mt-0 mb-3">發票清單</h5>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="cart" class="table cart-list">
                                    <thead>
                                        <tr>
                                            <th>消費日期<span class="text-danger">*</span></th>
                                            <th>會計項目<span class="text-danger">*</span></th>
                                            <th>發票號碼<span class="text-danger">*</span></th>
                                            <th>支出金額<span class="text-danger">*</span></th>
                                            <th>發票類型<span class="text-danger">*</span></th>
                                            <th></th>
                                            <th>備註<span class="text-danger">*</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(isset($data->pay_items))
                                            @foreach($data->pay_items as $key=>$item)
                                                <tr id="row-{{ $key }}">
                                                    <td scope="row">
                                                    <input id="pay_date-{{ $key }}" class="mobile form-control" type="date" name="pay_data_date[]" value="{{ $item->pay_date }}" required>
                                                    </td>
                                                    <td>
                                                        <select id="pay_id-{{ $key }}" class="form-select" aria-label="Default select example" name="pay_id[]"  required>
                                                            <option value="" selected>請選擇...</option>
                                                            @foreach($pays as $pay)
                                                            <option value="{{ $pay->id }}" @if($pay->id == $item->pay_id) selected @endif>{{ $pay->name  }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                    <input id="pay_invoice-{{ $key }}" class="mobile form-control" type="text" name="pay_invoice_number[]" value="{{ $item->invoice_number }}">
                                                    </td>
                                                    <td>
                                                    <input id="pay_price-{{ $key }}" class="mobile form-control" type="text" name="pay_price[]" value="{{ $item->price }}" required>
                                                    </td>
                                                    <td>
                                                        <select id="pay_invoice_type-{{ $key }}" class="mobile form-select" aria-label="Default select example" name="pay_invoice_type[]" required>
                                                        <option value="" selected>請選擇</option>
                                                        <option @if($item->invoice_type == 'FreeUniform') selected @endif value="FreeUniform">免用統一發票</option><!--FreeUniform-->
                                                        <option @if($item->invoice_type == 'Uniform') selected @endif value="Uniform">統一發票</option><!--Uniform-->
                                                        <option @if($item->invoice_type == 'Other') selected @endif value="Other">其他</option><!--Other-->
                                                    </select>
                                                    </td>
                                                    <td>
                                                        <input id="pay_invoice-{{ $key }}" class="invoice mobile form-control" type="text" name="pay_invoice_number[]" placeholder="請輸入發票號碼"  value="{{ $item->invoice_number }}">
                                                        <input list="vender_number_list_q" class="mobile form-control" id="vendor-{{ $key }}" name="vender_id[]"  @if(isset($item->vender_data)) value="{{ $item->vender_id }}" @else value="{{ $item->vender_id }}" @endif placeholder="請輸入統編號碼">
                                                        <datalist id="vender_number_list_q">
                                                        </datalist>
                                                    </td>
                                                    <td>
                                                        <input id="pay_text-{{ $key }}" class="mobile form-control" type="text" name="pay_text[]" value="{{ $item->comment }}">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div> <!-- end .table-responsive -->
                        </div>
                    </div>
                </div>
            </div> <!-- end card -->
        </div> <!-- end col -->
    </div>

    <form action="{{ route('pay.check.data',$data->id) }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-12">
                <div class="text-center mb-3">
                    <button type="button" class="btn w-sm btn-light waves-effect" onclick="history.go(-1)">
                        @if($data->status != 1)
                        不審核
                        @else
                        回上一頁
                        @endif
                    </button>
                    {{-- <button type="submit" name="submit1" value="flase" id="btn_submit" class="btn w-sm btn-danger waves-effect waves-light" onclick="if(!confirm('是否確定撤回?')){event.returnValue=false;return false;}">撤回</button> --}}
                    @if($data->status != 1)
                    <button type="submit" name="submit1" value="true" id="btn_submit" class="btn w-sm btn-success waves-effect waves-light" onclick="if(!confirm('是否確定審核?')){event.returnValue=false;return false;}">審核</button>
                    @endif
                    {{-- <button type="submit" name="submit1" value="flase" class="btn w-sm btn-danger waves-effect waves-light" onclick="if(!confirm('是否確定退件?')){event.returnValue=false;return false;}">退件</button> --}}
                </div>
            </div> <!-- end col -->
        </div>
    </form>
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
    $(document).ready(function(){
        rowCount = $('#cart tr').length - 1;

        for(var i = 0; i < rowCount; i++)
        {
            invoice_type = $("#pay_invoice_type-" + i).val();
            if(invoice_type == 'FreeUniform'){
                $("#vendor-"+i).show(300);
                $("input#pay_invoice-"+i).hide(300);
                $(".td_show").show(300);
            }else if(invoice_type == 'Uniform'){
                $("input#pay_invoice-"+i).show(300);
                $("#vendor-"+i).show(300);
                $(".td_show").show(300);
            }else if(invoice_type == 'Other'){
                $("input#pay_invoice-"+i).hide(300);
                $("#vendor-"+i).hide(300);
                $(".td_show").hide(300);
            }
        }
    });
</script>
@endsection