<!-- Modal 版本的確認對帳表單 - 只包含內容部分 -->
<link href="{{ asset('assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/css/customization.css') }}" id="app-style" rel="stylesheet" type="text/css" />

<style>
    @media screen and (max-width:768px) {
        .mobile {
            width: 180px;
        }
    }
</style>

<form action="{{ route('sale.data.check', $data->id) }}" method="POST" id="modal-check-form">
    @csrf
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="mt-0 mb-4 pb-2" style="font-size: 1.5rem; font-weight: 700; color: #2c3e50; border-bottom: 3px solid #3498db;">
                        基本資訊
                    </h4>
                    <div class="row">
                        <div class="mb-3 col-md-4">
                            <label for="type_list" class="form-label">案件類別選擇<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" value="{{ $data->type_list == 'dispatch' ? '派件單' : '追思單' }}" readonly>
                        </div>
                        <div class="mb-3 col-md-4">
                            <label for="pay_id" class="form-label">支付類別<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" value="@if($data->pay_id == 'A')一次付清@elseif($data->pay_id == 'C')訂金@elseif($data->pay_id == 'E')追加@elseif($data->pay_id == 'D')尾款@endif" readonly>
                        </div>
                        <div class="mb-3 col-md-4">
                            <label for="sale_on" class="form-label">單號<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" value="{{ $data->sale_on }}" readonly>
                        </div>
                        <div class="mb-3 col-md-4">
                            <label for="sale_date" class="form-label">日期<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" value="{{ $data->sale_date }}" readonly>
                        </div>
                        <div class="mb-3 col-md-4">
                            <label for="customer_id" class="form-label">客戶名稱<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" value="No.{{ $data->customer_id }} {{ $customers->where('id', $data->customer_id)->first()->name ?? '' }}（{{ $customers->where('id', $data->customer_id)->first()->mobile ?? '' }}）" readonly>
                        </div>
                        <div class="mb-3 col-md-4">
                            <label for="pet_name" class="form-label">寵物名稱<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" value="{{ $data->pet_name }}" readonly>
                        </div>
                        
                        @if($data->type_list != 'memorial' && !in_array($data->pay_id, ['D', 'E']))
                        <div class="mb-3 col-md-4 not_final_show not_memorial_show">
                            <label for="variety" class="form-label">寵物品種</label>
                            <input type="text" class="form-control" value="{{ $data->variety }}" readonly>
                        </div>
                        <div class="mb-3 col-md-4 not_final_show not_memorial_show">
                            <label for="kg" class="form-label">公斤數</label>
                            <input type="text" class="form-control" value="{{ $data->kg }}" readonly>
                        </div>
                        <div class="mb-3 col-md-4 not_final_show not_memorial_show">
                            <label for="type" class="form-label">案件來源</label>
                            <input type="text" class="form-control" value="{{ $data->source_type->name ?? $data->type }}" readonly>
                        </div>
                        @endif
                        
                        @if(isset($sale_company) && !in_array($data->pay_id, ['D', 'E']))
                        <div class="mb-3 col-md-4 not_final_show">
                            <label for="source_company_id" class="form-label">來源公司名稱</label>
                            @php
                                $companyDisplayValue = '';
                                if(isset($sale_company) && $sale_company->company_id) {
                                    if($sale_company->type == 'self') {
                                        if(isset($sale_company->self_name)) {
                                            $companyDisplayValue = '（員工）' . $sale_company->self_name->name . '（' . $sale_company->self_name->mobile . '）';
                                        }
                                    } else {
                                        if(isset($sale_company->company_name)) {
                                            if(isset($sale_company->company_name->group) && $sale_company->company_name->group) {
                                                $companyDisplayValue = '（' . $sale_company->company_name->group->name . '）' . $sale_company->company_name->name . '（' . $sale_company->company_name->mobile . '）';
                                            } else {
                                                $companyDisplayValue = $sale_company->company_name->name . '（' . $sale_company->company_name->mobile . '）';
                                            }
                                        }
                                    }
                                }
                            @endphp
                            <input type="text" class="form-control" value="{{ $companyDisplayValue }}" readonly>
                        </div>
                        @endif
                        
                        @if($data->plan_id && $data->type_list != 'memorial' && $data->pay_id != 'D')
                        <div class="mb-3 col-md-4 not_memorial_show plan">
                            <label for="plan_id" class="form-label">方案選擇</label>
                            <input type="text" class="form-control" value="{{ $plans->where('id', $data->plan_id)->first()->name ?? '' }}" readonly>
                        </div>
                        @endif
                        
                        <div class="mb-3 col-md-4" id="religion_field" style="display: none;">
                            <label for="religion" class="form-label">宗教信仰<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" value="@if($data->religion == 'buddhism_taoism')佛道教@elseif($data->religion == 'christianity')基督教@elseif($data->religion == 'catholicism')天主教@elseif($data->religion == 'none')無宗教@elseif($data->religion == 'other')其他@endif" readonly>
                            @if($data->religion == 'other' && isset($data->religion_other))
                                <div class="mt-2">
                                    <input type="text" class="form-control" value="{{ $data->religion_other }}" readonly placeholder="其他宗教信仰">
                                </div>
                            @endif
                            <div id="religion_reminder" class="mt-1" style="display: none;">
                                <small class="text-danger">提醒：資財袋為佛道教用品</small>
                            </div>
                        </div>
                        
                        <div class="mb-3 col-md-4" id="death_date_field" style="display: none;">
                            <label for="death_date" class="form-label">往生日期</label>
                            <input type="text" class="form-control" value="{{ $data->death_date }}" readonly>
                        </div>
                        
                        <div class="mb-3 col-md-4">
                            <label for="user_id" class="form-label">服務專員</label>
                            <input type="text" class="form-control" value="{{ $data->user_name->name }}" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(count($sale_proms) > 0)
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="mt-0 mb-4 pb-2" style="font-size: 1.5rem; font-weight: 700; color: #2c3e50; border-bottom: 3px solid #9b59b6;">
                        後續處理
                    </h4>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>處理方式</th>
                                    <th>名稱</th>
                                    <th>商品資訊</th>
                                    <th>售價</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sale_proms as $prom)
                                <tr>
                                    <td>
                                        @if($prom->prom_type == 'A')安葬處理
                                        @elseif($prom->prom_type == 'B')後續處理
                                        @elseif($prom->prom_type == 'C')其他處理
                                        @endif
                                    </td>
                                    <td>
                                        {{ $prom->prom_name->name ?? '' }}
                                        @if($prom->comment)
                                        <br><small class="text-muted">備註：{{ $prom->comment }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($prom->souvenir_data))
                                            @if($prom->souvenir_data->souvenir_type == null)
                                                {{-- 關聯商品 --}}
                                                @php
                                                    $product = \App\Models\Product::find($prom->souvenir_data->product_name);
                                                    $variant = null;
                                                    if($prom->souvenir_data->product_variant_id) {
                                                        $variant = \App\Models\ProductVariant::find($prom->souvenir_data->product_variant_id);
                                                    }
                                                @endphp
                                                <div class="text-muted small">
                                                    <strong>商品：</strong>{{ $product->name ?? '' }}
                                                    @if($variant)
                                                        <br><strong>細項：</strong>{{ $variant->variant_name }}@if($variant->color) ({{ $variant->color }})@endif
                                                    @endif
                                                    @if($prom->souvenir_data->product_num)
                                                        <br><strong>數量：</strong>{{ $prom->souvenir_data->product_num }}
                                                    @endif
                                                    @if($prom->souvenir_data->comment)
                                                        <br><strong>備註：</strong>{{ $prom->souvenir_data->comment }}
                                                    @endif
                                                </div>
                                            @else
                                                {{-- 自訂商品 --}}
                                                @php
                                                    $souvenirType = \App\Models\SouvenirType::find($prom->souvenir_data->souvenir_type);
                                                @endphp
                                                <div class="text-muted small">
                                                    <strong>類型：</strong>{{ $souvenirType->name ?? '' }}
                                                    <br><strong>商品名稱：</strong>{{ $prom->souvenir_data->product_name }}
                                                    @if($prom->souvenir_data->product_num)
                                                        <br><strong>數量：</strong>{{ $prom->souvenir_data->product_num }}
                                                    @endif
                                                    @if($prom->souvenir_data->comment)
                                                        <br><strong>備註：</strong>{{ $prom->souvenir_data->comment }}
                                                    @endif
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ number_format($prom->prom_total) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(count($sale_gdpapers) > 0)
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="mt-0 mb-4 pb-2" style="font-size: 1.5rem; font-weight: 700; color: #2c3e50; border-bottom: 3px solid #e74c3c;">
                        金紙選購
                    </h4>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>金紙名稱</th>
                                    <th>數量</th>
                                    <th>售價</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sale_gdpapers as $gdpaper)
                                <tr>
                                    <td>{{ $gdpaper->gdpaper_name->name ?? '' }}</td>
                                    <td>{{ $gdpaper->gdpaper_num }}</td>
                                    <td>{{ number_format($gdpaper->gdpaper_total) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="mt-0 mb-4 pb-2" style="font-size: 1.5rem; font-weight: 700; color: #2c3e50; border-bottom: 3px solid #27ae60;">
                        付款方式
                    </h4>
                    <div class="row">
                        <div class="mb-3 col-md-12">
                            <h2>應收金額<span class="text-danger">{{ number_format($data->total) }}</span>元</h2>
                        </div>
                        <div class="mb-3 col-md-4">
                            <label for="pay_method" class="form-label">收款方式</label>
                            <input type="text" class="form-control" value="@if($data->pay_method == 'A')現金@elseif($data->pay_method == 'B')匯款@elseif($data->pay_method == 'C')現金與匯款@endif" readonly>
                        </div>
                        @if($data->cash_price > 0)
                        <div class="mb-3 col-md-4">
                            <label for="cash_price" class="form-label">現金收款</label>
                            <input type="text" class="form-control" value="{{ number_format($data->cash_price) }}" readonly>
                        </div>
                        @endif
                        @if($data->transfer_price > 0)
                        <div class="mb-3 col-md-4">
                            <label for="transfer_price" class="form-label">匯款收款</label>
                            <input type="text" class="form-control" value="{{ number_format($data->transfer_price) }}" readonly>
                        </div>
                        @endif
                        @php
                            $shouldShowTransferDetail = in_array($data->pay_method, ['B', 'C']) || !empty($data->transfer_channel) || !empty($data->transfer_number);
                        @endphp
                        @if($shouldShowTransferDetail)
                        <div class="mb-3 col-md-4">
                            <label for="transfer_channel" class="form-label">匯款管道</label>
                            <input type="text" class="form-control" value="{{ $data->transfer_channel ?? '—' }}" readonly>
                        </div>
                        <div class="mb-3 col-md-4">
                            <label for="transfer_number" class="form-label">匯款後四碼</label>
                            <input type="text" class="form-control" value="{{ $data->transfer_number ?? '—' }}" readonly>
                        </div>
                        @endif
                        <div class="mb-3 col-md-4">
                            <label for="pay_price" class="form-label">本次收款</label>
                            <input type="text" class="form-control" value="{{ number_format($data->pay_price) }}" readonly>
                        </div>
                        @if($data->comm)
                        <div class="mb-3 col-md-12">
                            <label class="form-label">備註</label>
                            <textarea class="form-control" rows="3" readonly>{{ $data->comm }}</textarea>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="text-center mb-3">
                @if (Auth::user()->level != '2' || Auth::user()->job_id == 9)
                    @if ($data->status == '1')
                        <button type="button" class="btn w-sm btn-light waves-effect" data-bs-dismiss="modal">取消</button>
                        <button type="submit" class="btn w-sm btn-success waves-effect waves-light" value="check" name="admin_check" onclick="return confirm('是否已確定對帳，若要取消對帳，請進行撤回')">確定對帳</button>
                    @elseif($data->status == '3')
                        <button type="button" class="btn w-sm btn-light waves-effect" data-bs-dismiss="modal">取消</button>
                        <button type="submit" class="btn w-sm btn-danger waves-effect" value="not_check" name="admin_check">撤回對帳</button>
                        <button type="submit" class="btn w-sm btn-success waves-effect waves-light" value="check" name="admin_check" onclick="return confirm('是否已確定對帳，若要取消對帳，請進行撤回')">確定對帳</button>
                    @elseif($data->status == '9')
                        <button type="button" class="btn w-sm btn-light waves-effect" data-bs-dismiss="modal">取消</button>
                        <button type="submit" class="btn w-sm btn-success waves-effect waves-light" value="reset" name="admin_check">還原</button>
                    @else
                        <button type="button" class="btn w-sm btn-light waves-effect" data-bs-dismiss="modal">關閉</button>
                    @endif
                @else
                    @if ($data->status == '1' && $data->user_id == Auth::user()->id)
                        <button type="button" class="btn w-sm btn-light waves-effect" data-bs-dismiss="modal">取消</button>
                        <button type="submit" class="btn w-sm btn-success waves-effect waves-light" value="usercheck" name="user_check" onclick="return confirm('是否已確定對帳，若要取消對帳，請進行撤回')">確定對帳</button>
                    @elseif($data->status == '3' || $data->status == '9')
                        @if($data->status == '3' && $data->user_id != Auth::user()->id)
                            <button type="button" class="btn w-sm btn-light waves-effect" data-bs-dismiss="modal">取消</button>
                            <button type="submit" class="btn w-sm btn-danger waves-effect" value="not_check" name="admin_check">撤回對帳</button>
                            <button type="submit" class="btn w-sm btn-success waves-effect waves-light" value="check" name="admin_check" onclick="return confirm('是否已確定對帳，若要取消對帳，請進行撤回')">確定對帳</button>
                        @elseif($data->status == '9')
                            <button type="button" class="btn w-sm btn-light waves-effect" data-bs-dismiss="modal">取消</button>
                            @if(Auth::user()->job_id == 10 || Auth::user()->job_id == 3)
                                @if($data->user_id != Auth::user()->id)
                                    <button type="submit" class="btn w-sm btn-success waves-effect waves-light" value="reset" name="admin_check">還原</button>
                                @endif
                            @endif
                        @else
                            <button type="button" class="btn w-sm btn-light waves-effect" data-bs-dismiss="modal">關閉</button>
                        @endif
                    @else
                        <button type="button" class="btn w-sm btn-light waves-effect" data-bs-dismiss="modal">關閉</button>
                    @endif
                @endif
            </div>
        </div>
    </div>
</form>

<script>
    // Modal 專用的初始化腳本
    $(document).ready(function() {
        var type_list = '{{ $data->type_list }}';
        var payIdValue = '{{ $data->pay_id }}';
        var payMethod = '{{ $data->pay_method }}';
        var planId = '{{ $data->plan_id }}';
        var religion = '{{ $data->religion }}';
        var deathDate = '{{ $data->death_date }}';
        
        console.log('Modal form loaded:', { type_list, payIdValue, payMethod, planId, religion, deathDate });
        
        // 初始化宗教信仰和往生日期欄位顯示
        initializeReligionAndDeathDateFields();
        
        // 注意：不要使用 disabled，因為 disabled 的欄位不會被提交
        // 所有欄位已經在 HTML 中設置為 readonly
        
        // 初始化宗教信仰和往生日期欄位顯示
        function initializeReligionAndDeathDateFields() {
            console.log('初始化宗教信仰和往生日期欄位:', { type_list, payIdValue, planId, religion, deathDate });
            
            // 根據案件類別和支付類別決定是否顯示宗教欄位
            if (type_list === 'dispatch' && (payIdValue === 'A' || payIdValue === 'C')) {
                // 將 planId 轉換為字串進行比較
                var planIdStr = String(planId);
                
                if (planIdStr === '1' || planIdStr === '2' || planIdStr === '3') {
                    // 個人、團體、浪浪方案：顯示宗教欄位
                    $('#religion_field').show();
                    console.log('顯示宗教欄位 - 方案ID:', planIdStr);
                    
                    // 如果有宗教信仰資料，顯示宗教提醒
                    if (religion && religion !== 'buddhism_taoism') {
                        $('#religion_reminder').show();
                    }
                } else {
                    // 其他方案：不顯示宗教欄位
                    $('#religion_field').hide();
                    console.log('隱藏宗教欄位 - 方案ID:', planIdStr);
                }
            } else {
                // 非派件單或非一次付清/訂金，隱藏宗教欄位
                $('#religion_field').hide();
                console.log('隱藏宗教欄位 - 非派件單或非一次付清/訂金');
            }
            
            // 根據案件類別、支付類別、方案和宗教決定是否顯示往生日期欄位
            if (type_list === 'dispatch' && (payIdValue === 'A' || payIdValue === 'C')) {
                var planIdStr = String(planId);
                
                // 浪浪方案永遠不顯示往生日期
                if (planIdStr === '3') {
                    $('#death_date_field').hide();
                    console.log('隱藏往生日期欄位 - 浪浪方案');
                } else if (planIdStr === '1' || planIdStr === '2') {
                    // 個人、團體方案：所有宗教都可以填寫往生日期（非必填）
                    $('#death_date_field').show();
                    console.log('顯示往生日期欄位 - 個人/團體方案（所有宗教都可填寫，非必填）');
                } else {
                    // 其他方案：不顯示往生日期
                    $('#death_date_field').hide();
                    console.log('隱藏往生日期欄位 - 其他方案');
                }
            } else {
                // 非派件單或非一次付清/訂金，隱藏往生日期欄位
                $('#death_date_field').hide();
                console.log('隱藏往生日期欄位 - 非派件單或非一次付清/訂金');
            }
        }
    });
</script>
