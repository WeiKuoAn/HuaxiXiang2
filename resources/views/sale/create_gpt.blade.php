@extends('layouts.vertical', ['page_title' => '新增業務Key單'])

@section('css')
    <link href="{{ asset('assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/css/customization.css') }}" id="app-style" rel="stylesheet" type="text/css" />
    {{-- <meta name="csrf-token" content="{{ csrf_token() }}"> --}}
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

        /* 重要日期區塊樣式 */
        #memorial_dates_section .card {
            transition: all 0.3s ease;
            min-height: 80px;
        }
        
        #memorial_dates_section .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        #memorial_dates_section .card-title {
            font-weight: bold;
            font-size: 0.9rem;
        }
        
        #memorial_dates_section .card-text {
            font-size: 0.85rem;
            margin: 0;
            white-space: nowrap;
        }
        
        .opacity-50 {
            opacity: 0.5;
        }
        
        .bg-warning {
            background-color: #ffc107 !important;
        }

        /* 手機版重要日期優化 */
        @media screen and (max-width: 768px) {
            #memorial_dates_section .card {
                min-height: 70px;
            }
            
            #memorial_dates_section .card-title {
                font-size: 0.8rem;
                margin-bottom: 0.3rem !important;
            }
            
            #memorial_dates_section .card-text {
                font-size: 0.75rem;
                line-height: 1.2;
            }
            
            #memorial_dates_section .card-body {
                padding: 0.5rem !important;
            }
            
            #memorial_dates_section h6 {
                font-size: 0.9rem;
                padding: 0.5rem !important;
                margin-bottom: 0.5rem !important;
            }
        }
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

        <form action="{{ route('sale.data.create') }}" method="POST" id="your-form" enctype="multipart/form-data"
            data-plugin="dropzone" data-previews-container="#file-previews"
            data-upload-preview-template="#uploadPreviewTemplate">
            @csrf
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase bg-light  p-2 mt-0 mb-3">基本資訊</h5>
                            <div class="alert alert-info alert-dismissible fade show p-2" id="payment_type_hint" 
                                style="display: none;" role="alert">
                                <i class="fa fa-info-circle me-2"></i>
                                <span id="payment_type_hint_text">請選擇支付類別以顯示相關欄位</span>
                            </div>
                            <div class="alert alert-danger alert-dismissible fade show p-2" id="final_price_display"
                                role="alert"></div>
                            <div class="row">
                                <div class="mb-3 col-md-4">
                                    <label for="type_list" class="form-label">案件類別選擇<span
                                            class="text-danger">*</span></label>
                                    <select id="type_list" class="form-select" name="type_list">
                                        <option value="dispatch">派件單</option>
                                        <option value="memorial">追思單</option>
                                    </select>
                                </div>
                                <div class="mb-3 col-md-4">
                                    <label for="pay_id" class="form-label">支付類別<span class="text-danger">*</span></label>
                                    <select class="form-select" name="pay_id" id="pay_id" required>
                                        <option value="" selected>請選擇</option>
                                        <option value="A">一次付清</option>
                                        <option value="C">訂金</option>
                                        <option value="E">追加</option>
                                        <option value="D">尾款</option>
                                    </select>
                                </div>
                                <div class="mb-3 col-md-4">
                                    <label for="sale_on" class="form-label">單號<span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">No.</span>
                                        <input type="text" class="form-control" id="sale_on" name="sale_on" required placeholder="請輸入數字" maxlength="10" inputmode="numeric">
                                    </div>
                                    <div id="sale_on_feedback" class="mt-1"></div>
                                </div>
                                <div class="mb-3 col-md-4">
                                    <label for="sale_date" class="form-label">日期<span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="sale_date" name="sale_date" required value="{{ date('Y-m-d') }}">
                                </div>
                                <div class="mb-3 col-md-4">
                                    <label for="customer_id" class="form-label">客戶名稱<span
                                            class="text-danger required">*</span></label>
                                    <select class="form-control" data-toggle="select2" data-width="100%" name="cust_name_q"
                                        id="cust_name_q" required>
                                        <option value="">請選擇...</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}">No.{{ $customer->id }}
                                                {{ $customer->name }}（{{ $customer->mobile }}）</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3 col-md-4">
                                    <label for="pet_name" class="form-label">寵物名稱<span
                                            class="text-danger required">*</span></label>
                                    <input type="text" class="form-control" id="pet_name" name="pet_name">
                                </div>
                                <div class="mb-3 col-md-4 not_final_show not_memorial_show">
                                    <label for="variety" class="form-label">寵物品種<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="variety" name="variety">
                                </div>
                                <div class="mb-3 col-md-4 not_final_show not_memorial_show">
                                    <label for="kg" class="form-label">公斤數<span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="kg" name="kg" step="0.001" min="0">
                                </div>
                                <div class="mb-3 col-md-4 not_final_show not_memorial_show">
                                    <label for="type" class="form-label">案件來源<span
                                            class="text-danger">*</span></label>
                                    <select id="type" class="form-select" name="type">
                                        <option value="">請選擇...</option>
                                        @foreach ($sources as $source)
                                            <option value="{{ $source->code }}">{{ $source->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3 col-md-4" id="source_company">
                                    <label for="source_company_name_q" class="form-label">來源公司名稱<span
                                            class="text-danger">*</span></label>
                                    <select class="form-control" data-toggle="select2" data-width="100%"
                                        name="source_company_name_q" id="source_company_name_q">
                                        <option value="">請選擇...</option>
                                    </select>
                                </div>
                                <div class="mb-3 col-md-4  not_memorial_show plan">
                                    <label for="plan_id" class="form-label">方案選擇<span
                                            class="text-danger">*</span></label>
                                    <select id="plan_id" class="form-select" name="plan_id">
                                        <option value="">請選擇...</option>
                                        @foreach ($plans as $plan)
                                            <option value="{{ $plan->id }}">{{ $plan->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3 col-md-4 not_final_show not_memorial_show plan_price">
                                    <label for="plan_price" class="form-label">方案價格<span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control total_number" id="plan_price"
                                        name="plan_price" min="0">
                                </div>
                                {{-- <div class="mb-3 col-md-4" id="final_price">
                                    <label for="final_price" class="form-label" id="final_price_label">方案追加/收款金額<span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control total_number" id="final_price_input" name="final_price" min="0">
                                </div> --}}
                                <div class="mb-3 col-md-4" id="suit_field" style="display: none;">
                                    <label for="suit_id" class="form-label">套裝選擇<span class="text-danger">*</span></label>
                                    <select id="suit_id" class="form-select" name="suit_id">
                                        <option value="">請選擇...</option>
                                        @foreach ($suits as $suit)
                                            <option value="{{ $suit->id }}">{{ $suit->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3 col-md-4" id="religion_field" style="display: none;">
                                    <label for="religion" class="form-label">宗教信仰<span class="text-danger">*</span></label>
                                    <select id="religion" class="form-select" name="religion">
                                        <option value="">請選擇...</option>
                                        <option value="buddhism_taoism">佛道教</option>
                                        <option value="christianity">基督教</option>
                                        <option value="catholicism">天主教</option>
                                        <option value="none">無宗教</option>
                                        <option value="other">其他</option>
                                    </select>
                                    <div id="religion_other_input" class="mt-2" style="display: none;">
                                        <input type="text" class="form-control" id="religion_other" name="religion_other" placeholder="請輸入其他宗教信仰">
                                    </div>
                                    <div id="religion_reminder" class="mt-1" style="display: none;">
                                        <small class="text-danger">提醒：資財袋為佛道教用品</small>
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4" id="death_date_field" style="display: none;">
                                    <label for="death_date" class="form-label">往生日期</label>
                                    <input type="date" class="form-control" id="death_date" name="death_date">
                                </div>
                                <div class="mb-3 col-md-4">
                                    <label for="user_id" class="form-label">服務專員<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="user_id" name="user_id" readonly
                                        value="{{ Auth::user()->name }}">
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

                            </div>
                            

                            
                                <div class="row">
                                    <div class="mb-1 mt-1">
                                        <div class="form-check" id="send_div">
                                            <input type="checkbox" class="form-check-input" id="send"
                                                name="send">
                                            <label class="form-check-label" for="send"><b>親送</b></label>
                                        </div>
                                    </div>
                                    <div class="mb-1 mt-1" id="connector_div">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="connector_address"
                                                name="connector_address">
                                            <label class="form-check-label"
                                                for="connector_address"><b>接體地址不為客戶地址</b></label>
                                        </div>
                                        <div class="mt-2 row" id="connector_address_div">
                                            <div class="col-md-4 mb-3">
                                                <label for="plan_price" class="form-label">接體縣市<span
                                                        class="text-danger">*</span></label>
                                                <div class="twzipcode mb-2">
                                                    <select data-role="county" required></select>
                                                    <select data-role="district" required></select>
                                                    <select data-role="zipcode" required></select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label" for="AddNew-Phone">接體地址<span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="address"
                                                    name="address">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-1 mt-1" id="connector_hospital_div">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input"
                                                id="connector_hospital_address" name="connector_hospital_address">
                                            <label class="form-check-label"
                                                for="connector_hospital_address"><b>接體地址為醫院</b></label>
                                        </div>
                                        <div class="mt-1 row" id="connector_hospital_address_div">
                                            <div class="col-md-4">
                                                <label for="source_company_id" class="form-label">接體地址<span
                                                        class="text-danger">*</span></label>
                                                <select class="form-control" data-toggle="select2" data-width="100%"
                                                    name="hospital_address" id="hospital_address">
                                                    <option value="">請選擇...</option>
                                                    @foreach ($hospitals as $hospital)
                                                        <option value="{{ $hospital->id }}">
                                                            （{{ $hospital->group->name }}）{{ $hospital->name }}（{{ $hospital->mobile }}）
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-1 mt-1">
                                            <div class="form-check" id="cooperation_price_div">
                                                <input type="checkbox" class="form-check-input" id="cooperation_price"
                                                    name="cooperation_price">
                                                <label class="form-check-label" for="cooperation_price"><b>院內價</b></label>
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
                                                                    <input class="form-control" type="text" id="prom_extra_text_{{$i}}" name="prom_extra_text[]" placeholder="備註">
                                                                </div>
                                                            </div>
                                                            <div class="row mt-1 prom-product-container" id="prom_product_{{ $i }}">
                                                                <div class="col-3 mobile" id="souvenir_type_col_{{ $i }}" style="display:none;">
                                                                    <select id="product_souvenir_type_{{ $i }}"
                                                                        class="form-select" name="product_souvenir_types[]">
                                                                        <option value="">請選擇</option>
                                                                        @foreach ($souvenir_types as $souvenir_type)
                                                                            <option value="{{ $souvenir_type->id }}">{{ $souvenir_type->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-3 mobile" id="product_name_col_{{ $i }}" style="display:none;">
                                                                    <input type="text" id="product_name_{{ $i }}" class="form-control" name="product_name[]" placeholder="請輸入商品名稱">
                                                                </div>
                                                                <div class="col-3 mobile" id="product_prom_col_{{ $i }}">
                                                                    <select id="product_prom_{{ $i }}"
                                                                        class="form-select" name="product_proms[]" onchange="checkProductVariants({{ $i }})">
                                                                        <option value="">請選擇</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-3 mobile" id="variant_select_{{ $i }}">
                                                                    <select id="product_variant_{{ $i }}" class="form-select" name="product_variants[]">
                                                                        <option value="">無</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-3 mobile" id="product_num_col_{{ $i }}">
                                                                    <input class="form-control" type="number" id="product_num_{{$i}}" name="product_num[]" value="1" min="1">
                                                                </div>
                                                                <div class="col-3 mobile" id="product_comment_col_{{ $i }}">
                                                                    <input class="form-control" type="text" id="product_comment_{{$i}}" name="product_comment[]" placeholder="備註">
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="mobile form-control total_number"
                                                                id="prom_total_{{ $i }}" name="prom_total[]">
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
                                                                class="mobile form-control" min="0"
                                                                id="gdpaper_num_{{ $i }}" name="gdpaper_num[]"
                                                                onchange="chgNums(this)" onclick="chgNums(this)"
                                                                onkeydown="chgNums(this)">
                                                        </td>
                                                        <td>
                                                            <input type="text" class="mobile form-control total_number"
                                                                id="gdpaper_total_{{ $i }}"
                                                                name="gdpaper_total[]" value="">
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
                                    <input type="hidden" class="form-control" id="total" name="total"
                                        value="0" readonly>
                                </div>
                                <div class="mb-3 col-md-4">
                                    <label for="pay_id" class="form-label">支付方式<span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" name="pay_method" required>
                                        <option value="" selected>請選擇</option>
                                        <option value="A">現金</option>
                                        <option value="B">匯款</option>
                                        <option value="C">現金與匯款</option>
                                    </select>
                                </div>
                                <div class="mb-3 col-md-4" id="cash_price_div">
                                    <label for="pay_price" class="form-label">現金收款<span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="cash_price" name="cash_price" min="0">
                                </div>
                                <div class="mb-3 col-md-4" id="transfer_price_div">
                                    <label for="pay_price" class="form-label">匯款收款<span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="transfer_price"
                                        name="transfer_price" min="0">
                                </div>
                                <div class="mb-3 col-md-4" id="transfer_channel_div">
                                    <label for="pay_id" class="form-label">匯款管道<span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" name="transfer_channel">
                                        <option value="" selected>請選擇</option>
                                        <option value="銀行轉帳">銀行轉帳</option>
                                        <option value="Line Pay">Line Pay</option>
                                        <option value="臨櫃匯款">臨櫃匯款</option>
                                    </select>
                                </div>
                                <div class="mb-3 col-md-4" id="transfer_number_div">
                                    <label for="pay_price" class="form-label">匯款後四碼</label>
                                    <input type="text" class="form-control" id="transfer_number"
                                        name="transfer_number">
                                </div>
                                <div class="mb-3 col-md-4" id="this_price_div">
                                    <label for="pay_price" class="form-label">本次收款<span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="pay_price" name="pay_price" min="0" required>
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
                        <button type="button" class="btn w-sm btn-light waves-effect"
                            onclick="history.go(-1)">回上一頁</button>
                        <button type="submit" class="btn w-sm btn-success waves-effect waves-light"
                            id="submit_btn">新增</button>
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
        // ===== 全域變數宣告 =====
        var type_list, payIdValue, payMethod, connector_address, send, connector_hospital_address, type, rowCount, row_id, gdpaper_num;
        var saleOnCheckTimer;
        var isSaleOnValid = true;

       $(document).ready(function() {
            // 一開始隱藏所有 prom_product
            $('[id^=prom_product_]').hide();
            
            // 頁面載入時初始化欄位顯示狀態
            initializeFormFields();
            
            // 初始化防呆機制
            initializeFieldValidation();
            
            // 初始化商品細項必填驗證
            initializeProductVariantValidation();
        });

        // 頁面載入時初始化表單欄位
        function initializeFormFields() {
            console.log('初始化表單欄位');
            
            
            // 預設隱藏來源公司欄位
            $("#source_company").hide();
            
            // 預設隱藏往生日期欄位
            $("#death_date_field").hide();
            
            // 設定預設的支付方式相關欄位
            $("#cash_price_div, #transfer_price_div, #transfer_channel_div, #transfer_number_div").hide();
            
            // 如果已經選擇了支付類別，應用對應的欄位控制
            var currentPayId = $('select[name="pay_id"]').val();
            var currentTypeList = $('select[name="type_list"]').val();
            
            if (currentPayId) {
                controlFieldsByPaymentType(currentPayId, currentTypeList);
            } else {
                // 預設顯示派件單的完整表單
                handleFullPayment('dispatch');
            }
            
            // 初始化宗教欄位顯示狀態
            validateReligionAndDeathDate();
            
            // 初始化宗教提醒狀態
            var currentReligion = $('#religion').val();
            if (currentReligion && currentReligion !== 'buddhism_taoism') {
                $('#religion_reminder').show();
            }
            
            // 初始化後續處理區塊顯示狀態
            showPromSectionsIfNeeded();
            
            // 初始化方案價格欄位顯示狀態
            var currentPlanId = $('#plan_id').val();
            var currentPayId = $('select[name="pay_id"]').val();
            if (currentPlanId && currentPayId) {
                handlePlanPriceField(currentPlanId, currentPayId);
            }
        }

        $(document).on('change', 'select[id^=prom_]', function() {
                var selectId = $(this).attr('id');
            var idx = selectId.replace('prom_', '');
                var promId = $(this).val();

                // 檢查是否為套組法會 (prom value=8)，顯示/隱藏額外備註欄位
                if (promId == '8' || promId == '7') {
                    $('#prom_extra_text_col_' + idx).show(300);
                } else {
                    $('#prom_extra_text_col_' + idx).hide(300);
                    $('#prom_extra_text_' + idx).val(''); // 清空內容
                }

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
                            // 有商品資料，四欄顯示，全部 col-3
                            $('#souvenir_type_col_' + idx).hide();
                            $('#product_name_col_' + idx).hide();
                            $('#product_prom_col_' + idx).show().removeClass('col-4').addClass('col-3');
                            $('#variant_select_' + idx).show().removeClass('col-4').addClass('col-3');
                            $('#product_num_col_' + idx).show().removeClass('col-4').addClass('col-3');
                            $('#product_comment_col_' + idx).show().removeClass('col-4').addClass('col-3');
                            
                            // 填入商品下拉，不包含細項選項
                            var html = '<select id="product_prom_' + idx + '" class="form-select" name="product_proms[]" onchange="checkProductVariants(' + idx + ')">';
                            html += '<option value="">請選擇</option>';
                            data.products.forEach(function(item) {
                                var hasVariants = (item.variants && item.variants.length > 0) ? '1' : '0';
                                html += '<option value="' + item.id + '" data-has-variants="' + hasVariants + '">' + item.name + ' (' + item.price + ')</option>';
                            });
                            html += '</select>';
                            
                            // 細項選擇下拉選單已經在初始 HTML 中，不需要重複新增
                            
                            $('#product_prom_col_' + idx).html(html);
                            
                            // 儲存商品資料供細項選擇使用
                            window.productData = window.productData || {};
                            window.productData[idx] = data.products;
                            window.productData[idx].is_custom_product = data.is_custom_product;
                        } else if (data.is_custom_product == 1) {
                            // 自訂商品，四欄顯示，全部 col-3
                            $('#souvenir_type_col_' + idx).show().removeClass('col-4').addClass('col-3');
                            $('#product_name_col_' + idx).show().removeClass('col-4').addClass('col-3');
                            $('#product_prom_col_' + idx).hide();
                            $('#variant_select_' + idx).hide(); // 隱藏細項選擇
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
                        // 注意：不要隱藏套組法會額外欄位，因為它已經移到外面了
                        // 套組法會額外欄位由 prom 選擇邏輯單獨控制，不受商品搜尋結果影響
                    }
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
                    // 注意：不要隱藏套組法會額外欄位，因為它已經移到外面了
                    // 套組法會額外欄位由 prom 選擇邏輯單獨控制，不受 AJAX 錯誤影響
                    }
            });
        });

        $(".twzipcode").twzipcode({
            css: ["twzipcode-select", "twzipcode-select", "twzipcode-select"], // 自訂 "城市"、"地區" class 名稱 
            countyName: "county", // 自訂城市 select 標籤的 name 值
            districtName: "district", // 自訂地區 select 標籤的 name 值
            zipcodeName: "zipcode", // 自訂地區 select 標籤的 name 值
        });

        //判斷尾款、訂金
        $("#final_price_display").hide();

        //查詢尾款的ajax
        function fetchFinalPriceData(callback) {
            const payId = $('#pay_id').val();
            const customerId = $('#cust_name_q').val();
            const petName = $('#pet_name').val();
            const typeList = $('#type_list').val();

            if (payId && customerId && petName) {
                $.ajax({
                    url: '{{ route('sales.final_price') }}',
                    type: 'GET',
                    data: {
                        pay_id: payId,
                        customer_id: customerId,
                        pet_name: petName,
                        type_list: typeList,
                    },
                    success: function(response) {
                        console.log('final_price response:', response);

                        if (typeof callback === 'function') {
                            callback(response);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX 錯誤:', error);
                    }
                });
            } else {
                console.log('payId, customerId 或 petName 未填寫');
            }
        }

        //
        $('#pay_id, #cust_name_q, #pet_name ,#plan_id').on('change keyup', function() {
            fetchFinalPriceData(function(response) {
                if (response.message === 'OK') {
                    $('#final_price_display').hide(300);
                    $('#submit_btn').prop('disabled', false);
                } else {
                    $('#final_price_display').show();
                    $('#final_price_display').text(response.message);
                    $('#submit_btn').prop('disabled', true);
                }

                // ✅ 將 response 傳給 toggleSuitField
                toggleSuitField(response);
            });
        });


        function toggleSuitField(response) {
            console.log('計算方案');

            const planId = $('#plan_id').val();
            const typeList = $('#type_list').val();
            const payId = $('#pay_id').val();
            console.log('planId:', planId);
            if (typeList === 'dispatch' && (payId === 'A' || payId === 'D' || payId === 'E')) {
                if(planId === '1' && (payId === 'A' || payId === 'E')){
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
        }





        //親送開始
        send = $('input[name="send"]').val();
        if (send == 1) {
            $("#connector_address_div").show();

        } else {
            $("#connector_address_div").hide();
        }
        $("#send").on("change", function() {
            if ($(this).is(':checked')) {
                $(this).val(1);
                $("#connector_div").hide(300);
                $("#connector_hospital_div").hide(300);
            } else {
                $(this).val(0);
                $("#connector_div").show(300);
                $("#connector_hospital_div").show(300);
            }
        });
        //親送結束

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

        //地址
        connector_address = $('input[name="connector_address"]').val();
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
        if (connector_address == 1) {
            $("#connector_address_div").show();
        } else {
            $("#connector_address_div").hide();
        }

        //醫院地址
        connector_hospital_address = $('input[name="connector_hospital_address"]').val();
        $("#connector_hospital_address").on("change", function() {
            console.log(connector_hospital_address);
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
                $("#connector_div").show(300);
                $("#send_div").show(300);
                $(this).val(0);
                $('#your-form').off('submit');
                // Remove pet name required attribute
                $("#hospital_address").prop('required', false);
            }
        });
        if (connector_hospital_address == 1) {
            $("#connector_hospital_address_div").show();
        } else {
            $("#connector_hospital_address_div").hide();
        }



        $("#source_company").hide();
        
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
        
        // 當案件來源改變時
        $('select[name="type"]').on('change', function() {
            var selectedType = $(this).val();
            if (selectedType == 'H' || selectedType == 'B' || selectedType == 'Salon' || selectedType ==
                'dogpark' || selectedType == 'G' || selectedType == 'other' || selectedType == 'self') {
                $("#source_company").show(300);
                $("#source_company_name_q").prop('required', true);
                // 載入對應類型的客戶
                loadCustomersByType(selectedType);
            } else {
                $("#source_company").hide(300);
                $("#source_company_name_q").prop('required', false);
            }
        });


        //案件單類別
        $('select[name="type_list"]').on('change', function() {
            var typeList = $(this).val();
            var payIdValue = $('select[name="pay_id"]').val();
            console.log('案件類別變更:', typeList);
            
            // 當案件類別變更時，重新控制欄位顯示
            if (payIdValue) {
                controlFieldsByPaymentType(payIdValue, typeList);
                    } else {
                // 如果還沒選擇支付類別，根據案件類別決定預設顯示
                if (typeList === 'memorial') {
                    // 追思單：隱藏不必要欄位，但保留基本欄位
                    $(".not_memorial_show").hide();
                    showPaymentSections(false);
                    showPromSectionsIfNeeded();
                    showPaymentTypeHint('追思單', '請選擇支付類別以繼續。');
                } else {
                    // 派件單：顯示所有欄位
                    $(".not_final_show, .not_memorial_show").show();
                    showPaymentSections(true);
                    showPromSectionsIfNeeded();
                    showPaymentTypeHint('派件單', '請選擇支付類別以繼續。');
                }
            }
        });

        $('select[name="pay_id"]').on('change', function() {
            type_list = $('select[name="type_list"]').val();
            var payValue = $(this).val();
            var planId = $('#plan_id').val();
            console.log('支付類別變更:', payValue);
            
            // 根據支付類別控制欄位顯示
            controlFieldsByPaymentType(payValue, type_list);
            
            // 處理方案價格欄位
            handlePlanPriceField(planId, payValue);
        });

        // 支付類別欄位控制函數
        function controlFieldsByPaymentType(payValue, typeList) {
            console.log('控制欄位顯示 - 支付類別:', payValue, '案件類別:', typeList);
            
            // 重置所有欄位為預設狀態
            resetAllFields();
            
            switch(payValue) {
                case 'A': // 一次付清
                    handleFullPayment(typeList);
                    break;
                    
                case 'C': // 訂金
                    handleDepositPayment(typeList);
                    break;
                    
                case 'D': // 尾款
                    handleFinalPayment(typeList);
                    break;
                    
                case 'E': // 追加
                    handleAdditionalPayment(typeList);
                    break;
                    
                default:
                    // 預設顯示完整表單
                    handleFullPayment(typeList);
            }
        }

        // 重置所有欄位為預設狀態
        function resetAllFields() {
            // 移除所有必填限制
            $("#kg, #variety, #type, #plan_id, #plan_price").prop('required', false);
            
            // 隱藏部分區塊（not_memorial_show欄位的顯示由各個支付類別函數控制）
            $(".not_final_show, .plan").hide();
            $("#send_div, #connector_div, #connector_hospital_div").hide();
            // 後續處理和金紙區塊的顯示由 showPromSectionsIfNeeded 控制
            $("#prom_div, #gdpaper_div, #souvenir_div").hide();
            
            
            // 隱藏提示訊息
            hidePaymentTypeHint();
        }

        // 一次付清 (A)
        function handleFullPayment(typeList) {
            console.log('處理一次付清');
            
            if (typeList === 'memorial') {
                // 追思單：只顯示基本必要資訊，隱藏寵物詳細資料和方案資訊
                $(".not_memorial_show").hide(); // 隱藏不必要欄位
                showPaymentSections(false);
                setRequiredFields(['pet_name']); // 只有寵物名稱必填
                showPaymentTypeHint('一次付清（追思單）', '請填寫客戶名稱、寵物名稱、後續處理、金紙選購和付款資訊。');
                } else {
                // 派件單：顯示完整表單
                $(".not_final_show, .not_memorial_show").show();
                setRequiredFields(['pet_name', 'kg', 'variety', 'type', 'plan_id', 'plan_price']);
                showPaymentSections(true);
                showPaymentTypeHint('一次付清（派件單）', '請填寫完整的客戶資訊、寵物詳細資料、方案選擇、後續處理和接體相關設定。');
            }
            
            // 檢查後續處理區塊顯示（根據案件類別和支付類別決定）
            setTimeout(function() {
                showPromSectionsIfNeeded();
                validateReligionAndDeathDate();
            }, 100);
        }

        // 訂金 (C)
        function handleDepositPayment(typeList) {
            console.log('處理訂金');
            
            if (typeList === 'memorial') {
                // 追思單訂金：只顯示基本必要資訊，隱藏寵物詳細資料和方案資訊
                $(".not_memorial_show").hide(); // 隱藏不必要欄位
                showPaymentSections(false);
                setRequiredFields(['pet_name']); // 只有寵物名稱必填
                showPaymentTypeHint('訂金（追思單）', '請填寫客戶名稱、寵物名稱、金紙選購和付款資訊。後續處理項目將在尾款時處理。');
            } else {
                // 派件單訂金：顯示完整表單但不顯示後續處理
                $(".not_final_show, .not_memorial_show").show();
                setRequiredFields(['pet_name', 'kg', 'variety', 'type', 'plan_id', 'plan_price']);
                showPaymentSections(true);
                showPaymentTypeHint('訂金（派件單）', '請填寫完整的客戶和寵物資訊、方案選擇。後續處理項目將在尾款時處理。');
            }
            
            // 檢查後續處理區塊顯示（根據案件類別和支付類別決定）
            setTimeout(function() {
                showPromSectionsIfNeeded();
                validateReligionAndDeathDate();
            }, 100);
        }

        // 尾款 (D)
        function handleFinalPayment(typeList) {
            console.log('處理尾款');
            
            // 尾款只顯示：客戶選擇、寵物名稱、收款金額、後續處理
            $(".not_final_show").hide(); // 隱藏寵物詳細資訊、方案等
            $(".plan").hide(); // 隱藏方案選擇
            
            
            // 隱藏接體相關設定（尾款不需要）
            showPaymentSections(false);
            
            // 移除不需要的必填驗證
            $("#kg, #variety, #type, #plan_id, #plan_price").prop('required', false);
            
            // 保持客戶和寵物名稱必填
            $("#cust_name_q, #pet_name").prop('required', true);
            
            if (typeList === 'memorial') {
                // 追思單尾款
                showPaymentTypeHint('尾款（追思單）', '請選擇客戶和寵物名稱，選擇金紙，輸入尾款金額。可以選擇追加的後續處理項目。');
            } else {
                // 派件單尾款
                showPaymentTypeHint('尾款（派件單）', '請選擇客戶和寵物名稱，輸入尾款金額。可以選擇追加的後續處理項目。');
            }
            
            // 檢查後續處理區塊顯示（根據案件類別決定）
            setTimeout(function() {
                showPromSectionsIfNeeded();
                validateReligionAndDeathDate();
            }, 100);
        }

        // 追加 (E)
        function handleAdditionalPayment(typeList) {
            console.log('處理追加');
            
            // 追加顯示：客戶選擇、寵物名稱、方案選擇、後續處理、收款
            $(".not_final_show").hide(); // 隱藏寵物詳細資訊
            
            
            // 隱藏接體相關設定
            showPaymentSections(false);
            
            if (typeList === 'memorial') {
                // 追思單追加：隱藏方案選擇
                $(".plan").hide();
                setRequiredFields(['pet_name']);
                showPaymentTypeHint('追加（追思單）', '請選擇客戶和寵物名稱，選擇金紙，輸入追加金額。可以選擇追加的後續處理項目。');
                } else {
                // 派件單追加：顯示方案選擇
                $(".plan").show();
                setRequiredFields(['pet_name', 'plan_id']);
                showPaymentTypeHint('追加（派件單）', '請選擇客戶、寵物名稱和方案，新增追加項目，並輸入追加金額。');
                
                // 檢查是否需要顯示套裝欄位（當方案ID為1時）
                var currentPlanId = $("#plan_id").val();
                if (currentPlanId === '1') {
                    $('#suit_id').prop('required', true);
                    $('#suit_field').show(300);
                } else {
                    $('#suit_id').prop('required', false);
                    $('#suit_field').hide(300);
                    $('#suit_id').val('');
                }
            }
            
            // 檢查後續處理區塊顯示（根據案件類別決定）
            setTimeout(function() {
                showPromSectionsIfNeeded();
                validateReligionAndDeathDate();
            }, 100);
        }

        // 顯示/隱藏付款相關區塊
        function showPaymentSections(show) {
            if (show) {
                $("#send_div, #connector_div, #connector_hospital_div").show(300);
            } else {
                $("#send_div, #connector_div, #connector_hospital_div").hide(300);
            }
        }

        // 設定必填欄位
        function setRequiredFields(fields) {
            // 先清除所有必填
            $("#kg, #variety, #type, #plan_id, #plan_price, #pet_name, #cust_name_q, #death_date, #religion").prop('required', false);
            
            // 設定指定欄位為必填
            fields.forEach(function(field) {
                $("#" + field).prop('required', true);
            });
            
            // 客戶選擇永遠必填
            $("#cust_name_q").prop('required', true);
            
            // 宗教信仰在顯示時為必填
            if ($("#religion_field").is(':visible')) {
                $("#religion").prop('required', true);
            }
            
            // 檢查浪浪方案的特殊處理
            var planId = $('#plan_id').val();
            var payId = $('select[name="pay_id"]').val();
            if (planId === '4' && (payId === 'A' || payId === 'C' || payId === 'E')) {
                // 浪浪方案 + 一次付清/訂金/往生紀念：plan_price 不為必填
                $("#plan_price").prop('required', false);
            }
            
            // 往生日期改為非必填（因為不一定知道往生日期）
            // 移除原本的必填邏輯
        }

        // 顯示支付類別提示訊息
        function showPaymentTypeHint(paymentType, message) {
            resetHintStyle(); // 重設樣式
            $("#payment_type_hint_text").html('<strong>' + paymentType + '：</strong>' + message);
            $("#payment_type_hint").show(300);
        }

        // 隱藏支付類別提示訊息
        function hidePaymentTypeHint() {
            $("#payment_type_hint").hide(300);
        }

        // 注意：宗教、往生日期、方案的事件監聽器已移到 setupCrossFieldValidation() 中
        // 避免重複綁定事件

        // 計算重要日期
        function calculateMemorialDates(deathDateStr, planId) {
            if (!deathDateStr) return;
            
            var deathDate = new Date(deathDateStr);
            
            // 檢查日期是否有效
            if (isNaN(deathDate.getTime())) {
                console.error('無效的往生日期:', deathDateStr);
                return;
            }
            
            console.log('開始計算重要日期，往生日期:', deathDate, '方案ID:', planId);
            
            // 計算各個重要日期
            var seventhDay = new Date(deathDate);
            seventhDay.setDate(deathDate.getDate() + 6); // 頭七（第7天）
            
            var fortyNinthDay = new Date(deathDate);
            fortyNinthDay.setDate(deathDate.getDate() + 48); // 四十九日（第49天）
            
            var hundredthDay = new Date(deathDate);
            hundredthDay.setDate(deathDate.getDate() + 99); // 百日（第100天）
            
            var anniversaryDay = new Date(deathDate);
            anniversaryDay.setFullYear(deathDate.getFullYear() + 1); // 對年（一年後）
            
            // 更新頁面顯示
            updateMemorialDatesDisplay(seventhDay, fortyNinthDay, hundredthDay, anniversaryDay, planId);
        }

        // 更新重要日期顯示
        function updateMemorialDatesDisplay(seventhDay, fortyNinthDay, hundredthDay, anniversaryDay, planId) {
            // 格式化日期顯示
            var formatDate = function(date) {
                var year = date.getFullYear();
                var month = String(date.getMonth() + 1).padStart(2, '0');
                var day = String(date.getDate()).padStart(2, '0');
                var weekdays = ['日', '一', '二', '三', '四', '五', '六'];
                var weekday = weekdays[date.getDay()];
                return year + '/' + month + '/' + day + ' (' + weekday + ')';
            };
            
            // 檢查是否為浪浪方案（plan_id = 4）
            var isStrayPlan = (planId == '4');
            
            // 更新各個日期
            if (isStrayPlan) {
                // 浪浪方案不顯示頭七
                $('#seventh_day').text('不適用').parent().parent().addClass('opacity-50');
                console.log('浪浪方案，隱藏頭七');
            } else {
                $('#seventh_day').text(formatDate(seventhDay)).parent().parent().removeClass('opacity-50');
            }
            
            $('#forty_ninth_day').text(formatDate(fortyNinthDay));
            $('#hundredth_day').text(formatDate(hundredthDay));
            $('#anniversary_day').text(formatDate(anniversaryDay));
            
            // 顯示重要日期區塊
            $('#memorial_dates_section').show(300);
            
            console.log('重要日期已更新:', {
                seventh: formatDate(seventhDay),
                fortyNinth: formatDate(fortyNinthDay),
                hundredth: formatDate(hundredthDay),
                anniversary: formatDate(anniversaryDay),
                isStrayPlan: isStrayPlan
            });
        }

        // 隱藏重要日期區塊
        function hideMemorialDates() {
            $('#memorial_dates_section').hide(300);
            // 清空日期顯示
            $('#seventh_day, #forty_ninth_day, #hundredth_day, #anniversary_day').text('-');
            $('#seventh_day').parent().parent().removeClass('opacity-50');
        }

        // 初始化防呆機制
        function initializeFieldValidation() {
            console.log('初始化防呆機制');
            
            // 監聽所有主要欄位變更，進行交叉驗證
            setupCrossFieldValidation();
        }

        // 設定欄位間的交叉驗證
        function setupCrossFieldValidation() {
            
            // 案件類別變更時的防呆
            $('#type_list').on('change', function() {
                var typeList = $(this).val();
                console.log('案件類別變更防呆:', typeList);
                
                // 追思單時清除某些不適用的欄位
                if (typeList === 'memorial') {
                    clearMemorialIncompatibleFields();
                }
                
                // 所有案件類別都根據支付類別決定是否顯示後續處理區塊
                showPromSectionsIfNeeded();
                
                // 重新驗證宗教和往生日期邏輯
                validateReligionAndDeathDate();
            });

            // 支付類別變更時的防呆
            $('#pay_id').on('change', function() {
                var payId = $(this).val();
                console.log('支付類別變更防呆:', payId);
                
                // 尾款或追加時，某些欄位應該被清除或重置
                if (payId === 'D' || payId === 'E') {
                    clearNonPaymentFields();
                }
                
                // 檢查是否需要顯示後續處理區塊
                showPromSectionsIfNeeded();
                
                // 重新驗證所有相關邏輯
                validateReligionAndDeathDate();
            });

        // 方案變更時的增強防呆
        $('#plan_id').on('change', function() {
            var planId = $(this).val();
            var typeList = $('#type_list').val();
            var religion = $('#religion').val();
            var previousDeathDate = $('#death_date').val();
            var payId = $('#pay_id').val();
            
            console.log('方案變更防呆:', planId, '案件類別:', typeList, '宗教:', religion, '之前的往生日期:', previousDeathDate);
            
            // 特殊邏輯：浪浪方案的處理
            if (planId === '4') { // 浪浪方案
                console.log('切換到浪浪方案，特殊處理');
                handleStrayPlanSelection(typeList, religion, previousDeathDate);
            }
            
            // 處理宗教和往生日期的顯示邏輯
            handlePlanReligionInteraction(planId, typeList, religion);
            
            // 檢查套裝欄位顯示邏輯
            if (typeList === 'dispatch' && (payId === 'A' || payId === 'D' || payId === 'E') && planId === '1') {
                $('#suit_id').prop('required', true);
                $('#suit_field').show(300);
            } else {
                $('#suit_id').prop('required', false);
                $('#suit_field').hide(300);
                $('#suit_id').val('');
            }
            
            // 處理浪浪方案的 plan_price 欄位
            handlePlanPriceField(planId, payId);
            
            // 重新計算重要日期（如果有往生日期的話）
            var deathDate = $('#death_date').val();
            if (deathDate && (religion === 'buddhism' || religion === 'taoism' || religion === 'buddhism_taoism')) {
                calculateMemorialDates(deathDate, planId);
            }
            
            // 原有的價格計算邏輯保持不變
            calculate_price();
        });

            // 宗教變更時的增強防呆
            $('#religion').on('change', function() {
                var religion = $(this).val();
                var planId = $('#plan_id').val();
                var typeList = $('#type_list').val();
                
                console.log('宗教變更防呆:', religion, '方案:', planId, '案件類別:', typeList);
                
                // 檢查是否顯示宗教提醒
                if (religion && religion !== 'buddhism_taoism') {
                    // 非佛道教，顯示提醒
                    $('#religion_reminder').show(300);
                } else {
                    // 佛道教或未選擇，隱藏提醒
                    $('#religion_reminder').hide(300);
                }
                
                // 處理宗教和方案的交互邏輯
                handlePlanReligionInteraction(planId, typeList, religion);
            });

            // 往生日期變更時的防呆
            $('#death_date').on('change', function() {
                var deathDate = $(this).val();
                var religion = $('#religion').val();
                var planId = $('#plan_id').val();
                
                console.log('往生日期變更防呆:', deathDate, '宗教:', religion, '方案:', planId);
                
                // 驗證往生日期是否合理（不能是未來日期）
                if (deathDate && new Date(deathDate) > new Date()) {
                    alert('往生日期不能是未來日期，請重新選擇');
                    $(this).val('');
                    hideMemorialDates();
                    return;
                }
                
                // 重新計算重要日期
                if (deathDate && (religion === 'buddhism' || religion === 'taoism' || religion === 'buddhism_taoism')) {
                    calculateMemorialDates(deathDate, planId);
                } else {
                    hideMemorialDates();
                }
            });

            // 客戶選擇變更時的防呆
            $('#cust_name_q').on('change', function() {
                var customerId = $(this).val();
                console.log('客戶變更防呆:', customerId);
                
                // 如果是尾款或追加，可能需要驗證客戶是否匹配
                var payId = $('#pay_id').val();
                if (payId === 'D' || payId === 'E') {
                    console.log('尾款/追加模式，客戶變更');
                    // 這裡可以加入AJAX驗證邏輯，檢查客戶是否有對應的原始訂單
                }
            });

            // 寵物名稱變更時的防呆
            $('#pet_name').on('change', function() {
                var petName = $(this).val();
                console.log('寵物名稱變更防呆:', petName);
                
                // 寵物名稱變更時，往生日期可能需要重新確認
                var deathDate = $('#death_date').val();
                var payId = $('#pay_id').val();
                
                if (deathDate && (payId === 'D' || payId === 'E')) {
                    console.log('尾款/追加模式，寵物名稱變更，往生日期需重新確認');
                    // 可以在這裡添加警告提示
                }
            });
        }

        // 處理方案和宗教的交互邏輯
        function handlePlanReligionInteraction(planId, typeList, religion) {
            console.log('處理方案宗教交互:', { planId, typeList, religion });
            
            // 檢查是否應該顯示宗教和往生日期欄位
            var shouldShowReligion = shouldDisplayReligionFields(planId, typeList);
            var shouldShowDeathDate = shouldDisplayDeathDateField(planId, typeList, religion);
            
            if (!shouldShowReligion) {
                // 不應該顯示宗教欄位時，隱藏並清除相關數據
                console.log('隱藏宗教相關欄位');
                $('#religion_field').hide(300); // 隱藏整個宗教欄位
                $('#religion').val('').prop('required', false);
                $('#religion_other').val('').prop('required', false);
                $('#religion_other_input').hide(300);
                $('#death_date').val('');
                $('#death_date_field').hide(300);
                $('#death_date').prop('required', false);
                hideMemorialDates();
                return;
            } else {
                // 應該顯示宗教欄位
                console.log('顯示宗教欄位');
                $('#religion_field').show(300); // 顯示宗教欄位
                $('#religion').prop('required', true); // 設為必填
                
                // 如果當前選擇的是「其他」，顯示輸入框
                if (religion === 'other') {
                    $('#religion_other_input').show(300);
                    $('#religion_other').prop('required', true);
                } else {
                    $('#religion_other_input').hide(300);
                    $('#religion_other').val('').prop('required', false);
                }
            }
            
            if (!shouldShowDeathDate) {
                // 不應該顯示往生日期時，清除相關數據
                console.log('清除往生日期相關欄位');
                $('#death_date').val('');
                $('#death_date_field').hide(300);
                $('#death_date').prop('required', false);
                hideMemorialDates();
            } else {
                // 應該顯示往生日期（但改為非必填）
                $('#death_date_field').show(300);
                $('#death_date').prop('required', false);
            }
        }

        // 判斷是否應該顯示宗教欄位
        function shouldDisplayReligionFields(planId, typeList) {
            var payId = $('#pay_id').val();
            var planIdStr = String(planId);
            
            console.log('判斷宗教欄位顯示:', { typeList, payId, planId: planIdStr });
            
            // 只有派件單且支付類別為一次付清(A)或訂金(C)時才考慮顯示宗教
            if (typeList === 'dispatch' && (payId === 'A' || payId === 'C')) {
                // 個人(1)、團體(2)、浪浪(3)方案顯示宗教，其他(4)方案不顯示
                if (planIdStr === '1' || planIdStr === '2' || planIdStr === '3') {
                    console.log('方案', planIdStr, '：顯示宗教欄位');
                    return true;
                } else {
                    console.log('其他方案（方案ID:', planIdStr, '）：不顯示宗教欄位');
                    return false;
                }
            }
            
            console.log('非派件單或非一次付清/訂金，不顯示宗教欄位');
            return false;
        }

        // 判斷是否應該顯示往生日期欄位
        function shouldDisplayDeathDateField(planId, typeList, religion) {
            var planIdStr = String(planId);
            
            console.log('判斷往生日期欄位顯示:', { planId: planIdStr, typeList, religion });
            
            // 浪浪方案永遠不顯示往生日期
            if (planIdStr === '3') {
                console.log('浪浪方案：不顯示往生日期');
                return false;
            }
            
            // 只有個人、團體方案才考慮顯示往生日期
            if (planIdStr !== '1' && planIdStr !== '2') {
                console.log('非個人/團體方案：不顯示往生日期');
                return false;
            }
            
            // 所有宗教都可以填寫往生日期（非必填）
            console.log('個人/團體方案：顯示往生日期（所有宗教都可填寫）');
            return true;
        }

        // 清除追思單不相容的欄位
        function clearMemorialIncompatibleFields() {
            console.log('清除追思單不相容欄位');
            // 追思單可能不需要某些欄位，根據需求添加
        }

        // 清除非付款相關欄位（尾款、追加時使用）
        function clearNonPaymentFields() {
            console.log('清除非付款相關欄位');
            // 尾款和追加時，某些原始訂單欄位不應該被修改
            // 但宗教和往生日期可能還是需要的，所以暫時不清除
        }

        // 驗證宗教和往生日期邏輯
        function validateReligionAndDeathDate() {
            var religion = $('#religion').val();
            var planId = $('#plan_id').val();
            var typeList = $('#type_list').val();
            var deathDate = $('#death_date').val();
            
            console.log('驗證宗教往生日期邏輯:', { religion, planId, typeList, deathDate });
            
            // 重新處理方案宗教交互
            handlePlanReligionInteraction(planId, typeList, religion);
        }

        // 處理浪浪方案選擇的特殊邏輯
        function handleStrayPlanSelection(typeList, religion, previousDeathDate) {
            console.log('處理浪浪方案特殊邏輯:', { typeList, religion, previousDeathDate });
            
            // 如果之前有選擇佛道教和往生日期，詢問是否要清除
            if (previousDeathDate && (religion === 'buddhism' || religion === 'taoism' || religion === 'buddhism_taoism')) {
                var confirmClear = confirm(
                    '您選擇了浪浪方案，浪浪方案的重要日期計算規則與個別方案不同（無頭七）。\\n\\n' +
                    '是否要清除目前的往生日期並重新設定？\\n\\n' +
                    '選擇「確定」會清除往生日期\\n' +
                    '選擇「取消」會保留往生日期但重新計算重要日期'
                );
                
                if (confirmClear) {
                    console.log('用戶選擇清除往生日期');
                    $('#death_date').val('');
                    hideMemorialDates();
                    
                    // 可以考慮是否也清除宗教選擇
                    var confirmClearReligion = confirm('是否同時清除宗教選擇？');
                    if (confirmClearReligion) {
                        $('#religion').val('');
                        $('#death_date_field').hide(300);
                        $('#death_date').prop('required', false);
                    }
                } else {
                    console.log('用戶選擇保留往生日期，重新計算');
                    // 保留往生日期，但重新計算（會顯示浪浪方案的規則）
                    calculateMemorialDates(previousDeathDate, '4');
                }
            }
            
            // 顯示浪浪方案的特殊提示
            showStrayPlanNotification();
        }

        // 顯示浪浪方案特殊提示
        function showStrayPlanNotification() {
                          // 使用現有的提示區域顯示浪浪方案說明
              $("#payment_type_hint_text").html(
                  '<strong>浪浪方案提醒：</strong>浪浪方案的重要日期計算中，頭七不適用，僅計算四十九日、百日、對年。（僅供參考）'
              );
            $("#payment_type_hint").removeClass('alert-info').addClass('alert-warning').show(300);
            
            // 3秒後自動隱藏
            setTimeout(function() {
                $("#payment_type_hint").removeClass('alert-warning').addClass('alert-info').hide(300);
            }, 3000);
        }

        // 重設提示區域樣式（在其他地方使用時恢復原樣）
        function resetHintStyle() {
            $("#payment_type_hint").removeClass('alert-warning').addClass('alert-info');
        }

        // 處理方案價格欄位的顯示邏輯
        function handlePlanPriceField(planId, payId) {
            console.log('處理方案價格欄位:', { planId, payId });
            
            // 浪浪方案 (plan_id == 4) 且支付類別為 A、C 或 E 時，隱藏 plan_price 欄位
            // 或者支付類別為 D（尾款）時，隱藏 plan_price 欄位
            if ((planId === '4' && (payId === 'A' || payId === 'C' || payId === 'E')) || payId === 'D') {
                console.log('隱藏方案價格欄位 - 原因:', planId === '4' ? '浪浪方案 + 一次付清/訂金/往生紀念' : '尾款');
                $('.plan_price').hide(300);
                $('#plan_price').val('').prop('required', false);
            } else {
                console.log('其他方案或支付類別：顯示方案價格欄位');
                $('.plan_price').show(300);
                $('#plan_price').prop('required', true);
            }
        }

        // 隱藏後續處理相關區塊
        function hidePromSections() {
            console.log('隱藏後續處理區塊');
            $("#prom_div, #gdpaper_div").hide(300);
            // 如果有紀念品區塊也一併隱藏
            $("#souvenir_div").hide(300);
        }

        // 只隱藏後續處理，保留金紙選購
        function hidePromOnly() {
            console.log('只隱藏後續處理，保留金紙選購');
                        $("#prom_div").hide(300);
            // 如果有紀念品區塊也一併隱藏
                        $("#souvenir_div").hide(300);
                    }

        // 根據需要顯示後續處理區塊
        function showPromSectionsIfNeeded() {
            var typeList = $('#type_list').val();
            var payId = $('#pay_id').val();
            
            console.log('檢查是否需要顯示後續處理區塊:', { typeList, payId });
            
            // 所有案件類別都根據支付類別決定是否顯示後續處理
            if (payId === 'C') {
                // 訂金不顯示後續處理
                console.log('訂金 - 隱藏後續處理區塊');
                hidePromSections();
            } else if (payId === 'A' || payId === 'D' || payId === 'E') {
                // 一次付清、尾款、追加顯示後續處理
                console.log('支付類別(' + payId + ') - 顯示後續處理區塊');
                $("#prom_div, #gdpaper_div").show(300);
            } else {
                // 未選擇支付類別時，預設顯示
                console.log('未選擇支付類別 - 預設顯示後續處理區塊');
                $("#prom_div, #gdpaper_div").show(300);
            }
        }

        $("#cash_price_div").hide();
        $("#transfer_price_div").hide();
        $("#transfer_channel_div").hide();
        $("#transfer_number_div").hide();

        $('select[name="pay_method"]').on('change', function() {
            console.log($(this).val());
            if ($(this).val() == 'C') {
                $("#cash_price_div").show(300);
                $("#transfer_price_div").show(300);
                $("#transfer_number_div").show(300);
                $("#transfer_channel_div").show(300);
                $("#pay_price").prop('required', false);
                $("#cash_price").prop('required', true);
                $("#transfer_price").prop('required', true);
                $("#transfer_channel").prop('required', true);
            } else if ($(this).val() == 'B') {
                $("#cash_price_div").hide(300);
                $("#transfer_price_div").hide(300);
                $("#transfer_number_div").show(300);
                $("#transfer_channel_div").show(300);
                $("#pay_price").prop('required', true);
                $("#cash_price").prop('required', false);
                $("#transfer_price").prop('required', false);
                $("#transfer_channel").prop('required', true);
            } else {
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



        $("#plan_id").on('change', function() {
            calculate_price();
            
            // 根據方案選擇控制宗教和往生日期欄位顯示
            var planId = $(this).val();
            var typeList = $('#type_list').val();
            var payId = $('#pay_id').val();
            
            console.log('方案變更:', planId, '類型:', typeof planId, '案件類別:', typeList, '支付類別:', payId);
            
            // 根據方案選擇控制宗教和往生日期欄位
            if (typeList === 'dispatch' && (payId === 'A' || payId === 'C')) {
                // 將 planId 轉換為字串進行比較
                var planIdStr = String(planId);
                
                if (planIdStr === '1' || planIdStr === '2') {
                    // 個人、團體方案：顯示宗教和往生日期（往生日期為非必填）
                    $('#religion_field').show(300);
                    $('#death_date_field').show(300);
                    $('#death_date').prop('required', false); // 確保為非必填
                    console.log('個人/團體方案 (ID:', planIdStr, ')：顯示宗教和往生日期（非必填）');
                } else if (planIdStr === '3') {
                    // 浪浪方案：只顯示宗教，不顯示往生日期
                    $('#religion_field').show(300);
                    $('#death_date_field').hide(300);
                    $('#death_date').val('').prop('required', false); // 清空往生日期
                    hideMemorialDates(); // 隱藏重要日期
                    console.log('浪浪方案 (ID:', planIdStr, ')：只顯示宗教，不顯示往生日期');
                } else {
                    // 其他方案：不顯示宗教和往生日期
                    $('#religion_field').hide(300);
                    $('#death_date_field').hide(300);
                    $('#religion').val(''); // 清空宗教選擇
                    $('#death_date').val('').prop('required', false); // 清空往生日期
                    hideMemorialDates(); // 隱藏重要日期
                    console.log('其他方案 (ID:', planIdStr, ')：不顯示宗教和往生日期');
                }
            } else {
                // 非派件單或非一次付清/訂金，隱藏所有宗教相關欄位
                $('#religion_field').hide(300);
                $('#death_date_field').hide(300);
                $('#religion').val('');
                $('#death_date').val('').prop('required', false);
                hideMemorialDates();
            }
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
                    hideMemorialDates();
                    console.log('浪浪方案 (ID:', planIdStr, ')：不顯示往生日期');
                } else {
                    // 其他方案：不顯示往生日期（其實宗教也不會顯示）
                    $('#death_date_field').hide(300);
                    $('#death_date').val('').prop('required', false);
                    hideMemorialDates();
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
                hideMemorialDates();
                return;
            }
            
            // 只有個人、團體方案且佛道教相關宗教才計算重要日期
            var planIdStr = String(planId);
            
            if (deathDate && typeList === 'dispatch' && (payId === 'A' || payId === 'C') && 
                (planIdStr === '1' || planIdStr === '2') && 
                (religion === 'buddhism' || religion === 'taoism' || religion === 'buddhism_taoism')) {
                calculateMemorialDates(deathDate, planId);
                console.log('個人/團體方案 (ID:', planIdStr, ') + 佛道教：計算重要日期（包含頭七）');
            } else {
                hideMemorialDates();
                console.log('非佛道教或其他情況 (ID:', planIdStr, ')：不計算重要日期');
            }
        });

        // 支付類別變更事件
        $("#pay_id").on('change', function() {
            var payId = $(this).val();
            var typeList = $('#type_list').val();
            var planId = $('#plan_id').val();
            var religion = $('#religion').val();
            
            console.log('支付類別變更:', payId, '案件類別:', typeList, '方案:', planId, '宗教:', religion);
            
            // 重新驗證宗教和往生日期的顯示邏輯
            validateReligionAndDeathDate();
        });


        $("#plan_price").on('input', function() {
            calculate_price();
        });

        $(document).on('input', '.total_number', function() {
            calculate_price();
        });




        function chgItems(obj) {
            $("#row_id").val($("#" + obj.id).attr('alt'));
            row_id = $("#row_id").val();
            
            // 防呆：當變更select_proms時，隱藏對應的prom_product區塊
            $('#prom_product_' + row_id).hide(300);
            
            // 重新載入時重置套組法會額外欄位
            $('#prom_extra_text_col_' + row_id).hide(300);
            $('#prom_extra_text_' + row_id).val('');
            
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
                    
                    // 注意：不在這裡檢查套組法會，因為這時 prom 還沒被使用者選擇
                    // 套組法會的檢查邏輯交給 prom 下拉選單的 change 事件處理
                }
            });
        }

        //金紙專區
        function chgPapers(obj) {
            $("#row_id").val($("#" + obj.id).attr('alt'));
            row_id = $("#row_id").val();

            console.log(row_id);

            $.ajax({
                url: '{{ route('gdpaper.search') }}',
                data: {
                    'gdpaper_id': $("#gdpaper_id_" + row_id).val()
                },
                success: function(data) {
                    var gdpaper_num = $("#gdpaper_num_" + row_id).val();

                    // 如果數量為空，預設為 1gdpaper_num <= 0
                    if (!gdpaper_num) {
                        gdpaper_num = 1;
                        $("#gdpaper_num_" + row_id).val(gdpaper_num);
                    }

                    // 計算金額
                    $("#gdpaper_total_" + row_id).val(gdpaper_num * data);
                    calculate_price();

                    // 監聽數量變化，重新計算總價
                    $("#gdpaper_num_" + row_id).on('change', function() {
                        gdpaper_num = $(this).val();
                        $("#gdpaper_total_" + row_id).val(gdpaper_num * data);
                        calculate_price();
                    });
                }
            });
        }

        function chgNums(obj) {
            $("#row_id").val($("#" + obj.id).attr('alt'));
            var row_id = $("#row_id").val();

            $.ajax({
                url: '{{ route('gdpaper.search') }}',
                data: {
                    'gdpaper_id': $("#gdpaper_id_" + row_id).val()
                },
                success: function(data) {
                    var gdpaper_num = $("#gdpaper_num_" + row_id).val();

                    // 防止數量為 0 或空值
                    // if (!gdpaper_num) {
                    //     gdpaper_num = 1;
                    //     $("#gdpaper_num_" + row_id).val(gdpaper_num);
                    // }

                    // 計算總金額
                    $("#gdpaper_total_" + row_id).val(gdpaper_num * data);
                    calculate_price();

                    // 更新數量變更事件
                    $("#gdpaper_num_" + row_id).on('change', function() {
                        gdpaper_num = $(this).val();
                        // if (gdpaper_num <= 0) {
                        //     gdpaper_num = 1; // 確保數量最小值為 1
                        //     $(this).val(gdpaper_num);
                        // }
                        $("#gdpaper_total_" + row_id).val(gdpaper_num * data);
                        calculate_price();
                    });
                }
            });
        }

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

        // 檢查表單提交時 gdpaper_num 是否為 0
        $("form").on('submit', function(event) {
            var hasError = false;

            // 遍歷所有的 gdpaper_num 欄位
            $("input[id^='gdpaper_num_']").each(function() {
                var gdpaper_num = $(this).val();
                var row_id = $(this).attr('id').split('_')[2]; // 取得對應的 row_id
                var gdpaper_id = $("#gdpaper_id_" + row_id).val(); // 獲取對應的金紙 ID

                // 如果 gdpaper_num 有值且大於 0，檢查是否選擇了有效的金紙 ID
                if (gdpaper_num && gdpaper_num > 0) {
                    if (!gdpaper_id || gdpaper_id == '0') {
                        alert('請選擇金紙');
                        hasError = true;
                        return false; // 終止 each 循環
                    }
                }
                // 如果 gdpaper_num 為 0
                else if (gdpaper_num == 0 || gdpaper_num == '') {
                    // 檢查是否選擇了金紙 ID
                    if (gdpaper_id && gdpaper_id != '0') {
                        alert('金紙數量不能為 0');
                        hasError = true;
                        return false; // 終止 each 循環
                    }
                }
            });

            if (hasError) {
                event.preventDefault(); // 阻止表單提交
            }

        });





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
            cols +=
                '<td class="text-center"><button type="button" class="ibtnDel_gdpaper demo-delete-row btn btn-danger btn-sm btn-icon"><i class="fa fa-times"></i></button></td>';
            cols += '<td>';
            cols += '<select id="gdpaper_id_' + rowCount + '" alt="' + rowCount +
                '" class="mobile form-select" name="gdpaper_ids[]" onchange="chgPapers(this)">';
            cols += '<option value="" selected>請選擇...</option>';
            @foreach ($products as $product)
                cols +=
                    '<option value="{{ $product->id }}">{{ $product->name }}({{ $product->price }})</option>';
            @endforeach
            cols += '</select>';
            cols += '</td>';
            cols += '<td>';
            cols += '<input type="number"  alt="' + rowCount + '"  class="mobile form-control" id="gdpaper_num_' +
                rowCount +
                '" min="0" name="gdpaper_num[]" value="" onchange="chgNums(this)" onmousedown="chgNums(this)" onkeydown="chgNums(this)">';
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
            
            var plan_id = $('select[name="plan_id"]').val();
            console.log('plan_id:', plan_id, 'pay_id:', payId, 'total:', total);
        }
        



        $(".source_company_name").keydown(function() {
            var $value = $(this).val();
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
            console.log($value);
        });

        $(".ibtnAdd_prom").click(function() {
            rowCount = $('table.prom-list tr').length - 1;
            var newRow = $("<tr>");
            var cols = '';
            cols +=
                '<td class="text-center"><button type="button" class="ibtnDel_prom demo-delete-row btn btn-danger btn-sm btn-icon"><i class="fa fa-times"></i></button></td>';
            cols += '<td>';
            cols += '<select id="select_prom_' + rowCount + '" alt="' + rowCount +
                '" class="mobile form-select" name="select_proms[]" onchange="chgItems(this)">';
            cols += '<option value="" selected>請選擇...</option>';
            cols += '<option value="A">安葬處理</option>';
            cols += '<option value="B">後續處理</option>';
            cols += '<option value="C">其他處理</option>';
            cols += '</select>';
            cols += '</td>';
            cols += '<td>';
            cols += '<select id="prom_' + rowCount + '" class="mobile form-select" name="prom[]">';
            cols += '<option value="">請選擇...</option>';
            cols += '</select>';
            // 套組法會額外備註 (prom value=8) - 移到 prom_product 容器外
            cols += '<div class="row mt-2" id="prom_extra_text_col_' + rowCount + '" style="display:none;">';
            cols += '<div class="col-12">';
            cols += '<input class="form-control" type="text" id="prom_extra_text_' + rowCount + '" name="prom_extra_text[]" placeholder="備註">';
            cols += '</div>';
            cols += '</div>';
            cols += '</td>';
            cols += '<td>';
            cols += '<input type="text" class="mobile form-control total_number" id="prom_total_' + rowCount +
                '" name="prom_total[]">';
            cols += '</td>';
            cols += '</tr>';
            newRow.append(cols);
            $("table.prom-list tbody").append(newRow);
            
            // 新增對應的prom_product區塊（一開始隱藏）
            var promProductHtml = '';
            promProductHtml += '<div class="row mt-1 prom-product-container" id="prom_product_' + rowCount + '" style="display: none;">';
            promProductHtml += '<div class="col-3" id="souvenir_type_col_' + rowCount + '" style="display:none;">';
            promProductHtml += '<select id="product_souvenir_type_' + rowCount + '" class="form-select" name="product_souvenir_types[]">';
            promProductHtml += '<option value="">請選擇</option>';
            @foreach ($souvenir_types as $souvenir_type)
            promProductHtml += '<option value="{{ $souvenir_type->id }}">{{ $souvenir_type->name }}</option>';
            @endforeach
            promProductHtml += '</select>';
            promProductHtml += '</div>';
            promProductHtml += '<div class="col-3" id="product_name_col_' + rowCount + '" style="display:none;">';
            promProductHtml += '<input type="text" id="product_name_' + rowCount + '" class="form-control" name="product_name[]" placeholder="請輸入商品名稱">';
            promProductHtml += '</div>';
            promProductHtml += '<div class="col-3" id="product_prom_col_' + rowCount + '">';
            promProductHtml += '<select id="product_prom_' + rowCount + '" class="form-select" name="product_proms[]" onchange="checkProductVariants(' + rowCount + ')">';
            promProductHtml += '<option value="">請選擇</option>';
            promProductHtml += '</select>';
            promProductHtml += '</div>';
            promProductHtml += '<div class="col-3" id="variant_select_' + rowCount + '">';
            promProductHtml += '<select id="product_variant_' + rowCount + '" class="form-select" name="product_variants[]">';
            promProductHtml += '<option value="">無</option>';
            promProductHtml += '</select>';
            promProductHtml += '</div>';
            promProductHtml += '<div class="col-3" id="product_num_col_' + rowCount + '">';
            promProductHtml += '<input class="form-control" type="number" id="product_num_' + rowCount + '" name="product_num[]" value="1" min="1">';
            promProductHtml += '</div>';
            promProductHtml += '<div class="col-3" id="product_comment_col_' + rowCount + '">';
            promProductHtml += '<input class="form-control" type="text" id="product_comment_' + rowCount + '" name="product_comment[]" placeholder="備註">';
            promProductHtml += '</div>';
            promProductHtml += '</div>';
            
            // 將prom_product區塊插入到對應的td中
            $('table.prom-list tr:last-child td:nth-child(3)').append(promProductHtml);
        });

        $("#not_cust_adress").hide();

        $("#in_preson").on("change", function() {
            if ($(this).is(':checked')) {
                $("#not_cust_adress").hide(300);
                $(this).val(0);
            } else {
                $("#not_cust_adress").show(300);
                $(this).val(1);
            }
        });
        $("#not_cust_adress").on("change", function() {
            if ($(this).is(':checked')) {
                $("#connector_afdress_div").show(300);
                $(this).val(1);
            } else {
                $("#connector_afdress_div").hide(300);
                $(this).val(0);
            }
        });
        $.ajaxSetup({
            headers: {
                'csrftoken': '{{ csrf_token() }}'
            }
        });

        // 單號重複檢查（變數已在全域宣告）
        
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
            
            const saleOnNumber = inputValue.trim();
            
            // 清除之前的計時器
            clearTimeout(saleOnCheckTimer);
            
            // 清空之前的反饋
            feedback.html('').removeClass('text-danger text-success text-warning');
            
            // 如果輸入為空，不進行檢查
            if (!saleOnNumber) {
                isSaleOnValid = true;
                return;
            }
            
            // 檢查是否只包含數字（這步現在是多餘的，但保留作為安全檢查）
            if (!/^\d+$/.test(saleOnNumber)) {
                feedback.html('<small class="text-warning">請只輸入數字</small>').addClass('text-warning');
                isSaleOnValid = false;
                return;
            }
            
            // 直接使用數字進行檢查，不加上 No. 前綴
            const saleOnToCheck = saleOnNumber;
            
            // 延遲 500ms 後進行檢查，避免頻繁請求
            saleOnCheckTimer = setTimeout(function() {
                $.ajax({
                    type: 'GET',
                    url: '{{ route('sale.check_sale_on') }}',
                    data: {
                        'sale_on': saleOnToCheck
                    },
                    success: function(response) {
                        if (response.exists) {
                            feedback.html('<small class="text-danger">⚠️ ' + response.message + '</small>').addClass('text-danger');
                            isSaleOnValid = false;
                        } else {
                            feedback.html('<small class="text-success">✓ 單號 ' + saleOnToCheck + ' 可以使用</small>').addClass('text-success');
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
            
            // 不需要加上 No. 前綴，直接使用數字
            // 確保輸入的是純數字
            const saleOnNumber = $('#sale_on').val().trim();
            if (saleOnNumber && /^\d+$/.test(saleOnNumber)) {
                // 保持為純數字格式，不加上 No. 前綴
                $('#sale_on').val(saleOnNumber);
            }
        });
    </script>
    <!-- end demo js-->
@endsection
