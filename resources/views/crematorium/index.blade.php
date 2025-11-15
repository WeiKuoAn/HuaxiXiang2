@extends('layouts.vertical', ["page_title"=> "火化爐設備管理"])

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
                        <li class="breadcrumb-item active">設備管理</li>
                    </ol>
                </div>
                <h4 class="page-title">火化爐設備管理</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row justify-content-between">
                        <div class="col-auto">
                            <h5 class="card-title">設備類型列表（庫存管理）</h5>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('crematorium.instances.index') }}" class="btn btn-success me-2">
                                <i class="mdi mdi-map-marker-multiple me-1"></i>設備位置管理
                            </a>
                            <a href="{{ route('crematorium.repairs.index') }}" class="btn btn-danger me-2">
                                <i class="mdi mdi-alert-circle me-1"></i>報修單
                            </a>
                            <a href="{{ route('crematorium.maintenance') }}" class="btn btn-info me-2">
                                <i class="mdi mdi-clipboard-check me-1"></i>檢查記錄
                            </a>
                            <a href="{{ route('crematorium.purchases.index') }}" class="btn btn-warning me-2">
                                <i class="mdi mdi-package-variant me-1"></i>設備進貨
                            </a>
                            <a href="{{ route('crematorium.equipment.create') }}" class="btn btn-primary">
                                <i class="mdi mdi-plus-circle me-1"></i>新增設備類型
                            </a>
                        </div>
                    </div>

                    <!-- 篩選區域 -->
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <form method="GET" action="{{ route('crematorium.equipment.index') }}" class="d-flex align-items-center">
                                <select name="category" class="form-select me-2" onchange="this.form.submit()" style="min-width: 150px;">
                                    <option value="">全部類別</option>
                                    <option value="furnace_1" {{ $categoryFilter == 'furnace_1' ? 'selected' : '' }}>一爐</option>
                                    <option value="furnace_2" {{ $categoryFilter == 'furnace_2' ? 'selected' : '' }}>二爐</option>
                                    <option value="ventilation" {{ $categoryFilter == 'ventilation' ? 'selected' : '' }}>抽風</option>
                                </select>
                                @if($categoryFilter)
                                    <a href="{{ route('crematorium.equipment.index') }}" class="btn btn-outline-secondary" style="white-space: nowrap;">
                                        <i class="mdi mdi-close"></i>清除篩選
                                    </a>
                                @endif
                            </form>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>編號</th>
                                    <th>設備名稱</th>
                                    <th>配置位置</th>
                                    <th>庫存狀態</th>
                                    <th>使用中/故障</th>
                                    <th>動作</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($equipmentTypes as $key => $type)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <strong>{{ $type->name }}</strong>
                                            @if($type->exclude_from_inventory)
                                                <span class="badge bg-secondary ms-2">不列入庫存</span>
                                            @endif
                                            @if($type->description)
                                                <br>
                                                <small class="text-muted">{{ $type->description }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="text-muted small">配置於：</div>
                                            @php
                                                $locations = $type->instances->pluck('full_location')->unique()->values();
                                            @endphp
                                            @foreach($locations as $loc)
                                                <span class="badge bg-light text-dark me-1 mb-1">{{ $loc }}</span>
                                            @endforeach
                                        </td>
                                        <td>
                                            @if($type->exclude_from_inventory)
                                                <span class="text-muted">—</span>
                                            @else
                                                <div class="mb-1">
                                                    <span class="badge bg-primary me-1">全新：{{ $type->stock_new }}</span>
                                                    <span class="badge bg-info me-1">堪用：{{ $type->stock_usable }}</span>
                                                </div>
                                                <div>
                                                    <strong>總計：{{ $type->stock_total }}</strong>
                                                    @if($type->stock_total <= 0)
                                                        <span class="badge bg-danger ms-1">缺貨</span>
                                                    @elseif($type->stock_total <= 5)
                                                        <span class="badge bg-warning ms-1">庫存不足</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-success">使用中：{{ $type->active_count }}</span>
                                            @if($type->broken_count > 0)
                                                <span class="badge bg-danger ms-1">故障：{{ $type->broken_count }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group dropdown">
                                                <a href="javascript: void(0);" class="table-action-btn dropdown-toggle arrow-none btn btn-outline-secondary waves-effect" data-bs-toggle="dropdown" aria-expanded="false">
                                                    動作 <i class="mdi mdi-arrow-down-drop-circle"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a class="dropdown-item" href="{{ route('crematorium.equipment.edit', $type->id) }}">
                                                        <i class="mdi mdi-pencil me-2 text-muted font-18 vertical-middle"></i>編輯
                                                    </a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="deleteEquipmentType({{ $type->id }})">
                                                        <i class="mdi mdi-delete me-2 text-muted font-18 vertical-middle"></i>刪除
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">暫無設備類型資料</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- @if($equipments->hasPages())
                        <div class="mt-3">
                            {{ $equipments->links('vendor.pagination.bootstrap-4') }}
                        </div>
                    @endif --}}
                </div>
            </div>
        </div>
    </div>
</div> <!-- container -->

<script>
function deleteEquipmentType(id) {
    if (confirm('確定要刪除此設備類型嗎？如果此類型下有設備實例，將無法刪除。')) {
        fetch(`/crematorium/equipment/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || '刪除失敗');
            }
        })
        .catch(error => {
            alert('刪除失敗：' + error);
        });
    }
}
</script>
@endsection
