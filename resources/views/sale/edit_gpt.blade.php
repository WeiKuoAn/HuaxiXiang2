@extends('layouts.vertical', ['page_title' => '編輯業務Key單'])

@section('css')
    <link href="{{ asset('assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/css/customization.css') }}" id="app-style" rel="stylesheet" type="text/css" />
@endsection

@section('content')

    <style>
        @media screen and (max-width:768px) {
            .mobile {
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

        <form action="{{ route('sale.data.update', $data->id) }}" method="POST" id="your-form" enctype="multipart/form-data"
            data-plugin="dropzone" data-previews-container="#file-previews"
            data-upload-preview-template="#uploadPreviewTemplate">
            @csrf
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase bg-light  p-2 mt-0 mb-3">基本資訊</h5>
                            <div class="alert alert-danger alert-dismissible fade show p-2" id="final_price_display"
                                role="alert"></div>
                            <div class="row">
                                <div class="mb-3 col-md-4">
                                    <label for="type_list" class="form-label">案件類別選擇<span
                                            class="text-danger">*</span></label>
                                    <select id="type_list" class="form-select" name="type_list">
                                        <option value="dispatch" @if ($data->type_list == 'dispatch') selected @endif>派件單
                                        </option>
                                        <option value="memorial" @if ($data->type_list == 'memorial') selected @endif>追思單
                                        </option>
                                    </select>
                                </div>
                                <div class="mb-3 col-md-4">
                                    <label for="pay_id" class="form-label">支付類別<span class="text-danger">*</span></label>
                                    <select class="form-select" name="pay_id" id="pay_id" required>
                                        <option value="" selected>請選擇</option>
                                        <option value="A" @if ($data->pay_id == 'A') selected @endif>一次付清
                                        </option>
                                        <option value="C" @if ($data->pay_id == 'C') selected @endif>訂金</option>
                                        <option value="E" @if ($data->pay_id == 'E') selected @endif>追加</option>
                                        <option value="D" @if ($data->pay_id == 'D') selected @endif>尾款</option>
                                    </select>
                                </div>
                                <div class="mb-3 col-md-4">
                                    <label for="sale_on" class="form-label">單號<span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">No.</span>
                                        <input type="text" class="form-control" id="sale_on" name="sale_on"
                                            value="{{ $data->sale_on }}" required placeholder="請輸入數字" maxlength="10" inputmode="numeric">
                                    </div>
                                    <div id="sale_on_feedback" class="mt-1"></div>
                                </div>
                                <div class="mb-3 col-md-4">
                                    <label for="sale_date" class="form-label">日期<span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="sale_date" name="sale_date"
                                        value="{{ $data->sale_date }}" required>
                                </div>

                                <div class="mb-3 col-md-4">
                                    <label for="customer_id" class="form-label">客戶名稱<span
                                            class="text-danger required">*</span></label>
                                    <select class="form-control" data-toggle="select2" data-width="100%" name="cust_name_q"
                                        id="cust_name_q" required>
                                        <option value="">請選擇...</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}"
                                                @if ($data->customer_id == $customer->id) selected @endif>No.{{ $customer->id }}
                                                {{ $customer->name }}（{{ $customer->mobile }}）</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3 col-md-4">
                                    <label for="pet_name" class="form-label">寵物名稱<span
                                            class="text-danger required">*</span></label>
                                    <input type="text" class="form-control" id="pet_name" name="pet_name"
                                        value="{{ $data->pet_name }}">
                                </div>
                                <div class="mb-3 col-md-4 not_final_show not_memorial_show">
                                    <label for="variety" class="form-label">寵物品種<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="variety" name="variety"
                                        value="{{ $data->variety }}">
                                </div>
                                <div class="mb-3 col-md-4 not_final_show not_memorial_show">
                                    <label for="kg" class="form-label">公斤數<span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="kg" name="kg"
                                        min="0" step="0.01" value="{{ $data->kg }}">
                                </div>
                                <div class="mb-3 col-md-4 not_final_show not_memorial_show">
                                    <label for="type" class="form-label">案件來源<span
                                            class="text-danger">*</span></label>
                                    <select id="type" class="form-select" name="type">
                                        <option value="">請選擇...</option>
                                        @foreach ($sources as $source)
                                            <option value="{{ $source->code }}"
                                                @if ($source->code == $data->type) selected @endif>{{ $source->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3 col-md-4" id="source_company">
                                    <label for="source_company_id" class="form-label">來源公司名稱<span
                                            class="text-danger">*</span>
                                        @if (isset($sale_company))
                                            @if ($sale_company->type == 'self')
                                                （{{ $sale_company->self_name->name }}）
                                            @elseif(isset($sale_company))
                                                （{{ $sale_company->company_name->name }}）
                                            @else
                                                <b style="color: red;">（來源公司須重新至拜訪管理新增公司資料）</b>
                                            @endif
                                        @endif
                                    </label>
                                    <select class="form-control" data-toggle="select2" data-width="100%"
                                        name="source_company_name_q" id="source_company_name_q">
                                        <option value="">請選擇...</option>
                                        @if (isset($sale_company) && $sale_company->company_id)
                                            @foreach ($source_companys as $source_company)
                                                <option value="{{ $source_company->id }}"
                                                    @if ($sale_company->company_id == $source_company->id) selected @endif>
                                                    @if ($sale_company->type == 'self')
                                                        （員工）{{ $source_company->name }}（{{ $source_company->mobile }}）
                                                    @else
                                                        @if (isset($source_company->group) && $source_company->group)
                                                            （{{ $source_company->group->name }}）{{ $source_company->name }}（{{ $source_company->mobile }}）
                                                        @else
                                                            {{ $source_company->name }}（{{ $source_company->mobile }}）
                                                        @endif
                                                    @endif
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="mb-3 col-md-4 not_memorial_show plan">
                                    <label for="plan_id" class="form-label">方案選擇<span
                                            class="text-danger">*</span></label>
                                    <select id="plan_id" class="form-select" name="plan_id">
                                        <option value="">請選擇...</option>
                                        @foreach ($plans as $plan)
                                            <option value="{{ $plan->id }}"
                                                @if ($data->plan_id == $plan->id) selected @endif>{{ $plan->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3 col-md-4 not_final_show not_memorial_show">
                                    <label for="plan_price" class="form-label">方案價格<span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control total_number" id="plan_price"
                                        name="plan_price" value="{{ $data->plan_price }}">
                                </div>
                                <div class="mb-3 col-md-4" id="suit_field" style="display: none;">
                                    <label for="suit_id" class="form-label">套裝選擇<span
                                            class="text-danger">*</span></label>
                                    <select id="suit_id" class="form-select" name="suit_id">
                                        <option value="">請選擇...</option>
                                        @foreach ($suits as $suit)
                                            <option value="{{ $suit->id }}"
                                                @if ($data->suit_id == $suit->id) selected @endif>{{ $suit->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="mb-3 col-md-4" id="religion_field" style="display: none;">
                                    <label for="religion" class="form-label">宗教信仰<span class="text-danger">*</span></label>
                                    <select id="religion" class="form-select" name="religion">
                                        <option value="">請選擇...</option>
                                        <option value="buddhism_taoism" @if($data->religion == 'buddhism_taoism') selected @endif>佛道教</option>
                                        <option value="christianity" @if($data->religion == 'christianity') selected @endif>基督教</option>
                                        <option value="catholicism" @if($data->religion == 'catholicism') selected @endif>天主教</option>
                                        <option value="none" @if($data->religion == 'none') selected @endif>無宗教</option>
                                        <option value="other" @if($data->religion == 'other') selected @endif>其他</option>
                                    </select>
                                    <div id="religion_other_input" class="mt-2" style="display: none;">
                                        <input type="text" class="form-control" id="religion_other" name="religion_other" placeholder="請輸入其他宗教信仰" value="{{ $data->religion_other ?? '' }}">
                                    </div>
                                    <div id="religion_reminder" class="mt-1" style="display: none;">
                                        <small class="text-danger">提醒：資財袋為佛道教用品</small>
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4" id="death_date_field" style="display: none;">
                                    <label for="death_date" class="form-label">往生日期</label>
                                    <input type="date" class="form-control" id="death_date" name="death_date" value="{{ $data->death_date }}">
                                </div>
                                <div class="mb-3 col-md-4">
                                    <label for="user_id" class="form-label">服務專員<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="user_id" name="user_id"
                                        value="{{ $data->user_name->name }}" readonly>
                                </div>
                                {{-- <div class="mb-3 col-md-4 not_memorial_show" id="final_price">
                            <label for="plan_price" class="form-label" id="final_price_label">收款金額<span class="text-danger">*</span></label>
                            <input type="text" class="form-control total_number"  name="final_price" value="{{ $data->pay_price }}" >
                        </div> --}}
                                <div class="row">
                                    <div class="mb-1 mt-1">
                                        <div class="form-check" id="send_div">
                                            <input type="checkbox" class="form-check-input" id="send"
                                                name="send"
                                                @if ($data->send == 1) checked value="1" @endif>
                                            <label class="form-check-label" for="send"><b>親送</b></label>
                                        </div>
                                    </div>
                                    <div class="mb-1 mt-1" id="connector_div">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="connector_address"
                                                name="connector_address"
                                                @if ($data->connector_address == 1) checked value="1" @else  value="0" @endif>
                                            <label class="form-check-label"
                                                for="connector_address"><b>接體地址不為客戶地址</b></label>
                                        </div>
                                        <div class="mt-2 row" id="connector_address_div">
                                            <div class="col-md-4 mb-3">
                                                <label for="plan_price" class="form-label">接體縣市<span
                                                        class="text-danger">*</span></label>
                                                <div class="twzipcode mb-2">
                                                    <select data-role="county" required></select>
                                                    <select data-role="district"></select>
                                                    <select data-role="zipcode"></select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label" for="AddNew-Phone">接體地址<span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="address"
                                                    @if ($data->connector_address == 1) value="{{ $sale_address->address }}" @endif>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-1 mt-1" id="connector_hospital_div">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input"
                                                id="connector_hospital_address" name="connector_hospital_address"
                                                @if (isset($data->hospital_address) && $data->hospital_address != 0) checked value="1" @else  value="0" @endif>
                                            <label class="form-check-label"
                                                for="connector_hospital_address"><b>接體地址為醫院</b></label>
                                            @if (isset($data->hospital_address) && $data->hospital_address != 0)
                                                @if (isset($data->hospital_address_name))
                                                    【{{ $data->hospital_address_name->name }} 】
                                                @else
                                                    <span style="color: red;">（接體地址輸入錯誤）</span>
                                                @endif
                                            @endif
                                        </div>
                                        <div class="mt-2 row" id="connector_hospital_address_div">
                                            <div class="col-md-4">
                                                <select class="form-control" data-toggle="select2" data-width="100%"
                                                    name="hospital_address" id="hospital_address">
                                                    <option value="">請選擇...</option>
                                                    @foreach ($hospitals as $hospital)
                                                        <option value="{{ $hospital->id }}"
                                                            @if ($hospital->id == $data->hospital_address) selected @endif>
                                                            @if (isset($hospital->group) && $hospital->group)
                                                                （{{ $hospital->group->name }}）{{ $hospital->name }}（{{ $hospital->mobile }}）
                                                            @else
                                                                {{ $hospital->name }}（{{ $hospital->mobile }}）
                                                            @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-1 mt-1">
                                            <div class="form-check" id="cooperation_price_div">
                                                <input type="checkbox" class="form-check-input" id="cooperation_price"
                                                    name="cooperation_price"
                                                    @if ($data->cooperation_price == 1) checked value="1" @else  value="0" @endif>
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
                                                @if (count($sale_proms) > 0)
                                                    @foreach ($sale_proms as $key => $sale_prom)
                                                        <tr id="row-{{ $key }}">
                                                            <td class="text-center">
                                                                @if ($key == 0)
                                                                    <button type="button"
                                                                        class="ibtnAdd_prom demo-delete-row btn btn-primary btn-sm btn-icon"><i
                                                                            class="fa fas fa-plus"></i></button>
                                                                @else
                                                                    <button type="button"
                                                                        class="ibtnDel_prom demo-delete-row btn btn-danger btn-sm btn-icon"><i
                                                                            class="fa fa-times"></i></button>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <select id="select_prom_{{ $key }}"
                                                                    alt="{{ $key }}"
                                                                    data-key="{{ $key }}"
                                                                    class="mobile form-select" name="select_proms[]"
                                                                    onchange="chgItems(this)">
                                                                    <option value="" selected>請選擇</option>
                                                                    <option value="A"
                                                                        @if ($sale_prom->prom_type == 'A') selected @endif>
                                                                        安葬處理</option>
                                                                    <option value="B"
                                                                        @if ($sale_prom->prom_type == 'B') selected @endif>
                                                                        後續處理</option>
                                                                    <option value="C"
                                                                        @if ($sale_prom->prom_type == 'C') selected @endif>
                                                                        其他處理</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <select id="prom_{{ $key }}"
                                                                    class="mobile form-select" name="prom[]"
                                                                    onchange="chgPromItems(this)">
                                                                    @foreach ($proms as $prom)
                                                                        <option value="{{ $prom->id }}"
                                                                            @if ($sale_prom->prom_id == $prom->id) selected @endif>
                                                                            {{ $prom->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <!-- 套組法會額外備註 (prom value=8) - 移到 prom_product 容器外 -->
                                                                <div class="row mt-2" id="prom_extra_text_col_{{ $key }}" style="display:none;">
                                                                    <div class="col-12">
                                                                        <input class="form-control" type="text" id="prom_extra_text_{{$key}}" name="prom_extra_text[]" placeholder="套組法會備註" value="{{ $sale_prom->comment }}">
                                                                    </div>
                                                                </div>
                                                                <!-- 商品相關欄位 - 根據 prom_id 決定顯示 -->
                                                                <div class="row mt-1 prom-product-container" id="prom_product_{{ $key }}" style="display: none;" data-existing-product-id="{{ $sale_prom->is_custom_product ? '' : ($sale_prom->product_id ?? '') }}" data-existing-variant-id="{{ $sale_prom->is_custom_product ? '' : ($sale_prom->variant_id ?? '') }}" data-is-custom-product="{{ $sale_prom->is_custom_product ? '1' : '0' }}">
                                                                    <!-- 紀念品類型選擇 -->
                                                                    <div class="col-3" id="souvenir_type_col_{{ $key }}" style="display:none;">
                                                                        <select id="product_souvenir_type_{{ $key }}" class="form-select" name="product_souvenir_types[]">
                                                                            <option value="">請選擇</option>
                                                                            @foreach ($souvenir_types as $souvenir_type)
                                                                                <option value="{{ $souvenir_type->id }}" @if (isset($sale_prom->souvenir_data) && $sale_prom->souvenir_data->souvenir_type == $souvenir_type->id) selected @endif>{{ $souvenir_type->name }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <!-- 商品名稱輸入 -->
                                                                    <div class="col-3" id="product_name_col_{{ $key }}" style="display:none;">
                                                                        <input type="text" id="product_name_{{ $key }}" class="form-control" name="product_name[]" placeholder="請輸入商品名稱" value="{{ $sale_prom->souvenir_data->product_name ?? '' }}">
                                                                    </div>
                                                                                                                                                                            <!-- 商品選擇下拉 -->
                                                                    <div class="col-3" id="product_prom_col_{{ $key }}">
                                                                        <!-- Debug info -->
                                                                        @if(isset($sale_prom->souvenir_data))
                                                                            @if($sale_prom->souvenir_data->souvenir_type == null)
                                                                                <!-- Debug: 關聯商品 - Product ID: {{ $sale_prom->souvenir_data->product_name ?? 'NULL' }} -->
                                                                                <!-- Debug: is_custom_product: {{ $sale_prom->is_custom_product ?? 'NULL' }} -->
                                                                                <!-- Debug: product_id: {{ $sale_prom->product_id ?? 'NULL' }} -->
                                                                                <!-- Debug: variant_id: {{ $sale_prom->variant_id ?? 'NULL' }} -->
                                                                            @else
                                                                                <!-- Debug: 自訂商品 - Product Name: {{ $sale_prom->souvenir_data->product_name ?? 'NULL' }} -->
                                                                                <!-- Debug: is_custom_product: {{ $sale_prom->is_custom_product ?? 'NULL' }} -->
                                                                                <!-- Debug: souvenir_type_id: {{ $sale_prom->souvenir_type_id ?? 'NULL' }} -->
                                                                            @endif
                                                                        @else
                                                                            <!-- Debug: souvenir_data is NULL -->
                                                                        @endif
                                                                        <select id="product_prom_{{ $key }}" class="form-select" name="product_proms[]" onchange="checkProductVariants({{ $key }})" data-existing-product-id="{{ $sale_prom->is_custom_product ? '' : ($sale_prom->product_id ?? '') }}" data-is-custom-product="{{ $sale_prom->is_custom_product ? '1' : '0' }}">
                                                                            <option value="">請選擇</option>
                                                                        </select>
                                                                    </div>
                                                                    <!-- 細項選擇 -->
                                                                    <div class="col-3" id="variant_select_{{ $key }}">
                                                                        <select id="product_variant_{{ $key }}" class="form-select" name="product_variants[]" data-existing-variant-id="{{ $sale_prom->is_custom_product ? '' : ($sale_prom->variant_id ?? '') }}">
                                                                            <option value="">無</option>
                                                                        </select>
                                                                    </div>
                                                                    <!-- 數量 -->
                                                                    <div class="col-3" id="product_num_col_{{ $key }}">
                                                                        <input class="form-control" type="number" id="product_num_{{$key}}" name="product_num[]" value="{{ $sale_prom->souvenir_data->product_num ?? 1 }}" min="1">
                                                                    </div>
                                                                    <!-- 備註 -->
                                                                    <div class="col-3" id="product_comment_col_{{ $key }}">
                                                                        <input class="form-control" type="text" id="product_comment_{{$key}}" name="product_comment[]" placeholder="備註" value="{{ $sale_prom->souvenir_data->comment ?? '' }}">
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <input type="text"
                                                                    class="mobile form-control total_number"
                                                                    id="prom_total_{{ $key }}"
                                                                    name="prom_total[]"
                                                                    value="{{ $sale_prom->prom_total }}">
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
                                                                <button type="button"
                                                                    class="ibtnAdd_prom demo-delete-row btn btn-primary btn-sm btn-icon"><i
                                                                        class="fa fas fa-plus"></i></button>
                                                            @else
                                                                <button type="button"
                                                                    class="ibtnDel_prom demo-delete-row btn btn-danger btn-sm btn-icon"><i
                                                                        class="fa fa-times"></i></button>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <select id="select_prom_{{ $i }}"
                                                                alt="{{ $i }}" class="mobile form-select"
                                                                name="select_proms[]" onchange="chgItems(this)">
                                                                <option value="" selected>請選擇</option>
                                                                <option value="A">安葬處理</option>
                                                                <option value="B">後續處理</option>
                                                                <option value="C">其他處理</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select id="prom_{{ $i }}"
                                                                class="mobile form-select" name="prom[]">
                                                                <option value="">請選擇</option>
                                                            </select>
                                                            <!-- 套組法會額外備註 (prom value=8) - 移到 prom_product 容器外 -->
                                                            <div class="row mt-2" id="prom_extra_text_col_{{ $i }}" style="display:none;">
                                                                <div class="col-12">
                                                                    <input class="form-control" type="text" id="prom_extra_text_{{$i}}" name="prom_extra_text[]" placeholder="套組法會備註">
                                                                </div>
                                                            </div>
                                                                <!-- 商品相關欄位 - 根據 prom_id 決定顯示 -->
                                                                <div class="row mt-1 prom-product-container" id="prom_product_{{ $i }}" style="display: none;">
                                                                    <!-- 紀念品類型選擇 -->
                                                                    <div class="col-3" id="souvenir_type_col_{{ $i }}" style="display:none;">
                                                                        <select id="product_souvenir_type_{{ $i }}" class="form-select" name="product_souvenir_types[]">
                                                                        <option value="">請選擇</option>
                                                                        @foreach ($souvenir_types as $souvenir_type)
                                                                            <option value="{{ $souvenir_type->id }}">{{ $souvenir_type->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                    <!-- 商品名稱輸入 -->
                                                                    <div class="col-3" id="product_name_col_{{ $i }}" style="display:none;">
                                                                    <input type="text" id="product_name_{{ $i }}" class="form-control" name="product_name[]" placeholder="請輸入商品名稱">
                                                                </div>
                                                                    <!-- 商品選擇下拉 -->
                                                                    <div class="col-3" id="product_prom_col_{{ $i }}">
                                                                        <select id="product_prom_{{ $i }}" class="form-select" name="product_proms[]" onchange="checkProductVariants({{ $i }})">
                                                                        <option value="">請選擇</option>
                                                                    </select>
                                                                </div>
                                                                    <!-- 細項選擇 -->
                                                                    <div class="col-3" id="variant_select_{{ $i }}">
                                                                    <select id="product_variant_{{ $i }}" class="form-select" name="product_variants[]">
                                                                        <option value="">無</option>
                                                                    </select>
                                                                </div>
                                                                    <!-- 數量 -->
                                                                    <div class="col-3" id="product_num_col_{{ $i }}">
                                                                    <input class="form-control" type="number" id="product_num_{{$i}}" name="product_num[]" value="1" min="1">
                                                                </div>
                                                                    <!-- 備註 -->
                                                                    <div class="col-3" id="product_comment_col_{{ $i }}">
                                                                    <input class="form-control" type="text" id="product_comment_{{$i}}" name="product_comment[]" placeholder="備註">
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                                <input type="text"
                                                                    class="mobile form-control total_number"
                                                                    id="prom_total_{{ $i }}"
                                                                    name="prom_total[]">
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
                                                @if (count($sale_gdpapers) > 0)
                                                    @foreach ($sale_gdpapers as $key => $sale_gdpaper)
                                                        <tr id="row-{{ $key }}">
                                                            <td class="text-center">
                                                                @if ($key == 0)
                                                                    <button type="button"
                                                                        class="ibtnAdd_gdpaper demo-delete-row btn btn-primary btn-sm btn-icon"><i
                                                                            class="fa fas fa-plus"></i></button>
                                                                @else
                                                                    <button type="button"
                                                                        class="ibtnDel_gdpaper demo-delete-row btn btn-danger btn-sm btn-icon"><i
                                                                            class="fa fa-times"></i></button>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <select id="gdpaper_id_{{ $key }}"
                                                                    alt="{{ $key }}" class="mobile form-select"
                                                                    name="gdpaper_ids[]" onchange="chgPapers(this)">
                                                                    <option value="" selected>請選擇...</option>
                                                                    @foreach ($products as $product)
                                                                        <option value="{{ $product->id }}"
                                                                            @if ($product->id == $sale_gdpaper->gdpaper_id) selected @endif>
                                                                            {{ $product->name }}({{ $product->price }})
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="number" class="mobile form-control"
                                                                    id="gdpaper_num_{{ $key }}"
                                                                    alt="{{ $key }}" name="gdpaper_num[]"
                                                                    value="{{ $sale_gdpaper->gdpaper_num }}"
                                                                    onchange="chgNums(this)" onclick="chgNums(this)"
                                                                    onkeydown="chgNums(this)">
                                                            </td>
                                                            <td>
                                                                <input type="text"
                                                                    class="mobile form-control total_number"
                                                                    id="gdpaper_total_{{ $key }}"
                                                                    name="gdpaper_total[]"
                                                                    value="{{ $sale_gdpaper->gdpaper_total }}">
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
                                                                <button type="button"
                                                                    class="ibtnAdd_gdpaper demo-delete-row btn btn-primary btn-sm btn-icon"><i
                                                                        class="fa fas fa-plus"></i></button>
                                                            @else
                                                                <button type="button"
                                                                    class="ibtnDel_gdpaper demo-delete-row btn btn-danger btn-sm btn-icon"><i
                                                                        class="fa fa-times"></i></button>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <select id="gdpaper_id_{{ $i }}"
                                                                alt="{{ $i }}" class="mobile form-select"
                                                                name="gdpaper_ids[]" onchange="chgPapers(this)">
                                                                <option value="" selected>請選擇...</option>
                                                                @foreach ($products as $product)
                                                                    <option value="{{ $product->id }}">
                                                                        {{ $product->name }}({{ $product->price }})
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="number" alt="{{ $i }}"
                                                                    class="mobile form-control"
                                                                    id="gdpaper_num_{{ $i }}"
                                                                    name="gdpaper_num[]" onchange="chgNums(this)"
                                                                    onclick="chgNums(this)" onkeydown="chgNums(this)">
                                                        </td>
                                                        <td>
                                                                <input type="text"
                                                                    class="mobile form-control total_number"
                                                                id="gdpaper_total_{{ $i }}"
                                                                name="gdpaper_total[]" value="">
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



            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase bg-light  p-2 mt-0 mb-3">付款方式</h5>
                            <div class="row">
                                <div class="mb-3 col-md-12">
                                    <h2>應收金額<span id="total_text" class="text-danger">{{ $data->total }}</span>元</h2>
                                    <input type="hidden" class="form-control" id="total" name="total"
                                        value="{{ $data->total }}" readonly>
                                </div>
                                <div class="mb-3 col-md-4">
                                    <label for="pay_method" class="form-label">收款方式<span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="pay_method" name="pay_method" required>
                                        <option value="" selected>請選擇</option>
                                        <option value="A" @if ($data->pay_method == 'A') selected @endif>現金
                                        </option>
                                        <option value="B" @if ($data->pay_method == 'B') selected @endif>匯款
                                        </option>
                                        <option value="C" @if ($data->pay_method == 'C') selected @endif>現金與匯款
                                        </option>
                                    </select>
                                </div>
                                <div class="mb-3 col-md-4" id="cash_price_div">
                                    <label for="pay_price" class="form-label">現金收款<span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="cash_price" name="cash_price"
                                        value="{{ $data->cash_price }}">
                                </div>
                                <div class="mb-3 col-md-4" id="transfer_price_div">
                                    <label for="pay_price" class="form-label">匯款收款<span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="transfer_price" name="transfer_price"
                                        value="{{ $data->transfer_price }}">
                                </div>
                                <div class="mb-3 col-md-4" id="transfer_channel_div">
                                    <label for="pay_id" class="form-label">匯款管道<span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" name="transfer_channel">
                                        <option value="" selected>請選擇</option>
                                        <option value="銀行轉帳" @if ($data->transfer_channel == '銀行轉帳') selected @endif>銀行轉帳
                                        </option>
                                        <option value="Line Pay" @if ($data->transfer_channel == 'Line Pay') selected @endif>Line
                                            Pay</option>
                                        <option value="臨櫃匯款" @if ($data->transfer_channel == '臨櫃匯款') selected @endif>臨櫃匯款</option>
                                    </select>
                                </div>
                                <div class="mb-3 col-md-4" id="transfer_number_div">
                                    <label for="pay_price" class="form-label">匯款後四碼<span
                                            class="text-danger"></span></label>
                                    <input type="number" class="form-control" id="transfer_number"
                                        name="transfer_number" value="{{ $data->transfer_number }}">
                                </div>
                                <div class="mb-3 col-md-4">
                                    <label for="pay_price" class="form-label">本次收款<span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="pay_price" name="pay_price"
                                        value="{{ $data->pay_price }}" required>
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
                        <button type="button" class="btn w-sm btn-light waves-effect"
                            onclick="history.go(-1)">回上一頁</button>
                        <button type="submit" class="btn w-sm btn-success waves-effect waves-light"
                            id="submit_btn">編輯</button>
                        {{-- <button type="button" class="btn w-sm btn-danger waves-effect waves-light">Delete</button> --}}
                    </div>
                </div> <!-- end col -->
            </div>
            <input type="hidden" id="row_id" name="row_id" value="">
            <input type="hidden" id="original_pay_id" value="{{ $data->pay_id ?? '' }}">
            <input type="hidden" id="sale_id" value="{{ $data->id ?? '' }}">


        </form>


    </div> <!-- container -->
    </div>
@endsection


@section('script')
    <script src="{{ asset('assets/libs/selectize/selectize.min.js') }}"></script>
    <script src="{{ asset('assets/libs/mohithg-switchery/mohithg-switchery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/multiselect/multiselect.min.js') }}"></script>
    <script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/libs/jquery-mockjax/jquery-mockjax.min.js') }}"></script>
    <script src="{{ asset('assets/libs/devbridge-autocomplete/devbridge-autocomplete.min.js') }}"></script>
    {{-- <script src="{{asset('assets/libs/bootstrap-touchspin/bootstrap-touchspin.min.js')}}"></script>
<script src="{{asset('assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js')}}"></script> --}}
    <!-- demo app -->
    <script src="{{ asset('assets/js/twzipcode-1.4.1-min.js') }}"></script>
    <script src="{{ asset('assets/js/twzipcode.js') }}"></script>
    <!-- third party js ends -->
    <script src="{{ asset('assets/js/pages/form-advanced.init.js') }}"></script>

    <!-- demo app -->


    <script>
        var type_list = $('select[name="type_list"]').val();
        var payIdValue = $('select[name="pay_id"]').val();
        var payMethod = $('select[name="pay_method"]').val();
       $(document).ready(function() {
            var saleAddress = <?php echo json_encode(isset($sale_address) ? $sale_address : null); ?>;
            // Check if $sale_address exists
            var connector_address = $('input[name="connector_address"]').val();
            if (connector_address != 0) {
                // If $sale_address exists, initialize twzipcode with preselected values
                $(".twzipcode").twzipcode({
                    css: ["twzipcode-select", "twzipcode-select", "twzipcode-select"],
                    countyName: "county",
                    districtName: "district",
                    zipcodeName: "zipcode",
                    countySel: saleAddress.county,
                    districtSel: saleAddress.district
                });
            } else {
                // If $sale_address doesn't exist, initialize twzipcode without preselected values
                $(".twzipcode").twzipcode({
                    css: ["twzipcode-select", "twzipcode-select", "twzipcode-select"],
                    countyName: "county",
                    districtName: "district",
                    zipcodeName: "zipcode"
                });
            }

            // 初始化宗教和往生日期欄位的顯示狀態
            initializeReligionAndDeathDateFields();
            
            // 初始化方案價格欄位顯示狀態
            var currentPlanId = $('#plan_id').val();
            var currentPayId = $('select[name="pay_id"]').val();
            if (currentPlanId && currentPayId) {
                handlePlanPriceField(currentPlanId, currentPayId);
            }

            // 頁面載入時檢查現有後續處理項目的備註欄位和商品欄位顯示狀態
            $('select[name="prom[]"]').each(function() {
                var row_id = $(this).closest('tr').find('select[name="select_proms[]"]').attr('alt');
                var prom_id = $(this).val();
                var extra_text_col = $("#prom_extra_text_col_" + row_id);
                var prom_product_container = $("#prom_product_" + row_id);
                
                if (prom_id == '8' || prom_id == '7') {
                    // 套組法會：顯示備註，隱藏商品欄位
                    extra_text_col.show();
                    prom_product_container.hide();
                } else if (prom_id && prom_id != '') {
                    // 其他項目：隱藏備註，檢查是否需要顯示商品欄位
                    extra_text_col.hide();
                    
                    // 檢查是否有商品資料或is_custom_product為1
                $.ajax({
                    url: '{{ route('product.prom_product_search') }}',
                        data: { 'prom_id': prom_id },
                    dataType: 'json',
                    success: function(data) {
                    console.log('prom_product_search data:', data);
                    
                    // 從 prom_product_container 讀取 is_custom_product 值
                    var promProductContainer = $("#prom_product_" + row_id);
                    var isCustomProduct = promProductContainer.data('is-custom-product');
                    console.log('Row ' + row_id + ' - is_custom_product from HTML:', isCustomProduct);
                    
                    var shouldShow = (data.products && data.products.length > 0) || isCustomProduct == 1;
                    
                    if (shouldShow) {
                                if (isCustomProduct == 1) {
                                    // 自訂商品，顯示自訂商品相關欄位
                                    $('#souvenir_type_col_' + row_id).show();
                                    $('#product_name_col_' + row_id).show();
                                    $('#product_prom_col_' + row_id).hide();
                                    $('#variant_select_' + row_id).hide();
                                    $('#product_num_col_' + row_id).show();
                                    $('#product_comment_col_' + row_id).show();
                                } else {
                                    // 有商品資料，顯示商品選擇相關欄位
                                    $('#souvenir_type_col_' + row_id).hide();
                                    $('#product_name_col_' + row_id).hide();
                                    $('#product_prom_col_' + row_id).show();
                                    $('#variant_select_' + row_id).show();
                                    $('#product_num_col_' + row_id).show();
                                    $('#product_comment_col_' + row_id).show();
                                    
                                    // 填入商品下拉
                                    var html = '<select id="product_prom_' + row_id + '" class="form-select" name="product_proms[]" onchange="checkProductVariants(' + row_id + ')">';
                            html += '<option value="">請選擇</option>';
                            data.products.forEach(function(item) {
                                var hasVariants = (item.variants && item.variants.length > 0) ? '1' : '0';
                                html += '<option value="' + item.id + '" data-has-variants="' + hasVariants + '">' + item.name + ' (' + item.price + ')</option>';
                            });
                            html += '</select>';
                            
                                    $('#product_prom_col_' + row_id).html(html);
                            
                            // 儲存商品資料供細項選擇使用
                            window.productData = window.productData || {};
                                    window.productData[row_id] = data.products;
                                    window.productData[row_id].is_custom_product = data.is_custom_product;
                                    
                                    // 如果有現有的商品選擇，設定選中狀態並觸發細項檢查
                                    // 從 prom_product_container 讀取 data-existing-product-id
                                    var existingProductId = prom_product_container.data('existing-product-id');
                                    console.log('Row ' + row_id + ' - Existing product ID from container:', existingProductId);
                                    
                                    if (existingProductId && existingProductId !== '') {
                                        $('#product_prom_' + row_id).val(existingProductId);
                                        console.log('Row ' + row_id + ' - Set product value to:', existingProductId);
                                        // 延遲執行細項檢查，確保商品資料已載入
                                        setTimeout(function() {
                                            checkProductVariants(row_id);
                                        }, 100);
                                    } else {
                                        console.log('Row ' + row_id + ' - No existing product ID found or empty');
                                    }
                        }
                        
                        // 顯示整個prom_product區塊
                                prom_product_container.show(300);
                        } else {
                        // 沒有商品資料且is_custom_product不為1，隱藏區塊
                                prom_product_container.hide(300);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('prom_product_search error:', error);
                    // 發生錯誤時隱藏區塊
                            prom_product_container.hide(300);
                        }
                    });
                }
            });
        });

        //判斷尾款、訂金
        $("#final_price_display").hide();

        $(document).ready(function() {
            // 預設初始化
            checkFinalAndSuit();

            // 綁定欄位變更事件
            $('#pay_id, #cust_name_q, #pet_name, #plan_id, #type_list').on('change keyup', function() {
                checkFinalAndSuit();
            });

            // 初始化商品細項必填驗證
            initializeProductVariantValidation();

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
                    success: function(response) {
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

            if (typeList === 'dispatch' && (payId === 'A' || payId === 'D' || payId === 'E')) {
                if (planId === '1' && (payId === 'A' || payId === 'E')) {
                    $('#suit_id').prop('required', true);
                    $('#suit_field').show(300);
                } else if (response && response.data && response.data.plan_id === '1' && payId === 'D') {
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
                        error: function(xhr, status, error) {
                            console.error('AJAX 錯誤:', error);
                        }
                    });
                } else {
                    console.log('payId / customerId / petName 未填');
                }
            }
        });

        //親送
        var send = $('input[name="send"]').val();
        console.log(send);
        if (send == 1) {
            $("#connector_div").hide();
            $("#connector_hospital_div").hide();
        } else {
            $("#connector_div").show();
            $("#connector_hospital_div").show();
        }
        $("#send").on("change", function() {
            if ($(this).is(':checked')) {
                $(this).val(1);
                $("#connector_div").hide(300);
                $("#connector_hospital_div").hide();
            } else {
                $(this).val(0);
                $("#connector_div").show(300);
                $("#connector_hospital_div").show(300);
            }
        });

        //院內價開始
        cooperation_price = $('input[name="cooperation_price"]').val();
        console.log(cooperation_price);
        $("#cooperation_price").on("change", function() {
            if ($(this).is(':checked')) {
                $(this).val(1);
            } else {
                $(this).val(0);
            }
        });
        //院內價結束

        //地址
        var connector_address = $('input[name="connector_address"]').val();
        if (connector_address == 1) {
            $("#send_div").hide();
            $("#connector_hospital_div").hide();
            $("#connector_div").show();
        } else {
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
                $('#your-form').submit(function(event) {
                    var county = $('select[name="county"]').val();
                    if (county == '') {
                        alert('接體縣市不得為空！');
                        event.preventDefault();
                    }
                });
                $("#address").prop('required', true);
            } else {
                $("#connector_address_div").hide(300);
                $("#send_div").show(300);
                $("#connector_hospital_div").show(300);
                $(this).val(0);
                $('#your-form').off('submit');
                // Remove pet name required attribute
                $("#address").prop('required', false);
            }
        });

        //醫院地址
        var connector_hospital_address = $('input[name="connector_hospital_address"]').is(':checked');
            console.log('connector_hospital_address checked:', connector_hospital_address);
        if (connector_hospital_address) {
            $("#connector_hospital_address_div").show();
            $("#connector_div").hide();
            $("#send_div").hide();
        } else {
            $("#connector_hospital_address_div").hide();
        }
        $("#connector_hospital_address").on("change", function() {
            if ($(this).is(':checked')) {
                $("#connector_hospital_address_div").show(300);
                $("#connector_div").hide(300);
                $("#send_div").hide(300);
                $(this).val(1);
                $("#hospital_address").prop('required', true);
                $('#your-form').submit(function(event) {
                    var hospital_address = $('input[name="hospital_address"]').val();
                    if (hospital_address == '') {
                        alert('接體醫院不得為空！');
                        event.preventDefault();
                    }
                });
            } else {
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
        if (type_list == 'memorial') {
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
            if (payIdValue == 'A' || payIdValue == 'C') {
                $(".not_memorial_show").hide(300);
            }

        } else if (type_list == 'dispatch') {
            $(".not_memorial_show").show(300);
            $("#cust_name_q").prop('required', true);
            $(".required").show();
            if (payIdValue == 'D' || payIdValue == 'E') {
                $("#final_price").show(300);
                $(".not_final_show").hide();
                if (payIdValue == 'D') {
                    $(".plan").hide(300);
                    $("#plan_id").prop('required', false);
                } else {
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
            } else {
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
                if (payIdValue == 'C') {
                    $("#prom_div").hide(300);
                    $("#gdpaper_div").hide(300);
                    $("#souvenir_div").hide(300);
                }
            }
        }

        $('select[name="type_list"]').on('change', function() {
            payIdValue = $('select[name="pay_id"]').val();
            if ($(this).val() == 'memorial') {
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
            } else if ($(this).val() == 'dispatch') {
                $(".not_memorial_show").show(300);
                $("#cust_name_q").prop('required', true);
                $(".required").show();
                if (payIdValue == 'D' || payIdValue == 'E') {
            $("#final_price").show(300);
                    $(".not_final_show").hide();
                    if (payIdValue == 'D') {
                        $(".plan").hide(300);
                        $("#plan_id").prop('required', false);
                        $("#suit_id").prop('required', false);
                } else {
                        $(".plan").show(300);
                        $("#plan_id").prop('required', true);
                        // 追加(E)時，如果方案ID為1，套裝欄位應該顯示
                        var currentPlanId = $("#plan_id").val();
                        if (currentPlanId === '1') {
                            $("#suit_id").prop('required', true);
            } else {
                            $("#suit_id").prop('required', false);
                        }
                    }
                    $("#kg").prop('required', false);
                    $("#variety").prop('required', false);
                    $("#type").prop('required', false);
                    $("#plan_price").prop('required', false);
            } else {
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
                    if (payIdValue == 'C') {
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
            var planId = $('#plan_id').val();
            console.log(a);
            
            // 處理方案價格欄位
            handlePlanPriceField(planId, a);
            
            if ($(this).val() == 'D' || $(this).val() == 'E') {
                $(".not_final_show").hide(300);
                if ($(this).val() == 'D') {
                    $(".plan").hide(300);
                    $("#plan_id").prop('required', false);
                    $("#suit_id").prop('required', false);
                } else {
                    $(".plan").show(300);
                    $("#plan_id").prop('required', true);
                    // 追加(E)時，如果方案ID為1，套裝欄位應該顯示
                    var currentPlanId = $("#plan_id").val();
                    if (currentPlanId === '1') {
                        $("#suit_id").prop('required', true);
                } else {
                        $("#suit_id").prop('required', false);
                    }
                }
                $("#kg").prop('required', false);
                $("#variety").prop('required', false);
                $("#type").prop('required', false);
                $("#plan_price").prop('required', false);
                if (type_list == 'memorial') {
                    $("#final_price").hide();
                    $(".not_memorial_show").hide();
                    $("#send_div").hide(300);
                    $("#connector_div").hide(300);
                    $("#connector_hospital_div").hide(300);
                }
                $("#send_div").hide();
                $("#connector_div").hide();
                $("#connector_hospital_div").hide();
            } else {
                $("#prom_div").show(300);
                $("#gdpaper_div").show(300);
                $("#souvenir_div").show(300);
                $("#final_price").hide(300);
                $("#send_div").show(300);
                $("#connector_div").show(300);
                $("#connector_hospital_div").show(300);
                if (type_list == 'memorial') {
                    $("#final_price").hide();
                    $(".not_memorial_show").hide();
                    $("#send_div").hide();
                    $("#connector_div").hide();
                    $("#connector_hospital_div").hide();
            } else {
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
                    if ($(this).val() == 'C') {
                        $("#prom_div").hide(300);
                        $("#souvenir_div").hide(300);
                        $("#gdpaper_div").hide(300);
                    }
                }
            }
        });

        // 載入指定類型的客戶
        function loadCustomersByType(type) {
            // 獲取當前已選擇的值
            var currentSelected = $('#source_company_name_q').val();

            $.ajax({
                url: '{{ route('customers.by-type') }}',
                type: 'GET',
                data: {
                    type: type,
                    selected_id: currentSelected
                },
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

        var type = $('select[name="type"]').val();
        if (type == 'H' || type == 'B' || type == 'Salon' || type == 'G' || type == 'dogpark' || type == 'other' || type ==
            'self') {
            $("#source_company").show(300);
            $("#source_company_name_q").prop('required', true);
            // 載入對應類型的客戶
            loadCustomersByType(type);
                } else {
            $("#source_company").hide(300);
            $("#source_company_name_q").prop('required', false);
        }

        $('select[name="type"]').on('change', function() {
            var selectedType = $(this).val();
            if (selectedType == 'H' || selectedType == 'B' || selectedType == 'Salon' || selectedType == 'G' ||
                selectedType == 'dogpark' || selectedType == 'other' || selectedType == 'self') {
                $("#source_company").show(300);
                $("#source_company_name_q").prop('required', true);
                // 載入對應類型的客戶
                loadCustomersByType(selectedType);
            } else {
                $("#source_company").hide(300);
                $("#source_company_name_q").prop('required', false);
                $("#source_company_name_q").val('');
            }
        });

        $("#cash_price_div").hide();
        $("#transfer_price_div").hide();
        $("#transfer_number_div").hide();
        $("#transfer_channel_div").hide();
        if (payMethod == 'C') {
            $("#cash_price_div").show(300);
            $("#transfer_price_div").show(300);
            $("#transfer_number_div").show(300);
            $("#transfer_channel_div").show(300);
            $("#pay_price").prop('required', false);
            $("#cash_price").prop('required', true);
            $("#transfer_price").prop('required', true);
        } else if (payMethod == 'B') {
            $("#cash_price_div").hide(300);
            $("#transfer_price_div").hide(300);
            $("#transfer_number_div").show(300);
            $("#transfer_channel_div").show(300);
            $("#pay_price").prop('required', true);
            $("#cash_price").prop('required', false);
            $("#transfer_price").prop('required', false);
        } else {
            $("#cash_price_div").hide(300);
            $("#transfer_price_div").hide(300);
            $("#transfer_number_div").hide(300);
            $("#transfer_channel_div").hide(300);
            $("#pay_price").prop('required', true);
            $("#cash_price").prop('required', false);
            $("#transfer_price").prop('required', false);
        }
        $('select[name="pay_method"]').on('change', function() {
            if ($(this).val() == 'C') {
                $("#cash_price_div").show(300);
                $("#transfer_price_div").show(300);
                $("#transfer_number_div").show(300);
                $("#transfer_channel_div").show(300);
                $("#pay_price").prop('required', false);
                $("#cash_price").prop('required', true);
                $("#transfer_price").prop('required', true);
            } else if ($(this).val() == 'B') {
                $("#cash_price_div").hide(300);
                $("#transfer_price_div").hide(300);
                $("#transfer_number_div").show(300);
                $("#transfer_channel_div").show(300);
                $("#pay_price").prop('required', true);
                $("#cash_price").prop('required', false);
                $("#transfer_price").prop('required', false);
            } else {
                $("#cash_price_div").hide(300);
                $("#transfer_price_div").hide(300);
                $("#transfer_number_div").hide(300);
                $("#transfer_channel_div").hide(300);
                $("#pay_price").prop('required', true);
                $("#cash_price").prop('required', false);
                $("#transfer_price").prop('required', false);
            }
        });


        $("#final_price").on('input', function() {
            calculate_price();
        });

        $("#plan_price").on('input', function() {
            calculate_price();
        });
            
        // 方案變更時檢查套裝欄位顯示和宗教相關欄位
        $("#plan_id").on('change', function() {
            var planId = $(this).val();
            var payId = $('select[name="pay_id"]').val();
            var typeList = $('select[name="type_list"]').val();
            
            // 檢查是否應該顯示套裝欄位
            if (typeList === 'dispatch' && (payId === 'A' || payId === 'E') && planId === '1') {
                $('#suit_id').prop('required', true);
                $('#suit_field').show(300);
            } else if (typeList === 'dispatch' && payId === 'D' && planId === '1') {
                $('#suit_id').prop('required', true);
                $('#suit_field').show(300);
                } else {
                $('#suit_id').prop('required', false);
                $('#suit_field').hide(300);
                $('#suit_id').val('');
            }
            
            // 處理浪浪方案的 plan_price 欄位
            handlePlanPriceField(planId, payId);
            
            // 根據方案選擇控制宗教和往生日期欄位顯示
            if (typeList === 'dispatch' && (payId === 'A' || payId === 'C')) {
                // 將 planId 轉換為字串進行比較
                var planIdStr = String(planId);
                
                if (planIdStr === '1' || planIdStr === '2') {
                    // 個人、團體方案：顯示宗教和往生日期
                    $('#religion_field').show(300);
                    $('#death_date_field').show(300);
                    console.log('個人/團體方案 (ID:', planIdStr, ')：顯示宗教和往生日期');
                } else if (planIdStr === '3') {
                    // 浪浪方案：只顯示宗教，不顯示往生日期
                    $('#religion_field').show(300);
                    $('#death_date_field').hide(300);
                    $('#death_date').val('').prop('required', false); // 清空往生日期
                    console.log('浪浪方案 (ID:', planIdStr, ')：只顯示宗教，不顯示往生日期');
                } else {
                    // 其他方案：不顯示宗教和往生日期
                    $('#religion_field').hide(300);
                    $('#death_date_field').hide(300);
                    $('#religion').val(''); // 清空宗教選擇
                    $('#death_date').val('').prop('required', false); // 清空往生日期
                    console.log('其他方案 (ID:', planIdStr, ')：不顯示宗教和往生日期');
                }
            } else {
                // 非派件單或非一次付清/訂金，隱藏所有宗教相關欄位
                $('#religion_field').hide(300);
                $('#death_date_field').hide(300);
                $('#religion').val('');
                $('#death_date').val('').prop('required', false);
            }
        });

        $(document).on('input', '.total_number', function() {
            calculate_price();
        });

        // 宗教選擇變更事件
        $("#religion").on('change', function() {
            var religion = $(this).val();
            var planId = $('#plan_id').val();
            var typeList = $('#type_list').val();
            var payId = $('#pay_id').val();
            
            console.log('宗教變更:', religion, '方案:', planId);
            
            // 處理「其他」選項的顯示/隱藏
            if (religion === 'other') {
                $('#religion_other_input').show(300);
                $('#religion_other').prop('required', true);
            } else {
                $('#religion_other_input').hide(300);
                $('#religion_other').val('').prop('required', false);
            }
            
            // 檢查是否顯示宗教提醒
            if (religion && religion !== 'buddhism_taoism') {
                // 非佛道教，顯示提醒
                $('#religion_reminder').show(300);
            } else {
                // 佛道教或未選擇，隱藏提醒
                $('#religion_reminder').hide(300);
            }
            
            if (typeList === 'dispatch' && (payId === 'A' || payId === 'C')) {
                // 將 planId 轉換為字串進行比較
                var planIdStr = String(planId);
                
                if (planIdStr === '1' || planIdStr === '2') {
                    // 個人、團體方案：所有宗教都可以填寫往生日期（非必填）
                    $('#death_date_field').show(300);
                    $('#death_date').prop('required', false); // 非必填
                    console.log('個人/團體方案 (ID:', planIdStr, ')：顯示往生日期（所有宗教都可填寫，非必填）');
                } else if (planIdStr === '3') {
                    // 浪浪方案：永遠不顯示往生日期
                    $('#death_date_field').hide(300);
                    $('#death_date').val('').prop('required', false);
                    console.log('浪浪方案 (ID:', planIdStr, ')：不顯示往生日期');
                } else {
                    // 其他方案：不顯示往生日期（其實宗教也不會顯示）
                    $('#death_date_field').hide(300);
                    $('#death_date').val('').prop('required', false);
                    console.log('其他方案 (ID:', planIdStr, ')：不顯示往生日期');
                }
            }
        });

        // 往生日期變更事件
        $("#death_date").on('change', function() {
            var deathDate = $(this).val();
            var religion = $('#religion').val();
            var planId = $('#plan_id').val();
            var typeList = $('#type_list').val();
            var payId = $('#pay_id').val();
            
            console.log('往生日期變更:', deathDate, '宗教:', religion, '方案:', planId);
            
            // 驗證往生日期是否合理（不能是未來日期）
            if (deathDate && new Date(deathDate) > new Date()) {
                alert('往生日期不能是未來日期，請重新選擇');
                $(this).val('');
                return;
            }
            
            // 只有個人、團體方案且佛道教相關宗教才計算重要日期
            var planIdStr = String(planId);
            
            if (deathDate && typeList === 'dispatch' && (payId === 'A' || payId === 'C') && 
                (planIdStr === '1' || planIdStr === '2') && 
                (religion === 'buddhism' || religion === 'taoism' || religion === 'buddhism_taoism')) {
                console.log('個人/團體方案 (ID:', planIdStr, ') + 佛道教：往生日期已設定，可計算重要日期');
            } else if (deathDate && typeList === 'dispatch' && (payId === 'A' || payId === 'C') && 
                (planIdStr === '1' || planIdStr === '2')) {
                console.log('個人/團體方案 (ID:', planIdStr, ') + 非佛道教：往生日期已設定，但不計算重要日期');
            }
        });

        // 處理方案價格欄位的顯示邏輯
        function handlePlanPriceField(planId, payId) {
            console.log('處理方案價格欄位:', { planId, payId });
            
            // 浪浪方案 (plan_id == 4) 且支付類別為 A 或 C 時，隱藏 plan_price 欄位
            // 或者支付類別為 D（尾款）或 E（追加）時，隱藏 plan_price 欄位
            if ((planId === '4' && (payId === 'A' || payId === 'C')) || payId === 'D' || payId === 'E') {
                var reason = '';
                if (planId === '4') reason = '浪浪方案 + 一次付清/訂金';
                else if (payId === 'D') reason = '尾款';
                else if (payId === 'E') reason = '追加';
                console.log('隱藏方案價格欄位 - 原因:', reason);
                $('#plan_price').closest('.not_final_show.not_memorial_show').hide(300);
                $('#plan_price').val('').prop('required', false);
            } else {
                console.log('其他方案或支付類別：顯示方案價格欄位');
                $('#plan_price').closest('.not_final_show.not_memorial_show').show(300);
                $('#plan_price').prop('required', true);
            }
        }

        // 初始化宗教和往生日期欄位的顯示狀態
        function initializeReligionAndDeathDateFields() {
            var planId = $('#plan_id').val();
            var typeList = $('#type_list').val();
            var religion = $('#religion').val();
            var payId = $('#pay_id').val();
            
            console.log('初始化宗教和往生日期欄位:', { planId, typeList, religion, payId });
            
            // 根據方案選擇控制宗教和往生日期欄位顯示
            if (typeList === 'dispatch' && (payId === 'A' || payId === 'C')) {
                // 將 planId 轉換為字串進行比較
                var planIdStr = String(planId);
                
                if (planIdStr === '1' || planIdStr === '2') {
                    // 個人、團體方案：顯示宗教和往生日期
                    $('#religion_field').show(300);
                    $('#religion').prop('required', true); // 設為必填
                    $('#death_date_field').show(300);
                    console.log('個人/團體方案 (ID:', planIdStr, ')：顯示宗教和往生日期');
                    
                    // 如果已有宗教選擇，檢查是否應該顯示往生日期
                    if (religion && (religion === 'buddhism' || religion === 'taoism' || religion === 'buddhism_taoism')) {
                        $('#death_date').prop('required', false); // 改為非必填
                    } else {
                        $('#death_date').prop('required', false);
                    }
                } else if (planIdStr === '3') {
                    // 浪浪方案：只顯示宗教，不顯示往生日期
                    $('#religion_field').show(300);
                    $('#religion').prop('required', true); // 設為必填
                    $('#death_date_field').hide(300);
                    $('#death_date').val('').prop('required', false);
                    console.log('浪浪方案 (ID:', planIdStr, ')：只顯示宗教，不顯示往生日期');
                } else {
                    // 其他方案：不顯示宗教和往生日期
                    $('#religion_field').hide(300);
                    $('#religion').prop('required', false);
                    $('#death_date_field').hide(300);
                    console.log('其他方案 (ID:', planIdStr, ')：不顯示宗教和往生日期');
                }
            } else {
                // 非派件單或非一次付清/訂金，隱藏所有宗教相關欄位
                $('#religion_field').hide(300);
                $('#religion').prop('required', false);
                $('#death_date_field').hide(300);
            }
            
            // 檢查是否顯示宗教提醒
            if (religion && religion !== 'buddhism_taoism') {
                $('#religion_reminder').show(300);
            } else {
                $('#religion_reminder').hide(300);
            }
            
            // 如果當前選擇的是「其他」，顯示輸入框
            if (religion === 'other') {
                $('#religion_other_input').show(300);
                $('#religion_other').prop('required', true);
            } else {
                $('#religion_other_input').hide(300);
                $('#religion_other').prop('required', false);
            }
        }


        function chgItems(obj) {
            $("#row_id").val($("#" + obj.id).attr('alt'));
            row_id = $("#row_id").val();
            $.ajax({
                url: '{{ route('prom.search') }}',
                data: {
                    'select_prom': $("#select_prom_" + row_id).val()
                },
                success: function(data) {
                    $("#prom_" + row_id).html(data);
                    $("#prom_total_" + row_id).on('input', function() {
                        calculate_price();
                    });
                }
            });
        }

        // 監聽後續處理名稱選擇，控制備註欄位和商品欄位顯示
        $(document).on('change', 'select[name="prom[]"]', function() {
            var row_id = $(this).closest('tr').find('select[name="select_proms[]"]').attr('alt');
            var prom_id = $(this).val();
            var extra_text_col = $("#prom_extra_text_col_" + row_id);
            var prom_product_container = $("#prom_product_" + row_id);
            
            if (prom_id == '8' || prom_id == '7') {
                // 套組法會：顯示備註，隱藏商品欄位
                extra_text_col.show();
                prom_product_container.hide();
                    } else {
                // 其他項目：隱藏備註，檢查是否需要顯示商品欄位
                extra_text_col.hide();
                
                // 檢查是否有商品資料或is_custom_product為1
                $.ajax({
                    url: '{{ route('product.prom_product_search') }}',
                    data: { 'prom_id': prom_id },
                    dataType: 'json',
                    success: function(data) {
                        console.log('prom_product_search data:', data);
                        
                        var shouldShow = (data.products && data.products.length > 0) || data.is_custom_product == 1;
                        
                        if (shouldShow) {
                                if (data.is_custom_product == 1) {
                                    // 自訂商品，顯示自訂商品相關欄位
                                    $('#souvenir_type_col_' + row_id).show();
                                    $('#product_name_col_' + row_id).show();
                                    $('#product_prom_col_' + row_id).hide();
                                    $('#variant_select_' + row_id).hide();
                                    $('#product_num_col_' + row_id).show();
                                    $('#product_comment_col_' + row_id).show();
            } else {
                                    // 有商品資料，顯示商品選擇相關欄位
                                    $('#souvenir_type_col_' + row_id).hide();
                                    $('#product_name_col_' + row_id).hide();
                                    $('#product_prom_col_' + row_id).show();
                                    $('#variant_select_' + row_id).show();
                                    $('#product_num_col_' + row_id).show();
                                    $('#product_comment_col_' + row_id).show();
                                    
                                    // 填入商品下拉
                                    var html = '<select id="product_prom_' + row_id + '" class="form-select" name="product_proms[]" onchange="checkProductVariants(' + row_id + ')">';
                                    html += '<option value="">請選擇</option>';
                                    data.products.forEach(function(item) {
                                        var hasVariants = (item.variants && item.variants.length > 0) ? '1' : '0';
                                        html += '<option value="' + item.id + '" data-has-variants="' + hasVariants + '">' + item.name + ' (' + item.price + ')</option>';
                                    });
                                    html += '</select>';
                                    
                                    $('#product_prom_col_' + row_id).html(html);
                                    
                                    // 儲存商品資料供細項選擇使用
                                    window.productData = window.productData || {};
                                    window.productData[row_id] = data.products;
                                    window.productData[row_id].is_custom_product = data.is_custom_product;
                                    
                                    // 如果有現有的商品選擇，設定選中狀態並觸發細項檢查
                                    // 從 prom_product_container 讀取 data-existing-product-id
                                    var existingProductId = prom_product_container.data('existing-product-id');
                                    console.log('Row ' + row_id + ' - Existing product ID from container:', existingProductId);
                                    
                                    if (existingProductId && existingProductId !== '') {
                                        $('#product_prom_' + row_id).val(existingProductId);
                                        console.log('Row ' + row_id + ' - Set product value to:', existingProductId);
                                        // 延遲執行細項檢查，確保商品資料已載入
                                        setTimeout(function() {
                                            checkProductVariants(row_id);
                                        }, 100);
                                    }
                                }
                                
                                // 顯示整個prom_product區塊
                                prom_product_container.show(300);
            } else {
                                // 沒有商品資料且is_custom_product不為1，隱藏區塊
                                prom_product_container.hide(300);
                            }
                    },
                    error: function(xhr, status, error) {
                        console.error('prom_product_search error:', error);
                        // 發生錯誤時隱藏區塊
                        prom_product_container.hide(300);
                    }
                });
            }
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
                data: {
                        'select_prom': promType
                    },
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

        function chgPapers(obj) {
            var row_id = $(obj).attr('alt'); // 取得行 ID

            $.ajax({
                url: '{{ route('gdpaper.search') }}', // AJAX 查詢
                data: {
                    'gdpaper_id': $("#gdpaper_id_" + row_id).val()
                },
                success: function(data) {
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
                data: {
                    'gdpaper_id': $("#gdpaper_id_" + row_id).val()
                },
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
            var rowCount = $('table.gdpaper-list tr').length - 1;
            var newRow = $("<tr>");
            var cols = '';
            cols +=
                '<td class="text-center"><button type="button" class="ibtnDel_gdpaper demo-delete-row btn btn-danger btn-sm btn-icon"><i class="fa fa-times"></i></button></td>';
            cols += '<td>';
            cols += '<select id="gdpaper_id_' + rowCount + '" alt="' + rowCount +
                '" class="mobile form-select" name="gdpaper_ids[]" onchange="chgNums(this)" onclick="chgNums(this)" onkeydown="chgNums(this)">';
            cols += '<option value="" selected>請選擇...</option>';
            @foreach ($products as $product)
                cols +=
                    '<option value="{{ $product->id }}">{{ $product->name }}({{ $product->price }})</option>';
            @endforeach
            cols += '</select>';
            cols += '</td>';
            cols += '<td>';
            cols += '<input type="number" class="mobile form-control"  min="0"  id="gdpaper_num_' + rowCount +
                '" name="gdpaper_num[]" value="">';
            cols += '</td>';
            cols += '<td>';
            cols += '<input type="text" class="mobile form-control total_number" id="gdpaper_total_' + rowCount +
                '" name="gdpaper_total[]">';
            cols += '</td>';
            cols += '</tr>';
            newRow.append(cols);
            $("table.gdpaper-list tbody").append(newRow);
        });


        $("table.souvenir-list tbody").on("click", ".ibtnAdd_souvenir", function() {
            var rowCount = $('table.souvenir-list tr').length - 1;
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
            cols += '<input type="number" class="mobile form-control total_number" id="souvenir_total_' + rowCount +
                '" name="souvenir_totals[]" value="">';
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
                    data: {
                        'souvenir_id': souvenirId
                    },
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
            var payId = $('select[name="pay_id"]').val();
            
            // 計算所有 total_number 欄位的總和
            $(".total_number").each(function() {
                var value = parseFloat($(this).val());
                if (!isNaN(value)) {
                    total += value;
                }
            });
            
            // 直接顯示總金額，不再扣除訂金
            $("#total").val(total);
            $("#total_text").html(total);
        }
        


        $("#cust_name_q").keydown(function() {
            $value = $(this).val();
            $.ajax({
                type: 'get',
                url: '{{ route('customer.search') }}',
                data: {
                    'cust_name': $value
                },
                success: function(data) {
                    $('#cust_name_list_q').html(data);
                }
            });
        });

        $(".source_company_name").keydown(function() {
            $value = $(this).val();
            $.ajax({
                type: 'get',
                url: '{{ route('company.search') }}',
                data: {
                    'cust_name': $value
                },
                success: function(data) {
                    $('#source_company_name_list_q').html(data);
                }
            });
        });

        $(".ibtnAdd_prom").click(function() {
            $rowCount = $('table.prom-list tr').length - 1;
            var newRow = $("<tr>");
            var cols = '';
            cols +=
                '<td class="text-center"><button type="button" class="ibtnDel_prom demo-delete-row btn btn-danger btn-sm btn-icon"><i class="fa fa-times"></i></button></td>';
            cols += '<td>';
            cols += '<select id="select_prom_' + $rowCount + '" alt="' + $rowCount +
                '" class="mobile form-select" name="select_proms[]" onchange="chgItems(this)">';
            cols += '<option value="" selected>請選擇...</option>';
            cols += '<option value="A">安葬處理</option>';
            cols += '<option value="B">後續處理</option>';
            cols += '<option value="C">其他處理</option>';
            cols += '</select>';
            cols += '</td>';
            cols += '<td>';
            cols += '<select id="prom_' + $rowCount + '" class="mobile form-select" name="prom[]">';
            cols += '<option value="">請選擇...</option>';
            cols += '</select>';
            cols += '<!-- 套組法會額外備註 (prom value=8) - 移到 prom_product 容器外 -->';
            cols += '<div class="row mt-2" id="prom_extra_text_col_' + $rowCount + '" style="display:none;">';
            cols += '<div class="col-12">';
            cols += '<input class="form-control" type="text" id="prom_extra_text_' + $rowCount + '" name="prom_extra_text[]" placeholder="套組法會備註">';
            cols += '</div>';
            cols += '</div>';
            cols += '<!-- 商品相關欄位 - 根據 prom_id 決定顯示 -->';
            cols += '<div class="row mt-1 prom-product-container" id="prom_product_' + $rowCount + '" style="display: none;">';
            cols += '<!-- 紀念品類型選擇 -->';
            cols += '<div class="col-3" id="souvenir_type_col_' + $rowCount + '" style="display:none;">';
            cols += '<select id="product_souvenir_type_' + $rowCount + '" class="form-select" name="product_souvenir_types[]">';
            cols += '<option value="">請選擇</option>';
            @foreach ($souvenir_types as $souvenir_type)
            cols += '<option value="{{ $souvenir_type->id }}">{{ $souvenir_type->name }}</option>';
            @endforeach
            cols += '</select>';
            cols += '</div>';
            cols += '<!-- 商品名稱輸入 -->';
            cols += '<div class="col-3" id="product_name_col_' + $rowCount + '" style="display:none;">';
            cols += '<input type="text" id="product_name_' + $rowCount + '" class="form-control" name="product_name[]" placeholder="請輸入商品名稱">';
            cols += '</div>';
            cols += '<!-- 商品選擇下拉 -->';
            cols += '<div class="col-3" id="product_prom_col_' + $rowCount + '">';
            cols += '<select id="product_prom_' + $rowCount + '" class="form-select" name="product_proms[]" onchange="checkProductVariants(' + $rowCount + ')">';
            cols += '<option value="">請選擇</option>';
            cols += '</select>';
            cols += '</div>';
            cols += '<!-- 細項選擇 -->';
            cols += '<div class="col-3" id="variant_select_' + $rowCount + '">';
            cols += '<select id="product_variant_' + $rowCount + '" class="form-select" name="product_variants[]">';
            cols += '<option value="">無</option>';
            cols += '</select>';
            cols += '</div>';
            cols += '<!-- 數量 -->';
            cols += '<div class="col-3" id="product_num_col_' + $rowCount + '">';
            cols += '<input class="form-control" type="number" id="product_num_' + $rowCount + '" name="product_num[]" value="1" min="1">';
            cols += '</div>';
            cols += '<!-- 備註 -->';
            cols += '<div class="col-3" id="product_comment_col_' + $rowCount + '">';
            cols += '<input class="form-control" type="text" id="product_comment_' + $rowCount + '" name="product_comment[]" placeholder="備註">';
            cols += '</div>';
            cols += '</div>';
            cols += '</td>';
            cols += '<td>';
            cols += '<input type="text" class="mobile form-control total_number" id="prom_total_' + $rowCount +
                '" name="prom_total[]">';
            cols += '</td>';
            cols += '</tr>';
            newRow.append(cols);
            $("table.prom-list tbody").append(newRow);
        });
        $.ajaxSetup({
            headers: {
                'csrftoken': '{{ csrf_token() }}'
            }
        });

        // 單號重複檢查（編輯模式）
        let saleOnCheckTimer;
        let isSaleOnValid = true; // 追蹤單號是否有效

        // 頁面載入時移除單號的 "No." 前綴
        $(document).ready(function() {
            let saleOnValue = $('#sale_on').val();
            if (saleOnValue && saleOnValue.startsWith('No.')) {
                $('#sale_on').val(saleOnValue.replace('No.', ''));
            }
        });
        
        // 單號輸入驗證 - 只允許數字
        $('#sale_on').on('input', function() {
            let inputValue = $(this).val();
            const feedback = $('#sale_on_feedback');
            
            // 過濾掉非數字字符，只保留數字
            const filteredValue = inputValue.replace(/[^0-9]/g, '');
            
            // 如果過濾後的值與原值不同，更新輸入框
            if (filteredValue !== inputValue) {
                $(this).val(filteredValue);
                inputValue = filteredValue;
            }
            
            const saleOn = inputValue.trim();
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
                            feedback.html('<small class="text-danger">⚠️ ' + response.message +
                                '</small>').addClass('text-danger');
                            isSaleOnValid = false;
                        } else {
                            feedback.html('<small class="text-success">✓ ' + response.message +
                                '</small>').addClass('text-success');
                            isSaleOnValid = true;
                        }
                    },
                    error: function(xhr, status, error) {
                        feedback.html('<small class="text-danger">檢查單號時發生錯誤</small>').addClass(
                            'text-danger');
                        isSaleOnValid = false;
                        console.error('單號檢查錯誤:', error);
                    }
                });
            }, 500);
        });

        // 防止輸入非數字字符
        $('#sale_on').on('keydown', function(e) {
            // 允許：數字鍵、退格鍵、刪除鍵、方向鍵、Tab鍵、Enter鍵
            const allowedKeys = [8, 9, 13, 37, 38, 39, 40, 46]; // backspace, tab, enter, arrows, delete
            const isNumber = (e.keyCode >= 48 && e.keyCode <= 57) || (e.keyCode >= 96 && e.keyCode <= 105); // 數字鍵和數字鍵盤
            
            if (!allowedKeys.includes(e.keyCode) && !isNumber) {
                e.preventDefault();
                return false;
            }
        });

        // 防止貼上非數字內容
        $('#sale_on').on('paste', function(e) {
            e.preventDefault();
            
            // 獲取剪貼簿內容
            const clipboardData = e.originalEvent.clipboardData || window.clipboardData;
            const pastedData = clipboardData.getData('Text');
            
            // 過濾出數字
            const filteredData = pastedData.replace(/[^0-9]/g, '');
            
            // 如果過濾後有內容，插入到當前位置
            if (filteredData) {
                const currentValue = $(this).val();
                const cursorPos = this.selectionStart;
                const newValue = currentValue.slice(0, cursorPos) + filteredData + currentValue.slice(cursorPos);
                $(this).val(newValue);
                
                // 設置游標位置
                this.setSelectionRange(cursorPos + filteredData.length, cursorPos + filteredData.length);
                
                // 觸發 input 事件以進行驗證
                $(this).trigger('input');
            }
        });

        // 初始化商品細項必填驗證
        function initializeProductVariantValidation() {
            // 檢查所有現有的商品選擇，設定適當的必填驗證
            $('select[name="product_proms[]"]').each(function() {
                var idx = $(this).attr('id').replace('product_prom_', '');
                var selectedProductId = $(this).val();
                var variantSelect = $('#product_variant_' + idx);
                
                if (selectedProductId && selectedProductId !== '') {
                    // 如果有選擇商品，檢查細項狀態
                    checkProductVariants(idx);
                }
            });
        }

        // 檢查商品細項並更新細項選擇下拉選單
        function checkProductVariants(idx) {
            var selectedProductId = $('#product_prom_' + idx).val();
            var variantSelectDiv = $('#variant_select_' + idx);
            var variantSelect = $('#product_variant_' + idx);
            
            // 檢查是否為自訂商品模式
            var isCustomProduct = window.productData && window.productData[idx] && window.productData[idx].is_custom_product == 1;
            
            if (isCustomProduct) {
                // 自訂商品模式，隱藏細項選擇
                variantSelectDiv.hide();
                // 自訂商品不需要細項，移除必填驗證
                variantSelect.prop('required', false);
                return;
            }
            
            // 重置細項選擇為預設狀態
            var defaultVariantHtml = '<option value="">無</option>';
            variantSelect.html(defaultVariantHtml);
            
            if (!selectedProductId) {
                // 沒有選擇商品，保持預設狀態，移除必填驗證
                variantSelect.prop('required', false);
                return;
            }
            
            // 檢查選擇的商品是否有細項
            var selectedOption = $('#product_prom_' + idx + ' option:selected');
            var hasVariants = selectedOption.attr('data-has-variants');
            
            if (hasVariants === '1' && window.productData && window.productData[idx]) {
                // 找到對應的商品資料
                var product = window.productData[idx].find(function(p) {
                    return p.id == selectedProductId;
                });
                
                if (product && product.variants && product.variants.length > 0) {
                    // 有細項，更新細項選擇下拉選單
                    var variantHtml = '<option value="">請選擇細項</option>';
                    product.variants.forEach(function(variant) {
                        var variantDisplayName = variant.variant_name;
                        if (variant.color) {
                            variantDisplayName += ' (' + variant.color + ')';
                        }
                        var variantPrice = variant.price || product.price;
                        variantHtml += '<option value="' + variant.id + '">' + variantDisplayName + ' (' + variantPrice + ')</option>';
                    });
                    
                    variantSelect.html(variantHtml);
                    
                    // 有細項的商品，細項選擇為必填
                    variantSelect.prop('required', true);
                    console.log('Row ' + idx + ' - 商品有細項，細項選擇設為必填');
                    
                    // 如果有現有的細項選擇，設定選中狀態
                    // 從 prom_product_container 讀取 data-existing-variant-id
                    var promProductContainer = $("#prom_product_" + idx);
                    var existingVariantId = promProductContainer.data('existing-variant-id');
                    console.log('Row ' + idx + ' - Existing variant ID from container:', existingVariantId);
                    if (existingVariantId) {
                        $('#product_variant_' + idx).val(existingVariantId);
                        console.log('Row ' + idx + ' - Set variant value to:', existingVariantId);
                    }
                } else {
                    // 沒有細項，移除必填驗證
                    variantSelect.prop('required', false);
                    console.log('Row ' + idx + ' - 商品沒有細項，移除必填驗證');
                }
            } else {
                // 沒有細項的商品，移除必填驗證
                variantSelect.prop('required', false);
                console.log('Row ' + idx + ' - 商品沒有細項，移除必填驗證');
            }
        }



        // 表單提交檢查
        $('#your-form').on('submit', function(e) {
            if (!isSaleOnValid) {
                e.preventDefault();
                alert('單號有重複，請檢查後再提交');
                return false;
            }
            
            // 檢查宗教信仰必填
            if ($('#religion_field').is(':visible')) {
                var religion = $('#religion').val();
                if (!religion || religion === '') {
                    e.preventDefault();
                    alert('請選擇宗教信仰');
                    $('#religion').focus();
                    return false;
                }
                
                // 如果選擇「其他」，檢查是否填寫了其他宗教信仰
                if (religion === 'other') {
                    var religionOther = $('#religion_other').val().trim();
                    if (!religionOther) {
                        e.preventDefault();
                        alert('請輸入其他宗教信仰');
                        $('#religion_other').focus();
                        return false;
                    }
                }
            }
            
            // 檢查商品細項選擇
            var hasVariantError = false;
            $('select[name="product_proms[]"]').each(function() {
                var idx = $(this).attr('id').replace('product_prom_', '');
                var selectedProductId = $(this).val();
                var variantSelect = $('#product_variant_' + idx);
                
                // 檢查是否選擇了商品
                if (selectedProductId && selectedProductId !== '') {
                    // 檢查是否為自訂商品
                    var isCustomProduct = window.productData && window.productData[idx] && window.productData[idx].is_custom_product == 1;
                    
                    if (!isCustomProduct) {
                        // 檢查商品是否有細項
                        var selectedOption = $(this).find('option:selected');
                        var hasVariants = selectedOption.attr('data-has-variants');
                        
                        if (hasVariants === '1') {
                            // 有細項的商品，檢查是否選擇了細項
                            var selectedVariant = variantSelect.val();
                            if (!selectedVariant || selectedVariant === '') {
                                hasVariantError = true;
                                alert('商品「' + selectedOption.text() + '」有細項，請選擇細項');
                                variantSelect.focus();
                                return false;
                            }
                        }
                    }
                }
            });
            
            if (hasVariantError) {
                e.preventDefault();
                return false;
            }
        });
    </script>
@endsection
