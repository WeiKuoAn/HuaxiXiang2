@extends('layouts.vertical', ['page_title' => '流程管理'])

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
                            <li class="breadcrumb-item active">流程管理</li>
                        </ol>
                    </div>
                    <h4 class="page-title">流程管理</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <!-- 操作按鈕區域 -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row justify-content-between">
                            <div class="col-auto">
                                <h5 class="text-uppercase bg-light p-2 mt-0 mb-3">流程管理</h5>
                            </div>
                            <div class="col-auto">
                                <a href="{{ route('flow.create') }}">
                                    <button type="button" class="btn btn-primary waves-effect waves-light">
                                        <i class="mdi mdi-plus-circle me-1"></i>新增流程
                                    </button>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 職稱審核流程列表 -->
        <div class="row">
            @foreach($workflows as $workflow)
            <div class="col-lg-6 col-xl-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="mdi mdi-{{ $workflow->category == 'leave' ? 'calendar-clock' : 'account-alert' }} me-2 text-{{ $workflow->category == 'leave' ? 'primary' : 'warning' }}"></i>
                            {{ $workflow->job->name ?? '未指定職稱' }} - {{ $workflow->category == 'leave' ? '請假' : '懲處' }}審核流程
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="mb-1">{{ $workflow->name }}</h5>
                                <p class="text-muted mb-2">{{ $workflow->description }}</p>
                                <div class="d-flex gap-2">
                                    <span class="badge bg-{{ $workflow->category == 'leave' ? 'primary' : 'warning' }}">
                                        {{ $workflow->category == 'leave' ? '請假' : '懲處' }}
                                    </span>
                                    <span class="badge bg-info">{{ $workflow->steps_count ?? 0 }} 關卡</span>
                                    @if ($workflow->is_active)
                                        <span class="badge bg-success">啟用中</span>
                                    @else
                                        <span class="badge bg-secondary">已停用</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('flow.edit', $workflow->id) }}" class="btn btn-sm btn-primary">
                                <i class="mdi mdi-pencil"></i> 編輯流程
                            </a>
                            <button type="button" 
                                    class="btn btn-sm btn-outline-{{ $workflow->is_active ? 'warning' : 'success' }}"
                                    onclick="toggleStatus({{ $workflow->id }}, {{ $workflow->is_active ? 'false' : 'true' }})">
                                <i class="mdi mdi-{{ $workflow->is_active ? 'pause' : 'play' }}"></i> 
                                {{ $workflow->is_active ? '停用' : '啟用' }}
                            </button>
                            <a href="{{ route('flow.delete', $workflow->id) }}" 
                               class="btn btn-sm btn-outline-danger">
                                <i class="mdi mdi-delete"></i> 刪除
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            
            @if($workflows->isEmpty())
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="mdi mdi-plus-circle-outline text-muted" style="font-size: 4rem;"></i>
                        <h5 class="text-muted mt-3">尚未建立任何審核流程</h5>
                        <p class="text-muted">請先建立職稱審核流程</p>
                        <a href="{{ route('flow.create') }}" class="btn btn-primary">
                            <i class="mdi mdi-plus"></i> 建立流程
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>

    </div> <!-- container -->

@endsection

@push('scripts')
<script>
function toggleStatus(workflowId, status) {
    if (confirm('確定要' + (status ? '啟用' : '停用') + '此流程嗎？')) {
        $.ajax({
            url: '{{ url("flow") }}/' + workflowId + '/toggle-status',
            type: 'POST',
            data: {
                status: status,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('操作失敗：' + response.message);
                }
            },
            error: function() {
                alert('操作失敗，請稍後再試');
            }
        });
    }
}
</script>
@endpush