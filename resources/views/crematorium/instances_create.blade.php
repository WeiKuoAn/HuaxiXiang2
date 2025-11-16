@extends('layouts.vertical', ['page_title' => '配置設備到火化爐'])

@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Huaxixiang</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">火化爐管理</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('crematorium.instances.index') }}">設備位置管理</a></li>
                        <li class="breadcrumb-item active">配置設備</li>
                    </ol>
                </div>
                <h4 class="page-title">配置設備到火化爐位置</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('crematorium.instances.store') }}" method="POST">
                        @csrf

                        <div class="alert alert-info">
                            <i class="mdi mdi-information-outline me-2"></i>
                            <strong>說明：</strong>選擇設備類型，然後指定要配置到哪個火化爐位置。設備庫存是統一管理的，不會因為配置而減少。
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="equipment_type_id" class="form-label">設備類型 <span class="text-danger">*</span></label>
                                    <select class="form-select @error('equipment_type_id') is-invalid @enderror" 
                                            id="equipment_type_id" name="equipment_type_id" required>
                                        <option value="">請選擇設備</option>
                                        @foreach($equipmentTypes as $type)
                                            <option value="{{ $type->id }}" {{ old('equipment_type_id') == $type->id ? 'selected' : '' }}>
                                                {{ $type->name }}
                                                @if($type->exclude_from_inventory)
                                                    （不計庫存）
                                                @else
                                                    （庫存：{{ $type->stock_total }}）
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('equipment_type_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">狀態 <span class="text-danger">*</span></label>
                                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>正常使用</option>
                                        <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>維護中</option>
                                        <option value="broken" {{ old('status') == 'broken' ? 'selected' : '' }}>故障</option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>停用</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category" class="form-label">火化爐 <span class="text-danger">*</span></label>
                                    <select class="form-select @error('category') is-invalid @enderror" 
                                            id="category" name="category" required onchange="updateSubCategory()">
                                        <option value="">請選擇火化爐</option>
                                        <option value="furnace_1" {{ old('category') == 'furnace_1' ? 'selected' : '' }}>一爐</option>
                                        <option value="furnace_2" {{ old('category') == 'furnace_2' ? 'selected' : '' }}>二爐</option>
                                        <option value="furnace_1_ventilation" {{ old('category') == 'furnace_1_ventilation' ? 'selected' : '' }}>一爐抽風</option>
                                        <option value="furnace_2_ventilation" {{ old('category') == 'furnace_2_ventilation' ? 'selected' : '' }}>二爐抽風</option>
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sub_category" class="form-label">火位</label>
                                    <select class="form-select @error('sub_category') is-invalid @enderror" 
                                            id="sub_category" name="sub_category">
                                        <option value="">請選擇火位</option>
                                        <option value="fire_1" {{ old('sub_category') == 'fire_1' ? 'selected' : '' }}>一火</option>
                                        <option value="fire_2" {{ old('sub_category') == 'fire_2' ? 'selected' : '' }}>二火</option>
                                        <option value="fire_1a" {{ old('sub_category') == 'fire_1a' ? 'selected' : '' }}>一火A</option>
                                        <option value="fire_1b" {{ old('sub_category') == 'fire_1b' ? 'selected' : '' }}>一火B</option>
                                    </select>
                                    @error('sub_category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="installed_date" class="form-label">安裝日期</label>
                                    <input type="date" class="form-control" id="installed_date" name="installed_date" value="{{ old('installed_date') }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="last_maintenance_date" class="form-label">最後維護日期</label>
                                    <input type="date" class="form-control" id="last_maintenance_date" name="last_maintenance_date" value="{{ old('last_maintenance_date') }}">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">備註</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                        </div>

                        <div class="text-end">
                            <a href="{{ route('crematorium.instances.index') }}" class="btn btn-secondary me-2">取消</a>
                            <button type="submit" class="btn btn-primary">配置設備</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateSubCategory() {
    const category = document.getElementById('category').value;
    const subCategory = document.getElementById('sub_category');
    
    // 清空選項
    subCategory.innerHTML = '<option value="">請選擇火位</option>';
    
    if (category === 'furnace_1') {
        subCategory.innerHTML += '<option value="fire_1">一火</option>';
        subCategory.innerHTML += '<option value="fire_2">二火</option>';
        subCategory.disabled = false;
    } else if (category === 'furnace_2') {
        subCategory.innerHTML += '<option value="fire_1a">一火A</option>';
        subCategory.innerHTML += '<option value="fire_1b">一火B</option>';
        subCategory.innerHTML += '<option value="fire_2">二火</option>';
        subCategory.disabled = false;
    } else if (category === 'ventilation' || category === 'furnace_1_ventilation' || category === 'furnace_2_ventilation') {
        subCategory.innerHTML = '<option value="">無需選擇</option>';
        subCategory.disabled = true;
    } else {
        subCategory.disabled = true;
    }
}

// 頁面載入時初始化
document.addEventListener('DOMContentLoaded', function() {
    updateSubCategory();
});
</script>
@endsection

