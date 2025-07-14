@extends('layouts.vertical', ["page_title"=> "編輯業務Key單"])

@section('css')
<link href="{{asset('assets/libs/select2/select2.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/css/customization.css') }}" id="app-style" rel="stylesheet" type="text/css" />
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
                        <li class="breadcrumb-item active">編輯業務Key單</li>
                    </ol>
                </div>
                <h5 class="page-title">編輯業務Key單</h5>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <form action="{{ route('sale.data.edit',$data->id) }}" method="POST" id="your-form"  enctype="multipart/form-data" data-plugin="dropzone" data-previews-container="#file-previews" data-upload-preview-template="#uploadPreviewTemplate">
        @csrf
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="text-uppercase bg-light  p-2 mt-0 mb-3">基本資訊</h5>
                    <div class="alert alert-danger alert-dismissible fade show p-2" id="final_price_display" role="alert"></div>
                    <div class="row">
                        <div class="mb-3 col-md-4">
                            <label for="type_list" class="form-label">案件類別選擇<span class="text-danger">*</span></label>
                            <select id="type_list" class="form-select" name="type_list" >
                                <option value="dispatch" @if($data->type_list == 'dispatch') selected @endif>派件單</option>
                                <option value="memorial" @if($data->type_list == 'memorial') selected @endif>追思單</option>                            
                            </select>
                        </div>
                        <div class="mb-3 col-md-4">
                            <label for="pay_id" class="form-label">支付類別<span class="text-danger">*</span></label>
                            <select class="form-select" name="pay_id" id="pay_id" required>
                                <option value="" selected>請選擇</option>
                                <option value="A" @if($data->pay_id == 'A') selected @endif>一次付清</option>
                                <option value="C" @if($data->pay_id == 'C') selected @endif>訂金</option>
                                <option value="E" @if($data->pay_id == 'E') selected @endif>追加</option>
                                <option value="D" @if($data->pay_id == 'D') selected @endif>尾款</option>
                            </select>
                        </div>
                        <div class="mb-3 col-md-4">
                            <label for="sale_on" class="form-label">單號<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="sale_on" name="sale_on" value="{{ $data->sale_on }}" required>
                            <div id="sale_on_feedback" class="mt-1"></div>
                        </div>
                        <div class="mb-3 col-md-4">
                            <label for="sale_date" class="form-label">日期<span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="sale_date" name="sale_date" value="{{ $data->sale_date }}" required>
                        </div>
                        
                        <div class="mb-3 col-md-4">
                            <label for="customer_id" class="form-label">客戶名稱<span class="text-danger required">*</span></label>
                            <select class="form-control" data-toggle="select2" data-width="100%" name="cust_name_q" id="cust_name_q" required>
                                <option value="">請選擇...</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" @if($data->customer_id == $customer->id) selected @endif>No.{{ $customer->id }} {{ $customer->name }}（{{ $customer->mobile }}）</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 col-md-4">
                            <label for="pet_name" class="form-label">寵物名稱<span class="text-danger required">*</span></label>
                            <input type="text" class="form-control" id="pet_name" name="pet_name" value="{{ $data->pet_name }}">
                        </div>
                        <div class="mb-3 col-md-4 not_final_show not_memorial_show">
                            <label for="variety" class="form-label">寵物品種<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="variety" name="variety" value="{{ $data->variety }}">
                        </div>
                        <div class="mb-3 col-md-4 not_final_show not_memorial_show">
                            <label for="kg" class="form-label">公斤數<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="kg" name="kg" value="{{ $data->kg }}">
                        </div>
                        <div class="mb-3 col-md-4 not_final_show not_memorial_show">
                            <label for="type" class="form-label">案件來源<span class="text-danger">*</span></label>
                            <select id="type" class="form-select" name="type" >
                                <option value="">請選擇...</option>
                                @foreach($sources as $source)
                                    <option value="{{ $source->code }}" @if($source->code == $data->type) selected @endif>{{ $source->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 col-md-4" id="source_company">
                            <label for="source_company_name_q" class="form-label">來源公司名稱<span class="text-danger">*</span></label>
                            <select class="form-control" data-toggle="select2" data-width="100%" name="source_company_name_q" id="source_company_name_q">
                                <option value="">請選擇...</option>
                                @if(isset($sale_company) && isset($sale_company->company_name))
                                    @if($data->type == 'self')
                                        <option value="{{ $sale_company->company_id }}" selected>
                                            （員工）{{ $sale_company->user_name->name }}（{{ $sale_company->user_name->mobile }}）
                                        </option>
                                    @else
                                        <option value="{{ $sale_company->company_id }}" selected>
                                            （{{ $sale_company->company_name->group->name }}）{{ $sale_company->company_name->name }}（{{ $sale_company->company_name->mobile }}）
                                        </option>
                                    @endif
                                @endif
                            </select>
                        </div>
                        <div class="mb-3 col-md-4 not_memorial_show plan">
                            <label for="plan_id" class="form-label">方案選擇<span class="text-danger">*</span></label>
                            <select id="plan_id" class="form-select" name="plan_id" >
                                <option value="">請選擇...</option>
                                @foreach ($plans as $plan)
                                    <option value="{{ $plan->id }}" @if($data->plan_id == $plan->id) selected @endif>{{ $plan->name }}</option>
                                @endforeach                             
                            </select>
                        </div>
                        <div class="mb-3 col-md-4 not_final_show not_memorial_show">
                            <label for="plan_price" class="form-label">方案價格<span class="text-danger">*</span></label>
                            <input type="text" class="form-control total_number" id="plan_price" name="plan_price" value="{{ $data->plan_price }}" >
                        </div>
                        <div class="mb-3 col-md-4" id="suit_field" style="display: none;">
                            <label for="suit_id" class="form-label">套裝選擇<span class="text-danger">*</span></label>
                            <select id="suit_id" class="form-select" name="suit_id">
                                <option value="">請選擇...</option>
                                @foreach ($suits as $suit)
                                    <option value="{{ $suit->id }}" @if($data->suit_id == $suit->id) selected @endif>{{ $suit->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 col-md-4">
                            <label for="user_id" class="form-label">服務專員<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="user_id" name="user_id" value="{{ $data->user_name->name }}" readonly>
                        </div>
                        {{-- <div class="mb-3 col-md-4 not_memorial_show" id="final_price">
                            <label for="plan_price" class="form-label">方案追加/收款金額<span class="text-danger">*</span></label>
                            <input type="text" class="form-control total_number"  name="final_price" value="{{ $data->plan_price }}" >
                        </div> --}}
                        <div class="row">
                            <div class="mb-1 mt-1">
                                <div class="form-check" id="send_div">
                                    <input type="checkbox" class="form-check-input" id="send" name="send" @if(($data->send == 1)) checked value="1"  @endif >
                                    <label class="form-check-label" for="send"><b>親送</b></label>
                                </div>
                            </div>
                            <div class="mb-1 mt-1" id="connector_div">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="connector_address" name="connector_address" @if(($data->connector_address == 1))  checked value="1" @else  value="0" @endif >
                                    <label class="form-check-label" for="connector_address"><b>接體地址不為客戶地址</b></label>
                                </div>
                                <div class="mt-2 row" id="connector_address_div">
                                    <div class="col-md-4 mb-3">
                                        <label for="plan_price" class="form-label">接體縣市<span class="text-danger">*</span></label>
                                        <div class="twzipcode mb-2">
                                            <select data-role="county" required></select>
                                            <select data-role="district"></select>
                                            <select data-role="zipcode"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="AddNew-Phone">接體地址<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="address" @if(($data->connector_address == 1)) value="{{ $sale_address->address }}" @endif  >
                                    </div>
                                </div>
                            </div>
                            <div class="mb-1 mt-1" id="connector_hospital_div">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="connector_hospital_address" name="connector_hospital_address"  @if((isset($data->hospital_address)))  checked value="1" @else  value="0" @endif >
                                    <label class="form-check-label" for="connector_hospital_address"><b>接體地址為醫院</b></label>
                                    @if(isset($data->hospital_address) && $data->hospital_address != 0)
                                        @if(isset($data->hospital_address_name))
                                        【{{ $data->hospital_address_name->name }} 】
                                        @else
                                        <span style="color: red;">（接體地址輸入錯誤）</span>
                                        @endif
                                    @endif
                                </div>
                                <div class="mt-2 row" id="connector_hospital_address_div">
                                    <div class="col-md-4">
                                        <select class="form-control" data-toggle="select2" data-width="100%" name="hospital_address" id="hospital_address">
                                            <option value="">請選擇...</option>
                                            @foreach($source_companys as $source_company)
                                                <option value="{{ $source_company->id }}" @if($source_company->id == $data->hospital_address) selected @endif>（{{$source_company->group->name}}）{{ $source_company->name }}（{{ $source_company->mobile }}）</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-1 mt-1">
                                    <div class="form-check" id="cooperation_price_div">
                                        <input type="checkbox" class="form-check-input" id="cooperation_price"
                                            name="cooperation_price" @if(($data->cooperation_price == 1)) checked value="1" @else value="0" @endif>
                                        <label class="form-check-label" for="cooperation_price"><b>院內價</b></label>
                                    </div>
                                </div>
                            </div>
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
                                            <th></th>
                                            <th>處理方式<span class="text-danger">*</span></th>
                                            <th>名稱<span class="text-danger">*</span></th>
                                            <th>售價<span class="text-danger">*</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(count($sale_proms) > 0)
                                            @foreach($sale_proms as $key=>$sale_prom)
                                                <tr id="row-{{ $key }}">
                                                    <td class="text-center">
                                                        @if($key==0)
                                                        <button type="button" class="ibtnAdd_prom demo-delete-row btn btn-primary btn-sm btn-icon"><i class="fa fas fa-plus"></i></button>                                                    
                                                        @else
                                                        <button type="button" class="ibtnDel_prom demo-delete-row btn btn-danger btn-sm btn-icon"><i class="fa fa-times"></i></button>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <select id="select_prom_{{$key}}" alt="{{ $key }}" data-key="{{$key}}" class="mobile form-select" name="select_proms[]" onchange="chgItems(this)">
                                                            <option value="" selected>請選擇</option>
                                                            <option value="A" @if($sale_prom->prom_type == 'A') selected @endif>安葬處理</option>
                                                            <option value="B" @if($sale_prom->prom_type == 'B') selected @endif>後續處理</option>
                                                            <option value="C" @if($sale_prom->prom_type == 'C') selected @endif>其他處理</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select id="prom_{{$key}}" class="mobile form-select" name="prom[]">
                                                            @foreach($proms as $prom)
                                                                <option value="{{ $prom->id }}" @if($sale_prom->prom_id == $prom->id) selected @endif >{{ $prom->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="row mt-1 prom-product-container" id="prom_product_{{$key}}" style="{{ $sale_prom->souvenir ? '' : 'display:none;' }}">
                                                            <div class="col-3 mobile" id="souvenir_type_col_{{$key}}" style="display:none;">
                                                                <select id="product_souvenir_type_{{$key}}"
                                                                    class="form-select" name="product_souvenir_types[]">
                                                                    <option value="">請選擇</option>
                                                                    @foreach ($souvenir_types as $souvenir_type)
                                                                        <option value="{{ $souvenir_type->id }}" {{ ($sale_prom->souvenir && $sale_prom->souvenir->souvenir_type == $souvenir_type->id) ? 'selected' : '' }}>{{ $souvenir_type->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-3 mobile" id="product_name_col_{{$key}}" style="display:none;">
                                                                <input type="text" id="product_name_{{$key}}" class="form-control" name="product_name[]" placeholder="請輸入商品名稱" value="{{ $sale_prom->souvenir->product_name ?? '' }}">
                                                            </div>
                                                            <div class="col-3 mobile" id="product_prom_col_{{$key}}">
                                                                <select id="product_prom_{{$key}}"
                                                                    class="form-select" name="product_proms[]"
                                                                    data-selected="{{ $sale_prom->souvenir->product_name ?? '' }}">
                                                                    <option value="">請選擇</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-3 mobile" id="product_num_col_{{$key}}">
                                                                <input class="form-control" type="number" id="product_num_{{$key}}" name="product_num[]" value="{{ $sale_prom->souvenir->product_num ?? 1 }}" min="1">
                                                            </div>
                                                            <div class="col-3 mobile" id="product_comment_col_{{$key}}">
                                                                <input class="form-control" type="text" id="product_comment_{{$key}}" name="product_comment[]" placeholder="備註" value="{{ $sale_prom->souvenir->comment ?? '' }}">
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="mobile form-control total_number" id="prom_total_{{$key}}" name="prom_total[]" value="{{ $sale_prom->prom_total }}">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
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
                                                        <div class="row mt-1 prom-product-container" id="prom_product_{{$i}}">
                                                            <div class="col-3 mobile" id="souvenir_type_col_{{$i}}" style="display:none;">
                                                                <select id="product_souvenir_type_{{$i}}"
                                                                    class="form-select" name="product_souvenir_types[]">
                                                                    <option value="">請選擇</option>
                                                                    @foreach ($souvenir_types as $souvenir_type)
                                                                        <option value="{{ $souvenir_type->id }}">{{ $souvenir_type->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-3 mobile" id="product_name_col_{{$i}}" style="display:none;">
                                                                <input type="text" id="product_name_{{$i}}" class="form-control" name="product_name[]" placeholder="請輸入商品名稱">
                                                            </div>
                                                            <div class="col-3 mobile" id="product_prom_col_{{$i}}">
                                                                <select id="product_prom_{{$i}}"
                                                                    class="form-select" name="product_proms[]">
                                                                    <option value="">請選擇</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-3 mobile" id="product_num_col_{{$i}}">
                                                                <input class="form-control" type="number" id="product_num_{{$i}}" name="product_num[]" value="1" min="1">
                                                            </div>
                                                            <div class="col-3 mobile" id="product_comment_col_{{$i}}">
                                                                <input class="form-control" type="text" id="product_comment_{{$i}}" name="product_comment[]" placeholder="備註">
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="mobile form-control total_number" id="prom_total_{{$i}}" name="prom_total[]" >
                                                    </td>
                                                </tr>
                                            @endfor
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
                                        @if(count($sale_gdpapers)>0)
                                            @foreach($sale_gdpapers as $key=>$sale_gdpaper)
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
                                                            <option value="{{ $product->id }}" @if($product->id == $sale_gdpaper->gdpaper_id) selected @endif>{{ $product->name }}({{ $product->price }})</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" class="mobile form-control" id="gdpaper_num_{{$key}}" alt="{{ $key }}"  name="gdpaper_num[]" value="{{ $sale_gdpaper->gdpaper_num }}"  onchange="chgNums(this)" onclick="chgNums(this)" onkeydown="chgNums(this)">
                                                </td>
                                                <td>
                                                    <input type="text" class="mobile form-control total_number" id="gdpaper_total_{{$key}}" name="gdpaper_total[]" value="{{ $sale_gdpaper->gdpaper_total }}">
                                                </td>
                                            </tr>
                                            @endforeach
                                        @else
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

    {{-- <div class="row not_memorial_show" id="souvenir_div">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="text-uppercase bg-light p-2 mt-0 mb-3">紀念品選購</h5>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="tech-companies-1" class="table souvenir-list">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>類別<span class="text-danger">*</span></th>
                                            <th>品項<span class="text-danger">*</span></th>
                                            <th>金額<span class="text-danger">*</span></th>
                                            <th>款式<span class="text-danger">*</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(count($sale_souvenirs)>0)
                                            @foreach($sale_souvenirs as $key=>$sale_souvenir)
                                                <tr id="row-{{ $key }}">
                                                    <td class="text-center">
                                                        @if($key==0)
                                                        <button type="button" class="ibtnAdd_souvenir demo-delete-row btn btn-primary btn-sm btn-icon"><i class="fa fas fa-plus"></i></button>                                                    
                                                        @else
                                                        <button type="button" class="ibtnDel_souvenir demo-delete-row btn btn-danger btn-sm btn-icon"><i class="fa fa-times"></i></button>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <select id="souvenir_type_id_{{ $key }}" alt="{{ $key }}" class="mobile form-select" name="souvenir_types[]">
                                                            <option value="" selected>請選擇...</option>
                                                            @foreach($souvenir_types as $souvenir_type)
                                                                <option value="{{ $souvenir_type->id }}" @if($souvenir_type->id == $sale_souvenir->souvenir_type) selected @endif>{{ $souvenir_type->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select id="souvenir_id_{{ $key }}" class="mobile form-select" name="souvenir_ids[]">
                                                            <option value="">請選擇</option>
                                                            @foreach($souvenirs as $souvenir)
                                                                <option value="{{ $souvenir->id }}" @if($souvenir->id == $sale_souvenir->souvenir_id) selected @endif >{{ $souvenir->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="mobile form-control total_number" id="souvenir_total_{{ $key }}" name="souvenir_totals[]" value="{{ $sale_souvenir->total }}">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="mobile form-control" id="souvenir_comment_{{ $key }}" name="souvenir_comments[]" value="{{ $sale_souvenir->comment }}">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                        @php $j = 0; @endphp
                                        @for ($i = 0; $i < 1; $i++)
                                            @php $j = $i+1; @endphp
                                            <tr id="row-{{ $i }}">
                                                <td class="text-center">
                                                    @if ($j == 1)
                                                        <button type="button" class="ibtnAdd_souvenir demo-delete-row btn btn-primary btn-sm btn-icon">
                                                            <i class="fa fas fa-plus"></i>
                                                        </button>
                                                    @else
                                                        <button type="button" class="ibtnDel_souvenir demo-delete-row btn btn-danger btn-sm btn-icon">
                                                            <i class="fa fa-times"></i>
                                                        </button>
                                                    @endif
                                                </td>
                                                <td>
                                                    <select id="souvenir_type_id_{{ $i }}" alt="{{ $i }}" class="mobile form-select" name="souvenir_types[]" onchange="chgSouvenirType(this)">
                                                        <option value="" selected>請選擇...</option>
                                                        @foreach ($souvenir_types as $souvenir_type)
                                                            <option value="{{ $souvenir_type->id }}">
                                                                {{ $souvenir_type->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <select id="souvenir_id_{{ $i }}" class="mobile form-select" name="souvenir_ids[]">
                                                        <option value="">請選擇</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" class="mobile form-control total_number" id="souvenir_total_{{ $i }}" name="souvenir_totals[]" value="">
                                                </td>
                                                <td>
                                                    <input type="text" class="mobile form-control" id="souvenir_comment_{{ $i }}" name="souvenir_comments[]" value="">
                                                </td>
                                            </tr>
                                        @endfor
                                        @endif
                                    </tbody>
                                </table>
                            </div> <!-- end .table-responsive -->
                        </div>
                    </div>
                </div>
            </div> <!-- end card -->
        </div> <!-- end col -->
    </div> --}}

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="text-uppercase bg-light  p-2 mt-0 mb-3">付款方式</h5>
                    <div class="row">
                        <div class="mb-3 col-md-12">
                            <h2>應收金額<span id="total_text" class="text-danger">{{ $data->total }}</span>元</h2>
                            <input type="hidden" class="form-control" id="total" name="total" value="{{ $data->total }}" readonly>
                        </div>
                        <div class="mb-3 col-md-4">
                            <label for="pay_method" class="form-label">收款方式<span class="text-danger">*</span></label>
                            <select class="form-select" id="pay_method" name="pay_method" required>
                                <option value="" selected>請選擇</option>
                                <option value="A" @if($data->pay_method == 'A') selected @endif>現金</option>
                                <option value="B" @if($data->pay_method == 'B') selected @endif>匯款</option>
                                <option value="C" @if($data->pay_method == 'C') selected @endif>現金與匯款</option>
                            </select>
                        </div>
                        <div class="mb-3 col-md-4" id="cash_price_div">
                            <label for="pay_price" class="form-label">現金收款<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="cash_price" name="cash_price" value="{{ $data->cash_price }}">
                        </div>
                        <div class="mb-3 col-md-4" id="transfer_price_div">
                            <label for="pay_price" class="form-label">匯款收款<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="transfer_price" name="transfer_price" value="{{ $data->transfer_price }}">
                        </div>
                        <div class="mb-3 col-md-4" id="transfer_channel_div">
                            <label for="pay_id" class="form-label">匯款管道<span class="text-danger">*</span></label>
                            <select class="form-select" name="transfer_channel">
                                <option value="" selected>請選擇</option>
                                <option value="銀行轉帳" @if($data->transfer_channel == '銀行轉帳') selected @endif>銀行轉帳</option>
                                <option value="Line Pay" @if($data->transfer_channel == 'Line Pay') selected @endif>Line Pay</option>
                            </select>
                        </div>
                        <div class="mb-3 col-md-4" id="transfer_number_div">
                            <label for="pay_price" class="form-label">匯款後四碼<span class="text-danger"></span></label>
                            <input type="text" class="form-control" id="transfer_number" name="transfer_number" value="{{ $data->transfer_number }}">
                        </div>
                        <div class="mb-3 col-md-4">
                            <label for="pay_price" class="form-label">本次收款<span class="text-danger">*</span></label>
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
                <button type="submit" class="btn w-sm btn-success waves-effect waves-light" id="submit_btn">編輯</button>
                {{-- <button type="button" class="btn w-sm btn-danger waves-effect waves-light">Delete</button> --}}
            </div>
        </div> <!-- end col -->
    </div>
    <input type="hidden" id="row_id" name="row_id" value="">
    <input type="hidden" id="original_pay_id" value="{{ $data->pay_id ?? '' }}">
    <input type="hidden" id="sale_id" value="{{ $data->id ?? '' }}">


</form>


</div> <!-- container -->

@endsection


@section('script')
<script src="{{asset('assets/libs/selectize/selectize.min.js')}}"></script>
<script src="{{asset('assets/libs/mohithg-switchery/mohithg-switchery.min.js')}}"></script>
<script src="{{asset('assets/libs/multiselect/multiselect.min.js')}}"></script>
<script src="{{asset('assets/libs/select2/select2.min.js')}}"></script>
<script src="{{asset('assets/libs/jquery-mockjax/jquery-mockjax.min.js')}}"></script>
<script src="{{asset('assets/libs/devbridge-autocomplete/devbridge-autocomplete.min.js')}}"></script>
{{-- <script src="{{asset('assets/libs/bootstrap-touchspin/bootstrap-touchspin.min.js')}}"></script>
<script src="{{asset('assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js')}}"></script> --}}
<!-- demo app -->
<script src="{{ asset('assets/js/twzipcode-1.4.1-min.js') }}"></script>
<script src="{{ asset('assets/js/twzipcode.js') }}"></script>
<!-- third party js ends -->
<script src="{{asset('assets/js/pages/form-advanced.init.js')}}"></script>

<!-- demo app -->   


<script>
    type_list = $('select[name="type_list"]').val();
    payIdValue = $('select[name="pay_id"]').val();
    payMethod = $('select[name="pay_method"]').val();
    $(document).ready(function(){
        var saleAddress = <?php echo json_encode(isset($sale_address) ? $sale_address : null); ?>;
        // Check if $sale_address exists
        connector_address = $('input[name="connector_address"]').val();
        if (connector_address != 0) {
            // If $sale_address exists, initialize twzipcode with preselected values
            $(".twzipcode").twzipcode({
                css: ["twzipcode-select", "twzipcode-select" , "twzipcode-select"],
                countyName: "county",
                districtName: "district",
                zipcodeName: "zipcode",
                countySel: saleAddress.county,
                districtSel: saleAddress.district
            });
        } else {
            // If $sale_address doesn't exist, initialize twzipcode without preselected values
            $(".twzipcode").twzipcode({
                css: ["twzipcode-select", "twzipcode-select" , "twzipcode-select"],
                countyName: "county",
                districtName: "district",
                zipcodeName: "zipcode"
            });
        }
        
        // 初始化現有的 prom_product 區塊
        $('[id^=prom_product_]').each(function() {
            var idx = $(this).attr('id').replace('prom_product_', '');
            var promId = $('#prom_' + idx).val();
            
            // 先保存原本要選擇的商品值
            var originalSelectedVal = $('#product_prom_' + idx).attr('data-selected');
            
            if (promId) {
                // 如果有選擇 prom，檢查是否需要顯示紀念品區塊
                $.ajax({
                    url: '{{ route('product.prom_product_search') }}',
                    data: { 'prom_id': promId },
                    dataType: 'json',
                    success: function(data) {
                        var shouldShow = (data.products && data.products.length > 0) || data.is_custom_product == 1;
                        
                        if (shouldShow) {
                            if (data.products && data.products.length > 0) {
                                // 有商品資料，三欄顯示，全部 col-4
                                $('#souvenir_type_col_' + idx).hide();
                                $('#product_name_col_' + idx).hide();
                                $('#product_prom_col_' + idx).show().removeClass('col-3').addClass('col-4');
                                $('#product_num_col_' + idx).show().removeClass('col-3').addClass('col-4');
                                $('#product_comment_col_' + idx).show().removeClass('col-3').addClass('col-4');
                                // 填入商品下拉
                                var html = '<select id="product_prom_' + idx + '" class="form-select" name="product_proms[]">';
                                html += '<option value="">請選擇</option>';
                                data.products.forEach(function(item) {
                                    html += '<option value="' + item.id + '">' + item.name + ' (' + item.price + ')</option>';
                                });
                                html += '</select>';
                                $('#product_prom_col_' + idx).html(html);
                                
                                // 重新設回 data-selected 屬性
                                $('#product_prom_' + idx).attr('data-selected', originalSelectedVal);
                                
                                // 自動選擇原本的商品
                                if (originalSelectedVal) {
                                    $('#product_prom_' + idx).val(originalSelectedVal);
                                }
                                console.log('自動選擇商品id', originalSelectedVal, $('#product_prom_' + idx).val());
                            } else if (data.is_custom_product == 1) {
                                // 自訂商品，四欄顯示，全部 col-3
                                $('#souvenir_type_col_' + idx).show().removeClass('col-4').addClass('col-3');
                                $('#product_name_col_' + idx).show().removeClass('col-4').addClass('col-3');
                                $('#product_prom_col_' + idx).hide();
                                $('#product_num_col_' + idx).show().removeClass('col-4').addClass('col-3');
                                $('#product_comment_col_' + idx).show().removeClass('col-4').addClass('col-3');
                            }
                            
                            $('#prom_product_' + idx).show();
                        } else {
                            $('#prom_product_' + idx).hide();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('prom_product_search error:', error);
                        $('#prom_product_' + idx).hide();
                    }
                });
            }
        });
    });

    //判斷尾款、訂金
    $("#final_price_display").hide();
    
    $(document).ready(function () {
        // 預設初始化
        checkFinalAndSuit();

        // 綁定欄位變更事件
        $('#pay_id, #cust_name_q, #pet_name, #plan_id, #type_list').on('change keyup', function () {
            checkFinalAndSuit();
        });

        function checkFinalAndSuit() {
            const payId = $('#pay_id').val();
            const customerId = $('#cust_name_q').val();
            const petName = $('#pet_name').val();
            const planId = $('#plan_id').val();
            const typeList = $('#type_list').val();
            const saleId = $('#sale_id').val();

            if (payId && customerId && petName || planId) {
                $.ajax({
                    url: '{{ route('sales.final_price') }}',
                    type: 'GET',
                    data: {
                        pay_id: payId,
                        customer_id: customerId,
                        pet_name: petName,
                        current_id: saleId,
                        type_list: typeList
                    },
                    success: function (response) {
                        console.log('回傳結果:', response);

                        // 控制 submit 按鈕
                        if (response.message === 'OK') {
                            $('#final_price_display').hide(300);
                            $('#submit_btn').prop('disabled', false);
                        } else {
                            $('#final_price_display').show();
                            $('#final_price_display').text(response.message);
                            $('#submit_btn').prop('disabled', true);
                        }

                        if (typeList === 'dispatch' && (payId === 'A' || payId === 'D')) {
                            if(planId === '1' && payId === 'A'){
                                $('#suit_id').prop('required', true);
                                $('#suit_field').show(300);
                            } else if(response && response.data && response.data.plan_id === '1' && payId === 'D'){
                                $('#suit_id').prop('required', true);
                                $('#suit_field').show(300);
                            } else {
                                $('#suit_field').hide(300);
                                $('#suit_id').val('');
                                $('#suit_id').prop('required', false);
                            }
                        } else {
                            $('#suit_field').hide(300);
                            $('#suit_id').val('');
                            $('#suit_id').prop('required', false);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX 錯誤:', error);
                    }
                });
            } else {
                console.log('payId / customerId / petName 未填');
            }
        }
    });





    //親送
    send = $('input[name="send"]').val();
    console.log(send);
    if(send == 1){
        $("#connector_div").hide();
        $("#connector_hospital_div").hide();
    }else{
        $("#connector_div").show();
        $("#connector_hospital_div").show();
    }
    $("#send").on("change", function() {
        if ($(this).is(':checked')) {
            $(this).val(1);
            $("#connector_div").hide(300);
            $("#connector_hospital_div").hide();
        }
        else {
            $(this).val(0);
            $("#connector_div").show(300);
            $("#connector_hospital_div").show(300);
        }
    });

    //地址
    connector_address = $('input[name="connector_address"]').val();
    if(connector_address == 1){
        $("#send_div").hide();
        $("#connector_hospital_div").hide();
        $("#connector_div").show();
    }else{
        $("#send_div").show();
        // $("#connector_hospital_div").show();
        $("#connector_address_div").hide();
    }
    $("#connector_address").on("change", function() {
        if ($(this).is(':checked')) {
            $("#connector_address_div").show(300);
            $("#send_div").hide(300);
            $("#connector_hospital_div").hide(300);
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
            $("#connector_hospital_div").show(300);
            $(this).val(0);
            $('#your-form').off('submit');
            // Remove pet name required attribute
            $("#address").prop('required', false);
        }
    });

    //院內價開始
    cooperation_price = $('input[name="cooperation_price"]').val();
    $("#cooperation_price").on("change", function() {
        if ($(this).is(':checked')) {
            $(this).val(1);
        } else {
            $(this).val(0);
        }
    });
    //院內價結束

    //醫院地址
    connector_hospital_address = $('input[name="connector_hospital_address"]').val();
    console.log(connector_hospital_address);
    if(connector_hospital_address  ==  1){
        $("#connector_hospital_address_div").show();
        $("#connector_div").hide();
        $("#send_div").hide();
    }else{
        $("#connector_hospital_address_div").hide();
    }
    $("#connector_hospital_address").on("change", function() {
        if ($(this).is(':checked')) {
            $("#connector_hospital_address_div").show(300);
            $("#connector_div").hide(300);
            $("#send_div").hide(300);
            $(this).val(1);
            $("#hospital_address").prop('required', true);
            $('#your-form').submit(function(event){
                var hospital_address = $('input[name="hospital_address"]').val();
                if (hospital_address == '') {
                    alert('接體醫院不得為空！');
                    event.preventDefault();
                }
            });
        }
        else {
            $("#connector_hospital_address_div").hide(300);
            $("#connector_address_div").hide();
            $("#connector_div").show(300);
            $("#send_div").show(300);
            $(this).val(0);
            $('#your-form').off('submit');
            // Remove pet name required attribute
            $("#hospital_address").prop('required', false);
        }
    });



    //案件單類別
    if(type_list == 'memorial'){
        $(".not_memorial_show").hide(300);
        $("#final_price").hide(300);
        $("#cust_name_q").prop('required', false);
        // $("#pet_name").prop('required', false);
        $("#kg").prop('required', false);
        $("#variety").prop('required', false);
        $("#type").prop('required', false);
        $("#suit_id").prop('required', false);
        $("#plan_id").prop('required', false);
        $("#plan_price").prop('required', false);
        $("#hospital_address").prop('required', false);
        $("#send_div").hide(300);
        $("#connector_div").hide(300);
        $("#connector_hospital_div").hide(300);
        $(".required").hide();
        if(payIdValue == 'A' || payIdValue =='C'){
            $(".not_memorial_show").hide(300);
        }
        
    }else if(type_list == 'dispatch'){
            $(".not_memorial_show").show(300);
            $("#cust_name_q").prop('required', true);
            $(".required").show();
            if(payIdValue == 'D' || payIdValue =='E'){
                $("#final_price").show(300);
                $(".not_final_show").hide();
                if(payIdValue =='D'){
                    $(".plan").hide(300);
                    $("#plan_id").prop('required', false);
                }else{
                    $(".plan").show(300);
                    $("#plan_id").prop('required', true);
                }
                $("#kg").prop('required', false);
                $("#variety").prop('required', false);
                $("#type").prop('required', false);
                $("#plan_id").prop('required', false);
                $("#plan_price").prop('required', false);
                $("#send_div").hide();
                $("#connector_div").hide();
                $("#connector_hospital_div").hide();
            }else{
                $("#prom_div").show(300);
                $("#souvenir_div").show(300);
                $("#gdpaper_div").show(300);
                $("#final_price").hide(300);
                $(".not_final_show").show(300);
                $("#pet_name").prop('required', true);
                $("#kg").prop('required', true);
                $("#variety").prop('required', true);
                $("#type").prop('required', true);
                $("#suit_id").prop('required', true);
                $("#plan_id").prop('required', true);
                $("#plan_price").prop('required', true);
                if(payIdValue =='C'){
                    $("#prom_div").hide(300);
                    $("#gdpaper_div").hide(300);
                    $("#souvenir_div").hide(300);
                }
            }
    }

    $('select[name="type_list"]').on('change', function() {
        payIdValue = $('select[name="pay_id"]').val();
        if($(this).val() == 'memorial'){
            $(".not_memorial_show").hide(300);
            $("#final_price").hide(300);
            $("#cust_name_q").prop('required', false);
            // $("#pet_name").prop('required', false);
            $("#kg").prop('required', false);
            $("#variety").prop('required', false);
            $("#type").prop('required', false);
            $("#suit_id").prop('required', false);
            $("#plan_id").prop('required', false);
            $("#plan_price").prop('required', false);
            $("#hospital_address").prop('required', false);
            $(".required").hide();
        }else if($(this).val() == 'dispatch'){
            $(".not_memorial_show").show(300);
            $("#cust_name_q").prop('required', true);
            $(".required").show();
            if(payIdValue == 'D' || payIdValue =='E'){
                $("#final_price").show(300);
                $(".not_final_show").hide();
                if(payIdValue =='D'){
                    $(".plan").hide(300);
                    $("#plan_id").prop('required', false);
                }else{
                    $(".plan").show(300);
                    $("#plan_id").prop('required', true);
                }
                $("#kg").prop('required', false);
                $("#variety").prop('required', false);
                $("#type").prop('required', false);
                $("#suit_id").prop('required', false);
                $("#plan_price").prop('required', false);
            }else{
                $("#prom_div").show(300);
                $("#souvenir_div").show(300);
                $("#gdpaper_div").show(300);
                $("#final_price").hide(300);
                $(".not_final_show").show(300);
                $("#pet_name").prop('required', true);
                $("#kg").prop('required', true);
                $("#variety").prop('required', true);
                $("#type").prop('required', true);
                $("#suit_id").prop('required', true);
                $("#plan_id").prop('required', true);
                $("#plan_price").prop('required', true);
                if(payIdValue =='C'){
                    $("#prom_div").hide(300);
                    $("#souvenir_div").hide(300);
                    $("#gdpaper_div").hide(300);
                }
            }
        }
    });

    $('select[name="pay_id"]').on('change', function() {
        type_list = $('select[name="type_list"]').val();
        var a = $(this).val();
            console.log(a);
        if($(this).val() == 'D' || $(this).val() =='E'){
            $(".not_final_show").hide(300);
            if($(this).val() =='D'){
                $(".plan").hide(300);
                $("#plan_id").prop('required', false);
            }else{
                $(".plan").show(300);
                $("#plan_id").prop('required', true);
            }
            $("#kg").prop('required', false);
            $("#variety").prop('required', false);
            $("#type").prop('required', false);
            $("#suit_id").prop('required', false);
            // $("#plan_id").prop('required', false);
            $("#plan_price").prop('required', false);
            if(type_list == 'memorial'){
                $("#final_price").hide();
                $(".not_memorial_show").hide();
                $("#send_div").hide(300);
                $("#connector_div").hide(300);
                $("#connector_hospital_div").hide(300);
            }
            $("#send_div").hide();
            $("#connector_div").hide();
            $("#connector_hospital_div").hide();
        }else{
            $("#prom_div").show(300);
            $("#gdpaper_div").show(300);
            $("#souvenir_div").show(300);
            $("#final_price").hide(300);
            $("#send_div").show(300);
            $("#connector_div").show(300);
            $("#connector_hospital_div").show(300);
            if(type_list == 'memorial'){
                $("#final_price").hide();
                $(".not_memorial_show").hide();
                $("#send_div").hide();
                $("#connector_div").hide();
                $("#connector_hospital_div").hide();
            }else{
                $(".not_memorial_show").show();
                $("#pet_name").prop('required', true);
                $("#kg").prop('required', true);
                $("#variety").prop('required', true);
                $("#type").prop('required', true);
                $("#suit_id").prop('required', true);
                $("#plan_id").prop('required', true);
                $("#plan_price").prop('required', true);
                $("#send_div").show();
                $("#connector_div").show();
                $("#connector_hospital_div").show();
                if($(this).val() =='C'){
                    $("#prom_div").hide(300);
                    $("#souvenir_div").hide(300);
                    $("#gdpaper_div").hide(300);
                }
            }
        }
    });

    type = $('select[name="type"]').val();
    if(type == 'H' || type == 'B' || type == 'Salon' || type == 'G' || type == 'dogpark' || type == 'other' || type == 'self'){
        $("#source_company").show(300);
        $("#source_company_name_q").prop('required', true);
    }else{
        $("#source_company").hide(300);
        $("#source_company_name_q").prop('required', false);
    }

    // 載入指定類型的客戶
    function loadCustomersByType(type) {
        $.ajax({
            url: '{{ route("customers.by-type") }}',
            type: 'GET',
            data: { type: type },
            dataType: 'json',
            success: function(data) {
                var customerSelect = $('#source_company_name_q');
                customerSelect.empty();
                customerSelect.html(data.html);
            },
            error: function(xhr, status, error) {
                console.error('載入客戶資料失敗:', error);
                var customerSelect = $('#source_company_name_q');
                customerSelect.empty();
                customerSelect.append('<option value="">載入失敗，請重試</option>');
            }
        });
    }

    $('select[name="type"]').on('change', function() {
        var selectedType = $(this).val();
        if(selectedType == 'H' || selectedType == 'B' || selectedType == 'Salon' || selectedType == 'G' || selectedType == 'dogpark' || selectedType == 'other' || selectedType == 'self'){
            $("#source_company").show(300);
            $("#source_company_name_q").prop('required', true);
            // 載入對應類型的客戶
            loadCustomersByType(selectedType);
        }else{
            $("#source_company").hide(300);
            $("#source_company_name_q").prop('required', false);
        }
    });

    $("#cash_price_div").hide();
    $("#transfer_price_div").hide();
    $("#transfer_number_div").hide();
    $("#transfer_channel_div").hide();
    if(payMethod == 'C'){
            $("#cash_price_div").show(300);
            $("#transfer_price_div").show(300);
            $("#transfer_number_div").show(300);
            $("#transfer_channel_div").show(300);
            $("#pay_price").prop('required', false);
            $("#cash_price").prop('required', true);
            $("#transfer_price").prop('required', true);
        }else if(payMethod == 'B'){
            $("#cash_price_div").hide(300);
            $("#transfer_price_div").hide(300);
            $("#transfer_number_div").show(300);
            $("#transfer_channel_div").show(300);
            $("#pay_price").prop('required', true);
            $("#cash_price").prop('required', false);
            $("#transfer_price").prop('required', false);
        }else{
            $("#cash_price_div").hide(300);
            $("#transfer_price_div").hide(300);
            $("#transfer_number_div").hide(300);
            $("#transfer_channel_div").hide(300);
            $("#pay_price").prop('required', true);
            $("#cash_price").prop('required', false);
            $("#transfer_price").prop('required', false);
        }
    $('select[name="pay_method"]').on('change', function() {
        if($(this).val() == 'C'){
            $("#cash_price_div").show(300);
            $("#transfer_price_div").show(300);
            $("#transfer_number_div").show(300);
            $("#transfer_channel_div").show(300);
            $("#pay_price").prop('required', false);
            $("#cash_price").prop('required', true);
            $("#transfer_price").prop('required', true);
        }else if($(this).val() == 'B'){
            $("#cash_price_div").hide(300);
            $("#transfer_price_div").hide(300);
            $("#transfer_number_div").show(300);
            $("#transfer_channel_div").show(300);
            $("#pay_price").prop('required', true);
            $("#cash_price").prop('required', false);
            $("#transfer_price").prop('required', false);
        }else{
            $("#cash_price_div").hide(300);
            $("#transfer_price_div").hide(300);
            $("#transfer_number_div").hide(300);
            $("#transfer_channel_div").hide(300);
            $("#pay_price").prop('required', true);
            $("#cash_price").prop('required', false);
            $("#transfer_price").prop('required', false);
        }
    });

    
    $("#final_price").on('input', function(){
        calculate_price();
    });
    
    $("#plan_price").on('input', function(){
        calculate_price();
    });

    $(document).on('input', '.total_number', function() {
        calculate_price();
    });
    

    function chgItems(obj){
        $("#row_id").val($("#"+ obj.id).attr('alt'));
        row_id = $("#row_id").val();
        
        // 防呆：當變更select_proms時，隱藏對應的prom_product區塊
        $('#prom_product_' + row_id).hide(300);
        
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

    $(document).on('change', 'select[id^=prom_]', function() {
        var selectId = $(this).attr('id');
        var idx = selectId.replace('prom_', '');
        var promId = $(this).val();
        
        // 先保存原本要選擇的商品值
        var originalSelectedVal = $('#product_prom_' + idx).attr('data-selected');

        $.ajax({
            url: '{{ route('product.prom_product_search') }}',
            data: { 'prom_id': promId },
            dataType: 'json',
            success: function(data) {
                console.log('prom_product_search data:', data);
                
                // 檢查是否有商品資料或is_custom_product為1
                var shouldShow = (data.products && data.products.length > 0) || data.is_custom_product == 1;
                
                if (shouldShow) {
                    var html = '';
                    if (data.products && data.products.length > 0) {
                        // 有商品資料，三欄顯示，全部 col-4
                        $('#souvenir_type_col_' + idx).hide();
                        $('#product_name_col_' + idx).hide();
                        $('#product_prom_col_' + idx).show().removeClass('col-3').addClass('col-4');
                        $('#product_num_col_' + idx).show().removeClass('col-3').addClass('col-4');
                        $('#product_comment_col_' + idx).show().removeClass('col-3').addClass('col-4');
                        // 填入商品下拉
                        var html = '<select id="product_prom_' + idx + '" class="form-select" name="product_proms[]">';
                        html += '<option value="">請選擇</option>';
                        data.products.forEach(function(item) {
                            html += '<option value="' + item.id + '">' + item.name + ' (' + item.price + ')</option>';
                        });
                        html += '</select>';
                        $('#product_prom_col_' + idx).html(html);
                        
                        // 重新設回 data-selected 屬性
                        $('#product_prom_' + idx).attr('data-selected', originalSelectedVal);
                        
                        // 自動選擇原本的商品
                        if (originalSelectedVal) {
                            $('#product_prom_' + idx).val(originalSelectedVal);
                        }
                        console.log('自動選擇商品id', originalSelectedVal, $('#product_prom_' + idx).val());
                    } else if (data.is_custom_product == 1) {
                        // 自訂商品，四欄顯示，全部 col-3
                        $('#souvenir_type_col_' + idx).show().removeClass('col-4').addClass('col-3');
                        $('#product_name_col_' + idx).show().removeClass('col-4').addClass('col-3');
                        $('#product_prom_col_' + idx).hide();
                        $('#product_num_col_' + idx).show().removeClass('col-4').addClass('col-3');
                        $('#product_comment_col_' + idx).show().removeClass('col-4').addClass('col-3');
                    }
                    
                    // 顯示整個prom_product區塊
                    $('#prom_product_' + idx).show(300);
                } else {
                    // 沒有商品資料且is_custom_product不為1，隱藏區塊
                    $('#prom_product_' + idx).hide(300);
                    $('#souvenir_type_col_' + idx).hide();
                    $('#product_name_col_' + idx).hide();
                    $('#product_prom_col_' + idx).hide();
                    $('#product_num_col_' + idx).hide();
                    $('#product_comment_col_' + idx).hide();

                    // ====== 隱藏時清空所有 input/select ======
                    var $row = $('#prom_product_' + idx);
                    $row.find('input').val('');
                    $row.find('select').val('');
                    // 指定數量欄位預設為1
                    $row.find('input[id^=product_num_]').val('1');
                    // 指定商品選擇欄位預設為空，並清空 data-selected
                    $row.find('select[id^=product_prom_]').val('').attr('data-selected', '');
                    // =========================================
                }

                // ======= 這裡加上清空 input 的程式碼 =======
                if (typeof hasChanged !== 'undefined' && hasChanged[idx]) {
                    var $row = $('#prom_product_' + idx);
                    $row.find('input[id^=product_name_]').val('');
                    $row.find('input[id^=product_prom_]').val('');
                    $row.find('input[id^=product_num_]').val('1');
                    $row.find('input[id^=product_comment_]').val('');
                    $row.find('select[id^=product_souvenir_type_]').val('');
                }
                // ==========================================
            },
            error: function(xhr, status, error) {
                console.error('prom_product_search error:', error);
                // 發生錯誤時隱藏區塊
                $('#prom_product_' + idx).hide(300);
                $('#souvenir_type_col_' + idx).hide();
                $('#product_name_col_' + idx).hide();
                $('#product_prom_col_' + idx).hide();
                $('#product_num_col_' + idx).hide();
                $('#product_comment_col_' + idx).hide();
            }
        });
    });

    $('select[name="prom[]"]').on('mousedown', function(event) {
        var selectElement = $(this);
        var isLoaded = selectElement.data('loaded');

        // 阻止原生的下拉行为，直到数据加载完成
        if (!isLoaded) {
            event.preventDefault(); // 阻止下拉菜单自动展开

            var currentSelectedValue = selectElement.val();
            var promType = selectElement.closest('td').prev('td').find('select').val();

            $.ajax({
                url: '{{ route('prom.search') }}',
                method: 'GET',
                data: { 'select_prom': promType },
                success: function(response) {
                    selectElement.html(response).find('option').each(function() {
                        if ($(this).val() === currentSelectedValue) {
                            $(this).prop('selected', true);
                        }
                    });

                    selectElement.data('loaded', true);

                    // 数据加载后重新触发 focus 事件，这次允许下拉展开
                    selectElement.off('focus').trigger('focus');
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching prom options:', error);
                }
            });
        }
    });

    function chgPapers(obj){
        var row_id = $(obj).attr('alt'); // 取得行 ID

        $.ajax({
            url: '{{ route('gdpaper.search') }}', // AJAX 查詢
            data: {'gdpaper_id': $("#gdpaper_id_" + row_id).val()},
            success: function(data){
                var gdpaper_num = $("#gdpaper_num_" + row_id).val();
                
                // 如果數量為空或 <= 0，預設設置為 1
                if (!gdpaper_num) {
                    gdpaper_num = 1;
                    $("#gdpaper_num_" + row_id).val(gdpaper_num);
                }
                
                // 計算金額並更新總金額欄位
                $("#gdpaper_total_" + row_id).val(gdpaper_num * data);
                calculate_price();
                
                // 監聽數量變化，動態更新金額
                $("#gdpaper_num_" + row_id).on('change', function() {
                    gdpaper_num = $(this).val();
                   
                    $("#gdpaper_total_" + row_id).val(gdpaper_num * data); // 更新金額
                    calculate_price();
                });
            }
        });
    }

    function chgNums(obj) {
        var row_id = $(obj).attr('alt'); // 取得行 ID

        $.ajax({
            url: '{{ route('gdpaper.search') }}', // AJAX 查詢
            data: {'gdpaper_id': $("#gdpaper_id_" + row_id).val()},
            success: function(data) {
                var gdpaper_num = $("#gdpaper_num_" + row_id).val();
                
                // 防止數量為 0 或空值，設置最小值為 1
                if (!gdpaper_num) {
                    gdpaper_num = 1;
                    $("#gdpaper_num_" + row_id).val(gdpaper_num);
                }
                
                // 更新總金額
                $("#gdpaper_total_" + row_id).val(gdpaper_num * data);
                calculate_price();

                // 監聽數量變更事件，動態更新金額
                $("#gdpaper_num_" + row_id).on('change', function() {
                    gdpaper_num = $(this).val();
                   
                    $("#gdpaper_total_" + row_id).val(gdpaper_num * data); // 更新總金額
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
        cols += '<select id="gdpaper_id_'+rowCount+'" alt="'+rowCount+'" class="mobile form-select" name="gdpaper_ids[]" onchange="chgNums(this)" onclick="chgNums(this)" onkeydown="chgNums(this)">';
        cols += '<option value="" selected>請選擇...</option>';
            @foreach($products as $product)
                cols += '<option value="{{ $product->id }}">{{ $product->name }}({{ $product->price }})</option>';
            @endforeach
        cols += '</select>';
        cols += '</td>';
        cols += '<td>';
        cols += '<input type="number" class="mobile form-control"  min="0"  id="gdpaper_num_'+rowCount+'" name="gdpaper_num[]" value="">';
        cols += '</td>';
        cols += '<td>';
        cols += '<input type="text" class="mobile form-control total_number" id="gdpaper_total_'+rowCount+'" name="gdpaper_total[]">';
        cols += '</td>';
        cols += '</tr>';
        newRow.append(cols);
        $("table.gdpaper-list tbody").append(newRow);
    });


    $("table.souvenir-list tbody").on("click", ".ibtnAdd_souvenir", function() {
            rowCount = $('table.souvenir-list tr').length - 1;
            var newRow = $("<tr>");
            var cols = '';
            cols +=
                '<td class="text-center"><button type="button" class="ibtnDel_souvenir demo-delete-row btn btn-danger btn-sm btn-icon"><i class="fa fa-times"></i></button></td>';
            cols += '<td>';
            cols += '<select id="souvenir_type_id_' + rowCount + '" alt="' + rowCount +
                '" class="mobile form-select" name="souvenir_types[]" onchange="chgSouvenirType(this)">';
            cols += '<option value="" selected>請選擇...</option>';
            @foreach ($souvenir_types as $souvenir_type)
                cols +=
                    '<option value="{{ $souvenir_type->id }}">{{ $souvenir_type->name }}</option>';
            @endforeach
            cols += '</select>';
            cols += '</td>';
            cols += '<td>';
            cols += '<select id="souvenir_id_' + rowCount + '"';
            cols += ' class="mobile form-select" name="souvenir_ids[]">';
            cols += ' <option value="">請選擇</option>';
            cols += '</select>';
            cols += '</td>';
            cols += '<td>';
            cols += '<input type="number" class="mobile form-control total_number" id="souvenir_total_' + rowCount + '" name="souvenir_totals[]" value="">';
            cols += ' </td>';
            cols += '<td>';
            cols += '<input type="text" class="mobile form-control"';
            cols += ' id="souvenir_comment_' + rowCount + '"';
            cols += 'name="souvenir_comments[]" value="">';
            cols += '</td>';
            cols += '</tr>';
            newRow.append(cols);
            $("table.souvenir-list tbody").append(newRow);
        });
        

        $("table.souvenir-list tbody").on("click", ".ibtnDel_souvenir", function() {
            $(this).closest('tr').remove();
        });
    
        //紀念品專區
        function chgSouvenirType(obj) {
            $("#row_id").val($("#" + obj.id).attr('alt'));
            row_id = $("#row_id").val();
            $.ajax({
                url: '{{ route('souvenirType.search') }}',
                data: {
                    'souvenir_type_id': $("#souvenir_type_id_" + row_id).val()
                },
                success: function(data) {
                    $("#souvenir_id_" + row_id).html(data);
                    // 當souvenirType變更後，再觸發chgSouvenir
                    chgSouvenir(row_id);
                }
            });
        }

        //紀念品
        $('select[name="souvenir_ids[]"]').on('mousedown', function(event) {
        var selectElement = $(this);
        var isLoaded = selectElement.data('loaded');

        // 阻止原生的下拉行为，直到数据加载完成
        if (!isLoaded) {
            event.preventDefault(); // 阻止下拉菜单自动展开

            var currentSelectedValue = selectElement.val();
            var souvenirId = selectElement.closest('td').prev('td').find('select').val();

            $.ajax({
                url: '{{ route('souvenir.search') }}',
                method: 'GET',
                data: { 'souvenir_id': souvenirId },
                success: function(response) {
                    selectElement.html(response).find('option').each(function() {
                        if ($(this).val() === currentSelectedValue) {
                            $(this).prop('selected', true);
                        }
                    });

                    selectElement.data('loaded', true);

                    // 数据加载后重新触发 focus 事件，这次允许下拉展开
                    selectElement.off('focus').trigger('focus');
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching prom options:', error);
                }
            });
        }
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

        $( ".source_company_name" ).keydown(function() {
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
            
            // 新增對應的prom_product區塊（一開始隱藏）
            var promProductHtml = '';
            promProductHtml += '<div class="row mt-1 prom-product-container" id="prom_product_' + $rowCount + '" style="display: none;">';
            promProductHtml += '<div class="col-3" id="souvenir_type_col_' + $rowCount + '" style="display:none;">';
            promProductHtml += '<select id="product_souvenir_type_' + $rowCount + '" class="form-select" name="product_souvenir_types[]">';
            promProductHtml += '<option value="">請選擇</option>';
            @foreach ($souvenir_types as $souvenir_type)
            promProductHtml += '<option value="{{ $souvenir_type->id }}">{{ $souvenir_type->name }}</option>';
            @endforeach
            promProductHtml += '</select>';
            promProductHtml += '</div>';
            promProductHtml += '<div class="col-3" id="product_name_col_' + $rowCount + '" style="display:none;">';
            promProductHtml += '<input type="text" id="product_name_' + $rowCount + '" class="form-control" name="product_name[]" placeholder="請輸入商品名稱">';
            promProductHtml += '</div>';
            promProductHtml += '<div class="col-3" id="product_prom_col_' + $rowCount + '">';
            promProductHtml += '<select id="product_prom_' + $rowCount + '" class="form-select" name="product_proms[]">';
            promProductHtml += '<option value="">請選擇</option>';
            promProductHtml += '</select>';
            promProductHtml += '</div>';
            promProductHtml += '<div class="col-3" id="product_num_col_' + $rowCount + '">';
            promProductHtml += '<input class="form-control" type="number" id="product_num_' + $rowCount + '" name="product_num[]" value="1" min="1">';
            promProductHtml += '</div>';
            promProductHtml += '<div class="col-3" id="product_comment_col_' + $rowCount + '">';
            promProductHtml += '<input class="form-control" type="text" id="product_comment_' + $rowCount + '" name="product_comment[]" placeholder="備註">';
            promProductHtml += '</div>';
            promProductHtml += '</div>';
            
            // 將prom_product區塊插入到對應的td中
            $('table.prom-list tr:last-child td:nth-child(3)').append(promProductHtml);
        });
        $.ajaxSetup({ headers: { 'csrftoken' : '{{ csrf_token() }}' } });

        // 單號重複檢查（編輯模式）
        let saleOnCheckTimer;
        let isSaleOnValid = true; // 追蹤單號是否有效
        
        $('#sale_on').on('input', function() {
            const saleOn = $(this).val().trim();
            const feedback = $('#sale_on_feedback');
            const currentId = {{ $data->id }}; // 當前記錄的ID
            
            // 清除之前的計時器
            clearTimeout(saleOnCheckTimer);
            
            // 清空之前的反饋
            feedback.html('').removeClass('text-danger text-success');
            
            // 如果輸入為空，不進行檢查
            if (!saleOn) {
                isSaleOnValid = true;
                return;
            }
            
            // 檢查單號格式（必須包含數字）
            if (!/\d/.test(saleOn)) {
                feedback.html('<small class="text-warning">請輸入包含數字的單號</small>').addClass('text-warning');
                isSaleOnValid = false;
                return;
            }
            
            // 延遲 500ms 後進行檢查，避免頻繁請求
            saleOnCheckTimer = setTimeout(function() {
                $.ajax({
                    type: 'GET',
                    url: '{{ route('sale.check_sale_on') }}',
                    data: {
                        'sale_on': saleOn,
                        'current_id': currentId
                    },
                    success: function(response) {
                        if (response.exists) {
                            feedback.html('<small class="text-danger">⚠️ ' + response.message + '</small>').addClass('text-danger');
                            isSaleOnValid = false;
                        } else {
                            feedback.html('<small class="text-success">✓ ' + response.message + '</small>').addClass('text-success');
                            isSaleOnValid = true;
                        }
                    },
                    error: function(xhr, status, error) {
                        feedback.html('<small class="text-danger">檢查單號時發生錯誤</small>').addClass('text-danger');
                        isSaleOnValid = false;
                        console.error('單號檢查錯誤:', error);
                    }
                });
            }, 500);
        });

        // 表單提交檢查
        $('#your-form').on('submit', function(e) {
            if (!isSaleOnValid) {
                e.preventDefault();
                alert('單號有重複，請檢查後再提交');
                return false;
            }
        });

    var hasChanged = {};

    $(document).on('change', 'select[id^=product_prom_]', function() {
        var $row = $(this).closest('.prom-product-container');
        var idx = $(this).attr('id').replace('product_prom_', '');

        // 如果這一列已經切換過，就直接清空
        if (hasChanged[idx]) {
            $row.find('input[id^=product_name_]').val('');
            $row.find('input[id^=product_prom_]').val('');
            $row.find('input[id^=product_num_]').val('');
            $row.find('input[id^=product_comment_]').val('');
            $row.find('select[id^=product_souvenir_type_]').val('');
        }
        // 標記這一列已經切換過
        hasChanged[idx] = true;
    });
</script>
@endsection