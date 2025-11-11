@extends('layouts.vertical', ['page_title' => '夜間開爐時段管理'])

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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">加成管理</a></li>
                            <li class="breadcrumb-item active">夜間開爐時段管理</li>
                        </ol>
                    </div>
                    <h4 class="page-title">夜間開爐時段管理</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <!-- 操作按鈕區域 -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="text-end">
                    <button type="button" class="btn btn-success waves-effect waves-light me-2" data-bs-toggle="modal" data-bs-target="#addTimeSlotModal">
                        <i class="mdi mdi-plus-circle me-1"></i>新增時段
                    </button>
                    <a href="{{ route('increase.index') }}" class="btn btn-secondary waves-effect waves-light">
                        <i class="fe-arrow-left me-1"></i>返回加成列表
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
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
                                <tbody>
                                    @foreach ($timeSlots as $timeSlot)
                                        <tr>
                                            <td>{{ $timeSlot->sort_order }}</td>
                                            <td>
                                                <strong>{{ $timeSlot->name }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ $timeSlot->start_time->format('H:i') }} - {{ $timeSlot->end_time->format('H:i') }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $timeSlot->weight_range_display }}</span>
                                            </td>
                                            <td>
                                                <span class="text-success fw-bold">{{ $timeSlot->price_display }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $timeSlot->duration }}小時</span>
                                            </td>
                                            <td>
                                                @if ($timeSlot->is_active)
                                                    <span class="badge bg-success">啟用</span>
                                                @else
                                                    <span class="badge bg-danger">停用</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $timeSlot->description ?? '-' }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group dropdown">
                                                    <a href="javascript: void(0);" class="table-action-btn dropdown-toggle arrow-none btn btn-outline-secondary waves-effect" data-bs-toggle="dropdown" aria-expanded="false">動作 <i class="mdi mdi-arrow-down-drop-circle"></i></a>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a class="dropdown-item" href="#" onclick="editTimeSlot({{ $timeSlot->id }})">
                                                            <i class="mdi mdi-pencil me-2 text-muted font-18 vertical-middle"></i>編輯
                                                        </a>
                                                        <a class="dropdown-item" href="#" onclick="toggleTimeSlotStatus({{ $timeSlot->id }}, {{ $timeSlot->is_active ? 'false' : 'true' }})">
                                                            <i class="mdi mdi-{{ $timeSlot->is_active ? 'pause' : 'play' }} me-2 text-muted font-18 vertical-middle"></i>{{ $timeSlot->is_active ? '停用' : '啟用' }}
                                                        </a>
                                                        <div class="dropdown-divider"></div>
                                                        <a class="dropdown-item text-danger" href="#" onclick="deleteTimeSlot({{ $timeSlot->id }})">
                                                            <i class="mdi mdi-delete me-2 text-muted font-18 vertical-middle"></i>刪除
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div> <!-- container -->

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

@endsection

@section('script')
    <script>
        // 編輯時段
        function editTimeSlot(id) {
            // 這裡可以實作編輯功能
            alert('編輯功能待實作，時段ID: ' + id);
        }

        // 切換時段狀態
        function toggleTimeSlotStatus(id, status) {
            if (confirm('確定要' + (status === 'true' ? '啟用' : '停用') + '此時段嗎？')) {
                // 這裡可以實作狀態切換功能
                alert('狀態切換功能待實作，時段ID: ' + id + '，新狀態: ' + status);
            }
        }

        // 刪除時段
        function deleteTimeSlot(id) {
            if (confirm('確定要刪除此時段嗎？此操作無法復原。')) {
                // 這裡可以實作刪除功能
                alert('刪除功能待實作，時段ID: ' + id);
            }
        }

        // 時間驗證
        document.getElementById('end_time').addEventListener('change', function() {
            const startTime = document.getElementById('start_time').value;
            const endTime = this.value;
            
            if (startTime && endTime) {
                if (startTime >= endTime) {
                    alert('結束時間必須晚於開始時間');
                    this.value = '';
                }
            }
        });

        // 公斤數範圍驗證
        document.getElementById('max_weight').addEventListener('change', function() {
            const minWeight = parseFloat(document.getElementById('min_weight').value) || 0;
            const maxWeight = parseFloat(this.value) || 0;
            
            if (minWeight > 0 && maxWeight > 0 && minWeight >= maxWeight) {
                alert('最高公斤數必須大於最低公斤數');
                this.value = '';
            }
        });
    </script>
@endsection
