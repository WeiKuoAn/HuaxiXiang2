@extends('layouts.vertical', ["page_title"=> "新增設備進貨"])

@section('css')
<style>
    .item-row {
        border-bottom: 1px solid #e3ebf6;
        padding-bottom: 10px;
        margin-bottom: 10px;
    }
    .item-row:last-child {
        border-bottom: none;
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
                        <li class="breadcrumb-item"><a href="{{ route('crematorium.equipment.index') }}">火化爐管理</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('crematorium.purchases.index') }}">設備進貨管理</a></li>
                        <li class="breadcrumb-item active">新增進貨記錄</li>
                    </ol>
                </div>
                <h4 class="page-title">新增設備進貨記錄</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        <strong>錯誤！</strong>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('crematorium.purchases.store') }}" method="POST">
        @csrf

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="text-uppercase bg-light p-2 mt-0 mb-3">
                            <i class="mdi mdi-file-document-outline me-1"></i>
                            進貨基本資訊
                        </h5>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="purchase_number" class="form-label">
                                    進貨單號 <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="purchase_number" 
                                       value="{{ $purchaseNumber }}" readonly>
                                <small class="text-muted">系統自動生成</small>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="purchase_date" class="form-label">
                                    進貨日期 <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control @error('purchase_date') is-invalid @enderror" 
                                       id="purchase_date" name="purchase_date" 
                                       value="{{ old('purchase_date', date('Y-m-d')) }}" required>
                                @error('purchase_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="notes" class="form-label">
                                    備註
                                </label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          id="notes" name="notes" rows="2" 
                                          placeholder="其他說明或備註">{{ old('notes') }}</textarea>
                                @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5 class="text-uppercase bg-light p-2 mt-0 mb-3">
                            <i class="mdi mdi-package-variant me-1"></i>
                            進貨明細
                            <button type="button" class="btn btn-sm btn-success float-end" id="addRow">
                                <i class="mdi mdi-plus"></i> 新增設備
                            </button>
                        </h5>

                        <div id="itemsContainer">
                            <div class="item-row" data-row="0">
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <label class="form-label">設備類型 <span class="text-danger">*</span></label>
                                        <select class="form-select equipment-select" name="equipment_type_id[]" required>
                                            <option value="">請選擇設備類型</option>
                                            @foreach($equipmentTypes as $equipmentType)
                                            <option value="{{ $equipmentType->id }}"
                                                    data-stock-new="{{ $equipmentType->stock_new ?? 0 }}"
                                                    data-stock-usable="{{ $equipmentType->stock_usable ?? 0 }}">
                                                {{ $equipmentType->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                        <small class="stock-info text-muted"></small>
                                    </div>

                                    <div class="col-md-2 mb-2">
                                        <label class="form-label">數量 <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control quantity-input" 
                                               name="quantity[]" min="1" value="1" required>
                                    </div>

                                    <div class="col-md-2 mb-2">
                                        <label class="form-label">單價</label>
                                        <input type="number" class="form-control unit-price-input" 
                                               name="unit_price[]" min="0" step="1" placeholder="">
                                    </div>

                                    <div class="col-md-2 mb-2">
                                        <label class="form-label">小計</label>
                                        <input type="text" class="form-control subtotal-display" readonly>
                                    </div>

                                    <div class="col-md-2 mb-2">
                                        <label class="form-label">操作</label>
                                        <button type="button" class="btn btn-danger btn-sm w-100 remove-row">
                                            <i class="mdi mdi-delete"></i> 刪除
                                        </button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 mb-2">
                                        <label class="form-label">明細備註</label>
                                        <input type="text" class="form-control" name="item_notes[]" placeholder="此項目的備註">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <div class="row align-items-center">
                                        <div class="col-md-9">
                                            <h6 class="mb-0">
                                                <i class="mdi mdi-calculator me-1"></i>
                                                進貨總金額：<span id="grandTotal" class="text-primary fw-bold">0.00</span> 元
                                            </h6>
                                        </div>
                                        <div class="col-md-3 text-end">
                                            <button type="submit" class="btn btn-success">
                                                <i class="mdi mdi-check me-1"></i> 確認新增
                                            </button>
                                            <a href="{{ route('crematorium.purchases.index') }}" class="btn btn-secondary">
                                                <i class="mdi mdi-arrow-left me-1"></i> 取消
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

</div>
<!-- container -->
@endsection

@section('script')
<script>
$(document).ready(function() {
    let rowIndex = 0;

    // 新增設備行
    $('#addRow').on('click', function() {
        rowIndex++;
        const newRow = `
            <div class="item-row" data-row="${rowIndex}">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <label class="form-label">設備類型 <span class="text-danger">*</span></label>
                        <select class="form-select equipment-select" name="equipment_type_id[]" required>
                            <option value="">請選擇設備類型</option>
                            @foreach($equipmentTypes as $equipmentType)
                            <option value="{{ $equipmentType->id }}"
                                    data-stock-new="{{ $equipmentType->stock_new ?? 0 }}"
                                    data-stock-usable="{{ $equipmentType->stock_usable ?? 0 }}">
                                {{ $equipmentType->name }}
                            </option>
                            @endforeach
                        </select>
                        <small class="stock-info text-muted"></small>
                    </div>

                    <div class="col-md-2 mb-2">
                        <label class="form-label">數量 <span class="text-danger">*</span></label>
                        <input type="number" class="form-control quantity-input" 
                               name="quantity[]" min="1" value="1" required>
                    </div>

                    <div class="col-md-2 mb-2">
                        <label class="form-label">單價</label>
                        <input type="number" class="form-control unit-price-input" 
                               name="unit_price[]" min="0" step="1" placeholder="">
                    </div>

                    <div class="col-md-2 mb-2">
                        <label class="form-label">小計</label>
                        <input type="text" class="form-control subtotal-display" readonly>
                    </div>

                    <div class="col-md-2 mb-2">
                        <label class="form-label">操作</label>
                        <button type="button" class="btn btn-danger btn-sm w-100 remove-row">
                            <i class="mdi mdi-delete"></i> 刪除
                        </button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-2">
                        <label class="form-label">明細備註</label>
                        <input type="text" class="form-control" name="item_notes[]" placeholder="此項目的備註">
                    </div>
                </div>
            </div>
        `;
        $('#itemsContainer').append(newRow);
    });

    // 刪除設備行
    $(document).on('click', '.remove-row', function() {
        if ($('.item-row').length > 1) {
            $(this).closest('.item-row').remove();
            calculateGrandTotal();
        } else {
            alert('至少需要保留一個設備項目！');
        }
    });

    // 設備選擇時顯示庫存
    $(document).on('change', '.equipment-select', function() {
        const selectedOption = $(this).find('option:selected');
        const stockNew = selectedOption.data('stock-new');
        const stockUsable = selectedOption.data('stock-usable');
        const stockInfo = $(this).siblings('.stock-info');
        
        if (selectedOption.val()) {
            stockInfo.html(
                '<i class="mdi mdi-information-outline"></i> ' +
                '目前庫存：全新 <strong>' + stockNew + '</strong> / 堪用 <strong>' + stockUsable + '</strong>'
            );
        } else {
            stockInfo.html('');
        }
    });

    // 計算小計
    $(document).on('input', '.quantity-input, .unit-price-input', function() {
        const row = $(this).closest('.item-row');
        const quantity = parseFloat(row.find('.quantity-input').val()) || 0;
        const unitPrice = parseFloat(row.find('.unit-price-input').val()) || 0;
        const subtotal = quantity * unitPrice;
        
        row.find('.subtotal-display').val(subtotal.toFixed(0));
        calculateGrandTotal();
    });

    // 計算總金額
    function calculateGrandTotal() {
        let total = 0;
        $('.item-row').each(function() {
            const quantity = parseFloat($(this).find('.quantity-input').val()) || 0;
            const unitPrice = parseFloat($(this).find('.unit-price-input').val()) || 0;
            total += quantity * unitPrice;
        });
        $('#grandTotal').text(total.toFixed(0));
    }

    // 初始化
    calculateGrandTotal();
});
</script>
@endsection
