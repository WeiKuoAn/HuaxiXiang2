@extends('layouts.vertical', ["page_title"=> "新增支出key單"])

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
                        <li class="breadcrumb-item active">新增支出Key單</li>
                    </ol>
                </div>
                <h5 class="page-title">新增支出Key單</h5>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <form action="{{ route('pay.create.data') }}" method="POST" id="your-form"  enctype="multipart/form-data" data-plugin="dropzone" data-previews-container="#file-previews" data-upload-preview-template="#uploadPreviewTemplate">
        @csrf
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="text-uppercase bg-light  p-2 mt-0 mb-3">支出總資訊</h5>
                    <div class="row">
                        <div class="mb-3 col-md-3">
                            <label for="sale_on" class="form-label">支出單號<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="pay_on" name="pay_on" value="{{ $pay_on }}" readonly >
                        </div>
                        <div class="mb-3 col-md-3">
                            <label for="sale_date" class="form-label">總金額<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="price" name="price" required>
                        </div>
                        <div class="mb-3 col-md-3">
                            <label for="sale_date" class="form-label">用途說明</label>
                            <input type="text" class="form-control" id="comment" name="comment">
                        </div>
                        <div class="mb-3 col-md-3">
                            <label for="user_id" class="form-label">服務專員<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="user_id" name="user_id" readonly value="{{ Auth::user()->name }}">
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
                                            <th></th>
                                            <th>消費日期<span class="text-danger">*</span></th>
                                            <th>會計項目<span class="text-danger">*</span></th>
                                            <th>支出金額<span class="text-danger">*</span></th>
                                            <th>發票類型<span class="text-danger">*</span></th>
                                            <th></th>
                                            <th>備註<span class="text-danger"></span></th>
                                        </tr>
                                    </thead>
                                    <tbody valign="center" align="center">
                                        @for ($i = 0; $i < 1; $i++)
                                        <tr id="row-{{ $i }}" >
                                            <td>
                                            <button class="mobile btn btn-primary del-row" alt="{{ $i }}" type="button" name="button" onclick="del_row(this)">刪除</button>
                                            </td>
                                            <td scope="row">
                                            <input id="pay_date-{{ $i }}" class="mobile form-control" type="date" name="pay_data_date[]" value="{{ $create_today }}" required>
                                            </td>
                                            <td>
                                                <select id="pay_id-{{ $i }}" class="mobile form-select"  aria-label="Default select example" name="pay_id[]" required>
                                                    @foreach($pays as $pay)
                                                    <option value="{{ $pay->id }}" class="mobile">{{ $pay->name  }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                            <input id="pay_price-{{ $i }}" class="mobile form-control" type="text" name="pay_price[]" value="" required>
                                            </td>
                                            <td>
                                                <select id="pay_invoice_type-{{ $i }}" alt="{{ $i }}" class="mobile form-select" aria-label="Default select example" name="pay_invoice_type[]" onchange="chgInvoice(this)" required>
                                                    <option value="" selected>請選擇</option>
                                                    <option value="FreeUniform" >免用統一發票</option><!--FreeUniform-->
                                                    <option value="Uniform" >統一發票</option><!--Uniform-->
                                                    <option value="Other" >其他</option><!--Other-->
                                            </select>
                                            </td>
                                            <td>
                                                <input id="pay_invoice-{{ $i }}" class="invoice mobile form-control" type="text" name="pay_invoice_number[]" value="" placeholder="請輸入發票號碼">
                                                <input list="vender_number_list_q" class="vendor mobile form-control" id="vendor-{{ $i }}" name="vender_id[]" placeholder="請輸入統編號碼">
                                                <datalist id="vender_number_list_q">
                                                </datalist>
                                            </td>
                                            <td>
                                                <input id="pay_text-{{ $i }}" class="mobile form-control" type="text" name="pay_text[]" value="">
                                            </td>
                                        </tr>
                                    @endfor
                                    </tbody>
                                </table>
                            </div> <!-- end .table-responsive -->
                            <div class="form-group row">
                                <div class="col-12">
                                <input id="add_row" class="btn btn-secondary" type="button" name="" value="新增筆數">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- end card -->
        </div> <!-- end col -->
    </div>

    <div class="row">
        <div class="col-12">
            <div class="text-center mb-3">
                <button type="button" class="btn w-sm btn-light waves-effect" onclick="history.go(-1)">回上一頁</button>
                <button type="submit" id="btn_submit" class="btn w-sm btn-success waves-effect waves-light">新增</button>
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
    $(".vendor").hide();
    $(".invoice").hide();
    function del_row(obj){
        $number = $(obj).attr("alt");
        $('#row-'+$number).remove();
    }
    function chgInvoice(obj){
        $number = $(obj).attr("alt");
        var invoice_type = $("#pay_invoice_type-" + $number).val();
        if(invoice_type == 'FreeUniform'){
            $("#vendor-"+$number).show(300);
            $("#vendor-"+$number).prop('required', true);
            $("#pay_invoice-"+$number).hide(300);
        }else if(invoice_type == 'Uniform'){
            $("#pay_invoice-"+$number).show(300);
            $("#vendor-"+$number).show(300);
            $("#pay_invoice-"+$number).prop('required', true);
            $("#vendor-"+$number).prop('required', true);
        }else{
            $("#pay_invoice-"+$number).hide(300);
            $("#vendor-"+$number).hide(300);
        }
        console.log(invoice_type);
    }
        
    $(document).ready(function(){
        $("#add_row").click(function(){
            $rowCount = $('#cart tr').length - 1;
            var $lastRow = $("#cart tr:last"); //grab row before the last row

            $newRow = '<tr id="row-'+$rowCount+'">';
            $newRow += '<td>';    
            $newRow += '<button class="mobile btn btn-primary del-row" alt="'+$rowCount+'" type="button" name="button" onclick="del_row(this)">刪除</button>';
            $newRow += '</td>';
            $newRow += '<td scope="row">';
            $newRow += '<input id="pay_date-'+$rowCount+'" class="mobile form-control" type="date" name="pay_data_date[]" value="{{ $create_today }}" required>';
            $newRow += '</td>';
            $newRow += '<td>';
            $newRow += '<select id="pay_id-'+$rowCount+'" class="mobile form-select" aria-label="Default select example" name="pay_id[]" required>';
            @foreach($pays as $pay)
            $newRow += '<option value="{{ $pay->id }}">{{ $pay->name  }}</option>;'
            @endforeach
            $newRow += '</select>';
            $newRow += '</td>';
            $newRow += '<td>';
            $newRow += '<input id="pay_price-'+$rowCount+'" class="mobile form-control" type="text" name="pay_price[]" value="" required>';
            $newRow += '</td>';
            $newRow += '<td>';
            $newRow += '<select id="pay_invoice_type-'+$rowCount+'"  alt="'+$rowCount+'" class="form-select" aria-label="Default select example" name="pay_invoice_type[]" onchange="chgInvoice(this)"  required>';
            $newRow += '<option value="" selected>請選擇</option>';
            $newRow += '<option value="FreeUniform" >免用統一發票</option>';
            $newRow += '<option value="Uniform" >統一發票</option>';
            $newRow += '<option value="Other" >其他</option>';
            $newRow += '</td>';
            $newRow += '</select>';
            $newRow += '<td>';
            $newRow += '<input style="display: none;" id="pay_invoice-'+$rowCount+'" class="invoice form-control" type="text" name="pay_invoice_number[]" value="" placeholder="請輸入發票號碼" >';
            $newRow += '<input style="display: none;" list="vender_number_list_q"  class="vendor form-control" id="vendor-'+$rowCount+'" name="vender_id[]" placeholder="請輸入統編號碼">';
            $newRow += '<datalist id="vender_number_list_q">';
            $newRow += '</datalist>';
            $newRow += '</td>';
            $newRow += '<td>';
            $newRow += '<input id="pay_text-'+$rowCount+'" class="form-control" type="text" name="pay_text[]" value="">';
            $newRow += '</td>';
            $newRow += '</tr>';
            $lastRow.after($newRow); //add in the new row at the end
        });

        

        $("#btn_submit").click(function(){
            rowCount = $('#cart tr').length - 1;
            total_price = $("#price").val();
            pay_total = 0;
            for(var i = 0; i < rowCount; i++)
            {
                pay_total += parseInt($('#pay_price-'+i).val(),10);

                // pay_total+= Number($('#pay_price-'+$rowCount).val());
            }
            if(total_price != pay_total){
                alert('金額錯誤！');
                return false;
            }
            console.log(pay_total);
        });

        $.ajaxSetup({ headers: { 'csrftoken' : '{{ csrf_token() }}' } });
    });
</script>
<script type="text/javascript">
    $(document).ready(function(){
        rowCount = $('#cart tr').length - 1;
        for(var i = 0; i < rowCount; i++)
        {
            $('#vendor-'+i).keydown(function() {
                $value=$(this).val();
                $.ajax({
                type : 'get',
                url : '{{ route('vender.number') }}',
                data:{'number':$value},
                success:function(data){
                    $('#vender_number_list_q').html(data);
                }
                });
                console.log($value);
            });
        }
    });
</script>
@endsection