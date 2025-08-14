@extends('layouts.vertical', ['page_title' => '刪除業務Key單'])
@section('css')
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
                            <li class="breadcrumb-item active">刪除業務Key單</li>
                        </ol>
                    </div>
                    <h5 class="page-title">刪除業務Key單</h5>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <form action="{{ route('sale.data.del', $data->id) }}" method="POST" id="your-form" enctype="multipart/form-data"
            data-plugin="dropzone" data-previews-container="#file-previews"
            data-upload-preview-template="#uploadPreviewTemplate">
            @csrf
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase bg-light  p-2 mt-0 mb-3">基本資訊</h5>

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
                                    <select class="form-select" name="pay_id" required>
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
                                    <input type="text" class="form-control" id="sale_on" name="sale_on"
                                        value="{{ $data->sale_on }}" required>
                                </div>
                                <div class="mb-3 col-md-4">
                                    <label for="sale_date" class="form-label">日期<span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="sale_date" name="sale_date"
                                        value="{{ $data->sale_date }}" required>
                                </div>
                                <div class="mb-3 col-md-4 not_memorial_show">
                                    <label for="customer_id" class="form-label">客戶名稱<span
                                            class="text-danger">*</span></label>
                                    <select id="type" class="form-select" name="customer_id">
                                        @if (isset($data->cust_name))
                                            @foreach ($customers as $customer)
                                                <option value="{{ $customer->id }}"
                                                    @if ($data->customer_id == $customer->id) selected @endif>{{ $customer->name }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option value="null">*客戶姓名須重新登入*</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="mb-3 col-md-4 not_final_show not_memorial_show">
                                    <label for="pet_name" class="form-label">寵物名稱<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="pet_name" name="pet_name"
                                        value="{{ $data->pet_name }}">
                                </div>
                                <div class="mb-3 col-md-4 not_final_show not_memorial_show">
                                    <label for="variety" class="form-label">寵物品種<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="variety" name="variety"
                                        value="{{ $data->variety }}">
                                </div>
                                <div class="mb-3 col-md-4 not_final_show not_memorial_show">
                                    <label for="kg" class="form-label">公斤數<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="kg" name="kg"
                                        value="{{ $data->kg }}">
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
                                            @if (isset($sale_company->company_name))
                                                （{{ $sale_company->company_name->name }}）
                                            @elseif (isset($sale_company->self_name))
                                                （{{ $sale_company->self_name->name }}）
                                            @else
                                                <b style="color: red;">（來源公司須重新至拜訪管理新增公司資料）</b>
                                            @endif
                                        @endif
                                    </label>
                                    <input list="source_company_name_list_q" class="form-control"
                                        id="source_company_name_q" name="source_company_name_q"
                                        placeholder="請輸入醫院、禮儀社、美容院、繁殖場、狗園名稱"
                                        @if (isset($sale_company)) 
                                        value="@if (isset($sale_company))@if (isset($sale_company->company_name))（{{ $sale_company->company_name->name }}）@elseif (isset($sale_company->self_name))（{{ $sale_company->self_name->name }}）@else<b style="color: red;">（來源公司須重新至拜訪管理新增公司資料）</b>@endif @endif" @endif>
                                    <datalist id="source_company_name_list_q">
                                    </datalist>
                                </div>
                                <div class="mb-3 col-md-4 not_final_show not_memorial_show">
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
                                    <input type="text" class="form-control total_number" id="plan_price"
                                        name="plan_price" value="{{ $data->plan_price }}">
                                </div>
                                {{-- <div class="mb-3 col-md-4" id="final_price">
                            <label for="plan_price" class="form-label">收款金額<span class="text-danger">*</span></label>
                            <input type="text" class="form-control total_number"  name="final_price" value="{{ $data->pay_price }}" >
                        </div> --}}
                                <div class="mb-3 col-md-4 not_final_show not_memorial_show">
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
                                <div class="mb-3 col-md-4">
                                    <label for="user_id" class="form-label">服務專員<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="user_id" name="user_id"
                                        value="{{ $data->user_name->name }}" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="mb-1 mt-1">
                                    <div class="form-check" id="send_div">
                                        <input type="checkbox" class="form-check-input" id="send" name="send"
                                            @if ($data->send == 1) checked value="1" @endif>
                                        <label class="form-check-label" for="send"><b>親送</b></label>
                                    </div>
                                </div>
                                <div class="mb-1 mt-1" id="connector_div">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="connector_address"
                                            name="connector_address"
                                            @if ($data->connector_address == 1) checked value="1" @else  value="0" @endif>
                                        <label class="form-check-label" for="connector_address"><b>接體地址不為客戶地址</b></label>
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
                                        <input type="checkbox" class="form-check-input" id="connector_hospital_address"
                                            name="connector_hospital_address"
                                            @if (isset($data->hospital_address)) checked value="1" @else  value="0" @endif>
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
                                                @foreach ($source_companys as $source_company)
                                                    <option value="{{ $source_company->id }}"
                                                        @if ($source_company->id == $data->hospital_address) selected @endif>
                                                        @if (isset($source_company->group) && $source_company->group)
                                                            （{{ $source_company->group->name }}）{{ $source_company->name }}（{{ $source_company->mobile }}）
                                                        @else
                                                            {{ $source_company->name }}（{{ $source_company->mobile }}）
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
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
                                                                alt="{{ $key }}" class="mobile form-select"
                                                                name="select_proms[]" onchange="chgItems(this)">
                                                                <option value="" selected>請選擇</option>
                                                                <option value="A"
                                                                    @if ($sale_prom->prom_type == 'A') selected @endif>安葬處理
                                                                </option>
                                                                <option value="B"
                                                                    @if ($sale_prom->prom_type == 'B') selected @endif>後續處理
                                                                </option>
                                                                <option value="C"
                                                                    @if ($sale_prom->prom_type == 'C') selected @endif>其他處理
                                                                </option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select id="prom_{{ $key }}"
                                                                class="mobile form-select" name="prom[]">
                                                                @foreach ($proms as $prom)
                                                                    <option value="{{ $prom->id }}"
                                                                        @if ($sale_prom->prom_id == $prom->id) selected @endif>
                                                                        {{ $prom->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="mobile form-control total_number"
                                                                id="prom_total_{{ $key }}" name="prom_total[]"
                                                                value="{{ $sale_prom->prom_total }}">
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
                                                    <th></th>
                                                    <th>金紙名稱<span class="text-danger">*</span></th>
                                                    <th>數量<span class="text-danger">*</span></th>
                                                    <th>售價<span class="text-danger">*</span></th>
                                                </tr>
                                            </thead>
                                            <tbody>
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
                                                                onchange="chgNums(this)">
                                                        </td>
                                                        <td>
                                                            <input type="text" class="mobile form-control total_number"
                                                                id="gdpaper_total_{{ $key }}"
                                                                name="gdpaper_total[]"
                                                                value="{{ $sale_gdpaper->gdpaper_total }}">
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

            {{-- <div class="row" id="souvenir_div">
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
                                                @foreach ($sale_souvenirs as $key => $sale_souvenir)
                                                    <tr id="row-{{ $key }}">
                                                        <td class="text-center">
                                                            @if ($key == 0)
                                                                <button type="button"
                                                                    class="ibtnAdd_souvenir demo-delete-row btn btn-primary btn-sm btn-icon"><i
                                                                        class="fa fas fa-plus"></i></button>
                                                            @else
                                                                <button type="button"
                                                                    class="ibtnDel_souvenir demo-delete-row btn btn-danger btn-sm btn-icon"><i
                                                                        class="fa fa-times"></i></button>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <select id="souvenir_id_{{ $key }}"
                                                                alt="{{ $key }}" class="mobile form-select"
                                                                name="souvenir_ids[]">
                                                                <option value="" selected>請選擇...</option>
                                                                @foreach ($souvenirs as $souvenir)
                                                                    <option value="{{ $souvenir->id }}"
                                                                        @if ($souvenir->id == $sale_souvenir->prom_id) selected @endif>
                                                                        {{ $souvenir->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" alt="{{ $key }}"
                                                                class="mobile form-control"
                                                                id="souvenir_name_{{ $key }}"
                                                                name="souvenir_name[]"
                                                                value="{{ $sale_souvenir->name }}">
                                                        </td>
                                                        <td>
                                                            <input type="text" class="mobile form-control total_number"
                                                                id="souvenir_total_{{ $key }}"
                                                                name="souvenir_total[]"
                                                                value="{{ $sale_souvenir->total }}">
                                                        </td>
                                                        <td>
                                                            <input type="text" class="mobile form-control"
                                                                id="souvenir_shape_{{ $key }}"
                                                                name="souvenir_shape[]"
                                                                value="{{ $sale_souvenir->shape }}">
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
            </div> --}}

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
                                    <input type="text" class="form-control" id="cash_price" name="cash_price"
                                        value="{{ $data->cash_price }}">
                                </div>
                                <div class="mb-3 col-md-4" id="transfer_price_div">
                                    <label for="pay_price" class="form-label">匯款收款<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="transfer_price" name="transfer_price"
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
                                    </select>
                                </div>
                                <div class="mb-3 col-md-4" id="transfer_number_div">
                                    <label for="pay_price" class="form-label">匯款後四碼<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="transfer_number"
                                        name="transfer_number" value="{{ $data->transfer_number }}">
                                </div>
                                <div class="mb-3 col-md-4">
                                    <label for="pay_price" class="form-label">本次收款<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="pay_price" name="pay_price"
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
                        <button type="submit" class="btn w-sm btn-success waves-effect waves-light">刪除</button>
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
    <!-- third party js -->
    <script src="{{ asset('assets/js/twzipcode-1.4.1-min.js') }}"></script>
    <script src="{{ asset('assets/js/twzipcode.js') }}"></script>
    <!-- third party js ends -->


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
                            current_id: saleId
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
    
        $('select[name="type"]').on('change', function() {
            if($(this).val() == 'H' || $(this).val() == 'B' || $(this).val() == 'Salon' || $(this).val() == 'G' || $(this).val() == 'dogpark' || $(this).val() == 'other' || $(this).val() == 'self'){
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
            });
            $.ajaxSetup({ headers: { 'csrftoken' : '{{ csrf_token() }}' } });
    </script>
@endsection
