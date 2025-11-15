@extends('layouts.vertical', ['page_title' => '處理報修單'])

@section('css')
<style>
    .repair-detail-item {
        border: 1px solid #e9ecef;
        border-radius: 6px;
        padding: 15px;
        margin-bottom: 15px;
        background-color: #f8f9fa;
    }
    
    .action-options {
        margin-top: 10px;
        padding: 10px;
        background-color: #fff;
        border-radius: 4px;
    }
</style>
@endsection

@section('content')
<!-- Start Content-->
<div class="container-fluid">

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Huaxixiang</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">火化爐管理</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('crematorium.repairs.index') }}">報修單</a></li>
                        <li class="breadcrumb-item active">處理報修</li>
                    </ol>
                </div>
                <h4 class="page-title">處理報修單</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- 報修單資訊摘要 -->
                    <div class="alert alert-info mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <strong>報修單號：</strong>{{ $repair->repair_number }}
                            </div>
                            <div class="col-md-3">
                                <strong>報修人員：</strong>{{ $repair->reporter->name ?? '-' }}
                            </div>
                            <div class="col-md-3">
                                <strong>報修日期：</strong>{{ $repair->report_date->format('Y-m-d') }}
                            </div>
                            <div class="col-md-3">
                                <strong>狀態：</strong>
                                <span class="badge bg-{{ $repair->status_color }}">{{ $repair->status_text }}</span>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <strong>問題描述：</strong>{{ $repair->problem_description }}
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('crematorium.repairs.update', $repair->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <h5 class="mb-3">
                            <i class="mdi mdi-tools me-2"></i>處理報修設備
                        </h5>

                        @foreach($repair->repairDetails as $detail)
                            <div class="repair-detail-item" 
                                 data-detail-id="{{ $detail->id }}"
                                 data-exclude-inventory="{{ $detail->equipmentInstance->equipmentType->exclude_from_inventory ? 'true' : 'false' }}"
                                 data-stock-new="{{ $detail->equipmentInstance->equipmentType->stock_new }}"
                                 data-stock-usable="{{ $detail->equipmentInstance->equipmentType->stock_usable }}"
                                 data-equipment-name="{{ $detail->equipmentInstance->equipmentType->name }}">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-primary">
                                            <i class="mdi mdi-wrench me-1"></i>{{ $detail->equipmentInstance->equipmentType->name }}
                                        </h6>
                                        <small class="text-muted">
                                            {{ $detail->equipmentInstance->full_location }}
                                            @if($detail->equipmentInstance->equipmentType->exclude_from_inventory)
                                                <span class="badge bg-secondary badge-sm">不計庫存</span>
                                            @endif
                                        </small>
                                        @if($detail->problem_description)
                                            <p class="mt-2 mb-0">
                                                <strong class="small">問題描述：</strong>
                                                <span class="small">{{ $detail->problem_description }}</span>
                                            </p>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        @if(!$detail->equipmentInstance->equipmentType->exclude_from_inventory)
                                            <div class="mb-2">
                                                <label class="form-label small"><strong>庫存狀況：</strong></label>
                                                <div>
                                                    <span class="badge bg-primary">全新：{{ $detail->equipmentInstance->equipmentType->stock_new }}</span>
                                                    <span class="badge bg-info">堪用：{{ $detail->equipmentInstance->equipmentType->stock_usable }}</span>
                                                    <span class="badge bg-success">總計：{{ $detail->equipmentInstance->equipmentType->stock_total }}</span>
                                                </div>
                                            </div>
                                        @else
                                            <div class="mb-2">
                                                <label class="form-label small"><strong>庫存狀況：</strong></label>
                                                <div>
                                                    <span class="badge bg-secondary">此設備不列入庫存計算</span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- 處理方式選項 -->
                                <div class="action-options">
                                    <label class="form-label"><strong>處理方式：</strong></label><br>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" 
                                               name="detail_action[{{ $detail->id }}]" 
                                               id="detail_action_{{ $detail->id }}_repair" 
                                               value="repair"
                                               {{ $detail->action == 'repair' ? 'checked' : '' }}
                                               onchange="toggleReplacementOptions({{ $detail->id }})">
                                        <label class="form-check-label" for="detail_action_{{ $detail->id }}_repair">
                                            維修
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" 
                                               name="detail_action[{{ $detail->id }}]" 
                                               id="detail_action_{{ $detail->id }}_replace" 
                                               value="replace"
                                               {{ $detail->action == 'replace' ? 'checked' : '' }}
                                               onchange="toggleReplacementOptions({{ $detail->id }})">
                                        <label class="form-check-label" for="detail_action_{{ $detail->id }}_replace">
                                            更換
                                        </label>
                                    </div>
                                    
                                    <!-- 更換數量和類型 -->
                                    <div class="ms-2" id="replacement_options_{{ $detail->id }}" 
                                         style="display: none !important;">
                                        <input type="number" 
                                               class="form-control form-control-sm d-inline-block quantity-input" 
                                               id="detail_quantity_{{ $detail->id }}"
                                               name="detail_quantity[{{ $detail->id }}]" 
                                               value="{{ $detail->quantity ?? 1 }}" 
                                               min="1"
                                               disabled
                                               onchange="checkStock({{ $detail->id }})"
                                               style="width: 60px;">
                                        <span class="small ms-1">個</span>
                                        
                                        <select class="form-select form-select-sm d-inline-block ms-2 replacement-type-select" 
                                                id="detail_replacement_type_{{ $detail->id }}"
                                                name="detail_replacement_type[{{ $detail->id }}]" 
                                                disabled
                                                onchange="checkStock({{ $detail->id }})"
                                                style="width: 100px;">
                                            <option value="new" {{ ($detail->replacement_type ?? 'new') == 'new' ? 'selected' : '' }}>全新</option>
                                            <option value="usable" {{ ($detail->replacement_type ?? '') == 'usable' ? 'selected' : '' }}>堪用</option>
                                        </select>
                                    </div>
                                    
                                    <!-- 庫存警告訊息 -->
                                    <div id="stock_warning_{{ $detail->id }}" class="alert alert-danger mt-2" style="display: none; font-size: 0.875rem;">
                                        <i class="mdi mdi-alert me-1"></i>
                                        <span id="stock_warning_text_{{ $detail->id }}"></span>
                                    </div>
                                    
                                    <!-- 備註 -->
                                    <div class="mt-2">
                                        <label class="form-label small">備註</label>
                                        <textarea class="form-control form-control-sm" 
                                                  name="detail_notes[{{ $detail->id }}]" 
                                                  rows="2" 
                                                  placeholder="處理過程或其他說明...">{{ $detail->notes }}</textarea>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <!-- 整體備註 -->
                        <div class="mb-4">
                            <label for="notes" class="form-label">整體處理備註</label>
                            <textarea class="form-control" 
                                      id="notes" name="notes" rows="3" 
                                      placeholder="請輸入整體處理說明或備註...">{{ old('notes', $repair->notes) }}</textarea>
                        </div>

                        <div class="text-end">
                            <a href="{{ route('crematorium.repairs.index') }}" class="btn btn-secondary me-2">取消</a>
                            <button type="submit" class="btn btn-success">
                                <i class="mdi mdi-check me-1"></i>完成處理
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> <!-- container -->

<script>
function checkStock(detailId) {
    const detailItem = document.querySelector(`[data-detail-id="${detailId}"]`);
    const excludeInventory = detailItem.dataset.excludeInventory === 'true';
    const stockNew = parseInt(detailItem.dataset.stockNew);
    const stockUsable = parseInt(detailItem.dataset.stockUsable);
    const equipmentName = detailItem.dataset.equipmentName;
    
    const quantityInput = document.getElementById('detail_quantity_' + detailId);
    const replacementTypeSelect = document.getElementById('detail_replacement_type_' + detailId);
    const warningDiv = document.getElementById('stock_warning_' + detailId);
    const warningText = document.getElementById('stock_warning_text_' + detailId);
    
    // 如果不列入庫存，不需要檢查
    if (excludeInventory) {
        warningDiv.style.display = 'none';
        return true;
    }
    
    const quantity = parseInt(quantityInput.value) || 0;
    const replacementType = replacementTypeSelect.value;
    
    let hasStock = true;
    let message = '';
    
    if (replacementType === 'new') {
        if (stockNew < quantity) {
            hasStock = false;
            message = `庫存不足！「${equipmentName}」全新庫存僅剩 ${stockNew} 個，需要 ${quantity} 個。`;
        }
    } else if (replacementType === 'usable') {
        if (stockUsable < quantity) {
            hasStock = false;
            message = `庫存不足！「${equipmentName}」堪用庫存僅剩 ${stockUsable} 個，需要 ${quantity} 個。`;
        }
    }
    
    if (!hasStock) {
        warningText.textContent = message;
        warningDiv.style.display = 'block';
    } else {
        warningDiv.style.display = 'none';
    }
    
    return hasStock;
}

function toggleReplacementOptions(detailId) {
    const replaceRadio = document.getElementById('detail_action_' + detailId + '_replace');
    const replacementOptions = document.getElementById('replacement_options_' + detailId);
    const quantityInput = document.getElementById('detail_quantity_' + detailId);
    const replacementTypeSelect = document.getElementById('detail_replacement_type_' + detailId);
    const warningDiv = document.getElementById('stock_warning_' + detailId);
    
    if (replaceRadio && replaceRadio.checked) {
        // 顯示更換選項
        replacementOptions.style.setProperty('display', 'inline-block', 'important');
        // 啟用欄位
        if (quantityInput) {
            quantityInput.disabled = false;
            quantityInput.required = true;
        }
        if (replacementTypeSelect) {
            replacementTypeSelect.disabled = false;
            replacementTypeSelect.required = true;
        }
        // 檢查庫存
        checkStock(detailId);
    } else {
        // 隱藏更換選項
        replacementOptions.style.setProperty('display', 'none', 'important');
        // 禁用欄位（這樣就不會參與表單驗證）
        if (quantityInput) {
            quantityInput.disabled = true;
            quantityInput.required = false;
        }
        if (replacementTypeSelect) {
            replacementTypeSelect.disabled = true;
            replacementTypeSelect.required = false;
        }
        // 隱藏警告
        if (warningDiv) {
            warningDiv.style.display = 'none';
        }
    }
}

// 頁面載入時初始化
document.addEventListener('DOMContentLoaded', function() {
    // 先禁用並隱藏所有的更換選項
    const allReplacementOptions = document.querySelectorAll('[id^="replacement_options_"]');
    allReplacementOptions.forEach(option => {
        option.style.setProperty('display', 'none', 'important');
        
        // 禁用內部的輸入欄位
        const quantityInput = option.querySelector('.quantity-input');
        const typeSelect = option.querySelector('.replacement-type-select');
        if (quantityInput) {
            quantityInput.disabled = true;
            quantityInput.required = false;
        }
        if (typeSelect) {
            typeSelect.disabled = true;
            typeSelect.required = false;
        }
    });
    
    // 然後檢查已選中的「更換」選項，並顯示對應的欄位
    const replaceRadios = document.querySelectorAll('input[type="radio"][value="replace"]:checked');
    replaceRadios.forEach(radio => {
        const match = radio.name.match(/\[(\d+)\]/);
        if (match) {
            const detailId = match[1];
            toggleReplacementOptions(detailId);
        }
    });
    
    // 表單提交前驗證庫存
    document.querySelector('form').addEventListener('submit', function(e) {
        let hasStockIssue = false;
        const replaceRadios = document.querySelectorAll('input[type="radio"][value="replace"]:checked');
        
        replaceRadios.forEach(radio => {
            const match = radio.name.match(/\[(\d+)\]/);
            if (match) {
                const detailId = match[1];
                if (!checkStock(detailId)) {
                    hasStockIssue = true;
                }
            }
        });
        
        if (hasStockIssue) {
            e.preventDefault();
            alert('庫存不足！請檢查標示為紅色警告的項目，調整數量或更換類型。');
            // 滾動到第一個警告
            const firstWarning = document.querySelector('[id^="stock_warning_"]:not([style*="display: none"])');
            if (firstWarning) {
                firstWarning.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            return false;
        }
    });
});
</script>
@endsection

