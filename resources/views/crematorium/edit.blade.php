@extends('layouts.vertical', ["page_title"=> "編輯設備類型"])

@section('css')
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
                        <li class="breadcrumb-item"><a href="{{ route('crematorium.equipment.index') }}">設備類型管理</a></li>
                        <li class="breadcrumb-item active">編輯設備類型</li>
                    </ol>
                </div>
                <h4 class="page-title">編輯設備類型（庫存管理）</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('crematorium.equipment.update', $equipmentType->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="alert alert-warning">
                            <i class="mdi mdi-alert-outline me-2"></i>
                            <strong>提示：</strong>此設備類型有 <strong>{{ $equipmentType->instances->count() }}</strong> 個設備實例在使用中。
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="name" class="form-label">設備名稱 <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $equipmentType->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="exclude_from_inventory" 
                                               name="exclude_from_inventory" value="1" 
                                               {{ old('exclude_from_inventory', $equipmentType->exclude_from_inventory) ? 'checked' : '' }}
                                               onchange="toggleInventoryFields()">
                                        <label class="form-check-label" for="exclude_from_inventory">
                                            <strong>不列入庫存</strong>
                                            <small class="text-muted">（勾選後不需要管理庫存數量，適用於不需要備品的設備）</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row" id="inventory_fields">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="stock_new" class="form-label">全新庫存</label>
                                    <input type="number" class="form-control @error('stock_new') is-invalid @enderror" 
                                           id="stock_new" name="stock_new" value="{{ old('stock_new', $equipmentType->stock_new) }}" min="0" oninput="updateStockTotal()">
                                    @error('stock_new')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="stock_usable" class="form-label">堪用庫存</label>
                                    <input type="number" class="form-control @error('stock_usable') is-invalid @enderror" 
                                           id="stock_usable" name="stock_usable" value="{{ old('stock_usable', $equipmentType->stock_usable) }}" min="0" oninput="updateStockTotal()">
                                    @error('stock_usable')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">總庫存</label>
                                    <input type="text" class="form-control bg-light" id="stock_total" value="{{ $equipmentType->stock_total }}" readonly>
                                    <small class="text-muted">全新 + 堪用</small>
                                </div>
                            </div>
                        </div>


                        <div class="mb-3">
                            <label class="form-label">配置到火化爐位置（可多選）</label>
                            <div class="card">
                                <div class="card-body">
                                    @php
                                        $currentLocations = $equipmentType->instances->map(function($inst) {
                                            return $inst->category . '|' . ($inst->sub_category ?? '');
                                        })->toArray();
                                    @endphp
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="text-primary">一爐</h6>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="locations[]" value="furnace_1|fire_1" id="loc_1_1" 
                                                       {{ in_array('furnace_1|fire_1', $currentLocations) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="loc_1_1">一爐-一火</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="locations[]" value="furnace_1|fire_2" id="loc_1_2"
                                                       {{ in_array('furnace_1|fire_2', $currentLocations) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="loc_1_2">一爐-二火</label>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <h6 class="text-success">二爐</h6>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="locations[]" value="furnace_2|fire_1a" id="loc_2_1a"
                                                       {{ in_array('furnace_2|fire_1a', $currentLocations) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="loc_2_1a">二爐-一火A</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="locations[]" value="furnace_2|fire_1b" id="loc_2_1b"
                                                       {{ in_array('furnace_2|fire_1b', $currentLocations) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="loc_2_1b">二爐-一火B</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="locations[]" value="furnace_2|fire_2" id="loc_2_2"
                                                       {{ in_array('furnace_2|fire_2', $currentLocations) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="loc_2_2">二爐-二火</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            <h6 class="text-info">抽風</h6>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="locations[]" value="furnace_1_ventilation|" id="loc_v_1"
                                                       {{ in_array('furnace_1_ventilation|', $currentLocations) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="loc_v_1">一爐-抽風</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="locations[]" value="furnace_2_ventilation|" id="loc_v_2"
                                                       {{ in_array('furnace_2_ventilation|', $currentLocations) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="loc_v_2">二爐-抽風</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <button type="button" class="btn btn-sm btn-outline-primary me-2" onclick="selectAll()">全選</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAll()">全不選</button>
                                    </div>
                                </div>
                            </div>
                            <small class="text-muted">修改配置位置時，會自動新增/移除對應的設備實例</small>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">描述</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $equipmentType->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-end">
                            <a href="{{ route('crematorium.equipment.index') }}" class="btn btn-secondary me-2">取消</a>
                            <button type="submit" class="btn btn-primary">更新設備類型和配置</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> <!-- container -->

<script>
    // 切換庫存欄位顯示/隱藏
    function toggleInventoryFields() {
        const checkbox = document.getElementById('exclude_from_inventory');
        const inventoryFields = document.getElementById('inventory_fields');
        const stockNew = document.getElementById('stock_new');
        const stockUsable = document.getElementById('stock_usable');
        
        if (checkbox.checked) {
            inventoryFields.style.display = 'none';
            stockNew.value = 0;
            stockUsable.value = 0;
            stockNew.removeAttribute('required');
            stockUsable.removeAttribute('required');
        } else {
            inventoryFields.style.display = '';
            stockNew.setAttribute('required', 'required');
            stockUsable.setAttribute('required', 'required');
        }
        
        updateStockTotal();
    }

    // 更新總庫存
    function updateStockTotal() {
        const stockNew = parseInt(document.getElementById('stock_new').value) || 0;
        const stockUsable = parseInt(document.getElementById('stock_usable').value) || 0;
        const total = stockNew + stockUsable;
        document.getElementById('stock_total').value = total;
    }

    // 全選位置
    function selectAll() {
        document.querySelectorAll('input[name="locations[]"]').forEach(cb => cb.checked = true);
    }

    // 全不選
    function deselectAll() {
        document.querySelectorAll('input[name="locations[]"]').forEach(cb => cb.checked = false);
    }

    // 頁面載入時初始化
    document.addEventListener('DOMContentLoaded', function() {
        toggleInventoryFields();
        updateStockTotal();
    });
</script>
@endsection
