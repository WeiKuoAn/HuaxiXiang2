@extends('layouts.vertical', ['page_title' => '刪除流程'])

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
                            <li class="breadcrumb-item"><a href="{{ route('flow.index') }}">流程管理</a></li>
                            <li class="breadcrumb-item active">刪除流程</li>
                        </ol>
                    </div>
                    <h4 class="page-title">刪除流程確認</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('flow.destroy', $workflow->id) }}" method="POST" id="delete-form">
                            @csrf
                            @method('DELETE')
                            
                            <h5 class="text-uppercase bg-light p-2 mt-0 mb-3">基本資訊</h5>
                            
                            <div class="row">
                                <div class="col-xl-4">
                                    <div class="mb-3">
                                        <label class="form-label">流程類別</label>
                                        <input type="text" class="form-control" 
                                               value="{{ $workflow->category == 'leave' ? '請假管理' : '懲處管理' }}" readonly>
                                    </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="mb-3">
                                        <label class="form-label">適用職稱</label>
                                        <input type="text" class="form-control" 
                                               value="{{ $workflow->job->name ?? '未指定職稱' }}" readonly>
                                    </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="mb-3">
                                        <label class="form-label">流程名稱</label>
                                        <input type="text" class="form-control" 
                                               value="{{ $workflow->name }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="mb-3">
                                        <label class="form-label">流程描述</label>
                                        <textarea class="form-control" rows="3" readonly>{{ $workflow->description }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="mb-3">
                                        <label class="form-label">狀態</label>
                                        <input type="text" class="form-control" 
                                               value="{{ $workflow->is_active ? '啟用' : '停用' }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <h5 class="text-uppercase bg-light p-2 mt-4 mb-3">流程關卡設定</h5>
                            
                            <div id="steps-container">
                                @foreach($workflow->steps->sortBy('step_order') as $index => $step)
                                    <div class="step-item border p-3 mb-3 rounded">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">審核人員</label>
                                                    <input type="text" class="form-control" 
                                                           value="{{ $step->approver->name ?? '未指定' }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label class="form-label">順序</label>
                                                    <input type="number" class="form-control" 
                                                           value="{{ $step->step_order }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label class="form-label">狀態</label>
                                                    <input type="text" class="form-control" 
                                                           value="{{ $step->is_active ? '啟用' : '停用' }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="row mt-4">
                                <div class="col-12 text-center">
                                    @if($activeRequests > 0)
                                        <div class="alert alert-danger">
                                            <i class="mdi mdi-alert-circle me-1"></i>
                                            <strong>無法刪除：</strong>此流程有 {{ $activeRequests }} 筆待審核中的申請，請先處理完畢後再刪除
                                        </div>
                                        <a href="{{ route('flow.index') }}" class="btn btn-secondary waves-effect waves-light">
                                            <i class="mdi mdi-arrow-left me-1"></i>返回列表
                                        </a>
                                    @else
                                        <button type="submit" class="btn btn-danger waves-effect waves-light me-2" id="delete-btn">
                                            <i class="mdi mdi-delete me-1"></i>確認刪除
                                        </button>
                                        <a href="{{ route('flow.index') }}" 
                                           class="btn btn-secondary waves-effect waves-light">
                                            <i class="mdi mdi-arrow-left me-1"></i>返回列表
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="text-uppercase bg-light p-2 mt-0 mb-3">刪除警告</h5>
                        
                        <div class="alert alert-danger">
                            <h6><i class="mdi mdi-alert me-2"></i>注意事項</h6>
                            <ul class="mb-0">
                                <li>刪除流程後，該流程的所有審核關卡也會一併刪除</li>
                                <li>如果有進行中的申請使用此流程，將無法刪除</li>
                                <li>已完成或已駁回的歷史記錄不會受影響</li>
                                <li>此操作無法復原</li>
                            </ul>
                        </div>

                        <h5 class="text-uppercase bg-light p-2 mt-4 mb-3">流程統計</h5>
                        
                        <div class="mb-3">
                            <label class="form-label">流程ID</label>
                            <p class="form-control-plaintext">{{ $workflow->id }}</p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">關卡數量</label>
                            <p class="form-control-plaintext">{{ $workflow->steps->count() }} 個關卡</p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">使用此流程的申請</label>
                            <p class="form-control-plaintext">
                                總計 <span class="badge bg-info">{{ $totalRequests }}</span> 筆
                                @if($activeRequests > 0)
                                    <br><br>
                                    <span class="text-danger">
                                        <i class="mdi mdi-alert-circle me-1"></i>
                                        其中 <span class="badge bg-danger">{{ $activeRequests }}</span> 筆待審核中
                                    </span>
                                @endif
                            </p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">建立時間</label>
                            <p class="form-control-plaintext">{{ date('Y-m-d H:i:s', strtotime($workflow->created_at)) }}</p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">最後更新</label>
                            <p class="form-control-plaintext">{{ date('Y-m-d H:i:s', strtotime($workflow->updated_at)) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div> <!-- container -->
@endsection

@section('script')
<script>
// 表單提交處理
document.getElementById('delete-form').addEventListener('submit', function(e) {
    if (!confirm('確定要刪除「{{ $workflow->name }}」流程嗎？\n\n此操作無法復原，刪除後該流程的所有關卡也會一併刪除。')) {
        e.preventDefault();
        return false;
    }
    
    // 顯示載入狀態
    const deleteBtn = document.getElementById('delete-btn');
    if (deleteBtn) {
        deleteBtn.disabled = true;
        deleteBtn.innerHTML = '<i class="mdi mdi-loading mdi-spin me-1"></i>刪除中...';
    }
});
</script>
@endsection

