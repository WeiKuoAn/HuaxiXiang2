@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Huaxixiang</a></li>
                        <li class="breadcrumb-item">請假管理</li>
                        <li class="breadcrumb-item active">選擇審核流程</li>
                    </ol>
                </div>
                <h4 class="page-title">選擇審核流程</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="text-uppercase bg-light p-2 mt-0 mb-3">假單資訊</h5>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">申請人</label>
                                <input type="text" class="form-control" value="{{ $leaveDay->user->name }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">假別</label>
                                <input type="text" class="form-control" value="{{ $leaveDay->leave->name ?? '' }}" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">請假開始時間</label>
                                <input type="text" class="form-control" value="{{ $leaveDay->start_datetime }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">請假結束時間</label>
                                <input type="text" class="form-control" value="{{ $leaveDay->end_datetime }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="text-uppercase bg-light p-2 mt-0 mb-3">選擇審核流程</h5>
                    
                    <form action="{{ route('leave_day.submit', $leaveDay->id) }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">請選擇審核流程 <span class="text-danger">*</span></label>
                            <select class="form-select" name="workflow_id" required>
                                <option value="">請選擇審核流程</option>
                                @foreach ($workflows as $workflow)
                                    <option value="{{ $workflow->id }}">
                                        {{ $workflow->name }}
                                        @if($workflow->job)
                                            (適用：{{ $workflow->job->name }})
                                        @else
                                            (適用：全部職稱)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('workflow_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mt-4">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-success waves-effect waves-light me-2">
                                    <i class="mdi mdi-send me-1"></i>送出假單
                                </button>
                                <a href="{{ route('personnel.leave_days') }}" class="btn btn-secondary waves-effect waves-light">
                                    <i class="mdi mdi-arrow-left me-1"></i>返回列表
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="text-uppercase bg-light p-2 mt-0 mb-3">可用審核流程說明</h5>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>流程名稱</th>
                                    <th>適用職稱</th>
                                    <th>審核關卡數</th>
                                    <th>狀態</th>
                                    <th>說明</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($workflows as $workflow)
                                    <tr>
                                        <td>{{ $workflow->name }}</td>
                                        <td>
                                            @if($workflow->job)
                                                {{ $workflow->job->name }}
                                            @else
                                                <span class="text-muted">全部職稱</span>
                                            @endif
                                        </td>
                                        <td>{{ $workflow->steps_count ?? $workflow->steps->count() }}</td>
                                        <td>
                                            @if($workflow->is_active)
                                                <span class="badge bg-success">啟用</span>
                                            @else
                                                <span class="badge bg-secondary">停用</span>
                                            @endif
                                        </td>
                                        <td>{{ $workflow->description ?? '無' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
// 表單提交前的確認
document.querySelector('form').addEventListener('submit', function(e) {
    const workflowSelect = document.querySelector('select[name="workflow_id"]');
    if (!workflowSelect.value) {
        e.preventDefault();
        alert('請選擇審核流程');
        return false;
    }
    
    if (!confirm('確定要送出此假單嗎？送出後將無法修改。')) {
        e.preventDefault();
        return false;
    }
});
</script>
@endsection
