@extends('layouts.vertical', ['page_title' => '流程關卡設定'])

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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">人事管理</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('leaveworkflow.index') }}">請假流程管理</a></li>
                            <li class="breadcrumb-item active">流程關卡設定</li>
                        </ol>
                    </div>
                    <h4 class="page-title">流程關卡設定 - {{ $workflow->name }}</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="text-uppercase bg-light p-2 mt-0 mb-0">關卡列表</h5>
                            <button type="button" class="btn btn-primary waves-effect waves-light" 
                                    data-bs-toggle="modal" data-bs-target="#addStepModal">
                                <i class="mdi mdi-plus me-1"></i>新增關卡
                            </button>
                        </div>

                        @if($workflow->steps->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-centered table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="10%">順序</th>
                                            <th width="25%">關卡名稱</th>
                                            <th width="25%">審核人員</th>
                                            <th width="15%">狀態</th>
                                            <th width="15%">建立時間</th>
                                            <th width="15%">操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($workflow->steps->sortBy('step_order') as $step)
                                            <tr>
                                                <td>
                                                    <span class="badge bg-primary">{{ $step->step_order }}</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="mdi mdi-account-check me-2 text-success"></i>
                                                        {{ $step->step_name }}
                                                    </div>
                                                </td>
                                                <td>{{ $step->approver->name ?? '未指定' }}</td>
                                                <td>
                                                    @if($step->is_active)
                                                        <span class="badge bg-success">啟用</span>
                                                    @else
                                                        <span class="badge bg-secondary">停用</span>
                                                    @endif
                                                </td>
                                                <td>{{ date('Y-m-d H:i', strtotime($step->created_at)) }}</td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                                onclick="editStep({{ $step->id }})">
                                                            <i class="mdi mdi-pencil"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-{{ $step->is_active ? 'warning' : 'success' }}" 
                                                                onclick="toggleStepStatus({{ $step->id }}, {{ $step->is_active ? 0 : 1 }})">
                                                            <i class="mdi mdi-{{ $step->is_active ? 'pause' : 'play' }}"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                                onclick="deleteStep({{ $step->id }})">
                                                            <i class="mdi mdi-delete"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <div class="text-muted">
                                    <i class="mdi mdi-information-outline me-2"></i>
                                    尚未設定任何關卡
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="text-uppercase bg-light p-2 mt-0 mb-3">流程資訊</h5>
                        
                        <div class="mb-3">
                            <label class="form-label">流程名稱</label>
                            <p class="form-control-plaintext">{{ $workflow->name }}</p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">流程描述</label>
                            <p class="form-control-plaintext">{{ $workflow->description ?: '無' }}</p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">狀態</label>
                            <p>
                                @if($workflow->is_active)
                                    <span class="badge bg-success">啟用中</span>
                                @else
                                    <span class="badge bg-secondary">已停用</span>
                                @endif
                            </p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">關卡數量</label>
                            <p class="form-control-plaintext">{{ $workflow->steps->count() }} 個關卡</p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">建立時間</label>
                            <p class="form-control-plaintext">{{ date('Y-m-d H:i', strtotime($workflow->created_at)) }}</p>
                        </div>

                        <div class="d-grid gap-2">
                            <a href="{{ route('leaveworkflow.edit', $workflow->id) }}" 
                               class="btn btn-outline-primary">
                                <i class="mdi mdi-pencil me-1"></i>編輯流程
                            </a>
                            <a href="{{ route('leaveworkflow.index') }}" 
                               class="btn btn-outline-secondary">
                                <i class="mdi mdi-arrow-left me-1"></i>返回列表
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div> <!-- container -->

    <!-- 新增關卡 Modal -->
    <div class="modal fade" id="addStepModal" tabindex="-1" aria-labelledby="addStepModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addStepModalLabel">新增關卡</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('leaveworkflow.steps.store', $workflow->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">關卡名稱 <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="step_name" 
                                   placeholder="例：直屬主管" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">審核人員 <span class="text-danger">*</span></label>
                            <select class="form-select" name="approver_user_id" required>
                                <option value="">請選擇審核人員</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">順序 <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="step_order" 
                                   value="{{ $workflow->steps->count() + 1 }}" min="1" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">狀態</label>
                            <select class="form-select" name="is_active">
                                <option value="1" selected>啟用</option>
                                <option value="0">停用</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                        <button type="submit" class="btn btn-primary">新增關卡</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 編輯關卡 Modal -->
    <div class="modal fade" id="editStepModal" tabindex="-1" aria-labelledby="editStepModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editStepModalLabel">編輯關卡</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editStepForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">關卡名稱 <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="step_name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">審核人員 <span class="text-danger">*</span></label>
                            <select class="form-select" name="approver_user_id" required>
                                <option value="">請選擇審核人員</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">順序 <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="step_order" min="1" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">狀態</label>
                            <select class="form-select" name="is_active">
                                <option value="1">啟用</option>
                                <option value="0">停用</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                        <button type="submit" class="btn btn-primary">儲存修改</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
function editStep(stepId) {
    // 這裡會發送 AJAX 請求取得關卡資料並填入表單
    fetch(`/leaveworkflow/steps/${stepId}/edit`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const form = document.getElementById('editStepForm');
                form.action = `/leaveworkflow/steps/${stepId}`;
                form.querySelector('input[name="step_name"]').value = data.step.step_name;
                form.querySelector('select[name="approver_user_id"]').value = data.step.approver_user_id;
                form.querySelector('input[name="step_order"]').value = data.step.step_order;
                form.querySelector('select[name="is_active"]').value = data.step.is_active;
                
                const modal = new bootstrap.Modal(document.getElementById('editStepModal'));
                modal.show();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('載入關卡資料失敗');
        });
}

function toggleStepStatus(stepId, status) {
    const action = status ? '啟用' : '停用';
    if (confirm(`確定要${action}此關卡嗎？`)) {
        fetch(`/leaveworkflow/steps/${stepId}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('操作失敗：' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('操作失敗，請稍後再試');
        });
    }
}

function deleteStep(stepId) {
    if (confirm('確定要刪除此關卡嗎？此操作無法復原！')) {
        fetch(`/leaveworkflow/steps/${stepId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('刪除失敗：' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('刪除失敗，請稍後再試');
        });
    }
}
</script>
@endsection
