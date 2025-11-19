@extends('layouts.vertical', ['page_title' => '確認設備檢查記錄'])

@section('css')
    <style>
        /* 分組樣式 */
        .furnace-group {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            background-color: #fafafa;
            margin-bottom: 25px;
        }

        .group-header {
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .group-header h5 {
            font-weight: 600;
            font-size: 1.3rem;
            margin: 0;
        }

        .sub-group {
            margin-left: 20px;
            border-left: 3px solid #e9ecef;
            padding-left: 15px;
        }

        .sub-group h6 {
            font-weight: 500;
            font-size: 1rem;
            margin: 0;
            color: #6c757d;
        }

        /* 表格樣式 */
        .table {
            margin-bottom: 0;
            background-color: white;
        }

        .table th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            text-align: center;
            vertical-align: middle;
            font-size: 0.9rem;
        }

        .table td {
            vertical-align: middle;
            border-bottom: 1px solid #dee2e6;
            font-size: 0.9rem;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        /* 表單選項樣式 */
        .form-check-inline {
            margin: 0;
        }

        .form-check-inline label {
            cursor: pointer;
            font-weight: 500;
            font-size: 1rem;
            margin: 0;
        }

        .form-check-input:checked+.form-check-label {
            font-weight: 600;
        }

        /* 狀態選項樣式 */
        .form-check-inline .text-success {
            color: #198754 !important;
        }

        .form-check-inline .text-danger {
            color: #dc3545 !important;
        }

        /* 問題描述區域 */
        .problem-description-cell {
            vertical-align: middle;
            padding: 8px;
            background-color: #fff5f5;
        }
        
        .equip-thumb {
            max-height: 120px;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            margin-top: 6px;
        }

        .problem-description-inline {
            /* 問題描述框在表格欄位中 */
        }

        .problem-description-inline textarea {
            border: 1px solid #dc3545;
            border-radius: 4px;
            width: 100%;
            box-sizing: border-box;
        }

        .problem-description-inline textarea:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }

        /* 列印樣式 */
        @media print {
            @page {
                margin: 1cm;
                size: A4;
            }

            body {
                font-family: "Microsoft JhengHei", "PingFang TC", "Helvetica Neue", Arial, sans-serif;
                font-size: 11pt;
                line-height: 1.5;
                color: #333;
                background: white !important;
            }

            .container-fluid {
                padding: 0;
                max-width: none;
            }

            .page-title-box,
            .btn,
            .breadcrumb,
            .alert,
            .card-header {
                display: none !important;
            }

            /* 只隱藏基本資訊輸入框，保留標題 */
            .print-info {
                display: none !important;
            }

            .card {
                border: none;
                box-shadow: none;
                background: white !important;
            }

            .card-body {
                padding: 0;
            }

            /* 列印標題樣式 */
            .print-header {
                text-align: center;
                margin-bottom: 25px;
                padding: 20px 0;
                border-bottom: 3px solid #2c3e50;
                background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                border-radius: 8px;
            }

            .print-header h3 {
                font-size: 20pt;
                font-weight: bold;
                margin: 0 0 8px 0;
                color: #2c3e50;
                text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
            }

            .print-header p {
                font-size: 12pt;
                margin: 0;
                color: #6c757d;
            }

            /* 基本資訊樣式 */
            .print-info {
                margin-bottom: 25px;
                padding: 15px;
                background: #f8f9fa;
                border-radius: 6px;
                border-left: 4px solid #007bff;
            }

            /* 供電系統檢查樣式 */
            .power-system-check {
                margin-bottom: 25px;
                padding: 15px;
                background: #fff3cd;
                border-radius: 6px;
                border-left: 4px solid #ffc107;
            }

            .power-system-check h5 {
                font-size: 14pt;
                font-weight: bold;
                margin: 0 0 10px 0;
                color: #856404;
            }

            .power-system-check .form-check {
                margin-bottom: 8px;
            }

            .power-system-check .form-check-input {
                width: 16px;
                height: 16px;
                margin-right: 8px;
            }

            .power-system-check .form-check-label {
                font-size: 11pt;
                font-weight: 600;
                color: #856404;
            }

            .print-info .row {
                margin-bottom: 8px;
            }

            .print-info label {
                font-weight: 600;
                color: #495057;
                display: inline-block;
                width: 120px;
            }

            .print-info input {
                border: 1px solid #dee2e6;
                padding: 4px 8px;
                border-radius: 4px;
                background: white;
            }

            /* 設備分組樣式 */
            .furnace-group {
                border: 2px solid #e9ecef;
                margin-bottom: 25px;
                page-break-inside: avoid;
                padding: 20px;
                background: white !important;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }

            .group-header {
                border-bottom: 2px solid #007bff;
                margin-bottom: 15px;
                padding-bottom: 10px;
            }

            .group-header h5 {
                font-size: 16pt;
                font-weight: bold;
                margin: 0;
                color: #007bff;
                display: flex;
                align-items: center;
            }

            .group-header h5::before {
                content: "🔥";
                margin-right: 8px;
                font-size: 18pt;
            }

            .sub-group {
                margin-left: 0;
                border-left: none;
                padding-left: 0;
                margin-bottom: 20px;
                padding: 15px;
                background: #f8f9fa;
                border-radius: 6px;
                border-left: 4px solid #28a745;
            }

            .sub-group h6 {
                font-size: 13pt;
                font-weight: 600;
                margin: 0 0 10px 0;
                color: #28a745;
                display: flex;
                align-items: center;
            }

            .sub-group h6::before {
                content: "⚡";
                margin-right: 6px;
                font-size: 14pt;
            }

            /* 表格樣式 */
            .table {
                border-collapse: collapse;
                width: 100%;
                margin-bottom: 0;
                background: white;
                border-radius: 6px;
                overflow: hidden;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            }

            .table th {
                background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
                color: white !important;
                font-weight: 600;
                text-align: center;
                padding: 12px 8px;
                border: none;
                font-size: 11pt;
            }

            .table td {
                border: 1px solid #dee2e6;
                padding: 10px 8px;
                text-align: left;
                vertical-align: middle;
                background: white !important;
            }

            .table tbody tr:nth-child(even) {
                background-color: #f8f9fa !important;
            }

            .table tbody tr:hover {
                background-color: #e3f2fd !important;
            }

            /* 表單選項樣式 */
            .form-check-inline {
                display: inline-flex;
                align-items: center;
                margin-right: 25px;
                margin-bottom: 5px;
            }

            .form-check-inline input[type="radio"] {
                width: 16px;
                height: 16px;
                margin-right: 8px;
                border: 2px solid #007bff;
            }

            .form-check-inline label {
                font-size: 11pt;
                margin: 0;
                font-weight: 500;
                cursor: pointer;
            }

            .form-check-inline .text-success {
                color: #28a745 !important;
            }

            .form-check-inline .text-danger {
                color: #dc3545 !important;
            }

            /* 問題描述區域 */
            .problem-description-cell {
                min-height: 50px;
                padding: 8px;
            }

            .problem-description-inline textarea {
                border: 2px solid #dc3545;
                border-radius: 4px;
                width: 100%;
                min-height: 40px;
                font-size: 10pt;
                padding: 6px;
                background: #fff5f5;
            }

            /* 備註區域 */
            .print-notes {
                margin-top: 25px;
                padding: 20px;
                background: #f8f9fa;
                border-radius: 6px;
                border-left: 4px solid #ffc107;
            }

            .print-notes label {
                font-weight: 600;
                color: #495057;
                font-size: 12pt;
                margin-bottom: 10px;
                display: block;
            }

            .print-notes textarea {
                border: 2px solid #ffc107;
                border-radius: 4px;
                width: 100%;
                min-height: 80px;
                font-size: 11pt;
                padding: 10px;
                background: white;
            }

            /* 移除強制分頁 */
            .furnace-group {
                page-break-inside: auto;
            }

            .sub-group {
                page-break-inside: auto;
            }

            .power-system-check {
                page-break-inside: auto;
            }
        }

        /* 響應式調整 */
        @media (max-width: 768px) {
            .furnace-group {
                padding: 15px;
            }

            .table-responsive {
                font-size: 0.875rem;
            }

            .form-check-inline label {
                font-size: 0.875rem;
            }

            .group-header h5 {
                font-size: 1.1rem;
            }

            .sub-group {
                margin-left: 10px;
                padding-left: 10px;
            }

            .sub-group h6 {
                font-size: 0.9rem;
            }
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
                            <li class="breadcrumb-item"><a href="{{ route('crematorium.equipment.index') }}">設備管理</a></li>
                            <li class="breadcrumb-item active">確認檢查記錄</li>
                        </ol>
                    </div>
                    <h4 class="page-title">確認設備檢查記錄</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('crematorium.checkMaintenanceUpdate', $maintenance->id ?? 0) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- 列印專用標題 -->
                            <div class="print-header d-none">
                                <h3>懷翔寵物生命 - 火化爐設備檢查記錄表</h3>
                                <p>檢查單號：{{ $maintenance->maintenance_number ?? '' }} |
                                    檢查日期：{{ $maintenance->maintenance_date ?? '' }}</p>
                                <p>檢查人員：{{ $maintenance->inspectorUser->name ?? '未指派' }} |
                                    保養人員：{{ $maintenance->maintainerUser->name ?? '未指派' }}</p>
                                <p style="font-size: 10pt; color: #6c757d; margin-top: 8px;">
                                    ※ 請在現場檢查時勾選「正常」或「有問題」，如有問題請詳細描述
                                </p>
                            </div>

                            <!-- 檢查資訊摘要 -->
                            <div class="alert alert-info mb-4">
                                <div class="row">
                                    <div class="col-md-3">
                                        <strong>檢查單號：</strong>{{ $maintenance->maintenance_number ?? '' }}
                                    </div>
                                    <div class="col-md-3">
                                        <strong>檢查人員：</strong>{{ $maintenance->inspectorUser->name ?? '未指派' }}
                                    </div>
                                    <div class="col-md-3">
                                        <strong>保養人員：</strong>{{ $maintenance->maintainerUser->name ?? '未指派' }}
                                    </div>
                                    <div class="col-md-3">
                                        <strong>指派日期：</strong>{{ $maintenance->created_at ? $maintenance->created_at->format('Y-m-d') : '' }}
                                    </div>
                                </div>
                            </div>

                            <!-- 檢查基本資訊 -->
                            <div class="print-info">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="maintenance_date" class="form-label">實際檢查時間 <span
                                                    class="text-danger">*</span></label>
                                            <input type="date"
                                                class="form-control @error('maintenance_date') is-invalid @enderror"
                                                id="maintenance_date" name="maintenance_date"
                                                value="{{ old('maintenance_date', $maintenance->maintenance_date ?? date('Y-m-d')) }}"
                                                required>
                                            @error('maintenance_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="notes" class="form-label">備註</label>
                                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3"
                                                placeholder="請輸入檢查備註...">{{ old('notes', $maintenance->notes ?? '') }}</textarea>
                                            @error('notes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 設備檢查列表 - 爐→火→設備分組 -->
                            <div class="mb-4">
                                <h5 class="mb-3">設備檢查清單</h5>
                                <p class="text-muted">請檢查每個設備的狀況，如有問題請填寫詳細說明</p>

                                @php
                                    // 按大類別分組設備（一爐、二爐、抽風）
                                    $groupedEquipments = $equipments->groupBy(function ($equipment) {
                                        return $equipment->category;
                                    });

                                    // 定義類別顯示名稱
                                    $categoryNames = [
                                        'furnace_1' => '一爐',
                                        'furnace_2' => '二爐',
                                        'ventilation' => '抽風',
                                        'furnace_1_ventilation' => '一爐抽風',
                                        'furnace_2_ventilation' => '二爐抽風',
                                    ];

                                    // 定義子類別顯示名稱
                                    $subCategoryNames = [
                                        'fire_1' => '一火',
                                        'fire_2' => '二火',
                                        'fire_1a' => '一火A',
                                        'fire_1b' => '一火B',
                                    ];

                                    // 獲取現有檢查記錄（使用 equipment_instance_id）
                                    $existingMaintenances =
                                        isset($maintenance) && isset($maintenance->maintenanceDetails)
                                            ? $maintenance->maintenanceDetails->keyBy('equipment_instance_id')
                                            : collect();
                                @endphp

                                @foreach ($groupedEquipments as $categoryKey => $equipmentsInGroup)
                                    @php
                                        // 按子類別再次分組
                                        $subGroupedEquipments = $equipmentsInGroup->groupBy('sub_category');
                                    @endphp

                                    <div class="furnace-group mb-4">
                                        <div class="group-header mb-3">
                                            <h5 class="text-primary mb-2">
                                                <i class="mdi mdi-fire me-2"></i>
                                                {{ $categoryNames[$categoryKey] }}
                                            </h5>
                                        </div>

                                        @foreach ($subGroupedEquipments as $subCategoryKey => $equipmentsInSubGroup)
                                            <div class="sub-group mb-3">
                                                <h6 class="text-secondary mb-2">
                                                    <i class="mdi mdi-circle-small me-1"></i>
                                                    {{ $subCategoryNames[$subCategoryKey] ?? '抽風設備' }}
                                                </h6>

                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-hover">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th width="22%">設備名稱</th>
                                                                <th width="18%">設備圖片</th>
                                                                <th width="15%">正常</th>
                                                                <th width="15%">有問題</th>
                                                                <th width="30%">問題描述</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($equipmentsInSubGroup as $equipment)
                                                                @php
                                                                    $existingMaintenance = $existingMaintenances->get(
                                                                        $equipment->id,
                                                                    );
                                                                @endphp
                                                                <tr data-equipment-id="{{ $equipment->id }}"
                                                                    data-exclude-inventory="{{ $equipment->equipmentType->exclude_from_inventory ? 'true' : 'false' }}"
                                                                    data-stock-new="{{ $equipment->equipmentType->stock_new }}"
                                                                    data-stock-usable="{{ $equipment->equipmentType->stock_usable }}"
                                                                    data-equipment-name="{{ $equipment->equipmentType->name }}">
                                                                    <td>
                                                                        <strong>{{ $equipment->equipmentType->name }}</strong>
                                                                        @if ($equipment->equipmentType->exclude_from_inventory)
                                                                            <span
                                                                                class="badge bg-secondary badge-sm">不計庫存</span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-center">
                                                                        @if (!empty($equipment->equipmentType->image_path))
                                                                            @php
                                                                                $imageUrl = str_starts_with($equipment->equipmentType->image_path, 'assets/') ? asset($equipment->equipmentType->image_path) : Storage::url($equipment->equipmentType->image_path);
                                                                            @endphp
                                                                            <a href="{{ $imageUrl }}"
                                                                                target="_blank">
                                                                                <img src="{{ $imageUrl }}"
                                                                                    alt="{{ $equipment->equipmentType->name }}"
                                                                                    class="equip-thumb">
                                                                            </a>
                                                                        @else
                                                                            <span class="text-muted">—</span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <div class="form-check form-check-inline">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="equipment_status[{{ $equipment->id }}]"
                                                                                id="equipment_{{ $equipment->id }}_good"
                                                                                value="good"
                                                                                {{ $existingMaintenance && isset($existingMaintenance->status) && $existingMaintenance->status === 'good' ? 'checked' : '' }}
                                                                                onchange="toggleProblemText({{ $equipment->id }}, 'good')">
                                                                            <label class="form-check-label text-success"
                                                                                for="equipment_{{ $equipment->id }}_good">
                                                                                ✓
                                                                            </label>
                                                                        </div>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <div class="form-check form-check-inline">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="equipment_status[{{ $equipment->id }}]"
                                                                                id="equipment_{{ $equipment->id }}_problem"
                                                                                value="problem"
                                                                                {{ $existingMaintenance && isset($existingMaintenance->status) && $existingMaintenance->status === 'problem' ? 'checked' : '' }}
                                                                                onchange="toggleProblemText({{ $equipment->id }}, 'problem')">
                                                                            <label class="form-check-label text-danger"
                                                                                for="equipment_{{ $equipment->id }}_problem">
                                                                                ✗
                                                                            </label>
                                                                        </div>
                                                                    </td>
                                                                    <td class="problem-description-cell">
                                                                        <div id="problem_text_container_{{ $equipment->id }}"
                                                                            class="problem-description-inline"
                                                                            style="display: {{ $existingMaintenance && isset($existingMaintenance->status) && $existingMaintenance->status === 'problem' ? 'block' : 'none' }};">

                                                                            <!-- 處理方式選項 -->
                                                                            <div class="mb-2">
                                                                                <label
                                                                                    class="form-label small">處理方式：</label><br>
                                                                                <div class="form-check form-check-inline">
                                                                                    <input class="form-check-input"
                                                                                        type="radio"
                                                                                        name="equipment_action[{{ $equipment->id }}]"
                                                                                        id="equipment_action_{{ $equipment->id }}_repair"
                                                                                        value="repair"
                                                                                        {{ $existingMaintenance && isset($existingMaintenance->action) && $existingMaintenance->action === 'repair' ? 'checked' : '' }}
                                                                                        onchange="toggleReplacementType({{ $equipment->id }})">
                                                                                    <label class="form-check-label small"
                                                                                        for="equipment_action_{{ $equipment->id }}_repair">維修</label>
                                                                                </div>
                                                                                <div class="form-check form-check-inline">
                                                                                    <input class="form-check-input"
                                                                                        type="radio"
                                                                                        name="equipment_action[{{ $equipment->id }}]"
                                                                                        id="equipment_action_{{ $equipment->id }}_replace"
                                                                                        value="replace"
                                                                                        {{ $existingMaintenance && isset($existingMaintenance->action) && $existingMaintenance->action === 'replace' ? 'checked' : '' }}
                                                                                        onchange="toggleReplacementType({{ $equipment->id }})">
                                                                                    <label class="form-check-label small"
                                                                                        for="equipment_action_{{ $equipment->id }}_replace">更換</label>
                                                                                    <input type="number"
                                                                                        class="form-control form-control-sm d-inline-block ms-1"
                                                                                        id="equipment_quantity_{{ $equipment->id }}"
                                                                                        name="equipment_quantity[{{ $equipment->id }}]"
                                                                                        value="{{ $existingMaintenance && isset($existingMaintenance->quantity) ? $existingMaintenance->quantity : 1 }}"
                                                                                        min="1"
                                                                                        {{ $existingMaintenance && isset($existingMaintenance->action) && $existingMaintenance->action === 'replace' ? '' : 'disabled' }}
                                                                                        onchange="checkMaintenanceStock({{ $equipment->id }})"
                                                                                        style="width: 50px; display: inline-block;">
                                                                                    <span class="small ms-1">個</span>

                                                                                    <select
                                                                                        class="form-select form-select-sm d-inline-block ms-2"
                                                                                        id="equipment_replacement_type_{{ $equipment->id }}"
                                                                                        name="equipment_replacement_type[{{ $equipment->id }}]"
                                                                                        {{ $existingMaintenance && isset($existingMaintenance->action) && $existingMaintenance->action === 'replace' ? '' : 'disabled' }}
                                                                                        onchange="checkMaintenanceStock({{ $equipment->id }})"
                                                                                        style="width: 100px; display: {{ $existingMaintenance && isset($existingMaintenance->action) && $existingMaintenance->action === 'replace' ? 'inline-block' : 'none' }};">
                                                                                        <option value="new"
                                                                                            {{ $existingMaintenance && isset($existingMaintenance->replacement_type) && $existingMaintenance->replacement_type === 'new' ? 'selected' : '' }}>
                                                                                            全新</option>
                                                                                        <option value="usable"
                                                                                            {{ $existingMaintenance && isset($existingMaintenance->replacement_type) && $existingMaintenance->replacement_type === 'usable' ? 'selected' : '' }}>
                                                                                            堪用</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>

                                                                            <!-- 庫存警告訊息 -->
                                                                            <div id="maintenance_stock_warning_{{ $equipment->id }}"
                                                                                class="alert alert-danger mt-2"
                                                                                style="display: none; font-size: 0.75rem; padding: 0.5rem;">
                                                                                <i class="mdi mdi-alert me-1"></i>
                                                                                <span
                                                                                    id="maintenance_stock_warning_text_{{ $equipment->id }}"></span>
                                                                            </div>

                                                                            <textarea class="form-control" id="equipment_problem_{{ $equipment->id }}"
                                                                                name="equipment_problem[{{ $equipment->id }}]" rows="2" placeholder="請詳細描述發現的問題...">{{ $existingMaintenance && isset($existingMaintenance->problem_description) ? $existingMaintenance->problem_description : '' }}</textarea>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach

                            </div>

                            <!-- 供電系統檢查 -->
                            <div class="power-system-check mb-4">
                                <div class="group-header mb-3">
                                    <h5 class="text-primary mb-2">
                                        <i class="mdi mdi-flash me-2"></i>
                                        供電系統檢查
                                    </h5>
                                </div>

                                <div class="sub-group mb-3">
                                    <h6 class="text-secondary mb-2">
                                        <i class="mdi mdi-circle-small me-1"></i>
                                        供電設備
                                    </h6>

                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="30%">檢查項目</th>
                                                    <th width="20%">正常</th>
                                                    <th width="20%">有問題</th>
                                                    <th width="30%">問題描述</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <strong>供電系統能不能使用</strong>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio"
                                                                name="power_system_status" id="power_system_good"
                                                                value="good"
                                                                {{ isset($maintenance->power_system_status) && $maintenance->power_system_status === 'good' ? 'checked' : '' }}
                                                                onchange="togglePowerProblemText('power_system', 'good')">
                                                            <label class="form-check-label text-success"
                                                                for="power_system_good">
                                                                ✓
                                                            </label>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio"
                                                                name="power_system_status" id="power_system_problem"
                                                                value="problem"
                                                                {{ isset($maintenance->power_system_status) && $maintenance->power_system_status === 'problem' ? 'checked' : '' }}
                                                                onchange="togglePowerProblemText('power_system', 'problem')">
                                                            <label class="form-check-label text-danger"
                                                                for="power_system_problem">
                                                                ✗
                                                            </label>
                                                        </div>
                                                    </td>
                                                    <td class="problem-description-cell">
                                                        <div id="power_system_problem_container"
                                                            class="problem-description-inline"
                                                            style="display: {{ isset($maintenance->power_system_status) && $maintenance->power_system_status === 'problem' ? 'block' : 'none' }};">
                                                            <textarea class="form-control" id="power_system_problem_text" name="power_system_problem" rows="2"
                                                                placeholder="請詳細描述發現的問題...">{{ $maintenance->power_system_problem ?? '' }}</textarea>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <strong>220v伏特高壓電線</strong>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio"
                                                                name="high_voltage_wire_status"
                                                                id="high_voltage_wire_good" value="good"
                                                                {{ isset($maintenance->high_voltage_wire_status) && $maintenance->high_voltage_wire_status === 'good' ? 'checked' : '' }}
                                                                onchange="togglePowerProblemText('high_voltage_wire', 'good')">
                                                            <label class="form-check-label text-success"
                                                                for="high_voltage_wire_good">
                                                                ✓
                                                            </label>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio"
                                                                name="high_voltage_wire_status"
                                                                id="high_voltage_wire_problem" value="problem"
                                                                {{ isset($maintenance->high_voltage_wire_status) && $maintenance->high_voltage_wire_status === 'problem' ? 'checked' : '' }}
                                                                onchange="togglePowerProblemText('high_voltage_wire', 'problem')">
                                                            <label class="form-check-label text-danger"
                                                                for="high_voltage_wire_problem">
                                                                ✗
                                                            </label>
                                                        </div>
                                                    </td>
                                                    <td class="problem-description-cell">
                                                        <div id="high_voltage_wire_problem_container"
                                                            class="problem-description-inline"
                                                            style="display: {{ isset($maintenance->high_voltage_wire_status) && $maintenance->high_voltage_wire_status === 'problem' ? 'block' : 'none' }};">
                                                            <textarea class="form-control" id="high_voltage_wire_problem_text" name="high_voltage_wire_problem" rows="2"
                                                                placeholder="請詳細描述發現的問題...">{{ $maintenance->high_voltage_wire_problem ?? '' }}</textarea>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>


                            <div class="text-end">
                                @if ($maintenance->status == 3)
                                    <button type="button" class="btn btn-outline-info me-2" onclick="printChecklist()">
                                        <i class="mdi mdi-printer me-1"></i>列印檢查表
                                    </button>
                                    <a href="{{ route('crematorium.maintenance') }}"
                                        class="btn btn-secondary me-2">回上一頁</a>
                                    <button type="submit" name="submit" value="check" class="btn btn-primary">
                                        @if (Auth::user()->job_id == 1 ||
                                                Auth::user()->job_id == 2 ||
                                                Auth::user()->job_id == 3 ||
                                                Auth::user()->job_id == 7 ||
                                                Auth::user()->job_id == 10)
                                            <i class="mdi mdi-check me-1"></i>確認審核
                                        @else
                                            <i class="mdi mdi-check me-1"></i>送出審核
                                        @endif
                                    </button>
                                @else
                                    <a href="{{ route('crematorium.maintenance') }}"
                                        class="btn btn-secondary me-2">回上一頁</a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- container -->

    <script>
        // 列印功能
        function printChecklist() {
            // 顯示列印標題
            document.querySelector('.print-header').classList.remove('d-none');

            // 觸發列印
            window.print();

            // 列印完成後隱藏標題
            setTimeout(() => {
                document.querySelector('.print-header').classList.add('d-none');
            }, 1000);
        }

        function toggleProblemText(equipmentId, status) {
            const problemTextContainer = document.getElementById('problem_text_container_' + equipmentId);
            const problemTextarea = document.getElementById('equipment_problem_' + equipmentId);

            if (status === 'problem') {
                problemTextContainer.style.display = 'block';
                problemTextarea.required = true;
            } else {
                problemTextContainer.style.display = 'none';
                problemTextarea.required = false;
                problemTextarea.value = ''; // 清空問題描述
            }
        }

        // 檢查檢查維護的庫存
        function checkMaintenanceStock(equipmentId) {
            const equipmentRow = document.querySelector(`[data-equipment-id="${equipmentId}"]`);
            if (!equipmentRow) return true;

            const excludeInventory = equipmentRow.dataset.excludeInventory === 'true';
            const stockNew = parseInt(equipmentRow.dataset.stockNew);
            const stockUsable = parseInt(equipmentRow.dataset.stockUsable);
            const equipmentName = equipmentRow.dataset.equipmentName;

            const quantityInput = document.getElementById('equipment_quantity_' + equipmentId);
            const replacementTypeSelect = document.getElementById('equipment_replacement_type_' + equipmentId);
            const warningDiv = document.getElementById('maintenance_stock_warning_' + equipmentId);
            const warningText = document.getElementById('maintenance_stock_warning_text_' + equipmentId);

            // 如果不列入庫存，不需要檢查
            if (excludeInventory) {
                if (warningDiv) warningDiv.style.display = 'none';
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

            if (!hasStock && warningDiv) {
                warningText.textContent = message;
                warningDiv.style.display = 'block';
            } else if (warningDiv) {
                warningDiv.style.display = 'none';
            }

            return hasStock;
        }

        // 切換更換類型下拉選單的顯示/隱藏
        function toggleReplacementType(equipmentId) {
            const replaceRadio = document.getElementById('equipment_action_' + equipmentId + '_replace');
            const quantityInput = document.getElementById('equipment_quantity_' + equipmentId);
            const replacementTypeSelect = document.getElementById('equipment_replacement_type_' + equipmentId);
            const warningDiv = document.getElementById('maintenance_stock_warning_' + equipmentId);

            if (replaceRadio && replaceRadio.checked) {
                // 顯示並啟用
                if (quantityInput) {
                    quantityInput.disabled = false;
                    quantityInput.required = true;
                }
                if (replacementTypeSelect) {
                    replacementTypeSelect.style.display = 'inline-block';
                    replacementTypeSelect.disabled = false;
                    replacementTypeSelect.required = true;
                }
                // 檢查庫存
                checkMaintenanceStock(equipmentId);
            } else {
                // 隱藏並禁用（避免表單驗證錯誤）
                if (quantityInput) {
                    quantityInput.disabled = true;
                    quantityInput.required = false;
                }
                if (replacementTypeSelect) {
                    replacementTypeSelect.style.display = 'none';
                    replacementTypeSelect.disabled = true;
                    replacementTypeSelect.required = false;
                }
                // 隱藏警告
                if (warningDiv) {
                    warningDiv.style.display = 'none';
                }
            }
        }

        // 供電系統檢查的問題描述切換
        function togglePowerProblemText(itemName, status) {
            const problemContainer = document.getElementById(itemName + '_problem_container');
            const problemTextarea = document.getElementById(itemName + '_problem_text');

            if (status === 'problem') {
                problemContainer.style.display = 'block';
                problemTextarea.required = true;
            } else {
                problemContainer.style.display = 'none';
                problemTextarea.required = false;
                problemTextarea.value = ''; // 清空問題描述
            }
        }


        // 表單提交前的驗證
        document.querySelector('form').addEventListener('submit', function(e) {
            // 先檢查庫存
            let hasStockIssue = false;
            const replaceRadios = document.querySelectorAll('input[type="radio"][value="replace"]:checked');
            replaceRadios.forEach(radio => {
                const match = radio.name.match(/\[(\d+)\]/);
                if (match) {
                    const equipmentId = match[1];
                    if (!checkMaintenanceStock(equipmentId)) {
                        hasStockIssue = true;
                    }
                }
            });

            if (hasStockIssue) {
                e.preventDefault();
                alert('庫存不足！請檢查標示為紅色警告的項目，調整數量或更換類型。');
                const firstWarning = document.querySelector(
                    '[id^="maintenance_stock_warning_"]:not([style*="display: none"])');
                if (firstWarning) {
                    firstWarning.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
                return false;
            }

            let hasProblemWithoutDescription = false;
            let checkedCount = 0;

            // 檢查所有設備的狀態
            const allRadios = document.querySelectorAll('input[type="radio"][name^="equipment_status"]');
            const equipmentIds = new Set();

            // 收集所有設備ID
            allRadios.forEach(radio => {
                const match = radio.name.match(/\[(\d+)\]/);
                if (match) {
                    equipmentIds.add(match[1]);
                }
            });

            // 檢查每個設備
            equipmentIds.forEach(equipmentId => {
                const checkedRadio = document.querySelector(
                    `input[name="equipment_status[${equipmentId}]"]:checked`);

                if (checkedRadio) {
                    checkedCount++;

                    // 如果標記為有問題，檢查是否有填寫問題描述
                    if (checkedRadio.value === 'problem') {
                        const problemTextarea = document.getElementById('equipment_problem_' + equipmentId);
                        if (!problemTextarea.value.trim()) {
                            hasProblemWithoutDescription = true;
                        }
                    }
                }
            });

            if (hasProblemWithoutDescription) {
                e.preventDefault();
                alert('請為標記為「有問題」的設備填寫問題描述');
                return false;
            }

            // 檢查是否至少檢查了一個設備（排除未檢查狀態）
            const checkedGoodOrProblem = document.querySelectorAll(
                'input[type="radio"][value="good"]:checked, input[type="radio"][value="problem"]:checked');
            if (checkedGoodOrProblem.length === 0) {
                e.preventDefault();
                alert('請至少檢查一個設備（選擇「正常」或「有問題」）');
                return false;
            }
        });

        // 頁面載入時初始化
        document.addEventListener('DOMContentLoaded', function() {
            // 初始化已選擇的問題描述顯示狀態
            const problemRadios = document.querySelectorAll('input[type="radio"][value="problem"]:checked');
            problemRadios.forEach(radio => {
                const equipmentId = radio.name.match(/\[(\d+)\]/)[1];
                toggleProblemText(equipmentId, 'problem');
            });

            // 初始化供電系統問題描述顯示狀態
            const powerProblemRadios = document.querySelectorAll(
                'input[name="power_system_status"][value="problem"]:checked, input[name="high_voltage_wire_status"][value="problem"]:checked'
                );
            powerProblemRadios.forEach(radio => {
                const itemName = radio.name.replace('_status', '');
                togglePowerProblemText(itemName, 'problem');
            });

            // 初始化更換類型下拉選單的顯示狀態
            const replaceRadios = document.querySelectorAll('input[type="radio"][value="replace"]:checked');
            replaceRadios.forEach(radio => {
                const match = radio.name.match(/\[(\d+)\]/);
                if (match) {
                    const equipmentId = match[1];
                    toggleReplacementType(equipmentId);
                }
            });
        });
    </script>
@endsection

    </script>
@endsection
