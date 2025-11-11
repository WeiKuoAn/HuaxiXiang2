@extends('layouts.vertical', ['page_title' => '加成設定管理'])

@section('css')
    <style>
        .form-label {
            font-size: 1.05rem;
        }
        
        .form-control {
            font-size: 1.05rem;
        }
        
        .btn {
            font-size: 1.05rem;
        }
        
        h4, h5, h6 {
            font-size: 1.2rem;
        }
        
        .text-muted {
            font-size: 1rem;
        }
        
        .table {
            font-size: 1.05rem;
        }
        
        .badge {
            font-size: 1rem !important;
            padding: 0.6rem 1rem !important;
        }
        
        th, td {
            font-size: 1.05rem;
            vertical-align: middle;
        }
        
        .setting-card {
            border: 1px solid #e3eaef;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background-color: #fff;
        }
        
        .setting-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #dee2e6;
        }
        
        .input-group-text {
            font-size: 1.05rem;
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
                        <li class="breadcrumb-item"><a href="{{ route('increase.index') }}">加成管理</a></li>
                        <li class="breadcrumb-item active">系統設定</li>
                    </ol>
                </div>
                <h4 class="page-title">
                    <i class="fe-settings me-2"></i>加成系統設定
                </h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fe-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <!-- Tabs 導航 -->
                    <ul class="nav nav-pills bg-light nav-justified mb-3" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" data-bs-toggle="tab" href="#bonus-settings-tab" role="tab" aria-selected="true">
                                <i class="fe-dollar-sign me-1"></i>
                                <span class="d-none d-sm-inline">加成設定</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#time-slots-tab" role="tab" aria-selected="false">
                                <i class="fe-clock me-1"></i>
                                <span class="d-none d-sm-inline">夜間開爐時段</span>
                            </a>
                        </li>
                    </ul>

                    <!-- Tabs 內容 -->
                    <div class="tab-content">
                        <!-- 加成設定 Tab -->
                        <div class="tab-pane show active" id="bonus-settings-tab" role="tabpanel">
                            <form action="{{ route('increase-setting.batch-update') }}" method="POST" id="settingForm">
                                @csrf
                                @method('PUT')

                                <div class="alert alert-info">
                                    <i class="fe-info me-2"></i><strong>說明：</strong>
                                    設定各類加成的電話費用和接件費用。預設為電話 $100、接件 $500。
                                </div>

                        @php
                            $typeNames = [
                                'typhoon' => '颱風',
                                'newyear' => '過年',
                                'night' => '夜間加成',
                                'evening' => '晚間加成'
                            ];
                            $typeIcons = [
                                'typhoon' => 'fe-cloud-rain',
                                'newyear' => 'fe-gift',
                                'night' => 'fe-moon',
                                'evening' => 'fe-sun'
                            ];
                            $typeBadges = [
                                'typhoon' => 'bg-warning text-dark',
                                'newyear' => 'bg-danger text-white',
                                'night' => 'bg-dark text-white',
                                'evening' => 'bg-info text-white'
                            ];
                        @endphp

                        @forelse($settings as $index => $setting)
                            <div class="setting-card">
                                <input type="hidden" name="settings[{{ $index }}][id]" value="{{ $setting->id }}">
                                
                                <div class="setting-header">
                                    <h5 class="mb-0">
                                        <i class="{{ $typeIcons[$setting->type] ?? 'fe-settings' }} me-2"></i>
                                        <span class="badge {{ $typeBadges[$setting->type] ?? 'bg-secondary' }}">
                                            {{ $typeNames[$setting->type] ?? $setting->type }}
                                        </span>
                                    </h5>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                            name="settings[{{ $index }}][status]" value="active"
                                            id="status_{{ $setting->id }}"
                                            {{ $setting->status === 'active' ? 'checked' : '' }}
                                            onchange="this.value = this.checked ? 'active' : 'inactive'">
                                        <label class="form-check-label" for="status_{{ $setting->id }}">
                                            {{ $setting->status === 'active' ? '啟用' : '停用' }}
                                        </label>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i class="fe-phone me-1"></i>電話費用
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" class="form-control" 
                                                    name="settings[{{ $index }}][phone_bonus]"
                                                    value="{{ $setting->phone_bonus }}"
                                                    min="0" step="1" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i class="fe-user-check me-1"></i>接件費用
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" class="form-control"
                                                    name="settings[{{ $index }}][receive_bonus]"
                                                    value="{{ $setting->receive_bonus }}"
                                                    min="0" step="1" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="alert alert-warning">
                                <i class="fe-alert-triangle me-2"></i>目前沒有任何加成設定。
                            </div>
                        @endforelse

                                @if($settings->count() > 0)
                                    <div class="row mt-4">
                                        <div class="col-12 text-center">
                                            <button type="submit" class="btn btn-success waves-effect waves-light m-1">
                                                <i class="fe-save me-1"></i>儲存所有設定
                                            </button>
                                            <a href="{{ route('increase.index') }}" class="btn btn-secondary waves-effect waves-light m-1">
                                                <i class="fe-x me-1"></i>返回
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </form>
                        </div>
                        <!-- 加成設定 Tab 結束 -->

                        <!-- 夜間開爐時段 Tab -->
                        <div class="tab-pane" id="time-slots-tab" role="tabpanel">
                            <div class="mb-3 text-end">
                                <button type="button" class="btn btn-success waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#addTimeSlotModal">
                                    <i class="fe-plus-circle me-1"></i>新增時段
                                </button>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-centered table-nowrap table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>排序</th>
                                            <th>時段名稱</th>
                                            <th>時間範圍</th>
                                            <th>公斤數範圍</th>
                                            <th>價格</th>
                                            <th>持續時間</th>
                                            <th>狀態</th>
                                            <th>描述</th>
                                            <th>動作</th>
                                        </tr>
                                    </thead>
                                    <tbody id="timeSlotTableBody">
                                        <tr>
                                            <td colspan="9" class="text-center">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">載入中...</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- 夜間開爐時段 Tab 結束 -->

                    </div>
                    <!-- tab-content 結束 -->
                    
                </div> <!-- end card-body -->
            </div> <!-- end card-->
        </div> <!-- end col-->
    </div>
    <!-- end row-->

    <!-- 新增時段 Modal -->
    <div class="modal fade" id="addTimeSlotModal" tabindex="-1" aria-labelledby="addTimeSlotModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addTimeSlotModalLabel">新增夜間開爐時段</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="timeSlotForm" method="POST" action="{{ route('increase.time-slots.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">時段名稱 <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="start_time" class="form-label">開始時間 <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" id="start_time" name="start_time" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="end_time" class="form-label">結束時間 <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" id="end_time" name="end_time" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="min_weight" class="form-label">最低公斤數</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="min_weight" name="min_weight" step="0.1" min="0" placeholder="留空表示無下限">
                                        <span class="input-group-text">公斤</span>
                                    </div>
                                    <small class="text-muted">留空表示無下限</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="max_weight" class="form-label">最高公斤數</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="max_weight" name="max_weight" step="0.1" min="0" placeholder="留空表示無上限">
                                        <span class="input-group-text">公斤</span>
                                    </div>
                                    <small class="text-muted">留空表示無上限</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="price" class="form-label">價格 <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="sort_order" class="form-label">排序</label>
                                    <input type="number" class="form-control" id="sort_order" name="sort_order" min="0" value="0">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="description" class="form-label">描述</label>
                                    <textarea class="form-control" id="description" name="description" rows="3" placeholder="請輸入時段描述..."></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                                    <label class="form-check-label" for="is_active">
                                        啟用此時段
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                        <button type="submit" class="btn btn-success">新增時段</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 編輯時段 Modal -->
    <div class="modal fade" id="editTimeSlotModal" tabindex="-1" aria-labelledby="editTimeSlotModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editTimeSlotModalLabel">編輯夜間開爐時段</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editTimeSlotForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_time_slot_id" name="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_name" class="form-label">時段名稱 <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_name" name="name" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="edit_start_time" class="form-label">開始時間 <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" id="edit_start_time" name="start_time" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="edit_end_time" class="form-label">結束時間 <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" id="edit_end_time" name="end_time" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="edit_min_weight" class="form-label">最低公斤數</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="edit_min_weight" name="min_weight" step="0.1" min="0" placeholder="留空表示無下限">
                                        <span class="input-group-text">公斤</span>
                                    </div>
                                    <small class="text-muted">留空表示無下限</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="edit_max_weight" class="form-label">最高公斤數</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="edit_max_weight" name="max_weight" step="0.1" min="0" placeholder="留空表示無上限">
                                        <span class="input-group-text">公斤</span>
                                    </div>
                                    <small class="text-muted">留空表示無上限</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="edit_price" class="form-label">價格 <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="edit_price" name="price" step="0.01" min="0" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="edit_sort_order" class="form-label">排序</label>
                                    <input type="number" class="form-control" id="edit_sort_order" name="sort_order" min="0" value="0">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="edit_description" class="form-label">描述</label>
                                    <textarea class="form-control" id="edit_description" name="description" rows="3" placeholder="請輸入時段描述..."></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active" value="1">
                                    <label class="form-check-label" for="edit_is_active">
                                        啟用此時段
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                        <button type="submit" class="btn btn-primary">更新時段</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div> <!-- container -->
@endsection

@section('script')
    <script>
        // 監聽 switch 變化更新顯示文字
        document.querySelectorAll('.form-check-input[role="switch"]').forEach(function(switchEl) {
            switchEl.addEventListener('change', function() {
                const label = this.nextElementSibling;
                if (this.checked) {
                    label.textContent = '啟用';
                    this.value = 'active';
                } else {
                    label.textContent = '停用';
                    this.value = 'inactive';
                }
            });
        });

        // 表單驗證
        document.getElementById('settingForm')?.addEventListener('submit', function(e) {
            const inputs = this.querySelectorAll('input[type="number"]');
            let hasError = false;

            inputs.forEach(function(input) {
                if (input.value === '' || parseFloat(input.value) < 0) {
                    hasError = true;
                    input.classList.add('is-invalid');
                } else {
                    input.classList.remove('is-invalid');
                }
            });

            if (hasError) {
                e.preventDefault();
                alert('請確認所有金額欄位都已填寫且不能為負數！');
                return false;
            }
        });

        // ========== 夜間開爐時段管理 JavaScript ==========
        
        // 載入夜間開爐時段數據
        function loadTimeSlots() {
            fetch('/increase/time-slots-api/all', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderTimeSlots(data.data);
                } else {
                    document.getElementById('timeSlotTableBody').innerHTML = `
                        <tr>
                            <td colspan="9" class="text-center">
                                <div class="alert alert-danger mb-0">
                                    <i class="fe-alert-triangle me-2"></i>載入失敗
                                </div>
                            </td>
                        </tr>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('timeSlotTableBody').innerHTML = `
                    <tr>
                        <td colspan="9" class="text-center">
                            <div class="alert alert-danger mb-0">
                                <i class="fe-alert-triangle me-2"></i>載入失敗，請稍後再試
                            </div>
                        </td>
                    </tr>`;
            });
        }

        // 渲染時段列表
        function renderTimeSlots(timeSlots) {
            const tbody = document.getElementById('timeSlotTableBody');
            
            if (timeSlots.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="9" class="text-center">
                            <div class="alert alert-warning mb-0">
                                <i class="fe-alert-triangle me-2"></i>目前沒有任何夜間開爐時段設定。
                            </div>
                        </td>
                    </tr>`;
                return;
            }

            let html = '';
            timeSlots.forEach(timeSlot => {
                const startTime = timeSlot.start_time ? timeSlot.start_time.substring(0, 5) : '';
                const endTime = timeSlot.end_time ? timeSlot.end_time.substring(0, 5) : '';
                const statusBadge = timeSlot.is_active ? 
                    '<span class="badge bg-success">啟用</span>' : 
                    '<span class="badge bg-danger">停用</span>';
                
                // 計算公斤數範圍顯示
                let weightRange = '';
                if (!timeSlot.min_weight && !timeSlot.max_weight) {
                    weightRange = '不限公斤數';
                } else if (!timeSlot.min_weight) {
                    weightRange = `${timeSlot.max_weight} 公斤以下`;
                } else if (!timeSlot.max_weight) {
                    weightRange = `${timeSlot.min_weight} 公斤以上`;
                } else if (timeSlot.min_weight == 0 && timeSlot.max_weight == 10) {
                    weightRange = '10公斤以下';
                } else if (timeSlot.min_weight == 10 && timeSlot.max_weight == 30) {
                    weightRange = '10公斤以上';
                } else {
                    weightRange = `${timeSlot.min_weight} - ${timeSlot.max_weight} 公斤`;
                }

                html += `
                    <tr>
                        <td>${timeSlot.sort_order}</td>
                        <td><strong>${timeSlot.name}</strong></td>
                        <td><span class="badge bg-info">${startTime} - ${endTime}</span></td>
                        <td><span class="badge bg-info">${weightRange}</span></td>
                        <td><span class="text-success fw-bold">$${parseFloat(timeSlot.price).toLocaleString()}</span></td>
                        <td><span class="badge bg-secondary">${timeSlot.duration || 0}小時</span></td>
                        <td>${statusBadge}</td>
                        <td><small class="text-muted">${timeSlot.description || '-'}</small></td>
                        <td>
                            <div class="btn-group dropdown">
                                <a href="javascript: void(0);" class="table-action-btn dropdown-toggle arrow-none btn btn-outline-secondary btn-sm waves-effect" data-bs-toggle="dropdown" aria-expanded="false">
                                    動作 <i class="fe-chevron-down"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="#" onclick="editTimeSlot(${timeSlot.id}); return false;">
                                        <i class="fe-edit me-2"></i>編輯
                                    </a>
                                    <a class="dropdown-item" href="#" onclick="toggleTimeSlotStatus(${timeSlot.id}, ${!timeSlot.is_active}); return false;">
                                        <i class="fe-${timeSlot.is_active ? 'pause' : 'play'}-circle me-2"></i>${timeSlot.is_active ? '停用' : '啟用'}
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item text-danger" href="#" onclick="deleteTimeSlot(${timeSlot.id}); return false;">
                                        <i class="fe-trash-2 me-2"></i>刪除
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>`;
            });

            tbody.innerHTML = html;
        }
        
        // 編輯時段
        function editTimeSlot(id) {
            // 獲取時段資料
            fetch(`/increase/time-slots/${id}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const timeSlot = data.data;
                    
                    // 填入表單
                    document.getElementById('edit_time_slot_id').value = timeSlot.id;
                    document.getElementById('edit_name').value = timeSlot.name;
                    document.getElementById('edit_start_time').value = timeSlot.start_time ? timeSlot.start_time.substring(0, 5) : '';
                    document.getElementById('edit_end_time').value = timeSlot.end_time ? timeSlot.end_time.substring(0, 5) : '';
                    document.getElementById('edit_min_weight').value = timeSlot.min_weight || '';
                    document.getElementById('edit_max_weight').value = timeSlot.max_weight || '';
                    document.getElementById('edit_price').value = timeSlot.price;
                    document.getElementById('edit_sort_order').value = timeSlot.sort_order;
                    document.getElementById('edit_description').value = timeSlot.description || '';
                    document.getElementById('edit_is_active').checked = timeSlot.is_active;
                    
                    // 顯示 Modal
                    const modal = new bootstrap.Modal(document.getElementById('editTimeSlotModal'));
                    modal.show();
                } else {
                    alert('載入時段資料失敗');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('載入時段資料失敗，請稍後再試');
            });
        }

        // 處理編輯表單提交
        document.getElementById('editTimeSlotForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const id = document.getElementById('edit_time_slot_id').value;
            const formData = new FormData(this);
            
            // 轉換為 JSON，保留空值但正確處理
            const data = {
                name: document.getElementById('edit_name').value,
                start_time: document.getElementById('edit_start_time').value,
                end_time: document.getElementById('edit_end_time').value,
                min_weight: document.getElementById('edit_min_weight').value || '',
                max_weight: document.getElementById('edit_max_weight').value || '',
                price: document.getElementById('edit_price').value,
                sort_order: document.getElementById('edit_sort_order').value || 0,
                description: document.getElementById('edit_description').value || '',
                is_active: document.getElementById('edit_is_active').checked ? 1 : 0
            };
            
            console.log('提交數據：', data); // 調試用
            
            fetch(`/increase/time-slots/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => {
                        throw new Error(err.message || '更新失敗');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert('時段更新成功！');
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editTimeSlotModal'));
                    if (modal) {
                        modal.hide();
                    }
                    loadTimeSlots(); // 重新載入列表
                } else {
                    alert('更新失敗：' + (data.message || '未知錯誤'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('更新失敗：' + error.message);
            });
        });

        // 切換時段狀態
        function toggleTimeSlotStatus(id, status) {
            if (confirm('確定要' + (status ? '啟用' : '停用') + '此時段嗎？')) {
                fetch(`/increase/time-slots/${id}/toggle-status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('狀態已更新！');
                        loadTimeSlots(); // 重新載入列表
                    } else {
                        alert('更新失敗：' + (data.message || '未知錯誤'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('更新失敗，請稍後再試');
                });
            }
        }

        // 刪除時段
        function deleteTimeSlot(id) {
            if (confirm('確定要刪除此時段嗎？此操作無法復原。')) {
                fetch(`/increase/time-slots/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('時段已刪除！');
                        loadTimeSlots(); // 重新載入列表
                    } else {
                        alert('刪除失敗：' + (data.message || '未知錯誤'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('刪除失敗，請稍後再試');
                });
            }
        }

        // 處理新增表單提交
        document.getElementById('timeSlotForm')?.addEventListener('submit', function(e) {
            // 表單正常提交，成功後重新導向回此頁面
        });

        // 時間驗證
        const endTimeInput = document.getElementById('end_time');
        if (endTimeInput) {
            endTimeInput.addEventListener('change', function() {
                const startTime = document.getElementById('start_time').value;
                const endTime = this.value;
                
                if (startTime && endTime) {
                    if (startTime >= endTime) {
                        alert('結束時間必須晚於開始時間');
                        this.value = '';
                    }
                }
            });
        }

        // 公斤數範圍驗證
        const maxWeightInput = document.getElementById('max_weight');
        if (maxWeightInput) {
            maxWeightInput.addEventListener('change', function() {
                const minWeight = parseFloat(document.getElementById('min_weight').value) || 0;
                const maxWeight = parseFloat(this.value) || 0;
                
                if (minWeight > 0 && maxWeight > 0 && minWeight >= maxWeight) {
                    alert('最高公斤數必須大於最低公斤數');
                    this.value = '';
                }
            });
        }

        // 處理 URL 中的 hash，如果有 #time-slots 則切換到夜間開爐時段 tab
        document.addEventListener('DOMContentLoaded', function() {
            // 監聽 tab 切換事件，當切換到夜間開爐時段 tab 時載入數據
            const timeSlotTabLink = document.querySelector('a[href="#time-slots-tab"]');
            if (timeSlotTabLink) {
                timeSlotTabLink.addEventListener('shown.bs.tab', function (e) {
                    // 檢查是否已經載入過，避免重複載入
                    const tbody = document.getElementById('timeSlotTableBody');
                    if (tbody && tbody.querySelector('.spinner-border')) {
                        loadTimeSlots();
                    }
                });
            }

            // 如果 URL 中有 #time-slots，直接切換到夜間開爐時段 tab
            if (window.location.hash === '#time-slots') {
                const timeSlotTab = document.querySelector('a[href="#time-slots-tab"]');
                if (timeSlotTab) {
                    const tab = new bootstrap.Tab(timeSlotTab);
                    tab.show();
                    // 切換後載入數據
                    setTimeout(() => loadTimeSlots(), 100);
                }
            }
        });
    </script>
@endsection

