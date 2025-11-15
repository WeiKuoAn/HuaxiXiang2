@extends('layouts.vertical', ['page_title' => '線上報修'])

@section('css')
<style>
    .equipment-row {
        border: 1px solid #e9ecef;
        border-radius: 6px;
        padding: 15px;
        margin-bottom: 15px;
        background-color: #f8f9fa;
    }
    
    .remove-btn {
        cursor: pointer;
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
                        <li class="breadcrumb-item active">線上報修</li>
                    </ol>
                </div>
                <h4 class="page-title">線上報修</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="mdi mdi-alert-circle text-danger me-2"></i>填寫報修資訊
                    </h5>

                    <form action="{{ route('crematorium.repairs.store') }}" method="POST" id="repairForm">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="report_date" class="form-label">報修日期 <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('report_date') is-invalid @enderror" 
                                           id="report_date" name="report_date" value="{{ old('report_date', date('Y-m-d')) }}" required>
                                    @error('report_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="problem_description" class="form-label">整體問題描述 <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('problem_description') is-invalid @enderror" 
                                      id="problem_description" name="problem_description" rows="4" 
                                      placeholder="請描述發現的問題（可簡述）" required>{{ old('problem_description') }}</textarea>
                            @error('problem_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- 選擇報修設備 -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <label class="form-label mb-0">選擇報修設備 <span class="text-danger">*</span></label>
                                    <p class="text-muted small mb-0">請選擇類別和設備，並填寫問題描述</p>
                                </div>
                                <button type="button" class="btn btn-primary btn-sm" onclick="addEquipmentRow()">
                                    <i class="mdi mdi-plus me-1"></i>新增設備
                                </button>
                            </div>
                            
                            @error('equipment_instance_ids')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror

                            <div id="equipment-rows-container">
                                <!-- 初始設備行 -->
                                <div class="equipment-row" data-row-index="0">
                                    <div class="row align-items-center">
                                        <div class="col-md-3">
                                            <label class="form-label small">類別 <span class="text-danger">*</span></label>
                                            <select class="form-select category-select" name="categories[]" onchange="updateEquipmentOptions(0)" required>
                                                <option value="">請選擇類別</option>
                                                <option value="furnace_1">一爐</option>
                                                <option value="furnace_2">二爐</option>
                                                <option value="ventilation">抽風</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small">設備 <span class="text-danger">*</span></label>
                                            <select class="form-select equipment-select" name="equipment_instance_ids[]" data-row-index="0" required>
                                                <option value="">請先選擇類別</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small">問題描述 <span class="text-danger">*</span></label>
                                            <textarea class="form-control" name="equipment_problems[]" rows="1" placeholder="請描述此設備的問題..." required></textarea>
                                        </div>
                                        <div class="col-md-1 text-end">
                                            <label class="form-label small d-block">&nbsp;</label>
                                            <button type="button" class="btn btn-danger btn-sm remove-btn" onclick="removeEquipmentRow(0)" style="display: none;">
                                                <i class="mdi mdi-delete"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @php
                            // 將設備實例資料轉換為 JSON 供 JavaScript 使用
                            $equipmentsByCategory = [];
                            foreach($equipments as $equipment) {
                                if (!isset($equipmentsByCategory[$equipment->category])) {
                                    $equipmentsByCategory[$equipment->category] = [];
                                }
                                $equipmentsByCategory[$equipment->category][] = [
                                    'id' => $equipment->id,
                                    'name' => $equipment->equipmentType->name,
                                    'location' => $equipment->full_location,
                                    'status' => $equipment->status,
                                ];
                            }
                        @endphp
                        <script>
                            const equipmentsByCategory = @json($equipmentsByCategory);
                        </script>

                        <div class="text-end">
                            <a href="{{ route('crematorium.repairs.index') }}" class="btn btn-secondary me-2">取消</a>
                            <button type="submit" class="btn btn-danger">
                                <i class="mdi mdi-send me-1"></i>提交報修
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> <!-- container -->

<script>
let rowIndex = 1; // 從 1 開始，因為第 0 行已經存在

// 更新設備選項
function updateEquipmentOptions(index) {
    const categorySelect = document.querySelector(`.equipment-row[data-row-index="${index}"] .category-select`);
    const equipmentSelect = document.querySelector(`.equipment-row[data-row-index="${index}"] .equipment-select`);
    
    const category = categorySelect.value;
    
    // 清空設備選項
    equipmentSelect.innerHTML = '<option value="">請選擇設備</option>';
    
    if (category && equipmentsByCategory[category]) {
        equipmentsByCategory[category].forEach(equipment => {
            const option = document.createElement('option');
            option.value = equipment.id;
            option.textContent = `${equipment.name} (${equipment.location})`;
            equipmentSelect.appendChild(option);
        });
    }
}

// 新增設備行
function addEquipmentRow() {
    const container = document.getElementById('equipment-rows-container');
    
    const newRow = document.createElement('div');
    newRow.className = 'equipment-row';
    newRow.setAttribute('data-row-index', rowIndex);
    
    newRow.innerHTML = `
        <div class="row align-items-center">
            <div class="col-md-3">
                <label class="form-label small">類別 <span class="text-danger">*</span></label>
                <select class="form-select category-select" name="categories[]" onchange="updateEquipmentOptions(${rowIndex})" required>
                    <option value="">請選擇類別</option>
                    <option value="furnace_1">一爐</option>
                    <option value="furnace_2">二爐</option>
                    <option value="ventilation">抽風</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small">設備 <span class="text-danger">*</span></label>
                <select class="form-select equipment-select" name="equipment_instance_ids[]" data-row-index="${rowIndex}" required>
                    <option value="">請先選擇類別</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small">問題描述 <span class="text-danger">*</span></label>
                <textarea class="form-control" name="equipment_problems[]" rows="1" placeholder="請描述此設備的問題..." required></textarea>
            </div>
            <div class="col-md-1 text-end">
                <label class="form-label small d-block">&nbsp;</label>
                <button type="button" class="btn btn-danger btn-sm remove-btn" onclick="removeEquipmentRow(${rowIndex})">
                    <i class="mdi mdi-delete"></i>
                </button>
            </div>
        </div>
    `;
    
    container.appendChild(newRow);
    rowIndex++;
    
    // 更新刪除按鈕的顯示
    updateRemoveButtons();
}

// 移除設備行
function removeEquipmentRow(index) {
    const row = document.querySelector(`.equipment-row[data-row-index="${index}"]`);
    if (row) {
        row.remove();
    }
    
    // 更新刪除按鈕的顯示
    updateRemoveButtons();
}

// 更新刪除按鈕的顯示狀態
function updateRemoveButtons() {
    const rows = document.querySelectorAll('.equipment-row');
    rows.forEach((row, index) => {
        const removeBtn = row.querySelector('.remove-btn');
        if (removeBtn) {
            // 如果只有一行，隱藏刪除按鈕
            if (rows.length === 1) {
                removeBtn.style.display = 'none';
            } else {
                removeBtn.style.display = 'inline-block';
            }
        }
    });
}

// 表單提交驗證
document.getElementById('repairForm').addEventListener('submit', function(e) {
    const equipmentSelects = document.querySelectorAll('.equipment-select');
    let hasValidEquipment = false;
    
    equipmentSelects.forEach(select => {
        if (select.value) {
            hasValidEquipment = true;
        }
    });
    
    if (!hasValidEquipment) {
        e.preventDefault();
        alert('請至少選擇一個需要報修的設備！');
        return false;
    }
});

// 頁面載入時初始化
document.addEventListener('DOMContentLoaded', function() {
    updateRemoveButtons();
});
</script>
@endsection

