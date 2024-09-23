@extends('layouts.vertical', ["page_title"=> "修改業務方案"])

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
                        <li class="breadcrumb-item active">修改業務方案</li>
                    </ol>
                </div>
                <h5 class="page-title">修改業務方案</h5>
            </div>
        </div>
    </div>
    <!-- end page title -->


    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="text-uppercase bg-light  p-2 mt-0 mb-3">修改業務方案</h5>
                    <form action="{{ route('sale.data.change_plan',$data->id) }}" method="POST">
                        @csrf
                    <div class="row">
                        <div class="mb-1 mt-1">

                            <div class="mt-2 row">
                                @if($data->pay_id != 'E' && $data->pay_id != 'D')
                                    <div class="col-auto">
                                        <h4><label class="form-check-label" for="check_change">原方案：</label></h4>
                                    </div>
                                    <div class="col-3">
                                        <select class="form-control" data-toggle="select2" data-width="100%" name="old_plan_id" required>
                                            @if(isset($data->plan_id))
                                                @foreach($plans as $plan)
                                                    <option value="{{ $plan->id }}" @if($data->plan_id == $plan->id) selected  @endif>{{ $plan->name }}</option>
                                                @endforeach
                                            @else
                                                <option value="" selected >請選擇</option>
                                                @foreach($plans as $plan)
                                                    <option value="{{ $plan->id }}" @if($data->plan_id == $plan->id) selected  @endif>{{ $plan->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-auto">
                                        <h4><label class="form-check-label" for="check_change">轉至</label></h4>
                                    </div>
                                    <div class="col-3">
                                        <select class="form-control" data-toggle="select2" data-width="100%" name="new_plan_id" required>
                                                <option value="" selected >請選擇</option>
                                                @foreach($plans as $plan)
                                                    <option value="{{ $plan->id }}" @if($data->plan_id == $plan->id) selected  @endif>{{ $plan->name }}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                @else
                                    <div class="col-auto">
                                        <h4 class="text-danger">僅限訂金、一次付清單可以修改方案</h4>
                                    </div>
                                @endif
                            </div>
                        </div>
                </div>
            </div> <!-- end card -->
        
            <div class="col-12">
                <div class="text-center mb-3">
                        <button type="button" class="btn w-sm btn-light waves-effect" onclick="history.go(-1)">回上一頁</button>
                        @if($data->pay_id != 'E' && $data->pay_id != 'D')
                            <button type="submit" class="btn w-sm btn-success waves-effect waves-light" id="updateButton">確定更新</button>
                        @endif
                </div>
            </div>
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
                            <label for="plan_price" class="form-label">尾款價格<span class="text-danger">*</span></label>
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
    $(document).ready(function() {
        // Listen for changes on the new_plan_id select elemen

        $('#updateButton').click(function(event) {
            // Get the value of the new_plan_id select element
            var newPlanId = $('select[name="new_plan_id"]').val();
            // Get the value of the old_plan_id select element
            var oldPlanId = $('select[name="old_plan_id"]').val();

            // Check if the new and old plan IDs are different
            if(newPlanId !== oldPlanId) {
                // If they are different, show a confirmation dialog
                var confirmUpdate = confirm('是否已確定更新方案？');
                if(!confirmUpdate) {
                    // If the user cancels, prevent the button's default action
                    event.preventDefault();
                    return false;
                }
            }else{
                alert('舊轉新的業務方案重複！');
                event.preventDefault(); // Prevent form submission
                return false;
            }
        });
    });
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