@extends('layouts.vertical', ['page_title' => '指派檢查人員'])

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
    
    .form-check-input:checked + .form-check-label {
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
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
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
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
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
                            <li class="breadcrumb-item active">指派檢查人員</li>
                        </ol>
                    </div>
                    <h4 class="page-title">指派檢查人員</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('crematorium.assignMaintenance') }}" method="POST">
                            @csrf

                            <!-- 指派檢查任務資訊 -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="maintenance_number" class="form-label">檢查單號</label>
                                            <input type="text" class="form-control" id="maintenance_number" name="maintenance_number" 
                                                   value="{{ old('maintenance_number', $maintenanceNumber ?? '') }}" readonly>
                                            <small class="text-muted">系統自動產生</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                        <label for="scheduled_date" class="form-label">預定檢查日期 <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('scheduled_date') is-invalid @enderror" 
                                               id="scheduled_date" name="scheduled_date" value="{{ old('scheduled_date', date('Y-m-d')) }}" required>
                                        @error('scheduled_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                        <label for="assigned_inspector" class="form-label">指派檢查人員 <span class="text-danger">*</span></label>
                                        <select class="form-control @error('assigned_inspector') is-invalid @enderror" 
                                                id="assigned_inspector" name="assigned_inspector" required>
                                            <option value="">請選擇檢查人員</option>
                                            @if(isset($staff))
                                                @foreach($staff as $person)
                                                    <option value="{{ $person->id }}" {{ old('assigned_inspector') == $person->id ? 'selected' : '' }}>
                                                        {{ $person->name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('assigned_inspector')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                        <label for="assigned_maintainer" class="form-label">指派保養人員</label>
                                        <select class="form-control @error('assigned_maintainer') is-invalid @enderror" 
                                                id="assigned_maintainer" name="assigned_maintainer">
                                            <option value="">請選擇保養人員</option>
                                            @if(isset($staff))
                                                @foreach($staff as $person)
                                                    <option value="{{ $person->id }}" {{ old('assigned_maintainer') == $person->id ? 'selected' : '' }}>
                                                        {{ $person->name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('assigned_maintainer')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="instructions" class="form-label">檢查說明</label>
                                            <textarea class="form-control @error('instructions') is-invalid @enderror" 
                                                      id="instructions" name="instructions" rows="3" 
                                                      placeholder="請輸入檢查重點、注意事項或其他說明...">{{ old('instructions') }}</textarea>
                                            @error('instructions')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                            <!-- 檢查範圍 -->
                            <div class="mb-4">
                                <h5 class="mb-3">檢查範圍</h5>
                                <div class="alert alert-info">
                                    <i class="mdi mdi-information me-2"></i>
                                    <strong>本次檢查將包含以下設備：</strong>
                                    <ul class="mb-0 mt-2">
                                        <li>一爐設備（一火、一火A、一火B）</li>
                                        <li>二爐設備（二火）</li>
                                        <li>抽風設備</li>
                                        <li>供電系統（供電系統、220v伏特高壓電線）</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="text-end">
                                <a href="{{ route('crematorium.equipment.index') }}" class="btn btn-secondary me-2">取消</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-send me-1"></i>指派檢查任務
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- container -->

    <script>
        // 表單提交前的驗證
        document.querySelector('form').addEventListener('submit', function(e) {
            console.log('Form submit event triggered');
            
            const assignedInspector = document.getElementById('assigned_inspector').value;
            const scheduledDate = document.getElementById('scheduled_date').value;
            
            console.log('Inspector:', assignedInspector);
            console.log('Date:', scheduledDate);
            
            if (!assignedInspector) {
                e.preventDefault();
                alert('請選擇指派檢查人員');
                return false;
            }
            
            if (!scheduledDate) {
                e.preventDefault();
                alert('請選擇預定檢查日期');
                return false;
            }
            
            console.log('Form validation passed, submitting...');
        });

        // 頁面載入時初始化
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, initializing form...');
            
            // 設定預設日期為今天
            const today = new Date();
            const todayString = today.toISOString().split('T')[0];
            
            const dateInput = document.getElementById('scheduled_date');
            if (dateInput && !dateInput.value) {
                dateInput.value = todayString;
                console.log('Set default date to:', todayString);
            }
            
            // 檢查表單元素
            const form = document.querySelector('form');
            const submitBtn = document.querySelector('button[type="submit"]');
            
            console.log('Form found:', !!form);
            console.log('Submit button found:', !!submitBtn);
            
            if (submitBtn) {
                submitBtn.addEventListener('click', function(e) {
                    console.log('Submit button clicked');
                });
            }
        });
    </script>
@endsection
