@extends('layouts.vertical', ["page_title"=> "火化爐設定"])

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
                        <li class="breadcrumb-item active">火化爐設定</li>
                    </ol>
                </div>
                <h4 class="page-title">火化爐設定</h4>
            </div>
        </div>
    </div>

    <!-- 按火化爐位置分組顯示 -->
    @php
        $furnaceLocations = [
            ['category' => 'furnace_1', 'sub_category' => 'fire_1', 'name' => '一爐-一火'],
            ['category' => 'furnace_1', 'sub_category' => 'fire_2', 'name' => '一爐-二火'],
            ['category' => 'furnace_2', 'sub_category' => 'fire_1a', 'name' => '二爐-一火A'],
            ['category' => 'furnace_2', 'sub_category' => 'fire_1b', 'name' => '二爐-一火B'],
            ['category' => 'furnace_2', 'sub_category' => 'fire_2', 'name' => '二爐-二火'],
            ['category' => 'furnace_1_ventilation', 'sub_category' => null, 'name' => '一爐-抽風'],
            ['category' => 'furnace_2_ventilation', 'sub_category' => null, 'name' => '二爐-抽風'],
        ];
    @endphp

    @foreach($furnaceLocations as $furnace)
        @php
            $furnaceInstances = $instances->filter(function($inst) use ($furnace) {
                return $inst->category == $furnace['category'] && $inst->sub_category == $furnace['sub_category'];
            });
        @endphp

        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="mdi mdi-fire me-2 text-danger"></i>
                                {{ $furnace['name'] }}
                            </h5>
                            <span class="badge bg-primary">{{ $furnaceInstances->count() }} 個零件</span>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($furnaceInstances->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 5%;">編號</th>
                                            <th style="width: 25%;">零件名稱</th>
                                            <th style="width: 15%;">狀態</th>
                                            <th style="width: 15%;">庫存</th>
                                            <th style="width: 15%;">最後維護</th>
                                            <th style="width: 15%;">備註</th>
                                            <th style="width: 10%;">動作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($furnaceInstances as $instance)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    <strong>{{ $instance->equipmentType->name }}</strong>
                                                    @if($instance->equipmentType->exclude_from_inventory)
                                                        <span class="badge bg-secondary badge-sm">不計庫存</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $instance->status_color }}">
                                                        {{ $instance->status_text }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if(!$instance->equipmentType->exclude_from_inventory)
                                                        <small>
                                                            全新:{{ $instance->equipmentType->stock_new }} 
                                                            堪用:{{ $instance->equipmentType->stock_usable }}
                                                        </small>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <small>{{ $instance->last_maintenance_date ? $instance->last_maintenance_date->format('Y-m-d') : '—' }}</small>
                                                </td>
                                                <td>
                                                    <small class="text-muted">{{ $instance->notes ?? '—' }}</small>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        @if($instance->status == 'broken')
                                                            <button type="button" class="btn btn-sm btn-success" onclick="markAsActive({{ $instance->id }})">
                                                                <i class="mdi mdi-check"></i>
                                                            </button>
                                                        @else
                                                            <button type="button" class="btn btn-sm btn-danger" onclick="markAsBroken({{ $instance->id }})">
                                                                <i class="mdi mdi-alert"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted text-center py-3 mb-0">此火化爐位置尚未配置任何零件</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <div class="row">
        <div class="col-12 text-center">
            <a href="{{ route('crematorium.equipment.index') }}" class="btn btn-primary">
                <i class="mdi mdi-package-variant me-1"></i>管理零件和庫存
            </a>
        </div>
    </div>
</div>

<script>
function markAsBroken(id) {
    const reason = prompt('請輸入故障原因：');
    if (reason !== null) {
        fetch(`/crematorium/instances/${id}/broken`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ notes: reason })
        })
        .then(() => location.reload())
        .catch(error => alert('操作失敗：' + error));
    }
}

function markAsActive(id) {
    if (confirm('確定標記為正常使用嗎？')) {
        fetch(`/crematorium/instances/${id}/active`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            }
        })
        .then(() => location.reload())
        .catch(error => alert('操作失敗：' + error));
    }
}

function deleteInstance(id) {
    if (confirm('確定要移除此設備配置嗎？')) {
        fetch(`/crematorium/instances/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            }
        })
        .then(() => location.reload())
        .catch(error => alert('刪除失敗：' + error));
    }
}
</script>
@endsection

